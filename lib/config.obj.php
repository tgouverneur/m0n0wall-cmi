<?php
 /**
  * Config class
  * used to manipulate m0n0wall XML configuration as well as
  * database version of this configuration.
  * Also used to convert configuration from one format to another.
  *
  * @author Gouverneur Thomas <tgouverneur@be.tiauto.com>
  * @copyright Copyright (c) 2007-2008, Gouverneur Thomas - TI Automotive
  * @version 1.0
  * @package objects
  * @subpackage config
  * @category classes
  * @filesource
  * @todo Code cleaning
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

require_once("./lib/xmlparse.lib.php");


/**
  *
  * Config class
  * Used to manipulate m0n0wall XML configuration as well as
  * database version of this configuration.
  * Also used to convert configuration from one format to another.
  *
  * @category classes
  * @package objects
  * @subpackage config
  * @author Gouverneur Thomas <tgouverneur@be.tiauto.com>
  * @copyright Copyright (c) 2007-2008, Gouverneur Thomas - TI Automotive
  * @version 1.0
  * @uses Monowall
  * @uses xmlparse.lib.php
  * @uses Alias
  * @uses GAlias
  * @uses Buser
  * @uses AdvNat
  * @uses RuleNat
  * @uses O2ONat
  * @uses Rule
  * @uses RuleInt
  * @uses SrvNat
  * @uses Snmp
  * @uses Syslog
  * @uses Group
  * @uses User
  * @uses HwIface
  * @uses Iface
  * @uses Main
  * @uses Mysql
  * @uses Prop
  * @uses ProxyArp
  * @uses StaticRoute
  * @uses Syslog
  * @uses Vlan
  * 
  */
class Config
{
  /**
   * used to store XML raw configurtion took from m0n0wall device
   * @see fetchConfig()
   * @var string
   */
  public $rawconfig = NULL;
  /**
   * Array that contains the configuration before being converted to XML
   * @see dump_xml_config(), XML()
   * @var array
   */
  public $config = NULL;
   /**
   * Used to store enventual CURL error
   * @see fetchConfig()
   * @var string
   */
  public $error = NULL;
  /**
   * Timestamp of last modification
   * @see fillObj()
   * @var int
   */
  public $lastchange = NULL;
  /**
   * Part of arrayized XML config that concern system settings
   * @see fillObj()
   * @var array
   */
  public $system = NULL;
  /**
   * Part of arrayized XML config that concern interface settings
   * @see fillObj()
   * @var array
   */
  public $ifaces = NULL;
  /**
   * Part of arrayized XML config that concern Static routes settings
   * @see fillObj()
   * @var array
   */
  public $sroutes = NULL;
  /**
   * Part of arrayized XML config that concern SNMP settings
   * @see fillObj()
   * @var array
   */
  public $snmp = NULL;
  /**
   * Part of arrayized XML config that concern Syslog settings
   * @see fillObj()
   * @var array
   */
  public $syslog = NULL;
  /**
   * Part of arrayized XML config that concern Rules settings
   * @see fillObj()
   * @var array
   */
  public $rules = NULL;
  /**
   * Part of arrayized XML config that concern Filter settings
   * @see fillObj()
   * @var array
   */
  public $filter = NULL;
  /**
   * Part of arrayized XML config that concern NAT settings
   * @see fillObj()
   * @var array
   */
  public $nat = NULL;		/* normal NAT rules */
  /**
   * Part of arrayized XML config that concern Advanced Outbound settings
   * @see fillObj()
   * @var array
   */
  public $advnat = NULL;	/* Advanced Outbound */
  /**
   * Part of arrayized XML config that concern Server NAT settings
   * @see fillObj()
   * @var array
   */
  public $srvnat = NULL;	/* ServerNat */
  /**
   * Part of arrayized XML config that concern One To One NAT settings
   * @see fillObj()
   * @var array
   */
  public $o2onat = NULL;	/* One 2 One */
  /**
   * Part of arrayized XML config that concern Aliases  settings
   * @see fillObj()
   * @var array
   */
  public $aliases = NULL;
  /**
   * Part of arrayized XML config that concern Proxy-ARP settings
   * @see fillObj()
   * @var array
   */
  public $proxyarp = NULL;
  /**
   * Part of arrayized XML config that concern Users settings
   * @see fillObj()
   * @var array
   */
  public $user = NULL;
  /**
   * Part of arrayized XML config that concern Groups settings
   * @see fillObj()
   * @var array
   */
  public $group = NULL;
  /**
   * Part of arrayized XML config that concern VLANs settings
   * @see fillObj()
   * @var array
   */
  public $vlans = NULL;
  /**
   * Part of arrayized XML config that concern root monowall array
   * @see fillObj()
   * @var array
   */
  public $mono = NULL;

  /**
   * Constructor of class
   * @param Monowall $m Monowall object associated with this config obj
   * @return void
   * @see Monowall
   */
  public function __construct($m=NULL)
  {
    $this->mono = $m;
  }

  /**
   * Convert arrayized configuration to XML dump.
   * @return int one if succesful, zero otherwise
   */
  function XML()
  {
    if (($this->rawconfig = dump_xml_config($this->config["m0n0wall"], "m0n0wall"))) return 1;
    return 0;
  }

  function addToTab(&$array, $name, $value) {
    if (!array_key_exists($name, $array)) { /* easiest case */
      $array[$name] = $value;
      return;
    } else {
      if (is_array($value)) { /* don't discard */
	if (!is_array($array[$name]))
	  $array[$name] = array();

        foreach($value as $n => $v) {
	  if (!array_key_exists($n, $array[$name])) {
	    $array[$name][$n] = $v;
	  } else {
	    if (is_array($v)) {
	      $this->addToTab($array[$name], $n, $v);
	    }
          }
	}
      }
     }
   }

