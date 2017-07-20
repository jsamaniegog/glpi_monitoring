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
global $DB, $CFG_GLPI;
include ("../../../inc/includes.php");

Session::checkRight("plugin_monitoring_componentscatalog", READ);

Html::header(
    __('Monitoring', 'monitoring'),
    $_SERVER["PHP_SELF"], 
    "plugins",
    "PluginMonitoringDashboard", 
    "componentscatalog"
);

if (isset($_POST['deleteitem']) 
    and isset($_POST['item']) 
    and is_array($_POST['item']) 
    and count($_POST['item'])>0) {
    
    $pmDependency = new PluginMonitoringDependency();
    
    foreach ($_POST['item'] as $id => $value) {
        $pmDependency->delete(array('id'=>$id));
    }
}

if (isset($_POST['add']) 
    and isset($_POST['componentscatalogs_id']) 
    and is_numeric($_POST['componentscatalogs_id'])
    and isset($_POST["componentscatalogs_hosts_id"]) 
    and is_numeric($_POST["componentscatalogs_hosts_id"])
    and isset($_POST["componentscatalogs_hosts_id_depends_upon"]) 
    and is_numeric($_POST["componentscatalogs_hosts_id_depends_upon"])
    and isset($_POST["componentscatalogs_services_id"]) 
    and isset($_POST["componentscatalogs_services_id_depends_upon"])) {
    
    $pmDependency = new PluginMonitoringDependency();
    
    if ($_POST["componentscatalogs_services_id"] and $_POST["componentscatalogs_services_id_depends_upon"]) {
        $result = $pmDependency->getFromDBByQuery("WHERE "
            . " host = " . $_POST["componentscatalogs_hosts_id"]
            . " AND host_dependency = " . $_POST["componentscatalogs_hosts_id_depends_upon"]
            . " AND service " . $_POST["componentscatalogs_services_id"]
            . " AND service_dependency " . $_POST["componentscatalogs_services_id_depends_upon"]);
        
    } else {
        $result = $pmDependency->getFromDBByQuery("WHERE "
            . "host = " . $_POST["componentscatalogs_hosts_id"]
            . " AND host_dependency = " . $_POST["componentscatalogs_hosts_id_depends_upon"]
            . " AND service is null "
            . " AND service_dependency is null");
    }
    
    if (!$result) {
        if ($_POST["componentscatalogs_services_id"] and $_POST["componentscatalogs_services_id_depends_upon"]) {
            $pmDependency->add(
                array(
                    'host' => $_POST['componentscatalogs_hosts_id'],
                    'host_dependency' => $_POST['componentscatalogs_hosts_id_depends_upon'],
                    'service' => $_POST['componentscatalogs_services_id'],
                    'service_dependency' => $_POST['componentscatalogs_services_id_depends_upon']
                )
            );
        
        } else {
            $pmDependency->add(
                array(
                    'host' => $_POST['componentscatalogs_hosts_id'],
                    'host_dependency' => $_POST['componentscatalogs_hosts_id_depends_upon']
                )
            );
        }
    }
}

Html::back();
Html::footer();
