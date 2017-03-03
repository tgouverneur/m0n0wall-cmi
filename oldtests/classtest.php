<?php
 /* 
  **
  * first test for fetching configuration from m0n0wall
  **
  **
  * tgouverneur - 2007
  **
  */

/* require configuration */
require_once("./inc/config.inc.php");

/* require objects */
require_once("./lib/main.obj.php");
require_once("./lib/monowall.obj.php");
require_once("./lib/config.obj.php");
require_once("./lib/buser.obj.php");
require_once("./lib/rule.obj.php");
require_once("./lib/iface.obj.php");
require_once("./lib/vlan.obj.php");
require_once("./lib/nat.obj.php");
require_once("./lib/proxyarp.obj.php");
require_once("./lib/alias.obj.php");
require_once("./lib/syslog.obj.php");
require_once("./lib/snmp.obj.php");
require_once("./lib/staticroutes.obj.php");
require_once("./lib/prop.obj.php");
require_once("./lib/user.obj.php");
require_once("./lib/group.obj.php");


$readfrommono = 0;

if (!Mysql::getInstance()->connect()) { echo Mysql::getInstance()->getError()."\n"; die(); }

$main = Main::getInstance();
$main->fetchMonoId();
$main->fetchMonoDetails();

/* db data init */
if (!$readfrommono) {
  $main->fetchMonoProp();
  $main->fetchMonoGroups();
  $main->fetchMonoUsers();
  $main->fetchMonoSyslog();
  $main->fetchMonoSnmp();
  $main->fetchMonoIfaces();
  $main->fetchMonoIfacesDetails();
  $main->fetchMonoVlans();
  $main->fetchMonoVlansDetails();
  $main->fetchMonoAlias();
  $main->fetchMonoAliasDetails();
  $main->fetchMonoProxyarp();
  $main->fetchMonoProxyarpDetails();
  $main->fetchMonoRoutes();
  $main->fetchMonoRoutesDetails();
  $main->fetchRulesId();
  $main->fetchRulesDetails();
  $main->fetchRuleInt();
  $main->fetchAllNat();
  $main->fetchAllNatDetails();
}

/* configuration parsing */
if ($readfrommono) {
  foreach ($main->monowall as $mono) $mono->fetchBuser();
  $main->fetchAllConfig();
  $main->parseAllConfig();
}

echo "Number of devices in DB: ". $main->monoCount() ."\n";

if ($readfrommono)
  foreach ($main->monowall as $mono) {
//    $mono->config->viewConfig();
    var_dump($mono->config->rawconfig);
    var_dump($mono->config->config);
  }

foreach ($main->monowall as $mono) {

  //echo "--";
  //var_dump($mono->config->config);
  //echo "--";
  /* fill monowall object with config */
  if ($readfrommono)  $mono->config->fillObj();

  foreach ($mono->prop as $p) {

    if ($p->existsInDb())
    {
      $p->fetchId();
  
      if ($p->ischanged()) {
       $p->update();
      }
    }
    else {
     $p->insert();
     $p->fetchId();
    }   
  }

  if ($mono->syslog->existsInDb())
  {
    $mono->syslog->fetchId();
    $mono->idsyslog = $mono->syslog->id;

    if ($mono->syslog->ischanged()) {
     $mono->syslog->update();
    }
  }
  else { 
   $mono->syslog->insert(); 
   $mono->syslog->fetchId();
   $mono->idsyslog = $mono->syslog->id;
  }

  if ($mono->snmp->existsInDb())
  {
    $mono->snmp->fetchId();
    $mono->idsnmp = $mono->snmp->id;

    if ($mono->snmp->ischanged()) {
     $mono->snmp->update();
    }
  }
  else { 
   $mono->snmp->insert(); 
   $mono->snmp->fetchId();
   $mono->idsnmp = $mono->snmp->id;
  }

  foreach ($mono->group as $group) {

    if ($group->existsInDb())
    {
      $group->fetchId();

      if ($group->ischanged()) {
       $group->update();
      }
    }
    else {
     $group->insert();
     $group->fetchId();
    }
  }
  
  foreach ($mono->user as $user) {

    if ($user->existsInDb())
    {
      $user->fetchId();

      if ($user->ischanged()) {
       $user->update();
      }
    }
    else {
     $user->insert();
     $user->fetchId();
    }
  }


  /* if mono has changed, update in the db */
  if ($mono->ischanged() == 1) {
    echo "Monowall need update: ".$mono->hostname."\n";
    $mono->update();
  } else
    echo "Monowall doesn't need update...\n";

  /* loop through ifaces, if they already exist, update them if needed, else insert */
  foreach ($mono->ifaces as $iface) {

    if ($iface->existsInDb())
    {
      echo "Iface ".$iface->if." already in db..\n";
      if ($iface->ischanged())
      {
        echo " but need update: \n";
        $iface->update();
      } else { echo " No need to update it too...\n"; }
    }
    else {
     $iface->insert();
    }
  }
  
  /* loop through vlans */
  foreach ($mono->vlans as $vlan) {

   if ($vlan->existsInDb())
   { 
      echo "Vlan ".$vlan->tag." already in db..\n";
      if ($vlan->ischanged())
      { 
        echo " but need update: \n";
        $vlan->update();
      } else { echo " No need to update it too...\n"; }
    }
    else {
     echo "Vlan ".$vlan->tag." inserted\n";
     $vlan->insert();
    }
  }
 
  /* loop through alias */
  foreach ($mono->alias as $alias) {

   if ($alias->existsInDb())
   { 
      echo "Alias".$alias->name." already in db..\n";
      if ($alias->ischanged())
      { 
        echo " but need update: \n";
        $alias->update();
      } else { echo " No need to update it too...\n"; }
    }
    else {
     echo "Alias ".$alias->name." inserted\n";
     $alias->insert();
    }
  }
 
  /* loop through proxyarp */
  foreach ($mono->proxyarp as $pa) {

   if ($pa->existsInDb())
   { 
      echo "ProxyArp ".$pa->network." already in db..\n";
      if ($pa->ischanged())
      { 
        echo " but need update: \n";
        $pa->update();
      } else { echo " No need to update it too...\n"; }
    }
    else {
     echo "Proxyarp ".$pa->network." inserted\n";
     $pa->insert();
    }
  }
 

 
  /* loop through Static routes */
  foreach ($mono->sroutes as $sroute) {

   if ($sroute->existsInDb())
   { 
      echo "Static route to".$sroute->network." already in db..\n";
      if ($sroute->ischanged())
      { 
        echo " but need update: \n";
        $sroute->update();
      } else { echo " No need to update it too...\n"; }
    }
    else {
     echo "Static route ".$sroute->network." inserted\n";
     $vlan->insert();
    }
  }


}