  function addUnknownToConf(&$array) {
    if (is_array($this->mono->unknown)) {
      foreach($this->mono->unknown as $idx => $u) {
        if ($u->idparent == -1) { /* root */
	  if ($u->value == "array") {
            $tab = $this->mono->getUnknownChilds($u->id, 1);
	  }
	  else {
	    $tab = $u->value;
	  }
	  $this->addToTab($array, $u->name, $tab);
	}
      }
    }
  }

  /**
   * Convert Object into arrayized configuration ready to convert to XML. (Recursive function)
   * @return int one if succesful, zero otherwise
   * @param array $conf Array containing configuration for converting objects to array for later XML conversion.
   * @param array &$array Destination array for configuration.
   * @param mixed $obj Object for source data
   */
  function arrayToConf(&$array, $conf, $obj)
  {
    foreach ($conf as $key => $value)
    {
      if (!is_array($value)) {
        $type = split(":", $value, 2); $type = $type[0];
        $wtf = split(":", $value, 2); $wtf = $wtf[1];
        switch ($type) {

          case "var":
            $path = split("->", $wtf);
            if (count($path) <= 1)
              $array[$key] = $obj->{$wtf};
            else
            {
	      $v = $obj;
              for ($i=0; $i<count($path); $i++) {
                $v = $v->{$path[$i]};
              }
 	      $array[$key] = $v;
            }
          break;
          case "varo":
            $path = split("->", $wtf);
            if (count($path) <= 1) {
              if (!empty($obj->{$wtf}))
 	      {
                //echo "KEY:".$obj->{$wtf}."(".$key.")\n";
                $array[$key] = $obj->{$wtf};
              }
            }
            else
            {
              $v = $obj;
              for ($i=0; $i<count($path); $i++) {
                $v = $v->{$path[$i]};
              }
              if (!empty($v))
                $array[$key] = $v;
            }
	  break;

	  case "bool":
            $path = split("->", $wtf);
            if (count($path) <= 1) {
              if ($obj->{$wtf}) 
		$array[$key] = "";
            }
            else
            {
              $v = $obj;
              for ($i=0; $i<count($path); $i++) {
                $v = $v->{$path[$i]};
              }
	      if ($v)
                $array[$key] = "";
            }
          break;

	  case "nbool":
            $path = split("->", $wtf);
            if (count($path) <= 1) {
              if (!$obj->{$wtf})
                $array[$key] = "";
            }
            else
            {
              $v = $obj;
              for ($i=0; $i<count($path); $i++) {
                $v = $v->{$path[$i]};
              }
              if (!$v)
                $array[$key] = $v;
            }
          break;

	  case "mbool":
	   if ($wtf == 1)
	    $array[$key] = "";
          break;

          case "string":
	   $array[$key] = $wtf;
          break;
	
          case "obj":
            switch ($wtf) {

	      case "protocol":
	        if ($obj->https) {
                  $array[$key] = "https";
                } else {
                  $array[$key] = "http";
                }
	      break;
              case "ifaces":

                $array[$key] = array();
                foreach ($obj->{$wtf} as $if) {
                  if ($if->type == "opt")
                    $k = $if->type.$if->num;
		  else
		    $k = $if->type;
                  $array[$key][$k] = array();
                  $this->arrayToConf($array[$key][$k], $if->_conf[$if->type], $if);
                }
	      break;
	      case "alias":

		$array[$key] = array();
	        $i=0;
                if (!(count($obj->{$wtf}) + count(Main::getInstance()->galiases))) { 
                  unset($array[$key]); 
                  if (!count($array))
                    $array = "";
                } else {

		  foreach ($obj->{$wtf} as $nat) {

		    $array[$key][$i] = array();
		    $this->arrayToConf($array[$key][$i], $nat->_conf, $nat);
		    $i++;
		  }
		  foreach (Main::getInstance()->galiases as $nat) {

		    $present = 0;
		    foreach($obj->{$wtf} as $alias) {
			if ($alias->name == $nat->name) {
			 $present = 1;
			 break;
			}
		    }
		    if ($present) continue;
                    $array[$key][$i] = array();
                    $this->arrayToConf($array[$key][$i], $nat->_conf, $nat);
                    $i++;
                  }
                }
	      break;

	      case "user":
	      case "group":
	      case "vlans":
	      case "sroutes":
	      case "proxyarp":
	      case "nat":
	      case "o2onat":
	      case "srvnat":
	      case "advnat":

		$array[$key] = array();
	        $i=0;
                if (!count($obj->{$wtf})) { 
                  unset($array[$key]); 
                  if (!count($array))
                    $array = "";
                } else {

		  foreach ($obj->{$wtf} as $nat) {

		    $array[$key][$i] = array();
		    $this->arrayToConf($array[$key][$i], $nat->_conf, $nat);
		    $i++;
		  }
                }
	      break;

	      case "rule":
                $array[$key] = array();
		$i = 0;
		/* we should have a monowall object */
                $rularray = array();
                foreach($obj->ifaces as $if) {
		  foreach($if->rulesint as $ru) {
                    $rularray[] = $ru;
		  }
 	        }
		$i = 0;
		$j = 0;
		$nordered = 1;
		$max = count($rularray);
		for ($i=0; $i<$max && $nordered; $i++)
                {
		  $nordered = 0;
		  for ($j = 1; $j < $max - $i; $j++) {
 		    if ($rularray[$j]->position < $rularray[$j - 1]->position) { /* invert elements */
		      $tmp = $rularray[$j - 1];
		      $rularray[$j - 1] = $rularray[$j];
		      $rularray[$j] = $tmp;
		      $nordered = 1;
		    }
                  }
                }
		/* array sorted at this point */
                foreach($rularray as $ru) {
                  $array[$key][$i] = array();
                  $this->arrayToConf($array[$key][$i], $ru->_conf, $ru);
                  $i++;
                }
                
	      break;

              case "syslog":
              case "snmp":
	        $array[$key] = array();
	        $this->arrayToConf($array[$key], $obj->{$wtf}->_conf, $obj->{$wtf}); 
              break;

 	      case "prop":
                foreach($obj->{$wtf} as $p) {
		  if ($p->name == $key) {
                    if (is_numeric($p->value) && $p->value)
		      $array[$key] = "";
                    else if (!is_numeric($p->value))
		      $array[$key] = $p->value;
                  }
		}
	      break;

              default:
                echo "Object type not implemented: ".$wtf.".\n";
              break;
            }
          break;

          case "fct":
            $array[$key] = call_user_func($wtf);
          break;
 
	  case "ofct":
	    $array[$key] = call_user_func(array($obj, $wtf));    
	  break;

          default:
            echo "Error in localToDb(): type of value not found: ".$type.".\n";
  	  break;
        }
      } else {
        $array[$key] = array();
        $this->arrayToConf($array[$key], $value, $obj);
      }
      /*
       * check that name is present in unknown objects
       */
/*
      if (is_array($this->mono->unknown)) {
        $tab = null;
	foreach($this->mono->unknown as $idx => $u) {
          if ($key == $u->name) {
	    if ($u->value == "array") {
              $tab = $this->mono->getUnknownChilds($u->id, 1);
	      unset($this->mono->unknown[$idx]);
	      break;
	    }
	    else {
              $tab = $u->value;
	      unset($this->mono->unknown[$idx]);
	      break;
	    }
	  }
	}
	if ($tab) {
	    $array[$key] = $tab;
	}
      }
*/
    }
    return 1;
  }

