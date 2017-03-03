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
  $head->set("pagename", "Backup user: Edit");
  $menu = new Template("./tpl/menu.tpl");
  $menu->set("version", $config["version"]);
  $foot = new Template("./tpl/foot.tpl");
  $body = new Template("./tpl/message.tpl");
  $body->set("pagename", "Backup user: Edit");


  /* body */
  $msg = null;
  $error = null;

  /* connect to the database: */
  if (!@Mysql::getInstance()->connect()) {
    $error = "Cannot connect to MySQL: ".Mysql::getInstance()->getError()." !!";
    end_process();
  }

  $mid = getHTTPVar("mid");
  $bid = getHTTPVar("bid");
  $action = getHTTPVar("action");
  if (!isset($action)) $action = 1;

  $link = array("href" => "busers.php", "label" => "Return to Backup user list");

  $ok = 0;
  if ($action == 1) {
    if (isset($bid) && $bid) {
      $buser = new Buser($bid);
      if ($buser->fetchFromId()) {
        $ok = 1;
	$action = 3;
      } else {
        $error = "Cannot find backup user to edit";
	end_process();
      }
    } else {
      $buser = new Buser();
      $buser->id = 0;
      $ok = 1;
      $action = 2;
    }
    $body = new Template("./tpl/bumod.tpl");
    if (!$bid) {
      $body->set("pagename", "Backup User: Add");
    } else {
      $body->set("pagename", "Backup User: Edit");
    }
    $body->set("buser", array($buser));
    if (isset($action)) {
      $body->set("action", $action);
    }
    end_process();
    
  } else if ($action == 3) { /* mod */

    unset($action);
    $body = new Template("./tpl/message.tpl");
    $body->set("pagename", "Backup User: Edit");
   
    $ok = 0;
    if (isset($bid) && $bid) {
      $buser = new Buser($bid);
      if ($buser->fetchFromId()) {
        $ok = 1;
      } else {
        $error = "Cannot find the backup user to modify in the database";
	end_process();
      }
    } else {
	$error = "No backup user to modify specified";
	end_process();
    }
    $mod = 0;

    if (checkPost("login") && $_POST["login"] != $buser->login) {
      $buser->login = $_POST["login"];
      $mod = 1;
    }

    if (checkPost("description") && $buser->description != $_POST["description"]) {
      $buser->description = $_POST["description"];
      $mod = 1;
    }
  
    if (checkPost("password") && !empty($_POST["password"])) {
      $buser->password = $_POST["password"];
      $mod = 1;
    }

    if ($mod) {
       if ($buser->update()) {
         $msg = "Backup user updated.<br/>";
       } else {
         $msg = "Failed to update Backup user.<br/>";
         $error = "Failed";
	 
       }
     } else $msg = "Nothing to update.<br/>";

    end_process();

  } else if ($action == 2) { /* add */

    unset($action);
    $body = new Template("./tpl/message.tpl");
    $body->set("pagename", "Backup User: Add");

    $buser = new Buser();
    $buser->idhost = $mono->id;
    $err = 0;

    if (checkPost("login") && !empty($_POST["login"])) {
      $buser->login = $_POST["login"];
    } else {
      $err = 1;
    }
 
    if (checkPost("description")) {
      $buser->description = $_POST["description"];
    }

    if (checkPost("password") && !empty($_POST["password"])) {
      $buser->password = $_POST["password"];
    }

    if (!$err) {
      if ($buser->existsDb()) {
        $msg = "Backup user already in database...<br/>";
      } else {
        if ($buser->insert()) {
          $msg = "Backup user inserted.<br/>";
        } else {
 	  $msg = "Failed insert backup user.<br/>";
	  $error = "Unable to insert backup user into database..<br/>";
        }
      }
    } else $error = "Error, missing or incorrect field.<br/>";

    end_process();

  }

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
