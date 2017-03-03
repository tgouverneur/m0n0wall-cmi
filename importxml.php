<?php
 /**
  * Base file for HTML processing
  *
  * @author Gouverneur Thomas <tgouverneur@be.tiauto.com>
  * @copyright Copyright (c) 2007-2008, Gouverneur Thomas - TI Automotive
  * @version 1.0
  * @package html
  * @subpackage base
  * @category html
  * @filesource
  */
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
$head->set("subtitle", "XML Import");
$head->set("pagename", "import m0n0wall from XML");
$menu = new Template("./tpl/menu.tpl");
$menu->set("version", $config["version"]);
$foot = new Template("./tpl/foot.tpl");

/* body */
$msg = null;
$error = null;
 
if (isset($_POST["Submit"])) {
 if (isset($_POST["xml"]) && !empty($_POST["xml"])) {

   $body = new Template("./tpl/message.tpl");
   $body->set("pagename", "Import device from XML");
   $mono = new Monowall();

   $msg .= "Parsing configuration...";
   
   $mono->config->rawconfig = str_replace('\"', '"', $_POST["xml"]);

   if (!(@$mono->config->parseConfig())) {
     $error = "Error while parsing configuration...<br/>";
     $msg .= "failed<br/>";
     end_process();
   }

   $msg .= "done<br/>";

   $msg .= "Connecting to MySQL...";
   if (!Mysql::getInstance()->connect()) {
     $error = "Cannot connect to MySQL: ".Mysql::getInstance()->getError()." !!";
     $msg .= "failed<br/>";
     end_process();
   }
   $main = Main::getInstance();
   $msg .= "ok<br/>";


   Main::getInstance()->fetchRulesId();
   Main::getInstance()->fetchRulesDetails();

   $msg .= "Filling m0n0wall's objects...";
   if (!(@$mono->config->fillObj())) {
     $error = "Error while filling Objects...<br/>";
     $msg .= "failed<br/>";
     end_process();
   }
   $msg .= "done<br/>";

   $msg .= "Insertion of all objects into database...";
   $msg .= "&nbsp;&nbsp;&nbsp;Base...";
   $mono->insert();
   $mono->id = Mysql::getInstance()->getLastId();
   $msg .= "done<br/>";

   $msg .= "&nbsp;&nbsp;&nbsp;Properties...";
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
   $msg .= "done<br/>";

   $msg .= "&nbsp;&nbsp;&nbsp;Syslog settings...";
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
   $msg .= "done<br/>";

   $msg .= "&nbsp;&nbsp;&nbsp;SNMP Settings...";
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
   $msg .= "done<br/>";

   $msg .= "&nbsp;&nbsp;&nbsp;Groups...";
   foreach ($mono->group as $group) {
     $group->idhost = $mono->id;
     if ($group->existsInDb()) {
       $group->fetchId();
       if ($group->ischanged()) {
         $group->update();
       }
     } else {
       $group->insert();
       $group->fetchId();
     }
   }
   $msg .= "done<br/>";

   $msg .= "&nbsp;&nbsp;&nbsp;Users...";
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
   $msg .= "done<br/>";

   $msg .= "&nbsp;&nbsp;&nbsp;Interfaces...";
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
   $msg .= "done<br/>";

   $msg .= "&nbsp;&nbsp;&nbsp;VLANs...";
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
   $msg .= "done<br/>";


   $msg .= "&nbsp;&nbsp;&nbsp;Local aliases...";
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
   $msg .= "done<br/>";

   $msg .= "&nbsp;&nbsp;&nbsp;ProxyARP...";
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
   $msg .= "done<br/>";


   $msg .= "&nbsp;&nbsp;&nbsp;Static Routes...";
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
   $msg .= "done<br/>";

   foreach($mono->ifaces as $iface) {
     $msg .= "&nbsp;&nbsp;&nbsp;Inserting rules for iface ".$iface->type;
     $msg .= ($iface->num != 0)?$iface->num:"";
     $msg .= "...";
     foreach($iface->rules as $rule) {
       if ($rule->existsInDb()) {
         $rule->fetchId();
	 if($rule->ischanged())
	   $rule->update();
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
       $ri = new RuleInt($rule->id, $iface->id, $iface->rulesp[$i][1], $iface->rulesp[$i][0]);
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
     $msg .= "done<br/>";
   }

   $msg .= "&nbsp;&nbsp;&nbsp;NAT rules...";
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
   $msg .= "done<br/>";


   $msg .= "&nbsp;&nbsp;&nbsp;Advanced NAT...";
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
   $msg .= "done<br/>";


   $msg .= "&nbsp;&nbsp;&nbsp;Server NAT...";
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
   $msg .= "done<br/>";


   $msg .= "&nbsp;&nbsp;&nbsp;One to One NAT...";
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
   $msg .= "done<br/>";

   $msg .= "&nbsp;&nbsp;&nbsp;All remaining unknown objects...";
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
   $msg .= "done<br/>";

   if (isset($_POST["hostname"]) && !empty($_POST["hostname"])) {
     $msg .= "Overwritting the hostname to be ".$_POST["hostname"]."...";
     $mono->hostname = $_POST["hostname"];
     $msg .= "done<br/>";
   }
   
   if (isset($_POST["domain"]) && !empty($_POST["domain"])) {
     $msg .= "Overwritting the domain name to be ".$_POST["domain"]."...";
     $mono->domain = $_POST["domain"];
     $msg .= "done<br/>";
   }

   $msg .= "Updating m0n0wall base object...";
   $mono->update();
   $msg .= "done<br/>";

   $body->set("message", $msg);
   $body->set("error", $error);
   $body->set("link", array("href" => "index.php", "label" => "return to main page"));

 } else { /* XML empty */

   $body = new Template("./tpl/importxml.tpl");
   $body->set("pagename", "Import device from XML");
   $body->set("error", "You haven't pasted any XML content...");
 }
} else { /* not yet submitted */

 $body = new Template("./tpl/importxml.tpl");
 $body->set("pagename", "Import device from XML");

}

end_process();
/* end process and exit script */
function end_process() {
	global $body, $page, $form, $head, $menu, $foot, $msg, $error;

	/* template processing */
	$body->set("message", $msg);
	$body->set("error", $error);

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