  /**
   * Sync with database
   * @return int one of succesful, zero otherwise.
   */
  function dbToLocal()
  {
    /* build $this->config with in-mem objs */
    $mono = $this->mono;
    $config = array();

    /* begin with m0n0wall object */
    $config[$mono->_root] = array();
    if (!$this->arrayToConf($config[$mono->_root], $mono->_conf, $mono)) return 0;

    $this->addUnknownToConf($config[$mono->_root]);

    $this->config = $config;
    return 1;
  }

  /**
   * Fill objects with arrayized XML configuration
   * @return int one of succesful, zero otherwise.
   */
  function fillObj()
  {
    /* version */
    $this->mono->version = $this->config["version"];
    unset($this->config["version"]);
    $this->lastchange = $this->config["lastchange"];
    unset($this->config["lastchange"]);
    
    if (!is_array($this->system)) return 0;
    /* system */
    $this->mono->hostname = $this->system["hostname"];
    unset($this->config["system"]["hostname"]);
    $this->mono->domain = $this->system["domain"];
    unset($this->config["system"]["domain"]);
    $this->mono->timezone = $this->system["timezone"];
    unset($this->config["system"]["timezone"]);
    $this->mono->ntpserver = $this->system["timeservers"];
    unset($this->config["system"]["timeservers"]);
    $this->mono->ntpinterval = $this->system["time-update-interval"];
    unset($this->config["system"]["time-update-interval"]);

    if (array_key_exists("webgui", $this->system)) {
      $this->mono->port = $this->system["webgui"]["port"];
      if ($this->system["webgui"]["protocol"] == "https")
        $this->mono->https = 1;
      else
	$this->mono->https = 0;
      unset ($this->config["system"]["webgui"]["port"]);
      unset ($this->config["system"]["webgui"]["protocol"]);

      if(!$this->mono->port && $this->mono->https) $this->mono->port = 443;
      if(!$this->mono->port && !$this->mono->https) $this->mono->port = 80;
    }

    if (array_key_exists("dnsserver", $this->system)) {
      $d = "";
      foreach($this->system["dnsserver"] as $dns) {
        $d .= $dns.";";
      }
      $this->mono->dnsserver = $d;
      unset ($this->config["system"]["dnsserver"]);
    }

    /* admin */
    $this->mono->username = $this->system["username"];
    $this->mono->password = $this->system["password"];
    unset($this->config["system"]["username"]);
    unset($this->config["system"]["password"]);

    /* other properties */
    if (array_key_exists("dnsallowoverride", $this->system))
    {
      $this->mono->dnsoverride = 1;
    } else {
      $this->mono->dnsoverride = 0;
    }

    if (array_key_exists("disableconsolemenu", $this->system))
    {
      $p = new Prop();
      $p->idhost = $this->mono->id;
      $p->name = "disableconsolemenu";
      $p->value = 1;
      array_push($this->mono->prop, $p);
      unset($this->config["system"]["disableconsolemenu"]);
    }
    if (array_key_exists("disablefirmwarecheck", $this->system))
    {
      $p = new Prop();
      $p->idhost = $this->mono->id;
      $p->name = "disablefirmwarecheck";
      $p->value = 1;
      array_push($this->mono->prop, $p);
      unset($this->config["system"]["disablefirmwarecheck"]);
    }
    if (array_key_exists("shellcmd", $this->system))
    {
      $p = new Prop();
      $p->idhost = $this->mono->id;
      $p->name = "shellcmd";
      $p->value = $this->system["shellcmd"];
      array_push($this->mono->prop, $p);
      unset($this->config["system"]["shellcmd"]);
    }
    if (array_key_exists("earlyshellcmd", $this->system))
    {
      $p = new Prop();
      $p->idhost = $this->mono->id;
      $p->name = "earlyshellcmd";
      $p->value = $this->system["earlyshellcmd"];
      array_push($this->mono->prop, $p);
      unset($this->config["system"]["earlyshellcmd"]);
    }
    if (array_key_exists("harddiskstandby", $this->system))
    {
      $p = new Prop();
      $p->idhost = $this->mono->id;
      $p->name = "harddiskstandby";
      $p->value = 1;
      array_push($this->mono->prop, $p);
      unset($this->config["system"]["harddiskstandby"]);
    }
    if (array_key_exists("polling", $this->system))
    {
      $p = new Prop();
      $p->idhost = $this->mono->id;
      $p->name = "polling";
      $p->value = 1;
      array_push($this->mono->prop, $p);
      unset($this->config["system"]["polling"]);
    }
    if (array_key_exists("notes", $this->system))
    {
      $p = new Prop();
      $p->idhost = $this->mono->id;
      $p->name = "notes";
      $p->value = $this->system["notes"];
      array_push($this->mono->prop, $p);
      unset($this->config["system"]["notes"]);
    }
    if (array_key_exists("watchdog", $this->system))
    {
      $p = new Prop();
      $p->idhost = $this->mono->id;
      $p->name = "watchdog";
      $p->value = 1;
      array_push($this->mono->prop, $p);
      unset($this->config["system"]["watchdog"]);
    }

    if (is_array($this->group))
    {
      foreach ($this->group as $group) {

        $g = new Group();
	if (array_key_exists("name", $group))
          $g->name = $group["name"];
	if (array_key_exists("description", $group))
	  $g->description = $group["description"];
	if (array_key_exists("pages", $group)) {
          $p = "";
	  foreach($group["pages"] as $page) {
            $p .= $page.";";
          }
          $g->pages = $p;
        }
   
        $g->idhost = $this->mono->id;
        $g->mono = $this->mono;

	array_push($this->mono->group, $g);
      }
    }
    if (array_key_exists("group", $this->system))
      unset($this->config["system"]["group"]);


    if (is_array($this->user))
    {
      foreach($this->user as $user) {

        $u = new User();
	if (array_key_exists("name", $user))
	  $u->name = $user["name"];
	if (array_key_exists("fullname", $user))
	  $u->fullname = $user["fullname"];
	if (array_key_exists("groupname", $user))
	  $u->groupname = $user["groupname"];
	if (array_key_exists("password", $user))
	  $u->password = $user["password"];

	$u->idhost = $this->mono->id;
	$u->mono = $this->mono;

	array_push($this->mono->user, $u);
      }
    }
    if (array_key_exists("user", $this->system))
      unset($this->config["system"]["user"]);


    if (is_array($this->ifaces))
    { 
      foreach ($this->ifaces as $name => $int)
      { 
        $if = new Iface();
        $if->mono = $this->mono;

        $if->if = $int["if"];
        $if->type = substr($name, 0, 3);
        switch ($if->type)
        {
          case "lan":
            if (array_key_exists("ipaddr", $int))
              $if->ipaddr = $int["ipaddr"];
            if (array_key_exists("subnet", $int))
              $if->subnet = $int["subnet"];
            if (array_key_exists("media", $int))
              $if->media = $int["media"];
            if (array_key_exists("mediaopt", $int))
              $if->mediaopt = $int["mediaopt"];
          break;
          case "wan":
            if (array_key_exists("media", $int))
              $if->media = $int["media"];
            if (array_key_exists("mediaopt", $int))
              $if->mediaopt = $int["mediaopt"];
            if (array_key_exists("ipaddr", $int))
              $if->ipaddr = $int["ipaddr"];
            if (array_key_exists("subnet", $int))
              $if->subnet = $int["subnet"];
            if (array_key_exists("gateway", $int))
              $if->gateway = $int["gateway"];
            if (array_key_exists("spoofmac", $int))
              $if->spoofmac = $int["spoofmac"];
            if (array_key_exists("mtu", $int))
              $if->mtu = $int["mtu"];
            if (array_key_exists("dhcphostname", $int))
              $if->dhcp = $int["dhcphostname"];
            if (array_key_exists("blockpriv", $int))
              $if->blockpriv = $int["blockpriv"];
          break;
          case "opt":
            if (array_key_exists("enable", $int))
              $if->enable = 1;
	    else
	      $if->enable = 0;
            if (array_key_exists("ipaddr", $int))
              $if->ipaddr = $int["ipaddr"];
            if (array_key_exists("subnet", $int))
              $if->subnet = $int["subnet"];
            if (array_key_exists("media", $int))
              $if->media = $int["media"];
            if (array_key_exists("mediaopt", $int))
              $if->mediaopt = $int["mediaopt"];
            if (array_key_exists("bridge", $int))
              $if->bridge = $int["bridge"];
	    if (array_key_exists("descr", $int))
              $if->description = $int["descr"];

            $if->num = substr($name, 3, 2);
          break;
        }
        $if->idhost = $this->mono->id;
        //$if->fetchId();
        array_push($this->mono->ifaces, $if);
      }
    }
    if (array_key_exists("interfaces", $this->config))
      unset($this->config["interfaces"]);


    if (is_array($this->filter))
    {
      if (array_key_exists("tcpidletimeout", $this->filter))
      {   
        $p = new Prop();
        $p->idhost = $this->mono->id;
        $p->name = "tcpidletimeout";
        $p->value = $this->filter["tcpidletimeout"];
        array_push($this->mono->prop, $p);
      }
      if (array_key_exists("bypassstaticroutes", $this->filter))
      {
        $p = new Prop();
        $p->idhost = $this->mono->id;
        $p->name = "bypassstaticroutes";
        $p->value = 1;
        array_push($this->mono->prop, $p);
      }
      if (array_key_exists("allowipsecfrags", $this->filter))
      {
        $p = new Prop();
        $p->idhost = $this->mono->id;
        $p->name = "allowipsecfrags";
        $p->value = 1;
        array_push($this->mono->prop, $p);
      }
    }

    if (is_array($this->rules))
    { 
      $i=0;
      foreach ($this->rules as $name => $rule)
      { 
        $ru = new Rule();
        if (array_key_exists("descr", $rule))
          $ru->description = $rule["descr"];
        if (array_key_exists("type", $rule))
          $ru->type = $rule["type"];
        if (array_key_exists("interface", $rule))
          $ru->if = $rule["interface"];
        if (array_key_exists("protocol", $rule))
          $ru->protocol = $rule["protocol"];
        if (array_key_exists("icmptype", $rule))
          $ru->icmptype = $rule["icmptype"];

        if (array_key_exists("address", $rule["source"]))
          $ru->source = $rule["source"]["address"];
        else if (array_key_exists("network", $rule["source"]))
          $ru->source = $rule["source"]["network"];
        else if (array_key_exists("any", $rule["source"]))
          $ru->source = "ANY";

        if (array_key_exists("not", $rule["source"]))
          $ru->snot = 1;
        else 
          $ru->snot = 0;
        if (array_key_exists("port", $rule["source"]))
          $ru->sport = $rule["source"]["port"];

        if (array_key_exists("address", $rule["destination"]))
          $ru->destination = $rule["destination"]["address"];
        else if (array_key_exists("network", $rule["destination"]))
          $ru->destination = $rule["destination"]["network"];
        else if (array_key_exists("any", $rule["destination"]))
          $ru->destination= "ANY";

        if (array_key_exists("not", $rule["destination"]))
          $ru->dnot = 1;
        else 
          $ru->dnot = 0;
        if (array_key_exists("port", $rule["destination"]))
          $ru->dport = $rule["destination"]["port"];

        if (array_key_exists("frags", $rule))
          $ru->frags = 1;
        else $ru->frags = 0;
        if (array_key_exists("log", $rule))
          $ru->log = 1;
        else $ru->log = 0;

        if (array_key_exists("disabled", $rule))
          $ruena = 0;
        else 
	  $ruena = 1;
        
       if (! Main::getInstance()->ruleExist($ru)) {
          //echo "Rule added\n";
          array_push(Main::getInstance()->rules, $ru);
        } 
        else 
        { 
	  $rue = Main::getInstance()->getRuleByObj($ru);
          unset ($ru); 
          $ru = $rue;
        }
        $if = $this->mono->getIface($ru->if);
        if ($if) {
          array_push($if->rules, $ru); /* tmp table for insert into rules-int later on */
          array_push($if->rulesp, array($ruena, $i)); /* store enabled and position properties in tmp table */
        }
        $i++;
 
      }
    }    
    if (array_key_exists("filter", $this->config))
      unset($this->config["filter"]);

    if (is_array($this->vlans)) {
      $order = 0;
      foreach ($this->vlans as $name => $vlan)
      {   
        $vl = new Vlan();
        if (array_key_exists("tag", $vlan))
          $vl->tag = $vlan["tag"];
        if (array_key_exists("if", $vlan))
          $vl->if = $vlan["if"];
        if (array_key_exists("descr", $vlan))
          $vl->description = $vlan["descr"];

        $vl->mono = $this->mono;
        $vl->idhost = $this->mono->id;
        $vl->iface = $this->mono->getIface($vl->if);
        $vl->order = $order++;

        array_push($this->mono->vlans, $vl);
      }
    }
   if (array_key_exists("vlans", $this->config))
      unset($this->config["vlans"]);

    if (is_array($this->sroutes)) {
      foreach ($this->sroutes as $name => $sroute)
      {   
        $sr = new StaticRoute();
        if (array_key_exists("network", $sroute))
          $sr->network = $sroute["network"];
        if (array_key_exists("interface", $sroute))
          $sr->if = $sroute["interface"];
        if (array_key_exists("gateway", $sroute))
          $sr->gateway = $sroute["gateway"];
        if (array_key_exists("descr", $sroute))
          $sr->description = $sroute["descr"];

        $sr->mono = $this->mono;
        $sr->idhost = $this->mono->id;
        $sr->iface = $this->mono->getIface($sr->if);

        array_push($this->mono->sroutes, $sr);
      }
    }
    if (array_key_exists("staticroutes", $this->config))
      unset($this->config["staticroutes"]);

    if (is_array($this->aliases)) {
      foreach ($this->aliases as $name => $alias)
      {   
        $sr = new Alias();
        if (array_key_exists("name", $alias))
          $sr->name= $alias["name"];
        if (array_key_exists("address", $alias))
          $sr->address = $alias["address"];
        if (array_key_exists("descr", $alias))
          $sr->description = $alias["descr"];

        $sr->mono = $this->mono;
        $sr->idhost = $this->mono->id;

        array_push($this->mono->alias, $sr);
      }
      unset($this->config["aliases"]);
    }
   if (array_key_exists("aliases", $this->config))
      unset($this->config["aliases"]);


    if (is_array($this->proxyarp)) {
      foreach ($this->proxyarp as $name => $pa)
      {   
        $sr = new ProxyArp();
        if (array_key_exists("interface", $pa))
          $sr->if = $pa["interface"];
        if (array_key_exists("network", $pa))
          $sr->network = $pa["network"];
        if (array_key_exists("from", $pa))
          $sr->from = $pa["from"];
        if (array_key_exists("to", $pa))
          $sr->to = $pa["to"];
        if (array_key_exists("descr", $pa))
          $sr->description = $pa["descr"];

        $sr->mono = $this->mono;
        $sr->idhost = $this->mono->id;

        array_push($this->mono->proxyarp, $sr);
      }
    }

   if (array_key_exists("proxyarp", $this->config))
      unset($this->config["proxyarp"]);

    /* NAT */
    if ($this->nat && is_array($this->nat)) {
      foreach ($this->nat as $name => $nat) {
        
        $nr = new RuleNat();
       
        if (array_key_exists("interface", $nat))
          $nr->if = $nat["interface"];
        if (array_key_exists("external-address", $nat))
          $nr->external = $nat["external-address"];
        if (array_key_exists("external-port", $nat))
          $nr->eport = $nat["external-port"];
        if (array_key_exists("protocol", $nat))
          $nr->proto = $nat["protocol"];
        if (array_key_exists("target", $nat))
          $nr->target = $nat["target"];
        if (array_key_exists("local-port", $nat))
          $nr->lport = $nat["local-port"];
        if (array_key_exists("descr", $nat))
          $nr->description = $nat["descr"];

        $nr->mono = $this->mono;
        $nr->idhost = $this->mono->id;
        $nr->iface = $this->mono->getIface($nr->if);

        array_push($this->mono->nat, $nr);
      }
    }

    /* Advanced Outbound */
    if (is_array($this->advnat)) {

      if (array_key_exists("enable", $this->advnat))
        $this->mono->enablenat = 1;
      else
	$this->mono->enablenat = 0;

      if (array_key_exists("rule", $this->advnat))
      {
        if (is_array($this->advnat["rule"]))
        {
          foreach ($this->advnat["rule"] as $name => $nat)
          {
            $nr = new AdvNat();
          
            if (array_key_exists("interface", $nat))
              $nr->if = $nat["interface"];
            if (array_key_exists("target", $nat))
              $nr->target = $nat["target"];
            if (array_key_exists("noportmap", $nat))
              $nr->noportmap = 1;
            else
              $nr->noportmap = 0;

            if (array_key_exists("source", $nat))
            {
              if (array_key_exists("address", $nat["source"]))
                $nr->source = $nat["source"]["address"];
              else if (array_key_exists("network", $nat["source"]))
                $nr->source = $nat["source"]["network"];
              else if (array_key_exists("any", $nat["source"]))
                $nr->source =  "ANY";
            }
            if (array_key_exists("destination", $nat))
            {
              if (array_key_exists("address", $nat["destination"]))
                $nr->destination = $nat["destination"]["address"];
              else if (array_key_exists("network", $nat["destination"]))
                $nr->destination = $nat["destination"]["network"];
              else if (array_key_exists("any", $nat["destination"]))
                $nr->destination =  "ANY";
              if (array_key_exists("not", $nat["destination"]))
		$nr->dnot = 1;
            }
            if (array_key_exists("subnet", $nat))
              $nr->subnet = $nat["subnet"];
            if (array_key_exists("descr", $nat))
              $nr->description= $nat["descr"];

            $nr->mono = $this->mono;
            $nr->idhost = $this->mono->id;
            $nr->iface = $this->mono->getIface($nr->if);

            array_push($this->mono->advnat, $nr);
          }
        }
      }
    }

    /* Server NAT */
    if (is_array($this->srvnat)) {
      foreach ($this->srvnat as $name => $nat)
      { 
        $nr = new SrvNat();

        if (array_key_exists("ipaddr", $nat))
          $nr->ipaddr = $nat["ipaddr"];
        if (array_key_exists("descr", $nat))
          $nr->description = $nat["descr"];

        $nr->mono = $this->mono;
        $nr->idhost = $this->mono->id;

        array_push($this->mono->srvnat, $nr);
      }
    }
   
    /* One to One nat */
    if (is_array($this->o2onat)) {
      foreach ($this->o2onat as $name => $nat)
      { 
        $nr = new O2ONat();

        if (array_key_exists("interface", $nat))
          $nr->if = $nat["interface"];
        if (array_key_exists("descr", $nat))
          $nr->description = $nat["descr"];
        if (array_key_exists("internal", $nat))
          $nr->internal = $nat["internal"];
        if (array_key_exists("external", $nat))
          $nr->external = $nat["external"];
        if (array_key_exists("subnet", $nat))
          $nr->subnet = $nat["subnet"];

        $nr->mono = $this->mono;
        $nr->idhost = $this->mono->id;
        $nr->iface = $this->mono->getIface($nr->if);

        array_push($this->mono->o2onat, $nr);
      }
    }
    if (array_key_exists("nat", $this->config))
      unset($this->config["nat"]);

    /* syslog */
    if (is_array($this->syslog)) {
      $sysl = new Syslog();
      if (array_key_exists("reverse", $this->syslog))
        $sysl->reverse = 1;
      else
	$sysl->reverse = 0;
  
      if (array_key_exists("system", $this->syslog))
        $sysl->system = 1;
      else
	$sysl->system = 0;

      if (array_key_exists("filter", $this->syslog))
        $sysl->nentries = $this->syslog["filter"];

      if (array_key_exists("enable", $this->syslog))
        $sysl->enable = 1;
      else
        $sysl->enable = 0;

      if (array_key_exists("remoteserver", $this->syslog))
        $sysl->remoteserver = $this->syslog["remoteserver"];

      if (array_key_exists("nentries", $this->syslog))
        $sysl->nentries = $this->syslog["nentries"];

      $this->mono->syslog = $sysl;
      $sysl->mono = $this->mono;

      unset($this->config["syslog"]);
    } else $this->mono->syslog = new Syslog();

    /* snmp */
    if (is_array($this->snmp)) {
      $sysl = new Snmp();
      if (array_key_exists("bindlan", $this->snmp))
        $sysl->bindlan = 1;
      else
	$sysl->bindlan = 0;
  
      if (array_key_exists("syscontact", $this->snmp))
        $sysl->syscontact = $this->snmp["syscontact"];
   
      if (array_key_exists("syslocation", $this->snmp))
        $sysl->syslocation = $this->snmp["syslocation"];

 
      if (array_key_exists("rocommunity", $this->snmp))
        $sysl->rocommunity = $this->snmp["rocommunity"];

      if (array_key_exists("enable", $this->snmp))
        $sysl->enable = 1;
      else
        $sysl->enable = 0;

      $this->mono->snmp = $sysl;
      $sysl->mono = $this->mono;

      unset($this->config["snmpd"]);
    } else $this->mono->snmp = new Snmp();
    /* ALL UNKNOWN SETTINGS LEFT */
    $this->addUnknown(null, $this->config);

    return 1;
  }

