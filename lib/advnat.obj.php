<?php
 /**
  * Advanced NAT rules
  * @author Gouverneur Thomas <tgouverneur@be.tiauto.com>
  * @copyright Copyright (c) 2007-2008, Gouverneur Thomas - TI Automotive
  * @version 1.0
  * @package objects
  * @subpackage nat
  * @category classes
  * @filesource
  * @todo BUG: advanced outbound nat enabled function is not yet in db.
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

class AdvNat extends MysqlObj
{
  public $enable = 1;
  public $noportmap = 0;
  public $source = "";
  public $destination = "";
  public $dnot = 0;
  public $target = "";

  public $id = -1;
  public $description = "";
  public $if = "";
  public $idhost = -1;

  public $iface = NULL;
  public $mono = NULL;

  public $_root = "rule";
  public $_conf = array(
				"source" => array( "network" => "var:source" ),
				"descr" => "var:description",
				"target" => "var:target",
				"interface" => "var:if",
				"destination" => "ofct:arrayDest",
				"noportmap" => "bool:noportmap"
			);


  public function arrayDest()
  {
    $r = array();
    if ($this->dnot) $r["not"] = "";
    if ($this->destination == "ANY") $r["any"] = "";
    else $r["network"] = $this->destination;
    return $r;
  }

  function existsInDb()
  {
     return $this->existsDb();
  }

  public function __construct($id=-1)
  {
    $this->id = $id;


    $this->_my = array(
			"id" => SQL_INDEX,
			"description" => SQL_PROPE,
			"if" => SQL_PROPE | SQL_WHERE | SQL_EXIST,
			"idhost" => SQL_PROPE | SQL_WHERE | SQL_EXIST
			);
    $this->_myc = array( /* mysql => class */
			"id" => "id",
			"description" => "description",
			"if" => "if",
			"idhost" => "idhost"
			);

    $this->_myc = array_merge($this->_myc, array( /* mysql => class */
                           "enable" => "enable",
			   "noportmap" => "noportmap",
			   "source" => "source",
			   "destination" => "destination",
                           "dnot" => "dnot",
			   "target" => "target"
		         ));

    $this->_my = array_merge($this->_my, array(
				"enable" => SQL_PROPE,
				"noportmap" => SQL_PROPE,
				"source" => SQL_PROPE | SQL_EXIST | SQL_WHERE,
				"destination" => SQL_PROPE | SQL_EXIST | SQL_WHERE,
				"dnot" => SQL_PROPE | SQL_EXIST | SQL_WHERE,
				"target" => SQL_PROPE | SQL_EXIST | SQL_WHERE
			));

    $this->_table = "nat-advout";
  }
}

?>
