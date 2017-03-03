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
$head->set("subtitle", "Backup Users");
$head->set("pagename", "Backup Users");
$menu = new Template("./tpl/menu.tpl");
$menu->set("version", $config["version"]);
$foot = new Template("./tpl/foot.tpl");
$body = new Template("./tpl/message.tpl");
$body->set("pagename", "Remove Backup user");

/* body */
$msg = null;
$error = null;

/* connect to the database: */
if (!@Mysql::getInstance()->connect()) {
  $error = "Cannot connect to MySQL: ".Mysql::getInstance()->getError()." !!";
  end_process();
}

$main = Main::getInstance();

if (isset($_GET["bid"])) {
   $b = new Buser($_GET["bid"]);
   if (!$b->fetchFromId()) {
     $error = "Can't find backup user selected for deletion";
     end_process();
   }
} else {
  $error = "No backup user specified for deletion";
  end_process();
}

if (isset($b)) {
  $main->fetchMonoId();
  $main->fetchMonoDetails();
  foreach($main->monowall as $mono) {
    if ($mono->idbuser == $bu->id) {
      $mono->idbuser = -1;
      $mono->buser = NULL;
      $mono->update();
    }
  }
  $b->delete();
  $msg = "Backup User removed from database...";
} else {
  $error = "Error finding backup user ...";
}

$link = array("href" => "busers.php", "label" => "Return to backup user list");

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
