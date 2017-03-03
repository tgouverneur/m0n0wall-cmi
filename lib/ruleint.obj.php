<?php
 /**
  * Rules vs Interface link
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


class RuleInt extends MysqlObj
{
  public $idrule = -1;
  public $idint = -1;
  public $position;
  public $enabled = 1;

  /* link to other class */
  public $iface = NULL;		/* link to interface */
  public $rule = NULL;		/* link to rule */


  public $_root = "rule";
  public $_conf = array(
			"disabled" => "nbool:enabled",
			"type" => "var:rule->type",
			"interface" => "var:rule->if",
			"protocol" => "varo:rule->protocol",
			"source" => "ofct:arraySource",
			"destination" => "ofct:arrayDest",
			"log" => "bool:rule->log",
			"descr" => "var:rule->description",
			"icmptype" => "varo:rule->icmptype",
			"frags" => "bool:rule->frags"
		);

  function update() /* OVERLOADed function */
  {
    $set = "`position`='".$this->position."', `enabled`='".$this->enabled."'";
    $w = "WHERE `idint`='".$this->idint."' AND `idrule`='".$this->idrule."'";
    return Mysql::getInstance()->update($this->_table, $set, $w);
  }

  function delete() /* OVERLOADed function */
  {
    $w = "WHERE `idint`='".$this->idint."' AND `idrule`='".$this->idrule."'";
    return Mysql::getInstance()->delete($this->_table, $w);
  }

  function isChanged() /* OVERLOADed function */
  {
    $where = " WHERE ";
    $i = 0;
    
    if (!$this->existsDb()) return 0;

    foreach ($this->_my as $k => $v) {
      
      if ($v == SQL_INDEX) continue; /* skip index */
      if (!($v & SQL_PROPE)) continue;
      if ($i && $i < count($this->_my)) $where .= " AND ";

      $where .= "`".$k."`='".$this->{$this->_myc[$k]}."'";
      $i++;
    }
    
/*    $id = array_search(SQL_INDEX, $this->_my);

    if ($id !== FALSE) {
      
      if ($this->{$this->_myc[$id]} != -1) $where .= " AND `".$id."`='".$this->{$this->_myc[$id]}."'";
  */    
      $my = Mysql::getInstance();
      if (($data = $my->select("`idrule`", $this->_table, $where)) == FALSE)
        return 1;
      else {
        if ($my->getNbResult()) {
          return 0;
        } else
          return 1;
      }
//    }
  }

  function arraySource()
  {
    $r = array();
    if ($this->rule->snot)
      $r["not"] = "";

    if ($this->rule->source == "ANY" || $this->rule->source == "")
      $r["any"] = "";
    else if ($this->rule->source == "lan" || $this->rule->source == "wan" ||
	     $this->rule->source == "pptp" || (substr($this->rule->source , 0, 3) == "opt" && strlen($this->rule->source) <= 5))
      $r["network"] = $this->rule->source;
    else
      $r["address"] = $this->rule->source;

    if ($this->rule->sport != "")
      $r["port"] = $this->rule->sport;
    
    return $r;
  }

  function arrayDest()
  {
     $r = array();
    if ($this->rule->dnot)
      $r["not"] = "";
    if ($this->rule->destination == "ANY" || $this->rule->destination == "")
      $r["any"] = "";
    else if ($this->rule->destination == "lan" || $this->rule->destination == "wan" ||
	     $this->rule->destination == "pptp" || (substr($this->rule->destination, 0, 3) == "opt" && strlen($this->rule->destination) <= 5))
      $r["network"] = $this->rule->destination;
    else
      $r["address"] = $this->rule->destination;

    if ($this->rule->dport != "")
      $r["port"] = $this->rule->dport;
    
    return $r;
  }

  function dropAllIface($ifid)
  {
    $m = Mysql::getInstance();
    $m->delete("rules-int", "WHERE `idint`='".$ifid."'");
    return 1;
  }

  function existsInDb()
  {
    return $this->existsDb();
  }
 
  /* ctor */
  public function __construct($idr=-1, $idi=-1, $pos = -1, $ena = 1) 
  { 
    $this->idrule = $idr; 
    $this->idint = $idi; 
    $this->position = $pos;
    $this->enabled = $ena;
    $this->_table = "rules-int";
    $this->_my = array(
			"idrule" => SQL_WHERE | SQL_EXIST | SQL_PROPE,
			"idint" => SQL_WHERE | SQL_EXIST | SQL_PROPE,
			"position" => SQL_PROPE,
			"enabled" => SQL_PROPE
			);
    $this->_myc = array( /* mysql => class */
                        "idrule" => "idrule",
			"idint" => "idint",
			"position" => "position",
			"enabled" => "enabled"
		);

    }
}


?>
