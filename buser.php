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
  $head->set("subtitle", "Backup user");
  $head->set("pagename", "Backup user");
  $menu = new Template("./tpl/menu.tpl");
  $menu->set("version", $config["version"]);
  $foot = new Template("./tpl/foot.tpl");
  $body = new Template("./tpl/select.tpl");
  $body->set("pagename", "Assign backup user");

  /* body */
  $msg = null;
  $error = null;

  /* connect to the database: */
  if (!@Mysql::getInstance()->connect()) {
    $error = "Cannot connect to MySQL: ".Mysql::getInstance()->getError()." !!";
    end_process();
  }

  $ok = 0;
  $mid = getHTTPVar("mid");
  if (!empty($mid)) {
    $mono = new Monowall($mid);
    if ($mono->fetchFromId() ) {
      $ok = 1;
    } else {
      $ok = 0;
      $error = "Unable to fetch m0n0wall with provided ID.";
      end_process();
    }
  } else {
    $ok = 0;
    $error = "You must select a device to change its backup user.";
    end_process();
  }

  if (checkPost("buid")) {
    $bu = new Buser($_POST["buid"]);
    if (!$bu->fetchFromId()) {
      $error = "Can't find backup user selected in the database";
      $ok = 0;
    }
  } else {
    $ok = 0;
  }

  if (!$ok) {
    $body->set("action", "buser.php");
    $main = Main::getInstance();
    $main->fetchBusers();
    $hidden = array(
			array("name" => "mid",
			      "value" => $mono->id)
		);
    $body->set("hidden", $hidden);
    $select = array(	"title" => "backup user",
			"name" => "buid",
			"desc" => "Please select the new backup user"
		);
    $list = array();
    foreach ($main->busers as $bu) {
      $item = array("value" => $bu->id,
		    "label" => $bu->login
		   );
      array_push($list, $item);
    }
    $body->set("list", $list);
    $body->set("select", $select);
    end_process();
  }

  $body = new Template("./tpl/message.tpl");
  $body->set("pagename", "Assign backup user");

  $mono->idbuser = $bu->id;
  if ($mono->update()) {
    $msg = "Backup user updated correctly";
  } else {
    $error = "Can't update the backup user into the database";
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
