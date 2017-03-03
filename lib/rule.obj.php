<?php
 /**
  * Rules management
  * @author Gouverneur Thomas <tgouverneur@be.tiauto.com>
  * @copyright Copyright (c) 2007-2008, Gouverneur Thomas - TI Automotive
  * @version 1.0
  * @package objects
  * @subpackage rule
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


class Rule extends MysqlObj
{
  public $id = -1;		/* ID in the MySQL table */
  public $name = "";		/* name of rule (only for interface) */
  public $type = "";		/* type of rule: pass|block */
  public $if = "";		/* interface NAME, fixed by rule */
  public $protocol = "";	/* tcp/udp|tcp|udp|icmp */
  public $snot = 0;		/* negation of the source */
  public $source = "";		/* source: alias|network|ipaddr */
  public $sport = "";		/* port or range of port for source */
  public $dnot = 0;		/* negation of the destination */
  public $destination = "";	/* destination: alias|network|ipaddr */
  public $dport = "";		/* port or range of port for destination */
  public $icmptype = "";	/* type of icmp */
  public $frags = 0;		/* fragment ? */
  public $log = 0;		/* log ? */
  public $description = "";	/* 255 char */
  
  /* link to other class */
  public $iface = array();	/* link to interface */
  public $ruleint = array();	/* link to rule-int row */
  public $host = array();	/* link to monowall device */

  /* function for MySQL */
  function existsInDb()
  {
    return $this->existsDb();
  }

  /* ctor */
  public function __construct($id=-1) 
  { 
    $this->id=$id; 
    $this->_table = "rules";
    $this->_my = array(
			"id" => SQL_INDEX,
			"name" => SQL_PROPE | SQL_EXIST | SQL_WHERE,
			"type" => SQL_PROPE | SQL_EXIST | SQL_WHERE,
			"if" => SQL_PROPE | SQL_EXIST | SQL_WHERE,
			"protocol" => SQL_PROPE | SQL_EXIST | SQL_WHERE,
			"snot" => SQL_PROPE | SQL_WHERE | SQL_EXIST,
			"dnot" => SQL_PROPE | SQL_WHERE | SQL_EXIST,
			"sport" => SQL_PROPE | SQL_WHERE | SQL_EXIST,
			"source" => SQL_PROPE | SQL_WHERE | SQL_EXIST,
			"dport" => SQL_PROPE | SQL_WHERE | SQL_EXIST,
			"destination" => SQL_PROPE | SQL_WHERE | SQL_EXIST,
			"icmptype" => SQL_PROPE | SQL_WHERE | SQL_EXIST,
			"frags" => SQL_PROPE,
			"log" => SQL_PROPE,
			"description" => SQL_PROPE
			);
    $this->_myc = array( /* mysql => class */
			"id" => "id",
			"name" => "name",
			"type" => "type",
			"if" => "if",
			"protocol" => "protocol",
			"snot" => "snot",
			"dnot" => "dnot",
			"sport" => "sport",
			"source" => "source",
			"dport" => "dport",
			"destination" => "destination",
			"icmptype" => "icmptype",
			"frags" => "frags",
			"log" => "log",
			"description" => "description"
			);



  }
}

?>
