<?php
 /**
  * Monowall management
  * @author Gouverneur Thomas <tgouverneur@be.tiauto.com>
  * @copyright Copyright (c) 2007-2008, Gouverneur Thomas - TI Automotive
  * @version 1.0
  * @package objects
  * @subpackage monowall
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

class Monowall extends MysqlObj
{
  public $id = -1;		/* ID in the MySQL table */
  public $hostname = "";	/* hostname (sqdn) */
  public $domain = "";		/* domain name */
  public $dnsserver = "";	/* DNS server(s) */
  public $dnsoverride = 1;	/* dns override ? */
  public $version = 1.6;	/* version (default 1.6) */
  public $timezone = "";	/* time zone */
  public $ntpserver = "";	/* ntp server */
  public $ntpinterval = "";	/* ntp update interval */
  public $enabled = 1;		/* enabled ? */
  public $is_tpl = 0;		/* is this host a template ? */
  public $use_ip = 0;		/* should we use ipaddr to connect ? */
  public $ip = "";		/* ipaddress of this device */
  public $port = 443;		/* port of http interface */
  public $https = 1;		/* https = 1 | http = 0 */
  public $username = ""; 	/* admin username */
  public $password = "";	/* admin password (MD5 hash) */
  public $enablednat = 1;	/* enabled advanced outbound nat */
  public $idsyslog = "";	/* syslog entry */
  public $idsnmp = "";		/* snmp entry */
  public $idbuser = -1;
  public $lastchange = 0;
  public $changed = 0;
  public $fversion = 0;

  /* link to other class */
  public $unknown = array();	/* array of unkown objects */
  public $ifaces = array();	/* array of link to ifaces */
  public $vlans = array();	/* array of link of vlan */
  public $sroutes = array();	/* array of link of static routes */
  public $buser = NULL;		/* link to backup user */
  public $config = NULL;	/* link to configuration */
  public $alias = array();
  public $proxyarp = array();
  public $user = array();
  public $group = array();
  public $prop = array();	/* various properties */
  public $syslog = NULL;	/* link to syslog settings */
  public $snmp = NULL;		/* link to snmp settings */

  public $hwInt = array();	/* list of physical interfaces */
  public $galiases = NULL;

  /* nat */
  public $nat = array();
  public $srvnat = array();
  public $o2onat = array();
  public $advnat = array();

  public $error = NULL;		/* CURL Error string */

  /* array build for XML translation */
  public $_root = "m0n0wall";
  public $_conf = array ( 	/* vars to config */
			   	/* key name => what to fill in */
				"version" => "var:version",
				"lastchange" => "var:changed",
				"system" => array( 	"hostname" => "var:hostname",
							"domain" => "var:domain",
							"dnsallowoverride" => "bool:dnsoverride",
							"username" => "var:username",
							"password" => "var:password",
							"timezone" => "var:timezone",
							"time-update-interval" => "var:ntpinterval",
							"timeservers" => "var:ntpserver",
							"webgui" => array(
									"protocol" => "obj:protocol",
									"port" => "var:port",
									),
							"disableconsolemenu" => "obj:prop",
							"disablefirmwarecheck" => "obj:prop",
							"group" => "obj:group",
							"user" => "obj:user",
							"shellcmd" => "obj:prop",
							"earlyshellcmd" => "obj:prop",
							"harddiskstandby" => "obj:prop",
							"polling" => "obj:prop",
							"notes" => "obj:prop",
							"dnsserver" => "ofct:getDns",
							"watchdog" => "obj:prop"
						),
				"interfaces" => "obj:ifaces",
				"staticroutes" => array( "route" => "obj:sroutes" ),
				"snmpd" => "obj:snmp",
				"syslog" => "obj:syslog",
				"nat" => array(		
							"advancedoutbound" => array(    
											"enable" => "bool:enablednat",
											"rule" => "obj:advnat"
										   ),
							"servernat" => "obj:srvnat",
							"onetoone" => "obj:o2onat",
							"rule" => "obj:nat"
						),
				"filter" => array(	
							"rule" => "obj:rule",
							"tcpidletimeout" => "obj:prop",
							"bypassstaticroutes" => "obj:prop",
							"allowipsecfrags" => "obj:prop"
						),

				"aliases" => array(	"alias" => "obj:alias"

						),
				"proxyarp" => array(	"proxyarpnet" => "obj:proxyarp",
						),
				"vlans" => array( "vlan" => "obj:vlans" )
			);

  /* state var */
  private $_modified = FALSE;

  function getUnknownChilds($parent, $delete = 0) {
    $ret = array();
    foreach($this->unknown as $key => $value) {
      if ($value->idparent == $parent) { /* we have a child */
	if ($value->value == "array") { /* need to dig again */
	  $ret[$value->name] = $this->getUnknownChilds($value->id, $delete);
	} else {
	  $ret[$value->name] = $value->value;
	}
	if ($delete) {
	  unset($this->unknown[$key]);
	}
      }
    }
    return $ret;
  }

  function updateChanged() {
    $this->changed = time();
    $this->update();
  }

  function fetchHw()
  {
    $index = "`id`";
    $table = "hw-int";
    $where = "WHERE idhost='".$this->id."'";
    $m = Mysql::getInstance();
      
    if (($idx = $m->fetchIndex($index, $table, $where)))
    {
      foreach($idx as $t) {
        $sr = new HwIface($t["id"]);
        $sr->fetchFromId();
        $sr->mono = $this;
        $sr->idhost = $this->id;
        array_push($this->hwInt, $sr);
      }
      return 1;
    }
    return 0;  

  }

  function updateLChange()
  {
    global $config;
    if ($this->buser == NULL) return 0;

    if ($this->https)
      $url = "https://";
    else
      $url = "http://";

    if ($this->use_ip)
    { 
      $url .= $this->ip;
    } else {
      $url .= $this->hostname.".".$this->domain;
    }
    $url .= ":".$this->port;
    $url .= "/exec_raw.php";
    $url2 = "?cmd=cat%20/tmp/config.cache";

    /* fetch the config */ 
    $cm = curl_init();
      
    /* FIX proxy usage: */
    curl_setopt($cm, CURLOPT_PROXY, '');

    curl_setopt($cm, CURLOPT_URL, $url.$url2);
    curl_setopt($cm, CURLOPT_USERPWD, $this->buser->login.":".$this->buser->password);
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
      $f = explode("\"lastchange\"", $res);
      $f = explode("\"", $f[1]);
      if ($this->lastchange != $f[1]) {
        $this->lastchange = $f[1];
 	$this->update();
      }
      curl_close($cm);
      return $httpcode;
    }
  }

  function hwDetect()
  {
    if ($this->buser == NULL) return 0;

    if ($this->https)
      $url = "https://";
    else
      $url = "http://";

    if ($this->use_ip)
    { 
      $url .= $this->ip;
    } else {
      $url .= $this->hostname.".".$this->domain;
    }
    $url .= ":".$this->port;
    $url .= "/exec_raw.php";
    $url2 = "?cmd=ifconfig%20-a";

    /* fetch the config */ 
    $cm = curl_init();
      
    /* FIX proxy usage: */
    curl_setopt($cm, CURLOPT_PROXY, '');

    curl_setopt($cm, CURLOPT_URL, $url.$url2);
    curl_setopt($cm, CURLOPT_USERPWD, $this->buser->login.":".$this->buser->password);
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
      $this->hwInt = array();
      $hw = split("\n", $res);
      
      $loopback = 0;
      $ether = 0;
      foreach($hw as $line) {
	if ($loopback) { $loopback = 0; continue; }
	if ($ether && ereg("ether\ ..:..:..:..:..:..", $line)) {
	   $mac_array = explode("ether ", $line);
	   $mac = $mac_array[1];
	   $ether = 0;
	   $e = new hwIface();
	   $e->name = $eth;
	   $e->mac = $mac;
	   $e->idhost = $this->id;
	   array_push($this->hwInt, $e);
	}
        if (ereg("^[a-z]*[0-9]*:", $line)) {
  	  if (ereg("LOOPBACK", $line)) {
	    $loopback = 1;
 	    continue;
	  } else if (ereg("^vlan[0-9]*:", $line)) {
	    $loopback = 1;
	    continue;
	  } else {
            $eth_array = explode(':', $line);
	    $eth = $eth_array[0];
	    $ether = 1;
	  }
	}
      }

      $url2 = "?cmd=cat%20/etc/version";
      curl_setopt($cm, CURLOPT_URL, $url.$url2);
      $res = curl_exec($cm);    

      if ($res == FALSE) {
        $this->error = curl_error($cm);
        curl_close($cm);
        return 0;
      } else {
        $httpcode = curl_getinfo($cm, CURLINFO_HTTP_CODE);
	if ($res != $this->fversion && $httpcode) {
          $this->fversion = $res;
	  $this->update();
	}
      }
      curl_close($cm);
      return $httpcode;
    }
  }


  function getDns()
  {
    $d = array();
    $dnsserver = explode(';', $this->dnsserver);
    $i = 0;
    foreach ($dnsserver as $dns) {
      $d[] = $dns;
      $i++;
    }
    unset($d[$i-1]);
    return $d;
  }

  function fetchBuser()
  {
    if ($this->idbuser != -1)
    {
      $this->buser = new Buser($this->idbuser);
      $this->buser->fetchFromId();
    }
  }

  function fetchSnmp()
  {
    if ($this->snmp == NULL && $this->idsnmp) {
      $this->snmp = new Snmp($this->idsnmp);
      $this->snmp ->fetchFromId();
    }
  }

  function fetchSyslog()
  {
    if ($this->syslog == NULL && $this->idsyslog) {
      $this->syslog = new Syslog($this->idsyslog);
      $this->syslog->fetchFromId();
    }
  }
  function fetchRules()
  {
    foreach ($this->ifaces as $if)
      $if->fetchRules();
  }
  
  function fetchFromHostname()
  {
    return $this->fetchFromField("hostname");
  }

  function fetchUnknown()
  {
    $index = "`id`";
    $table = "unknown";
    $where = "WHERE idhost='".$this->id."'";
    $m = Mysql::getInstance();

    if (($idx = $m->fetchIndex($index, $table, $where)))
    {
      foreach($idx as $t) {
        $sr = new Unknown($t["id"]);
	$sr->fetchFromId();
        $sr->mono = $this;
        $sr->idhost = $this->id;
        array_push($this->unknown, $sr);
      }
      return 1;
    }
    return 0;
  }
 

  function fetchProp()
  {
    $index = "`id`";
    $table = "properties";
    $where = "WHERE idhost='".$this->id."'";
    $m = Mysql::getInstance();

    if (($idx = $m->fetchIndex($index, $table, $where)))
    {
      foreach($idx as $t) {
        $sr = new Prop($t["id"]);
	$sr->fetchFromId();
        $sr->mono = $this;
        $sr->idhost = $this->id;
        array_push($this->prop, $sr);
      }
      return 1;
    }
    return 0;
  }
 

  function fetchRoutes()
  {
    $index = "`id`";
    $table = "staticroutes";
    $where = "WHERE idhost='".$this->id."'";
    $m = Mysql::getInstance();

    if (($idx = $m->fetchIndex($index, $table, $where)))
    {
      foreach($idx as $t) {
        $sr = new StaticRoute($t["id"]);
        $sr->mono = $this;
        $sr->idhost = $this->id;
        array_push($this->sroutes, $sr);
      }
      return 1;
    }
    return 0;
  }
 
  function fetchRoutesDetails()
  {
    foreach($this->sroutes as $sroute) $sroute->fetchFromId();
    return 1;
  }

  function fetchGroups()
  {
    $index = "`id`";
    $table = "group";
    $where = "where idhost='".$this->id."'";
    $m = mysql::getInstance();

    if (($idx = $m->fetchIndex($index, $table, $where)))
    {
      foreach($idx as $t) {
        $gr = new Group($t["id"]);
        $gr->fetchFromId();
        $gr->mono = $this;
        $gr->idhost = $this->id;
        array_push($this->group, $gr);
      }
      return 1;
    }
    return 0;
  }
 
  function fetchUsers()
  {
    $index = "`id`";
    $table = "user";
    $where = "where idhost='".$this->id."'";
    $m = mysql::getinstance();

    if (($idx = $m->fetchIndex($index, $table, $where)))
    {
      foreach($idx as $t) {
        $gr = new User($t["id"]);
        $gr->fetchFromId();
        $gr->mono = $this;
        $gr->idhost = $this->id;
        array_push($this->user, $gr);
      }
      return 1;
    }
    return 0;
  }
 
 
  function fetchAlias()
  {
    $index = "`id`";
    $table = "alias";
    $where = "where idhost='".$this->id."'";
    $m = mysql::getinstance();

    if (($idx = $m->fetchIndex($index, $table, $where)))
    {
      foreach($idx as $t) {
        $gr = new Alias($t["id"]);
        $gr->mono = $this;
        $gr->idhost = $this->id;
        array_push($this->alias, $gr);
      }
      return 1;
    }
    return 0;
  }

  function fetchProxyArp()
  {
    $index = "`id`";
    $table = "proxyarp";
    $where = "where idhost='".$this->id."'";
    $m = mysql::getinstance();

    if (($idx = $m->fetchIndex($index, $table, $where)))
    {
      foreach($idx as $t) {
        $gr = new ProxyArp($t["id"]);
        $gr->mono = $this;
        $gr->idhost = $this->id;
        array_push($this->proxyarp, $gr);
      }
      return 1;
    }
    return 0;
  }

  function fetchVlans()
  {
    $index = "`id`";
    $table = "vlans";
    $where = "where idhost='".$this->id."' ORDER BY `order` ASC";
    $m = mysql::getinstance();

    if (($idx = $m->fetchIndex($index, $table, $where)))
    {
      foreach($idx as $t) {
        $gr = new Vlan($t["id"]);
        $gr->mono = $this;
        $gr->idhost = $this->id;
        array_push($this->vlans, $gr);
      }
      return 1;
    }
    return 0;
  }


 
  function fetchAllNat()
  {
    $this->fetchNatRules();
    $this->fetchAdvNat();
    $this->fetchO2ONat();
    $this->fetchSrvNat();
  }
  
  function fetchAllNatDetails()
  {
    foreach($this->nat as $nat) $nat->fetchFromId();
    foreach($this->advnat as $nat) $nat->fetchFromId();
    foreach($this->o2onat as $nat) $nat->fetchFromId();
    foreach($this->srvnat as $nat) $nat->fetchFromId();
    return 1;
  }

  function fetchNatRules()
  {
    $index = "`id`";
    $table = "nat-rules";
    $where = "where idhost='".$this->id."'";
    $m = mysql::getinstance();

    if (($idx = $m->fetchIndex($index, $table, $where)))
    {
      foreach($idx as $t) {
        $gr = new RuleNat($t["id"]);
        $gr->mono = $this;
        $gr->idhost = $this->id;
        array_push($this->nat, $gr);
      }
      return 1;
    }
    return 0;
  }

  function fetchAdvNat()
  {
    $index = "`id`";
    $table = "nat-advout";
    $where = "where idhost='".$this->id."'";
    $m = mysql::getinstance();

    if (($idx = $m->fetchIndex($index, $table, $where)))
    {
      foreach($idx as $t) {
        $gr = new AdvNat($t["id"]);
        $gr->mono = $this;
        $gr->idhost = $this->id;
        array_push($this->advnat, $gr);
      }
      return 1;
    }
    return 0;
  }


  function fetchO2ONat()
  {
    $index = "`id`";
    $table = "nat-one2one";
    $where = "where idhost='".$this->id."'";
    $m = mysql::getinstance();

    if (($idx = $m->fetchIndex($index, $table, $where)))
    {
      foreach($idx as $t) {
        $gr = new O2ONat($t["id"]);
        $gr->mono = $this;
        $gr->idhost = $this->id;
        array_push($this->o2onat, $gr);
      }
      return 1;
    }
    return 0;
  }



  function fetchSrvNat()
  {
    $index = "`id`";
    $table = "nat-srv";
    $where = "where idhost='".$this->id."'";
    $m = mysql::getinstance();

    if (($idx = $m->fetchIndex($index, $table, $where)))
    {
      foreach($idx as $t) {
        $gr = new SrvNat($t["id"]);
        $gr->mono = $this;
        $gr->idhost = $this->id;
        array_push($this->srvnat, $gr);
      }
      return 1;
    }
    return 0;
  }

  function fetchVlansDetails()
  {
    foreach($this->vlans as $vlan) $vlan->fetchFromId();
    return 1;
  }

  function fetchAliasDetails()
  {
    foreach($this->alias as $alias) $alias->fetchFromId();
    return 1;
  }

  function fetchProxyarpDetails()
  {
    foreach($this->proxyarp as $proxyarp) $proxyarp->fetchFromId();
    return 1;
  }

  function fetchIfaces()
  {
    $index = "`id`";
    $table = "interfaces";
    $where = "where idhost='".$this->id."'";
    $m = mysql::getinstance();

    if (($idx = $m->fetchIndex($index, $table, $where)))
    {
      foreach($idx as $t) {
        $gr = new Iface($t["id"]);
        $gr->mono = $this;
        $gr->idhost = $this->id;
        array_push($this->ifaces, $gr);
      }
      return 1;
    }
    return 0;
  }

  function fetchIfacesDetails()
  {
    foreach($this->ifaces as $iface) $iface->fetchFromId();
    return 1;
  }

  /* others */
  function countIface()
  {
    return count($this->ifaces);
  }

  function modified()
  {
    $this->_modified = TRUE;
  }

  function getIface($if)
  {
    if (substr($if, 0, 3) == "opt")
    {
      $num = substr($if, 3, 1);
      foreach($this->ifaces as $iface) {
        if ($iface->type == "opt" && $iface->num == $num) 
	 return $iface;
      }
    }
    else {
      foreach($this->ifaces as $iface) {
        if ($iface->type == $if) return $iface;
      }
    }
    /* find it on iface real name if nothing else found */
    foreach ($this->ifaces as $iface) {
      if ($iface->if == $if) return $iface;
    }
    return NULL;
  }

  /* ctor */
  public function __construct($id=-1) 
  { 
    $this->galiases = &Main::getInstance()->galiases;
    $this->id=$id;
    $this->config = new Config($this);
    $this->_table = "hosts";
    $this->_my = array( 
			"id" => SQL_INDEX, 
		        "hostname" => SQL_PROPE|SQL_EXIST|SQL_WHERE, 
 			"version" => SQL_PROPE, 
		        "domain" => SQL_PROPE|SQL_EXIST|SQL_WHERE,
			"dnsserver" => SQL_PROPE,
			"timezone" => SQL_PROPE,
			"ntpserver" => SQL_PROPE,
			"ntpinterval" => SQL_PROPE,
			"dnsoverride" => SQL_PROPE,
			"username" => SQL_PROPE,
			"password" => SQL_PROPE,
			"enabled" => SQL_PROPE,
			"is_tpl" => SQL_PROPE,
			"use_ip" => SQL_PROPE,
			"ip" => SQL_PROPE,
			"enablednat" => SQL_PROPE,
			"idsyslog" => SQL_PROPE,
			"idsnmp" => SQL_PROPE,
			"idbuser" => SQL_PROPE,
			"lastchange" => SQL_PROPE,
			"changed" => SQL_PROPE,
			"fversion" => SQL_PROPE,
			"port" => SQL_PROPE,
			"https" => SQL_PROPE
 		 );


    $this->_myc = array( /* mysql => class */
			"id" => "id", 
		        "hostname" => "hostname", 
 			"version" => "version", 
		        "domain" => "domain",
			"dnsserver" => "dnsserver",
			"timezone" => "timezone",
			"ntpserver" => "ntpserver",
			"ntpinterval" => "ntpinterval",
			"dnsoverride" => "dnsoverride",
			"username" => "username",
			"password" => "password",
			"enabled" => "enabled",
			"is_tpl" => "is_tpl",
			"use_ip" => "use_ip",
			"ip" => "ip",
			"enablednat" => "enablednat",
			"idsyslog" => "idsyslog",
			"idsnmp" => "idsnmp",
			"idbuser" => "idbuser",
			"lastchange" => "lastchange",
			"changed" => "changed",
			"fversion" => "fversion",
			"port" => "port",
			"https" => "https"
 		 );


  }
}

?>