  /**
   * Add unknown object to m0n0wall.
   * @return void
   */ 
  function addUnknown($parent = null, $tab)
  {
    $exception = array("bridge" => 1, "ipsec" => 1);
    foreach($tab as $name => $value) {
      if (is_array($value) || array_key_exists($name, $exception)) {
        $u = new Unknown(-1, $name, "array", $parent);
        array_push($this->mono->unknown, $u);
	$this->addUnknown($u, $value);
      } else {
        array_push($this->mono->unknown, new Unknown(-1, $name, $value, $parent));
      }
    }
  }

  /**
   * print configuration in a CLI mode.
   * @return void
   * @deprecated
   */
  function viewConfig()
  {
    echo "Configuration for ".$this->mono->hostname."\n";

    echo "Configuration Version: ". $this->config["version"] . "\n";

    echo "Hostname: ".     $this->system["hostname"] . "\n";
    echo "Domain: ".       $this->system["domain"] . "\n";
    echo "NTP Server: : ". $this->system["timeservers"] . "\n";

    echo "Interfaces:\n";

    if (is_array($this->ifaces))
    {
      foreach ($this->ifaces as $name => $int)
      {

        echo " Int(".$name."): ". $int["if"] . "/";
        if (array_key_exists("ipaddr", $int)) echo $int["ipaddr"];
        echo "/";
        if (array_key_exists("subnet", $int)) echo $int["subnet"];
        echo "\n";
      }
    }

    echo "Static Routes:";
    if (is_array($this->sroutes))
    {
      foreach ($this->sroutes as $idx => $route)
      {
        echo " WIP\n";
      }
     }
   
     echo "SNMP";
     if (is_array($this->snmp))
     {
       foreach ($this->snmp as $name => $value)
       {
         echo " $name: $value\n";
       }
     }
   
     echo "Syslog Server";
     if (is_array($this->syslog))
     {
       foreach ($this->syslog as $name => $value)
       {
         echo " $name: $value\n";
       }
     }
   
     /* TODO: NAT rules */
     echo "Firewall rules:\n";
     if (is_array($this->rules))
     {
       foreach ($this->rules as $nb => $rule)
       {
         echo " ".$nb.": ".$rule["type"]." on ".$rule["interface"];
         if (array_key_exists("protocol", $rule)) echo " ".$rule["protocol"];
         echo " from ";
         if (array_key_exists("not", $rule["source"])) echo "! ";
         if (array_key_exists("address", $rule["source"])) echo $rule["source"]["address"];
         else if (array_key_exists("network", $rule["source"])) echo $rule["source"]["network"];
         else if (array_key_exists("any", $rule["source"])) echo "any";
     
         if (array_key_exists("port", $rule["source"])) echo " port ".$rule["source"]["port"];
     
         echo " to ";

         if (array_key_exists("not", $rule["destination"])) echo "! ";
         if (array_key_exists("address", $rule["destination"])) echo $rule["destination"]["address"];
         else if (array_key_exists("network", $rule["destination"])) echo $rule["destination"]["network"];
         else if (array_key_exists("any", $rule["destination"])) echo "any";
     
         if (array_key_exists("port", $rule["destination"])) echo " port ".$rule["destination"]["port"];
       
         if (array_key_exists("frags", $rule)) echo " frags";
         if (array_key_exists("log", $rule)) echo " log";
      
         echo "\n";
       } 
     }   
    
     echo "Aliases:\n";
     if (is_array($this->aliases))
     {
       foreach ($this->aliases as $id => $alias)
       {
         echo " $id: ".$alias["name"]." => ".$alias["address"]." (". $alias["descr"]. ")\n";
       }
     }   
       
     echo "Proxy ARP:\n";
     /* TODO */
     
     echo "VLANs:\n";
     if (array_key_exists("vlans", $this->config))
     {
       if (is_array($this->vlans))
       { 
         foreach ($this->vlans as $pos => $vlan)
         {
           echo " $pos: ".$vlan["if"]."/".$vlan["tag"]." (".$vlan["descr"].")\n";
         }
       }
     }
  }

