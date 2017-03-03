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
  $head->set("subtitle", "Users");
  $head->set("pagename", "Users: Edit");
  $menu = new Template("./tpl/menu.tpl");
  $menu->set("version", $config["version"]);
  $foot = new Template("./tpl/foot.tpl");
  $body = new Template("./tpl/message.tpl");
  $body->set("pagename", "Users: Edit");


  /* body */
  $msg = null;
  $error = null;

  /* connect to the database: */
  if (!@Mysql::getInstance()->connect()) {
    $error = "Cannot connect to MySQL: ".Mysql::getInstance()->getError()." !!";
    end_process();
  }

  $mid = getHTTPVar("mid");
  $uid = getHTTPVar("uid");
  $action = getHTTPVar("action");
  if (!isset($action)) $action = 1;

  if (isset($mid) && $mid) {
    $mono = new Monowall($mid);
    if (!$mono->fetchFromId()) {
      $error = "Cannot find specified device in database";
      end_process();
    }
    $mono->fetchGroups();
  } else {
    $error = "No device specified.";
    end_process();
  }
  $link = array("href" => "users.php?mid=".$mono->id, "label" => "Return to User list");

  $ok = 0;
  if ($action == 1) {
    if (isset($uid) && $uid) {
      $user = new User($uid);
      if ($user->fetchFromId()) {
        $ok = 1;
	$action = 3;
      } else {
        $error = "Cannot find user to edit";
	end_process();
      }
    } else {
      $user = new User();
      $user->id = 0;
      $ok = 1;
      $action = 2;
    }
    $body = new Template("./tpl/umod.tpl");
    if (!$uid) {
      $body->set("pagename", "User: Add");
    } else {
      $body->set("pagename", "User: Edit");
    }
    $body->set("user", array($user));
    $body->set("mono", array($mono));
    if (isset($action)) {
      $body->set("action", $action);
    }
    end_process();
    
  } else if ($action == 3) { /* mod */

    unset($action);
    $body = new Template("./tpl/message.tpl");
    $body->set("pagename", "User: Edit");
   
    $ok = 0;
    if (isset($uid) && $uid) {
      $user = new User($uid);
      if ($user->fetchFromId()) {
        $ok = 1;
      } else {
        $error = "Cannot find the user to modify in the database";
	end_process();
      }
    } else {
	$error = "No user to modify specified";
	end_process();
    }
    $mod = 0;

    if (checkPost("username") && $_POST["username"] != $user->name) {
      $user->name = $_POST["username"];
      $mod = 1;
    }

    if (checkPost("fullname") && $user->fullname != $_POST["fullname"]) {
      $user->fullname= $_POST["fullname"];
      $mod = 1;
    }

    if (checkPost("groupname")) {
      $g = new Group($_POST["groupname"]);
      if (!$g->fetchFromId()) {
	$error = "Cannot find the specified group in the database.";
	end_process();
      }
      if ($g->name != $user->grouname) {
        $user->groupname = $g->name;
        $mod = 1;
      }
    }

    if (checkPost("password") && checkPost("password2") && $_POST["password"] != $_POST["password2"]) {
      $error = "Password and its confirmation aren't the same.";
      end_process();
    }
 
    if (checkPost("password") && checkPost("password2") && $_POST["password"] == $_POST["password2"] && $_POST["password"] != $u->password) {
      $user->password = $_POST["password"];
      $mod = 1;
    }

    if ($mod) {
       if ($user->update()) {
         $msg = "User updated.<br/>";
 	 $mono->updateChanged();
       } else {
         $msg = "Failed to update User.<br/>";
         $error = "Failed";
	 
       }
     } else $msg = "Nothing to update.<br/>";

    end_process();

  } else if ($action == 2) { /* add */

    unset($action);
    $body = new Template("./tpl/message.tpl");
    $body->set("pagename", "User: Add");

    $user = new User();
    $user->idhost = $mono->id;
    $err = 0;

    if (checkPost("username"))
      $user->name = $_POST["username"];
    else $err = 1;
 
    if (checkPost("fullname"))
      $user->fullname = $_POST["fullname"];

    if (checkPost("password") && checkPost("password2") && $_POST["password"] != $_POST["password2"]) {
      $error = "Password and its confirmation aren't the same.";
      end_process();
    }

    if (checkPost("password") && checkPost("password2") && $_POST["password"] == $_POST["password2"]) {
      $user->password = $_POST["password"];
    }

    if (checkPost("groupname")) {
      $g = new Group($_POST["groupname"]);
      if (!$g->fetchFromId()) {
        $error = "Cannot find the specified group in the database.";
        end_process();
      }
      if ($g->name != $user->grouname) {
        $user->groupname = $g->name;
      }
    } else $err = 1;

    if (!$err) {
      if ($user->existsDb()) $msg = "User already in database...<br/>";
      else {
        if ($user->insert()) {
	  $mono->updateChanged();
          $msg = "User inserted.<br/>";
        } else {
 	  $msg = "Failed insert user.<br/>";
	  $error = "Unable to insert user into database..<br/>";
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
