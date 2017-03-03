<?php
 /**
  * SNMP management
  *
  * @author Gouverneur Thomas <tgouverneur@be.tiauto.com>
  * @copyright Copyright (c) 2007-2008, Gouverneur Thomas - TI Automotive
  * @version 1.0
  * @package objects
  * @subpackage snmp
  * @category classes
  * @filesource
  */
 /*
    m0n0wall Central Management Interface
    Copyright (C) 2007  Gouverneur Thomas

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
  */

class Snmp extends MysqlObj
{
  public $id = -1;
  public $syslocation = "";
  public $syscontact = "";
  public $rocommunity = "";
  public $enable = 0;
  public $bindlan = 0;

  /* link to other classes */
  public $mono = NULL;
  
   public $_root = "snmpd";
  public $_conf = array(
				"syslocation" => "var:syslocation",
				"syscontact" => "var:syscontact",
				"bindlan" => "bool:bindlan",
				"rocommunity" => "var:rocommunity",
				"enable" => "bool:enable",
			);

  function existsInDb()
  {
    return $this->existsDb();
  }

  /* other */
  public function __construct($id=-1)
  {
    $this->id = $id;

    $this->_table = "snmp";

    $this->_my = array(
			"id" => SQL_INDEX,
			"syslocation" => SQL_PROPE | SQL_WHERE | SQL_EXIST,
			"syscontact" => SQL_PROPE | SQL_WHERE | SQL_EXIST,
			"rocommunity" => SQL_PROPE | SQL_WHERE | SQL_EXIST,
			"bindlan" => SQL_PROPE,
			"enable" => SQL_PROPE,
			);
 
    /* var correspondance */
    $this->_myc = array( /* mysql => class */
			"id" => "id",
			"syslocation" => "syslocation",
			"syscontact" => "syscontact",
			"rocommunity" => "rocommunity",
			"bindlan" => "bindlan",
			"enable" => "enable"
		      );



  }


}


?>
