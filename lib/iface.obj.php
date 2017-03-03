<?php
 /**
  * Interface management
  * @author Gouverneur Thomas <tgouverneur@be.tiauto.com>
  * @copyright Copyright (c) 2007-2008, Gouverneur Thomas - TI Automotive
  * @version 1.0
  * @package objects
  * @subpackage interface
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

class Iface extends MysqlObj
{
  public $id = -1;		/* ID in the MySQL table */
  public $type = "";		/* lan|wan|opt */
  public $num = 0;		/* number of interface (with optX) */
  public $enable = 1;		/* enabled or not */
  public $if = "";		/* interface name */
  public $description = "";	/* 255 char */
  public $ipaddr = "";		/* XXX.XXX.XXX.XXX */
  public $subnet = "";		/* XXX.XXX.XXX.XXX */
  public $media = "";
  public $mediaopt = "";
  public $gateway = "";		/* default gateway */
  public $dhcp = "";
  public $blockpriv = 0;
  public $bridge = "";
  public $mtu = "";
  public $spoofmac = "";
  public $idhost = -1; 		/* link to hostname table */

  /* link to other class */
  public $rules = array();          /* array of link to rule */
  public $rulesint = array();	/* array of link to ruleint rows */
  public $rulesp = array();	/* array of properties for temp tables of rules */
  public $mono = NULL;

  public $_root = "interfaces";
  public $_conf = array(
		  "lan" => array( 	"if" => "var:if",
					"ipaddr" => "var:ipaddr",
					"subnet" => "var:subnet",
					"media" => "var:media",
					"mediaopt" => "var:mediaopt"
          		   ),
       		  "wan" => array(
					"if" => "var:if",
					"mtu" => "var:mtu",
					"media" => "var:media",
                                        "mediaopt" => "var:mediaopt",
					"spoofmac" => "var:spoofmac",
					"ipaddr" => "var:ipaddr",
					"subnet" => "var:subnet",
					"gateway" => "var:gateway",
					"blockpriv" => "bool:blockpriv",
					"dhcphostname" => "varo:dhcp",
 			   ),
		  "opt" => array(
					"descr" => "var:description",
					"if" => "var:if",
					"ipaddr" => "varo:ipaddr",
					"subnet" => "varo:subnet",
					"media" => "varo:media",
                                        "mediaopt" => "varo:mediaopt",
					"bridge" => "var:bridge",
					"enable" => "bool:enable"
			   )
                  );

  private $_modified = FALSE;

  function existsInDb()
  {
    return $this->existsDb();
  }

  function fetchRules()
  {
    $fields = "`idrule`, `idint`, `position`, `enabled`";
    $table = "rules-int";
    $where = " WHERE `idint`='".$this->id."' ORDER BY `position` ASC";
    $m = Mysql::getInstance();

    if (($data = $m->select($fields, $table, $where)) === FALSE)
      return 0;

    foreach ($data as $d)
    {
      $ri = new RuleInt($d["idrule"], $d["idint"], $d["position"], $d["enabled"]);
      $ri->iface = $this;
      $ri->rule = Main::getInstance()->getRule($ri->idrule);
      array_push($this->rules, $ri->rule);
      array_push(Main::getInstance()->ruleint, $ri);
      array_push($this->rulesint, $ri);
    }

    return 1;
  }

  function isRuleInt($ru)
  {
    foreach ($this->ruleint as $ri)
    {
      if (($ru->idrule == $ri->idrule) &&
	  ($ru->idint == $ri->idint))
          return 1;
    }
    return 0;
  }

  /* ctor */
  public function __construct($id=-1) 
  {
    $this->id=$id; 

    $this->_table = "interfaces";
    $this->_my = array( 
			"id" => SQL_INDEX,
			"type" => SQL_PROPE | SQL_EXIST | SQL_WHERE,
			"num" => SQL_PROPE | SQL_EXIST | SQL_WHERE,
			"enable" => SQL_PROPE,
			"if" => SQL_PROPE | SQL_EXIST | SQL_WHERE,
			"description" => SQL_PROPE,
			"ipaddr" => SQL_PROPE,
			"subnet" => SQL_PROPE,
			"media" => SQL_PROPE,
			"mediaopt" => SQL_PROPE,
			"gateway" => SQL_PROPE,
			"dhcp" => SQL_PROPE,
			"blockpriv" => SQL_PROPE,
			"bridge" => SQL_PROPE,
			"mtu" => SQL_PROPE,
			"spoofmac" => SQL_PROPE,
			"idhost" => SQL_PROPE | SQL_EXIST | SQL_WHERE
		);

    /* mysql to var correspondance */
    $this->_myc = array( /* mysql => class */
                        "id" => "id",
			"type" => "type",
			"num" => "num",
			"enable" => "enable",
			"if" => "if",
			"description" => "description",
			"ipaddr" => "ipaddr",
			"subnet" => "subnet",
			"media" => "media",
			"mediaopt" => "mediaopt",
			"gateway" => "gateway",
			"dhcp" => "dhcp",
			"blockpriv" => "blockpriv",
			"bridge" => "bridge",
			"mtu" => "mtu",
			"spoofmac" => "spoofmac",
			"idhost" => "idhost",
                 );

  }
}

?>
