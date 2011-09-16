<?php

/*
   ----------------------------------------------------------------------
   Monitoring plugin for GLPI
   Copyright (C) 2010-2011 by the GLPI plugin monitoring Team.

   https://forge.indepnet.net/projects/monitoring/
   ----------------------------------------------------------------------

   LICENSE

   This file is part of Monitoring plugin for GLPI.

   Monitoring plugin for GLPI is free software: you can redistribute it and/or modify
   it under the terms of the GNU General Public License as published by
   the Free Software Foundation, either version 2 of the License, or
   any later version.

   Monitoring plugin for GLPI is distributed in the hope that it will be useful,
   but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   GNU General Public License for more details.

   You should have received a copy of the GNU General Public License
   along with Monitoring plugin for GLPI.  If not, see <http://www.gnu.org/licenses/>.

   ------------------------------------------------------------------------
   Original Author of file: David DURIEUX
   Co-authors of file:
   Purpose of file:
   ----------------------------------------------------------------------
 */

if (!defined('GLPI_ROOT')) {
   die("Sorry. You can't access directly to this file");
}

class PluginMonitoringBusinessrule extends CommonDBTM {
   

   function canCreate() {
      return true;
   }


   
   function canView() {
      return true;
   }


   
   function canCancel() {
      return true;
   }


   
   function canUndo() {
      return true;
   }


   
   function canValidate() {
      return true;
   }

   

   /**
   * Display form for agent configuration
   *
   * @param $items_id integer ID 
   * @param $options array
   *
   *@return bool true if form is ok
   *
   **/
   function showForm($businessapplications_id, $options=array()) {
      global $DB,$CFG_GLPI,$LANG;


//      $this->showFormHeader($options);
      
      $first_operator = array();
      $first_operator[''] = "------";
      $first_operator['2 of:'] = $LANG['plugin_monitoring']['businessrule'][2];
      $first_operator['3 of:'] = $LANG['plugin_monitoring']['businessrule'][3];
      $first_operator['4 of:'] = $LANG['plugin_monitoring']['businessrule'][4];
      $first_operator['5 of:'] = $LANG['plugin_monitoring']['businessrule'][5];
      $first_operator['6 of:'] = $LANG['plugin_monitoring']['businessrule'][6];
      $first_operator['7 of:'] = $LANG['plugin_monitoring']['businessrule'][7];
      $first_operator['8 of:'] = $LANG['plugin_monitoring']['businessrule'][8];
      $first_operator['9 of:'] = $LANG['plugin_monitoring']['businessrule'][9];
      $first_operator['10 of:'] = $LANG['plugin_monitoring']['businessrule'][10];
      
      $operator = array();
      $operator['and'] = $LANG['choice'][3];
      $operator['or']= $LANG['choice'][2];
      
      echo "<form name='form' method='post' 
         action='".$CFG_GLPI['root_doc']."/plugins/monitoring/front/businessrule.form.php'>";
      echo "<input type='hidden' name='businessapplications_id' value='".$businessapplications_id."'/>";

      $a_list = $this->find("`plugin_monitoring_businessapplications_id`='".$businessapplications_id."'", 
              "`group`, `position`");

      $groupnum = 0;
      $position = 0;
      $i = 0;
      foreach ($a_list as $data) {
         if ($groupnum == '0') {
            echo "<table class='tab_cadre' width='600'>";
            echo "<tr class='tab_bg_1'>";
            echo "<th colspan='2'>";
            echo "Group N°".$data['group'];
            echo "</th>";
            echo "</tr>";
         } else if ($groupnum != $data['group']) {
            
            $position++;
            echo "<tr class='tab_bg_1'>";
            echo "<td>";
            echo "<input type='hidden' name='num[]' value='".$groupnum."-".$position."' />";
            Dropdown::showFromArray('operator[]', $operator);
            echo "</td>";
            echo "<td>";
            $this->showService(0, '');
            echo "</td>";
            echo "</tr>";
            echo "</table><br/>";
            
            echo "<table class='tab_cadre'>";
            echo "<tr class='tab_bg_1'>";
            echo "<th>";
            echo $LANG['choice'][3];
            echo "</th>";
            echo "</tr>";
            echo "</table><br/>";

            echo "<table class='tab_cadre' width='600'>";
            echo "<tr class='tab_bg_1'>";
            echo "<th colspan='2'>";
            echo "Group N°".$data['group'];
            echo "</th>";
            echo "</tr>";
         }
         $groupnum = $data['group'];


         echo "<tr class='tab_bg_1'>";
         echo "<td>";
         echo "<input type='hidden' name='num[]' value='".$groupnum."-".$data['position']."-".$data['id']."' />";
         if ($data['position'] == '0') {
            Dropdown::showFromArray('operator[]', $first_operator);
         } else {
            Dropdown::showFromArray('operator[]', $operator);
         }
         echo "</td>";
         echo "<td>";
         $this->showService($data['items_id'], $data['itemtype'], $data['id']);
         echo "</td>";
         echo "</tr>";
         $i++;
      }
      
      if ($i > 0) {
         $position++;
         echo "<tr class='tab_bg_1'>";
         echo "<td>";
         echo "<input type='hidden' name='num[]' value='".$groupnum."-".$position."' />";
         Dropdown::showFromArray('operator[]', $operator);
         echo "</td>";
         echo "<td>";
         $this->showService(0, '');
         echo "</td>";
         echo "</tr>";
         echo "</table><br/>";

         echo "<table class='tab_cadre'>";
         echo "<tr class='tab_bg_1'>";
         echo "<th>";
         echo $LANG['choice'][3];
         echo "</th>";
         echo "</tr>";
         echo "</table><br/>";
      }
      
      // New group
      $groupnum++;
      echo "<table class='tab_cadre' width='400'>";
      echo "<tr class='tab_bg_1'>";
      echo "<th colspan='2'>";
      echo "Group N°".$groupnum;
      echo "</th>";
      echo "</tr>";
      
      $position = 0;
      echo "<tr class='tab_bg_1'>";
      echo "<td>";
      echo "<input type='hidden' name='num[]' value='".$groupnum."-".$position."' />";
      Dropdown::showFromArray('operator[]', $first_operator);
      echo "</td>";
      echo "<td>";
      $this->showService(0, '');
      echo "</td>";
      echo "</tr>";
      echo "</table>"; 

      echo "<br/><input type='submit' class='submit' name='update' value='update'/>";
      
      echo "</form><br/>";
      
//      $this->showFormButtons($options);
      return true;
   }
   
   
   
