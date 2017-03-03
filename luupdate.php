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
  require_once("./lib/html.lib.php");
  require_once("./lib/autoload.lib.php");

  /* sanitize _GET and _POST */
  sanitizeArray($_GET);
  sanitizeArray($_POST);

  /* common to all pages */
  $page = new Template("./tpl/main.tpl");
  $head = new Template("./tpl/head.tpl");
  $head->set("title", "m0n0wall Central Management Interface");
  $head->set("subtitle", "Last Update time update");
  $head->set("pagename", "Last Update time update");
  $menu = new Template("./tpl/menu.tpl");
  $menu->set("version", $config["version"]);
  $foot = new Template("./tpl/foot.tpl");

  /* body */
  $msg = null;
  $error = null;

  $main = Main::getInstance();

  /* connect to the database: */
  if (!@Mysql::getInstance()->connect()) {
    $error = "Cannot connect to MySQL: ".Mysql::getInstance()->getError()." !!";
    end_process();
  }

  $body = new Template("./tpl/message.tpl");
  $body->set("pagename", "Last updated time update");

  $ok = 0;
  if (getHTTPVar("mid")) {
    $mono = new Monowall(getHTTPVar("mid"));
    if ($mono->fetchFromId() ) {
      $ok = 1;
    } else {
      $error = "Unable to fetch m0n0wall with provided ID.";
    }
  }

  if (isset($mono) && $mono) {
    $mono->fetchFromId();
    $mono->fetchBuser();
  } else {
    $main->fetchMonoId();
    $main->fetchMonoDetails();
    foreach ($main->monowall as $mono) { $mono->fetchBuser(); }
  }

  $mysql = Mysql::getInstance();
  if (isset($mono)) {
    $ret = $mono->updateLChange();
    $ok = getcurlerror($ret);
    if ($ok) {
      $msg = $mono->hostname.".".$mono->domain." last change time updated";
    } else {
      $error = $mono->hostname.".".$mono->domain." can't be updated: ".$error;
    }
  } else {
   foreach($main->monowall as $mono) {
     $ret = $mono->updateLChange();
     $ok = getcurlerror($ret);
     if ($ok) {
       $msg .= $mono->hostname.".".$mono->domain." last change time updated<br/>";
     }
     else {
       $error .= $mono->hostname.".".$mono->domain." can't be updated: ".$error."<br/>";
     }
   }
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
