<?php

/*
  ------------------------------------------------------------------------
  Plugin Monitoring for GLPI
  Copyright (C) 2011-2016 by the Plugin Monitoring for GLPI Development Team.

  https://forge.indepnet.net/projects/monitoring/
  ------------------------------------------------------------------------

  LICENSE

  This file is part of Plugin Monitoring project.

  Plugin Monitoring for GLPI is free software: you can redistribute it and/or modify
  it under the terms of the GNU Affero General Public License as published by
  the Free Software Foundation, either version 3 of the License, or
  (at your option) any later version.

  Plugin Monitoring for GLPI is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
  GNU Affero General Public License for more details.

  You should have received a copy of the GNU Affero General Public License
  along with Monitoring. If not, see <http://www.gnu.org/licenses/>.

  ------------------------------------------------------------------------

  @package   Plugin Monitoring for GLPI
  @author    Javier Samaniego
  @co-author
  @comment
  @copyright Copyright (c) 2011-2017 Plugin Monitoring for GLPI team
  @license   AGPL License 3.0 or (at your option) any later version
  http://www.gnu.org/licenses/agpl-3.0-standalone.html
  @link      https://forge.indepnet.net/projects/monitoring/
  @since     2017

  ------------------------------------------------------------------------
 */

if (!defined('GLPI_ROOT')) {
    die("Sorry. You can't access directly to this file");
}

class PluginMonitoringComponentscatalog_Dependency extends CommonDBTM {

    static $rightname = 'plugin_monitoring_componentscatalog';

    static function getTypeName($nb = 0) {
        return __('Host dependencies', 'monitoring');
    }

    /**
     * Returns SQL to get host dependencies.
     * @return string SQL
     */
    static function sqlDependencies($componentscatalogs_id = null) {
        $query = "SELECT d.id, concat_ws('',c.name,p.name,pr.name,n.name) as host_name,
			concat_ws('',c2.name,p2.name,pr2.name,n2.name) as hostname_dependency,
			s.name service_name,
                        sd.name service_name_dependency

                FROM glpi_plugin_monitoring_dependencies d
                
                LEFT JOIN glpi_plugin_monitoring_hosts h ON d.host = h.id
                LEFT JOIN glpi_plugin_monitoring_hosts hd ON d.host_dependency = hd.id
                LEFT JOIN glpi_plugin_monitoring_services s ON d.service = s.id
                LEFT JOIN glpi_plugin_monitoring_services sd ON d.service_dependency = sd.id
                
                LEFT JOIN glpi_computers c ON h.items_id = c.id AND h.itemtype = 'Computer'
                LEFT JOIN glpi_peripherals p ON h.items_id = p.id AND h.itemtype = 'Peripheral'
                LEFT JOIN glpi_printers pr ON h.items_id = pr.id AND h.itemtype = 'Printer'
                LEFT JOIN glpi_networkequipments n ON h.items_id = n.id AND h.itemtype = 'NetworkEquipment'
                
                LEFT JOIN glpi_computers c2 ON hd.items_id = c2.id AND hd.itemtype = 'Computer'
                LEFT JOIN glpi_peripherals p2 ON hd.items_id = p2.id AND hd.itemtype = 'Peripheral'
                LEFT JOIN glpi_printers pr2 ON hd.items_id = pr2.id AND hd.itemtype = 'Printer'
                LEFT JOIN glpi_networkequipments n2 ON hd.items_id = n2.id AND hd.itemtype = 'NetworkEquipment'";
        
        $query .= " WHERE 1=1 ";
        
        if (isset($componentscatalogs_id)) {
            $query .= " AND d.host in "
                . "(select h.id from glpi_plugin_monitoring_hosts h, "
                . "glpi_plugin_monitoring_componentscatalogs_hosts ch "
                . "where h.items_id = ch.items_id and h.itemtype = ch.itemtype "
                . "and ch.plugin_monitoring_componentscalalog_id = $componentscatalogs_id)";
        }
        
        return $query;
    }
    
