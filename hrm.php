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
$head->set("subtitle", "Devices");
$head->set("pagename", "Devices");
$menu = new Template("./tpl/menu.tpl");
$menu->set("version", $config["version"]);
$foot = new Template("./tpl/foot.tpl");
$body = new Template("./tpl/message.tpl");
$body->set("pagename", "Remove Device");

/* body */
$msg = null;
$error = null;

/* connect to the database: */
if (!@Mysql::getInstance()->connect()) {
  $error = "Cannot connect to MySQL: ".Mysql::getInstance()->getError()." !!";
  end_process();
}

$main = Main::getInstance();
$mysql = Mysql::getInstance();

if (isset($_GET["mid"])) {
   $mono = new Monowall($_GET["mid"]);
   if (!$mono->fetchFromId()) {
     $error = "Can't find device selected for deletion";
     end_process();
   }
   $mono->fetchBuser();
   $mono->fetchProp();
   $mono->fetchIfaces();
   $main->fetchRulesId();
   $main->fetchRulesDetails();
   $mono->fetchRules();
   $mono->fetchSyslog();
   $mono->fetchSnmp();
   $mono->fetchHw();

} else {
  $error = "No Device specified for deletion";
  end_process();
}

if (isset($mono)) {
   foreach($mono->ifaces as $if) {
    foreach($if->rulesint as $ri) {
      $ri->delete();
      $index = "`idrule`";
      $table = "rules-int";
      $where = "WHERE `idrule`='".$ri->idrule."'";
      $m = Mysql::getInstance();
      $data = $m->select("idrule", $table, $where);
      if (!count($data)) {
        $ru = new Rule($ri->idrule);
        $ru->fetchFromId();
        $ru->delete();
  //      echo "Rule number ".$ru->id." deleted totally from database as there were no longer monowall using it...<br/>";
      }
    }
  }
  $mysql->delete("alias", "WHERE `idhost`='".$mono->id."'");           /* delete aliases */
  $mysql->delete("group", "WHERE `idhost`='".$mono->id."'");           /* delete groups */
  $mysql->delete("user", "WHERE `idhost`='".$mono->id."'");            /* delete users */
  $mysql->delete("interfaces", "WHERE `idhost`='".$mono->id."'");      /* delete interfaces */
  $mysql->delete("nat-advout", "WHERE `idhost`='".$mono->id."'");      /* delete NAT */
  $mysql->delete("nat-one2one", "WHERE `idhost`='".$mono->id."'");     /* delete NAT */
  $mysql->delete("nat-rules", "WHERE `idhost`='".$mono->id."'");       /* delete NAT */
  $mysql->delete("nat-srv", "WHERE `idhost`='".$mono->id."'");         /* delete NAT */
  $mysql->delete("properties", "WHERE `idhost`='".$mono->id."'");      /* delete properties */
  $mysql->delete("proxyarp", "WHERE `idhost`='".$mono->id."'");        /* delete proxyarp */
  $mysql->delete("snmp", "WHERE `id`='".$mono->idsnmp."'");            /* delete snmp */
  $mysql->delete("syslog", "WHERE `id`='".$mono->idsyslog."'");        /* delete syslog */
  $mysql->delete("staticroutes", "WHERE `idhost`='".$mono->id."'");    /* delete static routes */
  $mysql->delete("vlans", "WHERE `idhost`='".$mono->id."'");           /* delete vlans */
  $mysql->delete("hw-int", "WHERE `idhost`='".$mono->id."'");          /* HW interface */
  $mysql->delete("unknown", "WHERE `idhost`='".$mono->id."'");         /* Unknown objects */
  $mono->delete();
  $msg = "Device removed from database...";
} else {
  $error = "Error finding device...";
}

$link = array("href" => "index.php", "label" => "Return to device list");

end_process();


/* end process and exit script */
function end_process() {
        global $body, $page, $form, $head, $menu, $foot, $msg, $error, $link, $list;

        /* template processing */
        $body->set("message", $msg);
        $body->set("error", $error);
        $body->set("link", $link);
        $body->set("list", $list);

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