  /**
   * Parse XML configuration and turn it into an arrayized version.
   * @return int one of succesful, zero otherwise.
   */
  function parseConfig()
  {
    global $current, $mysql;
    if ($this->rawconfig) {
      if (parse_xml_config($this->rawconfig, "m0n0wall"))
      {
        $this->config = $current;
      } else return 0;
    } else return 0;

    /* fill vars */
    $this->system = & $this->config["system"];
    $this->user = & $this->config["system"]["user"];
    $this->group = & $this->config["system"]["group"];
    $this->ifaces = & $this->config["interfaces"];
    if (array_key_exists("staticroutes", $this->config))
      if (is_array($this->config["staticroutes"]))
        if (array_key_exists("route", $this->config["staticroutes"]))
          $this->sroutes = & $this->config["staticroutes"]["route"];
    $this->snmp = & $this->config["snmpd"];
    if (array_key_exists("syslog", $this->config))
      $this->syslog = & $this->config["syslog"];
    if (array_key_exists("filter", $this->config)) {
      $this->filter = & $this->config["filter"];
      if (array_key_exists("rule", $this->filter))
        $this->rules = & $this->config["filter"]["rule"];
    }
    $this->nat = NULL;
    if (array_key_exists("aliases", $this->config))
      $this->aliases = & $this->config["aliases"]["alias"];
    if (array_key_exists("proxyarp", $this->config))
      if (is_array($this->config["proxyarp"]))
        if (array_key_exists("proxyarpnet", $this->config["proxyarp"]))
          $this->proxyarp = & $this->config["proxyarp"]["proxyarpnet"];
    if (array_key_exists("vlans", $this->config))
      if (is_array($this->config["vlans"]))
        if (array_key_exists("vlan", $this->config["vlans"]))
          $this->vlans = & $this->config["vlans"]["vlan"];
    if (array_key_exists("nat", $this->config)) {
     $this->nat = $this->config["nat"];
     if (array_key_exists("advancedoutbound", $this->nat))
       $this->advnat = & $this->nat["advancedoutbound"];
     if (array_key_exists("servernat", $this->nat)) 
       $this->srvnat = & $this->nat["servernat"];
     if (array_key_exists("onetoone", $this->nat))
       $this->o2onat = & $this->nat["onetoone"];
     if (array_key_exists("rule", $this->nat))
       $this->nat = & $this->nat["rule"];
     else $this->nat = NULL;
    }
    return 1;
  }
  
