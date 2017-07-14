<?php

/*
   ------------------------------------------------------------------------
   FusionInventory
   Copyright (C) 2010-2015 by the FusionInventory Development Team.

   http://www.fusioninventory.org/   http://forge.fusioninventory.org/
   ------------------------------------------------------------------------

   LICENSE

   This file is part of FusionInventory project.

   FusionInventory is free software: you can redistribute it and/or modify
   it under the terms of the GNU Affero General Public License as published by
   the Free Software Foundation, either version 3 of the License, or
   (at your option) any later version.

   FusionInventory is distributed in the hope that it will be useful,
   but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
   GNU Affero General Public License for more details.

   You should have received a copy of the GNU Affero General Public License
   along with FusionInventory. If not, see <http://www.gnu.org/licenses/>.

   ------------------------------------------------------------------------

   @package   FusionInventory
   @author    David Durieux
   @co-author
   @copyright Copyright (c) 2010-2015 FusionInventory team
   @license   AGPL License 3.0 or (at your option) any later version
              http://www.gnu.org/licenses/agpl-3.0-standalone.html
   @link      http://www.fusioninventory.org/
   @link      http://forge.fusioninventory.org/projects/fusioninventory-for-glpi/
   @since     2010

   ------------------------------------------------------------------------
 */

class GLPIlogs extends PHPUnit_Framework_TestCase {

   public function testSQLlogs() {

      $filecontent = '';
      $filecontent = file_get_contents(GLPI_ROOT."/files/_log/sql-errors.log");

      $this->assertEquals($filecontent, '', 'sql-errors.log not empty');
      // Reinitialize file
      file_put_contents(GLPI_ROOT."/files/_log/sql-errors.log", '');
   }



   public function testPHPlogs() {

      $filecontent = '';
      $filecontent = file_get_contents(GLPI_ROOT."/files/_log/php-errors.log");

      $this->assertEquals($filecontent, '', 'php-errors.log not empty');
      // Reinitialize file
      file_put_contents(GLPI_ROOT."/files/_log/php-errors.log", '');
   }

}



class GLPIlogs_AllTests  {

   public static function suite() {

      $suite = new PHPUnit_Framework_TestSuite('GLPIlogs');
      return $suite;
   }
}

?>