   static function showService($items_id, $itemtype, $businessrules_id=0) {
      global $CFG_GLPI, $LANG;

      if (!empty($items_id)) {
         $pMonitoringHost = new PluginMonitoringHost();
         $item = new $itemtype();
         
         $item->getFromDB($items_id);
         echo "\n<table width='100%'><tr>";
         echo "<td>";
         echo "<input type='hidden' name='services_id[]' value='".$items_id."' />";
         echo "<strong>".$item->getName()."</strong>";
         echo " ".$LANG['networking'][25]." ";
         $pMonitoringHost->getFromDB($item->fields['plugin_monitoring_hosts_id']);
         $itemtype2 = $pMonitoringHost->fields['itemtype'];
         $item2 = new $itemtype2();
         $item2->getFromDB($pMonitoringHost->fields['items_id']);
         echo $item2->getLink(1);
         echo "</td><td width='100'>";
         echo "<input type='hidden' name='businessrules_id' value='".$businessrules_id."' />";
         echo " <input type='submit' class='submit' name='delete' value='".$LANG['buttons'][6]."'";
         echo "</td></tr></table>\n";
      } else {
         echo "\n<table width='100%'><tr>";

            echo "<td class='left'>";

            self::dropdownService($items_id, array('name' => 'type'));

            echo "</td>\n";

         echo "</tr></table>\n";
      }
   }

   
   
   static function dropdownService($ID,$options=array()) {
      global $LANG, $CFG_GLPI;

      $p['name']        = 'networkports_id';
      $p['comments']    = 1;
      $p['entity']      = -1;
      $p['entity_sons'] = false;

     if (is_array($options) && count($options)) {
         foreach ($options as $key => $val) {
            $p[$key] = $val;
         }
      }

      // Manage entity_sons
      if (!($p['entity']<0) && $p['entity_sons']) {
         if (is_array($p['entity'])) {
            echo "entity_sons options is not available with array of entity";
         } else {
            $p['entity'] = getSonsOf('glpi_entities', $p['entity']);
         }
      }

      $rand = mt_rand();
      echo "<select name='itemtype[$ID]' id='itemtype$rand'>";
      echo "<option value='0'>".DROPDOWN_EMPTY_VALUE."</option>";

      $a_types =array();
      echo "<option value='Computer'>".Computer::getTypeName()."</option>";
      echo "<option value='NetworkEquipment'>".NetworkEquipment::getTypeName()."</option>";
      echo "</select>";

      $params = array('itemtype'        => '__VALUE__',
                      'entity_restrict' => $p['entity'],
                      'current'         => $ID,
                      'comments'        => $p['comments'],
                      'myname'          => $p['name'],
                      'rand'            => $rand);

      ajaxUpdateItemOnSelectEvent("itemtype$rand", "show_".$p['name']."$rand",
                                  $CFG_GLPI["root_doc"]."/plugins/monitoring/ajax/dropdownServiceHostType.php",
                                  $params);

      echo "<span id='show_".$p['name']."$rand'><input type='hidden' name='services_id[]' value='0'/></span>\n";

      return $rand;
   }
}

?>