foreach ($main->rules as $rule) {
 
  if ($rule->existsInDb())
  {
    if ($rule->id == -1)
      $rule->fetchId();
    echo "Rule ".$rule->type."/".$rule->if."/".$rule->protocol." is already in db..\n";
    if ($rule->ischanged())
    {
      echo " but need update..\n";
      $rule->update();
    } else { echo " and no changes needed..\n"; }
  } else {
   $rule->insert();
   echo "RULEID: ".$rule->id."\n";
  }

}

if ($readfrommono) /* if we are readinf monowall's config, loop and so on */
foreach ($main->monowall as $mono) {

    foreach ($mono->ifaces as $iface)
    {
      $main->removeRuleIntIf($iface->id);
      RuleInt::dropAllIface($iface->id);
      $iface->rulesint = array();
    }

    $riarray = array();
    foreach ($mono->ifaces as $iface) {

      /* loop through rules associated */
      reset($iface->rules);
      $i=0;
      foreach ($iface->rules as $rule)
      {
        $ri = new RuleInt($rule->id, $iface->id, $iface->rulesp[$i][1], $iface->rulesp[$i][0]);
        $ri->iface = $iface;
        $ri->rule = $main->getRule($ri->idrule);
        array_push($main->ruleint, $ri);
        array_push($iface->rulesint, $ri);
        $riarray[$ri->position] = $ri;
        $i++;
      }
    }
    reset($riarray);
    foreach ($riarray as $ri) {
      $ri->insert();
      echo "Rule inserted: ".$ri->idint."/".$ri->idrule.".\n";
    }

  /* classic nat */
  foreach ($mono->nat as $nat)
  {
    if ($nat->existsInDb())
    {
      echo "Nat ".$nat->external."/".$nat->eport."/".$nat->proto." is already in db..\n";
      if ($nat->ischanged())
      { 
        echo " but need update..\n";
        $nat->update();
      } else { echo " and no changes needed..\n"; }
    } else {
      echo "Nat ".$nat->external."/".$nat->eport."/".$nat->proto." inserted\n";
     $nat->insert();
    }    
  }

  /* advnat */
  foreach ($mono->advnat as $nat)
  {
    if ($nat->existsInDb())
    {
      echo "AdvNat ".$nat->if."/".$nat->destination."/".$nat->target." is already in db..\n";
      if ($nat->ischanged())
      { 
        echo " but need update..\n";
        $nat->update();
      } else { echo " and no changes needed..\n"; }
    } else {
      echo "AdvNat ".$nat->if."/".$nat->destination."/".$nat->target." inserted..\n";
     $nat->insert();
    }    
  }

  /* server nat */
  foreach ($mono->srvnat as $nat)
  {
    if ($nat->existsInDb())
    {
      echo "SrvNat ".$nat->ipaddr." is already in db..\n";
      if ($nat->ischanged())
      { 
        echo " but need update..\n";
        $nat->update();
      } else { echo " and no changes needed..\n"; }
    } else {
      echo "SrvNat ".$nat->ipaddr." inserted..\n";
     $nat->insert();
    }    
  }

  /* O2O nat */
  foreach ($mono->o2onat as $nat)
  {
    if ($nat->existsInDb())
    {
      echo "O2ONat ".$nat->if."/".$nat->internal."/".$nat->external." is already in db..\n";
      if ($nat->ischanged())
      { 
        echo " but need update..\n";
        $nat->update();
      } else { echo " and no changes needed..\n"; }
    } else {
      echo "O2ONat ".$nat->if."/".$nat->internal."/".$nat->external." inserted\n";
     $nat->insert();
    }    
  }

}


echo "---------------------------------\n";

foreach ($main->monowall as $mono) {

  $mono->config->dbToLocal();
  $mono->config->XML();
  $mono->config->saveConfig("./conf/".$mono->hostname.".xml");
  echo "-----------------------------------\n";
//  echo $mono->config->rawconfig."\n";
}


Mysql::getInstance()->disconnect();
echo "[X] End of script, exiting...\n";

?>
