<?php
 /**
  * Base file for HTML processing
  *
  * @author Gouverneur Thomas <tgo@ians.be>
  * @copyright Copyright (c) 2007-2008, Gouverneur Thomas
  * @version 1.0
  * @package html
  * @subpackage base
  * @category html
  * @filesource
  */

require_once("./inc/config.inc.php");

require_once("./inc/config.inc.php");
require_once("./lib/html.lib.php");
require_once("./lib/autoload.lib.php");

/* sanitize _GET and _POST */
sanitizeArray($_GET);
sanitizeArray($_POST);

/* common to all pages */
$page = new Template("./tpl/main.tpl");
$head = new Template("./tpl/head.tpl");
$head->set("title", "m0n0wall Central Management Interface");
$head->set("subtitle", "Import device");
$head->set("pagename", "Import device");
$menu = new Template("./tpl/menu.tpl");
$menu->set("version", $config["version"]);
$foot = new Template("./tpl/foot.tpl");
$body = new Template("./tpl/import.tpl");
$body->set("pagename", "Import device");

/* body */
$msg = null;
$error = null;
$action = 1;

/* connect to the database: */
if (!@Mysql::getInstance()->connect()) {
  $error = "Cannot connect to MySQL: ".Mysql::getInstance()->getError()." !!";
  end_process();
}

$main = Main::getInstance();
if (isset($action)) {
 $main->fetchMonoId();
 $main->fetchMonoDetails();
}
$main->fetchBusers();
$body->set("busers", $main->busers);

