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
   @author    David Durieux
   @co-author
   @comment
   @copyright Copyright (c) 2011-2016 Plugin Monitoring for GLPI team
   @license   AGPL License 3.0 or (at your option) any later version
              http://www.gnu.org/licenses/agpl-3.0-standalone.html
   @link      https://forge.indepnet.net/projects/monitoring/
   @since     2011

   ------------------------------------------------------------------------
 */

include ("../../../inc/includes.php");

Session::checkCentralAccess();

Html::header(__('Monitoring - dashboard', 'monitoring'), $_SERVER["PHP_SELF"], "plugins",
             "PluginMonitoringDashboard", "menu");

$pmMessage = new PluginMonitoringMessage();
$pmMessage->getMessages();

$toDisplayArea=0;

if (Session::haveRight("plugin_monitoring_dashboard", READ)
        && !Session::haveRight("config", READ)) {
   Html::redirect($CFG_GLPI['root_doc']."/plugins/monitoring/front/dashboard.php");
}

if (Session::haveRight("plugin_monitoring_dashboard", READ)
      && (
        Session::haveRight("plugin_monitoring_systemstatus", PluginMonitoringSystem::DASHBOARD)
         || Session::haveRight("plugin_monitoring_hoststatus", PluginMonitoringHost::DASHBOARD)
         || Session::haveRight("plugin_monitoring_componentscatalog", PluginMonitoringComponentscatalog::DASHBOARD)
         || Session::haveRight("plugin_monitoring_service", PluginMonitoringService::DASHBOARD)
         || Session::haveRight("plugin_monitoring_displayview", PluginMonitoringDisplayview::DASHBOARD))) {
   $toDisplayArea++;

   echo "<table class='tab_cadre' width='950'>";
   echo "<tr class='tab_bg_1'>";
   echo "<th>";
   echo "NEW MENU ALIGNAK";
   echo "</th>";
   echo "</tr>";

   echo "<tr class='tab_bg_1'>";
   echo "<td height='30' align='center'>";
   echo "<a href='".$CFG_GLPI['root_doc']."/plugins/monitoring/front/componentscatalog.php'>".__('Components catalog (templates + rules)', 'monitoring')."</a>";
   echo "</td>";
   echo "</tr>";

   echo "<tr class='tab_bg_1'>";
   echo "<td>";
   echo "<a href='".$CFG_GLPI['root_doc']."/plugins/monitoring/front/command.php'>".__('Commands', 'monitoring')."</a>";
   echo "
livestate
logs
hosts
services
realms
hostgroups
servicegroups
users
timeperiod
commands
worldmap
minemap

   ";
   echo "</td>";
   echo "</tr>";
   echo "</table>";

   echo "<br/>";












//   echo "<table class='tab_cadre' width='950'>";
//   echo "<tr class='tab_bg_1'>";
//   echo "<th height='80'>";
//   echo "<a href='".$CFG_GLPI['root_doc']."/plugins/monitoring/front/dashboard.php'>".__('Dashboard', 'monitoring')."</a>";
//   echo "</th>";
//   echo "</tr>";
//   echo "</table>";
//
//   echo "<br/>";
}
if (Session::haveRight("plugin_monitoring_displayview", READ)) {

   echo "<table class='tab_cadre' width='950'>";
   echo "<tr class='tab_bg_1'>";
   if (Session::haveRight("plugin_monitoring_displayview", READ)) {
      $toDisplayArea++;
      echo "<th align='center' height='40' width='34%'>";
      echo "<a href='".$CFG_GLPI['root_doc']."/plugins/monitoring/front/displayview.php'>".__('Views', 'monitoring')."</a>";
      echo "</th>";
   }
   echo "</tr>";
   echo "</table>";
   echo "<br/>";
}


if (Session::haveRight("plugin_monitoring_weathermap", READ)
      || Session::haveRight("plugin_monitoring_displayview", READ)) {
   echo "<table class='tab_cadre' width='950'>";
   echo "<tr class='tab_bg_1'>";

   if (Session::haveRight("plugin_monitoring_weathermap", READ)) {
      $toDisplayArea++;
      echo "<th align='center' height='30' width='33%'>";
      echo "<a href='".$CFG_GLPI['root_doc']."/plugins/monitoring/front/weathermap.php'>".__('Weathermaps', 'monitoring')."</a>";
      echo "</th>";
   }
   echo "</tr>";

//   echo "<tr class='tab_bg_1'>";
//   if (Session::haveRight("plugin_monitoring_displayview", READ)) {
//      $toDisplayArea++;
//      echo "<th align='center' height='30' width='34%'>";
//      echo "<a href='".$CFG_GLPI['root_doc']."/plugins/monitoring/front/customitem_gauge.php'>".PluginMonitoringCustomitem_Gauge::getTypeName()."</a>";
//      echo "</th>";
//
//      echo "<th align='center' height='30' width='34%'>";
//      echo "<a href='".$CFG_GLPI['root_doc']."/plugins/monitoring/front/customitem_counter.php'>".PluginMonitoringCustomitem_Counter::getTypeName()."</a>";
//      echo "</th>";
//   }
//   echo "</tr>";
   echo "</table>";
   echo "<br/>";
}

if (Session::haveRight("config", READ)) {

   $toDisplayArea++;
   echo "<table class='tab_cadre' width='950'>";
   echo "<tr class='tab_bg_1'>";
   echo "<th width='11%' height='25'>";
   echo "</th>";

   echo "<th width='11%'>";
   echo "</th>";

   echo "<th width='11%'>";
   echo "</th>";

   echo "<th width='11%'>";
   echo "</th>";

   echo "<th width='11%'>";
   echo "<a href='".$CFG_GLPI['root_doc']."/plugins/monitoring/front/perfdata.php'>".__('Graph templates', 'monitoring')."</a>";
   echo "</th>";

   echo "<th>";
   echo "</th>";

   echo "<th>";
   echo "<a href='".$CFG_GLPI['root_doc']."/plugins/monitoring/front/realm.php'>".__('Reamls', 'monitoring')."</a>";
   echo "</th>";

   echo "<th>";
   echo "<a href='".$CFG_GLPI['root_doc']."/plugins/monitoring/front/tag.php'>".__('Tag', 'monitoring')."</a>";
   echo "</th>";
   echo "</tr>";

   echo "</table>";



}

if ($toDisplayArea <= 0) {
   echo "<table class='tab_cadre' width='950'>";
   echo "<tr class='tab_bg_1'>";
   echo "<th height='80'>";
   echo __('Sorry, your profile does not allow any views in the Monitoring', 'monitoring');
   echo "</th>";
   echo "</tr>";
   echo "</table>";
}

Html::footer();

?>