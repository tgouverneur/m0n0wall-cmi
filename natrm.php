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
$head->set("subtitle", "NAT");
$head->set("pagename", "NAT");
$menu = new Template("./tpl/menu.tpl");
$menu->set("version", $config["version"]);
$foot = new Template("./tpl/foot.tpl");
$body = new Template("./tpl/message.tpl");
$body->set("pagename", "Remove NAT");

/* body */
$msg = null;
$error = null;

/* connect to the database: */
if (!@Mysql::getInstance()->connect()) {
  $error = "Cannot connect to MySQL: ".Mysql::getInstance()->getError()." !!";
  end_process();
}

$main = Main::getInstance();

if (getHTTPVar("mid")) {
  $mono = new Monowall(getHTTPVar("mid"));
  if (!$mono->fetchFromId()) {
    $error = "Cannot find specified device in the database";
    end_process();
  }
} else {
  $error = "No device selected for NAT entry deletion";
  end_process();
}

if (getHTTPVar("nid") && getHTTPVar("type")) {
    $nid = getHTTPVar("nid");
    $type = getHTTPVar("type");
    switch($type) {
     case 1:
         $nat = new RuleNat($nid);
     break;
     case 2:
         $nat = new SrvNat($nid);
     break;
     case 3:
         $nat = new O2ONat($nid);
     break;
     case 4:
         $nat = new AdvNat($nid);
     break;
   }
   $nat->fetchFromId();
   if (!$nat->fetchFromId()) {
     $error = "Can't find nat selected for deletion";
     end_process();
   }
} else {
  $error = "No NAT specified for deletion";
  end_process();
}

if (isset($nat)) {
  if ($nat->idhost != $mono->id) {
    $error = "The selected NAT entry is not owned by the specified device, abording deletion";
    end_process();
  }
  $nat->delete();
  $mono->updateChanged();
  $msg = "NAT removed from device database...";
} else {
  $error = "Error finding NAT entry...";
}

$link = array("href" => "viewnat.php?mid=".$mono->id."&type=".$type, "label" => "Return to NAT entry list");

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