    function showDependencies($componentscatalogs_id) {
        global $DB, $CFG_GLPI;

        $rand = mt_rand();

        $query = $this->sqlDependencies($componentscatalogs_id);
        $result = $DB->query($query);

        // INFO message
        echo Html::image($CFG_GLPI["root_doc"] . "/pics/info-big.png", array('alt' => __('Info')));
        echo "<span style='color:black;font-size:large;vertical-align:center;'> " 
            . __('Dependencies because of connections to network equipments will'
            . ' be loaded automatically when shinken imports the hosts.', 'linesmanager') 
            . "</span><br><br>";
        
        echo "<form method='post' name='componentscatalog_dependencies_form$rand' "
        . "id='componentscatalog_dependencies_form$rand' action=\""
        . $CFG_GLPI["root_doc"] . "/plugins/monitoring/front/componentscatalog_dependency.form.php\">";
        
        echo "<table class='tab_cadre_fixe'>";
        echo "<tr>";
        echo "<th colspan='5'>";
        if ($DB->numrows($result) == 0) {
            echo __('No associated dependencies', 'monitoring');
        } else {
            echo __('Associated dependencies', 'monitoring');
        }
        echo "</th>";
        echo "</tr>";
        echo "</table>";


        echo "<table class='tab_cadre_fixe'>";
        echo "<tr>";
        echo "<th width='10'>&nbsp;</th>";
        echo "<th>" . __('Host') . "</th>";
        echo "<th>" . __('Host depends upon') . "</th>";
        echo "<th>" . __('Service') . "</th>";
        echo "<th>" . __('Service depends upon') . "</th>";
        echo "</tr>";

        while ($data = $DB->fetch_array($result)) {
            echo "<tr>";
            echo "<td class='center'>";
            echo "<input type='checkbox' name='item[".$data["id"]."]' value='1'>";
            echo "</td>";
            echo "<td class='center'>";
            echo $data["host_name"];
            echo "</td>";
            echo "<td class='center'>";
            echo $data["hostname_dependency"];
            echo "</td>";
            echo "<td class='center'>";
            echo $data["service_name"];
            echo "</td>";
            echo "<td class='center'>";
            echo $data["service_name_dependency"];
            echo "</td>";
            echo "</tr>";
        }
        
        Html::openArrowMassives("componentscatalog_dependencies_form$rand", true);
        Html::closeArrowMassives(array('deleteitem' => _sx('button', 'Delete permanently')));

        echo "</table>";
        Html::closeForm();
        
        echo "<form method='post' name='componentscatalog_dependencies_form$rand' "
        . "id='componentscatalog_dependencies_form$rand' action=\""
        . $CFG_GLPI["root_doc"] . "/plugins/monitoring/front/componentscatalog_dependency.form.php\">";
        
        echo "<table class='tab_cadre_fixe'>";
        
        $array_of_hosts = array();
        $array_of_hosts_depends_upon = array();
        $array_of_services = array();
        $array_of_services_depends_upon = array();
        
        $query = "SELECT ch.plugin_monitoring_componentscalalog_id,h.id,
                concat_ws('',c.name,p.name,pr.name,n.name) as host_name
                FROM glpi_plugin_monitoring_hosts h, glpi_plugin_monitoring_componentscatalogs_hosts ch
                LEFT JOIN glpi_computers c ON ch.items_id = c.id AND itemtype = 'Computer'
                LEFT JOIN glpi_peripherals p ON ch.items_id = p.id AND itemtype = 'Peripheral'
                LEFT JOIN glpi_printers pr ON ch.items_id = pr.id AND itemtype = 'Printer'
                LEFT JOIN glpi_networkequipments n ON ch.items_id = n.id AND itemtype = 'NetworkEquipment'
                WHERE h.items_id = ch.items_id AND h.itemtype = ch.itemtype";
        $result = $DB->query($query);
        
        while ($data = $DB->fetch_array($result)) {
            if ($data['plugin_monitoring_componentscalalog_id'] == $componentscatalogs_id) {
                $array_of_hosts[$data['id']] = $data['host_name'];
            }
            $array_of_hosts_depends_upon[$data['id']] = $data['host_name'];
        }
        
        $query = "SELECT s.id, s.name as service_name, concat_ws('',c.name,p.name,pr.name,n.name) as host_name,
                ch.plugin_monitoring_componentscalalog_id
                FROM glpi_plugin_monitoring_services s, glpi_plugin_monitoring_componentscatalogs_hosts ch
                LEFT JOIN glpi_computers c ON ch.items_id = c.id AND itemtype = 'Computer'
                LEFT JOIN glpi_peripherals p ON ch.items_id = p.id AND itemtype = 'Peripheral'
                LEFT JOIN glpi_printers pr ON ch.items_id = pr.id AND itemtype = 'Printer'
                LEFT JOIN glpi_networkequipments n ON ch.items_id = n.id AND itemtype = 'NetworkEquipment'
                WHERE s.plugin_monitoring_componentscatalogs_hosts_id = ch.id";
        $result = $DB->query($query);
        
        while ($data = $DB->fetch_array($result)) {
            if ($data['plugin_monitoring_componentscalalog_id'] == $componentscatalogs_id) {
                $array_of_services[$data['id']] = $data['service_name'] . __(" in ", "monitoring") . $data['host_name'];
            }
            $array_of_services_depends_upon[$data['id']] = $data['service_name'] . __(" in ", "monitoring") . $data['host_name'];
        }
        
        $array_of_hosts = array_unique($array_of_hosts);
        $array_of_hosts_depends_upon = array_unique($array_of_hosts_depends_upon);
        
        echo "<tr><td>";
        echo __("Host name", "monitoring") . ": ";
        echo "</td><td>";
        Dropdown::showFromArray("componentscatalogs_hosts_id", $array_of_hosts);
        echo "</td><td>";
        echo __("Depends upon", "monitoring") . ": ";
        echo "</td><td>";
        Dropdown::showFromArray("componentscatalogs_hosts_id_depends_upon", $array_of_hosts_depends_upon);
        echo "</td><td>";
        echo __("Service name", "monitoring") . ": ";
        echo "</td><td>";
        Dropdown::showFromArray("componentscatalogs_services_id", $array_of_services, array('display_emptychoice' => true));
        echo "</td><td>";
        echo __("Depends upon", "monitoring") . ": ";
        echo "</td><td>";
        Dropdown::showFromArray("componentscatalogs_services_id_depends_upon", $array_of_services_depends_upon, array('display_emptychoice' => true));
        echo "</td><td>";
        echo Html::hidden("componentscatalogs_id", array('value'=>$componentscatalogs_id));
        echo "<input type='submit' name='add' class='submit' value=\"" . _sx('button', 'Add') . "\">";
        $options = array();
        echo "</td></tr>";
        
        echo "</table>";
        Html::closeForm();
    }

}
