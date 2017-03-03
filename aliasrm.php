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
$head->set("subtitle", "Aliases");
$head->set("pagename", "Aliases");
$menu = new Template("./tpl/menu.tpl");
$menu->set("version", $config["version"]);
$foot = new Template("./tpl/foot.tpl");
$body = new Template("./tpl/message.tpl");
$body->set("pagename", "Remove Alias");

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
  $error = "No device selected for alias deletion";
  end_process();
}

if (getHTTPVar("aid")) {
   $a = new Alias(getHTTPVar("aid"));
   if (!$a->fetchFromId()) {
     $error = "Can't find alias selected for deletion";
     end_process();
   }
} else {
  $error = "No Alias specified for deletion";
  end_process();
}

if (isset($a)) {
  if ($a->idhost != $mono->id) {
    $error = "The selected alias is not owned by the specified device, abording deletion";
    end_process();
  }
  $a->delete();
  $mono->updateChanged();
  $msg = "Alias removed from device database...";
} else {
  $error = "Error finding alias...";
}

$link = array("href" => "viewaliases.php?mid=".$mono->id, "label" => "Return to aliases");

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