  /**
   * Save configuration into a file
   * Used before restoring configuration to monowall device
   * @return void
   */
  function saveConfig($file)
  {
    if (($fp = fopen($file, "w"))) {
      if (fwrite($fp, $this->rawconfig)) {
        fclose($fp);
        return 1;
      }
      fclose($fp);
    }
    return 0;
  }

  /**
   * restore configuration to m0n0wall device with HTTPS protocol
   * @return int one if successful, zero otherwise
   */
  function restoreConfig()
  {
    global $mysql, $config;
    
    if ($this->mono)
    {
      if ($this->mono->buser == NULL) return 0;

      /* first save config to a tmp file */
      $this->saveConfig("./tmp/config.xml");

      $postData = array();
      $postData['conffile'] = '@./tmp/config.xml';
      $postData['Submit'] = "Restore configuration";

      if ($this->mono->https)
        $url = "https://";
      else
        $url = "http://";

      if ($this->mono->use_ip)
      {
        $url .= $this->mono->ip;
      } else {
        $url .= $this->mono->hostname.".".$this->mono->domain;
      }
      $url .= ":".$this->mono->port;
      $url .= "/diag_backup.php";

      /* fetch the config */
      $cm = curl_init();

      /* FIX proxy usage: */
      curl_setopt($cm, CURLOPT_PROXY, '');

      curl_setopt($cm, CURLOPT_URL, $url);
      curl_setopt($cm, CURLOPT_USERPWD, $this->mono->buser->login.":".$this->mono->buser->password);
      curl_setopt($cm, CURLOPT_HTTPAUTH, CURLAUTH_BASIC | CURLAUTH_DIGEST);
      curl_setopt($cm, CURLOPT_SSL_VERIFYPEER, FALSE);
      curl_setopt($cm, CURLOPT_SSL_VERIFYHOST, FALSE);
      curl_setopt($cm, CURLOPT_TIMEOUT, $config['curl']['timeout']);
      curl_setopt($cm, CURLOPT_RETURNTRANSFER, TRUE);
      //curl_setopt($cm, CURLOPT_HTTPHEADER, array("Content-Type: multipart/form-data"));

      curl_setopt($cm, CURLOPT_POST, true);
      curl_setopt($cm, CURLOPT_POSTFIELDS, $postData);

      $res = curl_exec($cm);
      unlink("./tmp/config.xml");
      if ($res == FALSE) {
        $this->error = curl_error($cm);
        curl_close($cm);
        return 0;
      } else {
	$httpcode = curl_getinfo($cm, CURLINFO_HTTP_CODE);	
        curl_close($cm);
	if (strstr($res, "The configuration has been restored. The firewall is now rebooting."))
          return 1; 
	else {
	  return $httpcode; }
      }
    } else return 0;
  }
 

