<?php
require_once("./inc/config.inc.php");
require_once("./lib/mysql.obj.php");
require_once("./lib/config.obj.php");
require_once("./lib/buser.obj.php");
require_once("./lib/hw.obj.php");
require_once("./lib/monowall.obj.php");

if (!Mysql::getInstance()->connect())
 { echo "Cannot connect to mysql database<br/>\n"; die(); }

$m = new Monowall(7);
$m->fetchFromId();
$m->fetchBuser();
$m->hwDetect();

foreach ($m->hwInt as $eth) {
  echo $eth->name."/".$eth->mac."\n";
  if (!$eth->existsDb()) {
    $eth->insert();
    echo   "    iface inserted in db\n";
  } else {
    if ($eth->ischanged()) {
      $eth->update();
      echo "    iface updated in db\n";
    }
  }
}
?>