if (checkPost("action") && $_POST["action"] == 1) {
 $ok = 0;
 if (!checkPost("hostname") || empty($_POST["hostname"]) || 
     !checkPost("domain") || empty($_POST["domain"])) {
   $ok = 0;
   $error = "Hostname and Domain name value are mandatory.";
   end_process();
 }
 if (!checkPost("buser") || empty($_POST["buser"])) {
   $ok = 0;
   $error = "No backup user has been selected";
   end_process();
 }
 $bu = new Buser(mysql_escape_string($_POST["buser"]));
 if (!$bu->fetchFromId()) {
   $ok = 0;
   $error = "Backup user can't be fetched...";
   end_process();
 }
 foreach($main->monowall as $mono) {
   if ($mono->hostname == $_POST["hostname"] && 
       $mono->domain == $_POST["domain"]) {
     $error = "Error, monowall already in database...";
     $ok = 0;
     end_process();
   }
 }
 if (isset($_POST["use_ip"]) && $_POST["use_ip"] == 1) {
   if (!isset($_POST["ip"]) || empty($_POST["ip"])) {
     $error = "IP Address not set although it seems to be used";
     $ok = 0;
     end_process();
   }
 }
 $mono = new Monowall();
 $mono->idbuser = $bu->id;
 $mono->buser = $bu;
 $mono->hostname = $_POST["hostname"];
 $mono->domain = $_POST["domain"];
 if (checkPost($_POST["use_ip"]) && $_POST["use_ip"] == 1) {
   $mono->use_ip = 1;
 }
 $mono->ip = $_POST["ip"];
 if (checkPost("https") && $_POST["https"] == 1) {
   $mono->https = 1;
 } else {
   $mono->https = 0;
 }
 if (checkPost("port")) {
   $mono->port = $_POST["port"];
 }
 $ret = $mono->config->fetchConfig();
 $ok = getcurlerror($ret);
 if (!$ok) {
   $mono->delete();
   end_process();
 } else if ($ok) {
  $mono->config->parseConfig();
  $mono->config->fillObj();
  /* monowall */
  $mono->insert();
  $mono->id = Mysql::getInstance()->getLastId();
  foreach ($mono->prop as $p) {
    $p->idhost = $mono->id;
    if ($p->existsInDb()) {
      $p->fetchId();
      if ($p->ischanged()) {
        $p->update();
      }
    } else {
      $p->insert();
      $p->id = Mysql::getInstance()->getLastId();
    }
  }
  /* syslog */
  if ($mono->syslog->existsInDb()) {
    $mono->syslog->fetchId();
    $mono->idsyslog = $mono->syslog->id;
    if ($mono->syslog->ischanged()) {
      $mono->syslog->update();
    }
  } else {
    $mono->syslog->insert();
    $mono->syslog->fetchId();
    $mono->idsyslog = $mono->syslog->id;
  }
  /* SNMP */
  if ($mono->snmp->existsInDb())
  {
    $mono->snmp->fetchId();
    $mono->idsnmp = $mono->snmp->id;
    if ($mono->snmp->ischanged()) {
      $mono->snmp->update();
    }
  } else {
    $mono->snmp->insert();
    $mono->snmp->fetchId();
    $mono->idsnmp = $mono->snmp->id;
  }
  /* groups */
  foreach ($mono->group as $group) {
    $group->idhost = $mono->id;
    if ($group->existsInDb())
    {
      $group->fetchId();
      if ($group->ischanged()) {
        $group->update();
      }
    } else {
      $group->insert();
      $group->fetchId();
    }
  }
  /* Users */
  foreach ($mono->user as $user) {
    $user->idhost = $mono->id;
    if ($user->existsInDb())
    {
      $user->fetchId();
      if ($user->ischanged()) {
        $user->update();
      }
    } else {
      $user->insert();
      $user->fetchId();
    }
  }
  /* updating m0n0wall base obj */
  if ($mono->ischanged() == 1) {
    $mono->update();
  }
  /* Interfaces */
  foreach ($mono->ifaces as $iface) {
    $iface->idhost = $mono->id;
    if ($iface->existsInDb())
    {
      if ($iface->ischanged())
      {
        $iface->update();
      }
    } else {
      $iface->insert();
    }
  }
  /* VLANs */
  foreach ($mono->vlans as $vlan) {
    $vlan->idhost = $mono->id;
    if ($vlan->existsInDb())
    {
      if ($vlan->ischanged())
      {
        $vlan->update();
      }
    } else {
      $vlan->insert();
    }
  }
  /* Local Aliases */
  foreach ($mono->alias as $alias) {
    $alias->idhost = $mono->id;
    if ($alias->existsInDb())
    {
      if ($alias->ischanged())
      {
        $alias->update();
      }
    } else {
      $alias->insert();
    }
  }
  /* Proxy ARP */
  foreach ($mono->proxyarp as $pa) {
    $pa->idhost = $mono->id;
    if ($pa->existsInDb())
    {
      if ($pa->ischanged())
      {
        $pa->update();
      }
    } else {
      $pa->insert();
    }
  }
  /* Static routes */
  foreach ($mono->sroutes as $sroute) {
    $sroute->idhost = $mono->id;
    if ($sroute->existsInDb())
    {
      if ($sroute->ischanged())
      {
        $sroute->update();
      }
    } else {
      $sroute->insert();
    }
  }
  /* Rules */
  foreach($mono->ifaces as $iface) {
    $iface->idhost = $mono->id;
    foreach($iface->rules as $rule) {
      if ($rule->existsInDb()) {
        $rule->fetchId();
        if($rule->ischanged()) $rule->update();
      } else {
        $rule->insert();
        $rule->id = Mysql::getInstance()->getLastId();
      }
    }
    $main->removeRuleIntIf($iface->id);
    RuleInt::dropAllIface($iface->id);
    $iface->rulesint = array();
    reset($iface->rules);
    $i = 0;
    foreach($iface->rules as $rule) {
      $ri = new RuleInt($rule->id, $iface->id, 
 				  $iface->rulesp[$i][1], 
 				  $iface->rulesp[$i][0]);
      $ri->iface = $iface;
      $ri->rule = $main->getRule($ri->idrule);
      array_push($main->ruleint, $ri);
      array_push($iface->rulesint, $ri);
      $riarray[$ri->position] = $ri;
      $i++;
    }
    reset($riarray);
    foreach($riarray as $ri) {
      $ri->insert();
    }
  }
  /* Normal NAT */
  foreach ($mono->nat as $nat)
  {
    $nat->idhost = $mono->id;
    if ($nat->existsInDb())
    {
      if ($nat->ischanged())
      {
        $nat->update();
      }
    } else {
      $nat->insert();
    }
  }
  /* Adv NAT */
  foreach ($mono->advnat as $nat)
  {
    $nat->idhost = $mono->id;
    if ($nat->existsInDb())
    {
      if ($nat->ischanged())
      {
        $nat->update();
      }
    } else {
      $nat->insert();
    }
  }
  /* Server NAT */
  foreach ($mono->srvnat as $nat)
  {
    $nat->idhost = $mono->id;
    if ($nat->existsInDb())
    {
      if ($nat->ischanged())
      {
        $nat->update();
      }
    } else {
      $nat->insert();
    }
  }
  /* One to One NAT */
  foreach ($mono->o2onat as $nat)
  {
    $nat->idhost = $mono->id;
    if ($nat->existsInDb())
    {
      if ($nat->ischanged())
      {
        $nat->update();
      }
    } else {
      $nat->insert();
    }
  }
  /* Various */
  foreach ($mono->unknown as $unknown)
  {
    $unknown->idhost = $mono->id;
    $unknown->insert();
    $unknown->id = Mysql::getInstance()->getLastId();
  }
  foreach ($mono->unknown as $unknown) {
    if ($unknown->parent) {
      $unknown->idparent = $unknown->parent->id;
      $unknown->update();
    }
  }
  /* Finalize */
  $mysql = Mysql::getInstance();
  $ret = $mono->updateLChange();
  $ok = getcurlerror($ret);
  $ret = $mono->hwDetect();
  $ok = getcurlerror($ret);
  if ($ok) {
    /* delete current hardware interfaces */
    $mysql->delete("hw-int", "WHERE `idhost`='".$mono->id."'"); 
    foreach ($mono->hwInt as $eth) {
      if (!$eth->existsDb()) {
        $eth->insert();
      }
    }
  }
 }
 $body = new Template("./tpl/message.tpl");
 $body->set("pagename", "Import device");
 $msg = "m0n0wall successfully imported.";
 $link = array("href" => "hlist.php", "label" => "Return to device list");
 $body->set("message", $msg);
 $body->set("link", $link);
 $ok = 1;
}


end_process();

/* end process and exit script */
function end_process() {
        global $action, $body, $page, $form, $head, $menu, $foot, $msg, $error, $link, $list;

        /* template processing */
        $body->set("message", $msg);
        $body->set("error", $error);
        $body->set("link", $link);
        $body->set("list", $list);
  	$body->set("action", $action);

        $body->set("form", $form);
        $page->set("head", $head);
        $page->set("menu", $menu);
        $page->set("foot", $foot);
        $page->set("body", $body);
        echo $page->fetch();

        @Mysql::getInstance()->disconnect();
        exit(0);
}

?>
