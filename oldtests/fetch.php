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

db_disconnect();
echo "[X] End of script, exiting...\n";

?>
