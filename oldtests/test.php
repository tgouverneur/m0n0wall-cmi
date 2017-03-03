<?php
 /* 
  **
  * first test for fetching configuration from m0n0wall
  **
  **
  * tgouverneur - 2007
  **
  */

/* require configuration file */
require_once("./inc/config.inc.php");
/* require database library */
require_once("./lib/mysql.lib.php");
/* require config fetching library */
require_once("./lib/conf.lib.php");
/* require configuration parsing library */
require_once("./lib/xmlparse.lib.php");


/* start of code */
echo "[-] ".db_connect();

/* get list of m0n0walls */
echo ($r = get_hosts());
if (!$r) {
  $m0n0wall = $mysql['result'];
  for ($i = 1; $i <= $m0n0wall[0]; $i++) {
    echo " [>] Doing: ". $m0n0wall[$i]["hostname"] .".". $m0n0wall[$i]["domain"] ."...\n";
    echo ($r = get_buser($m0n0wall[$i]["idbuser"]));
    if (!$r) {
      $buser = $mysql['result'];
      echo " [->] Got the backup user to use (". $buser["login"] .":********)\n";
    } else {
      echo " [X>] Cannot find the backup user for this host, abording...\n";
      continue;
    }
    if ($m0n0wall[$i]["use_ip"]) {
      echo " [ii] Using url: https://". $m0n0wall[$i]["ip"]."\n";
      $r = get_config_file($m0n0wall[$i]["ip"], $buser["login"], $buser["password"]);

    } else {
      echo " [ii] Using url: https://". $m0n0wall[$i]["hostname"].".".$m0n0wall[$i]["domain"]."\n";
      $r = get_config_file($m0n0wall[$i]["hostname"].".".$m0n0wall[$i]["domain"], $buser["login"], $buser["password"]);
    }
    if ($r) { /* configuration correctly retreived... */
     $m0n0wall[$i]["rawconfig"] = $current;
     echo " [->] Condiguration successfully retreived..\n";
 
     /* parsing configuration... */
     echo " [->] Parsing configuration...\n";
     $r = parse_xml_config($m0n0wall[$i]["rawconfig"], $config['xml']['rootobj']);

     if ($r) {
       $m0n0wall[$i]["config"] = $current;
       echo " [->] Configuration successfully parsed..\n";
     } else {
       echo " [X>] Error in parsing: ".$current."\n";
       continue;
     }
    } else { /* CURL error */
     echo " [X>] Cannot retreive config: ".$current."\n";
     continue;
    }
  }
}

reset($m0n0wall);
unset($m0n0wall[0]); /* remove the counter of monowall */

foreach ($m0n0wall as $mono)
{
  echo "Configuration for ".$mono["hostname"]."\n";

  $config = $mono["config"];
  echo "Configuration Version: ". $config["version"] . "\n";

  $system = $config["system"];
  echo "Hostname: ".     $system["hostname"] . "\n";
  echo "Domain: ".       $system["domain"] . "\n";
  echo "NTP Server: : ". $system["timeservers"] . "\n";

  echo "Interfaces:\n";
  $interfaces = $config["interfaces"];
  
  if (is_array($interfaces))
  {
    foreach ($interfaces as $name => $int)
    {
    
      echo " Int(".$name."): ". $int["if"] . "/";
      if (array_key_exists("ipaddr", $int)) echo $int["ipaddr"];
      echo "/";
      if (array_key_exists("subnet", $int)) echo $int["subnet"];
      echo "\n";
    }
  }

  echo "Static Routes:";
  $sroutes = $config["staticroutes"];
  if (is_array($sroutes))
  {
    foreach ($sroutes as $idx => $route)
    {
      echo " WIP\n";
    }
  }

  echo "SNMP";
  $snmpd = $config["snmpd"];
  if (is_array($snmpd))
  {
    foreach ($snmpd as $name => $value)
    {
      echo " $name: $value\n";
    } 
  }

  echo "Syslog Server";
  $syslog = $config["syslog"];
  if (is_array($syslog))
  {
    foreach ($syslog as $name => $value)
    {
      echo " $name: $value\n";
    }
  }

  /* TODO: NAT rules */

  echo "Firewall rules:\n";
  $rules = $config["filter"]["rule"];
  if (is_array($rules))
  {
    foreach ($rules as $nb => $rule)
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
  $aliases = $config["aliases"]["alias"];
  if (is_array($aliases))
  {
    foreach ($aliases as $id => $alias)
    {
      echo " $id: ".$alias["name"]." => ".$alias["address"]." (". $alias["descr"]. ")\n";
    }
  }

  echo "Proxy ARP:\n";
  /* TODO */

  echo "VLANs:\n";
  if (array_key_exists("vlans", $config))
  {
    $vlans = $config["vlans"]["vlan"];
    if (is_array($vlans))
    {
      foreach ($vlans as $pos => $vlan)
      {
        echo " $pos: ".$vlan["if"]."/".$vlan["tag"]." (".$vlan["descr"].")\n";
      }
    }
  }

  echo "----------------------------------------------------------------------------------------------------\n";
  echo "----------------------------------------------------------------------------------------------------\n";
  echo "----------------------------------------------------------------------------------------------------\n";
  echo "----------------------------------------------------------------------------------------------------\n";
//  echo $m0n0wall[2]["rawconfig"];
}

db_disconnect();
echo "[X] End of script, exiting...\n";

?>