  /**
   * Fetch configuration from m0n0wall device with HTTPS protocol
   * @return int one if succesful, zero otherwise.
   * @todo make settings of fetching more dynamic (http vs https, port, etc..)
   */
  function fetchConfig()
  {
    global $mysql, $config;
    
    if ($this->mono)
    {
      if ($this->mono->buser == NULL) return 0;

      if ($this->mono->https)
        $url = "https://";
      else
        $url = "http://";

      if ($this->mono->use_ip)
      {
        $url .= $this->mono->ip;
      } else {
        $url .= $this->mono->hostname.".".$this->mono->domain;
      }
      $url .= ":".$this->mono->port;
      $url .= "/diag_backup.php";

      /* fetch the config */
      $cm = curl_init();

      /* FIX proxy usage: */
      curl_setopt($cm, CURLOPT_PROXY, '');

      curl_setopt($cm, CURLOPT_URL, $url);
      curl_setopt($cm, CURLOPT_USERPWD, $this->mono->buser->login.":".$this->mono->buser->password);
      curl_setopt($cm, CURLOPT_HTTPAUTH, CURLAUTH_BASIC | CURLAUTH_DIGEST);
      curl_setopt($cm, CURLOPT_SSL_VERIFYPEER, FALSE);
      curl_setopt($cm, CURLOPT_SSL_VERIFYHOST, FALSE);
      curl_setopt($cm, CURLOPT_RETURNTRANSFER, TRUE);
      curl_setopt($cm, CURLOPT_TIMEOUT, $config['curl']['timeout']);
      curl_setopt($cm, CURLOPT_POSTFIELDS, "Submit=Download configuration");
      curl_setopt($cm, CURLOPT_POST, true);

      //  curl_setopt($cm, CURLOPT_VERBOSE, TRUE);
      $res = curl_exec($cm);
      if ($res == FALSE) {
        $this->error = curl_error($cm);
        curl_close($cm);
        return 0;
      } else {
        $httpcode = curl_getinfo($cm, CURLINFO_HTTP_CODE);
        curl_close($cm);
        $this->rawconfig = $res;
        return $httpcode;
      }
    } else return 0;
  }
}

/* EOF */
?>
