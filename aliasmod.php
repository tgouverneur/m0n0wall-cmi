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
  $head->set("subtitle", "Aliases");
  $head->set("pagename", "Aliases: Edit");
  $menu = new Template("./tpl/menu.tpl");
  $menu->set("version", $config["version"]);
  $foot = new Template("./tpl/foot.tpl");
  $body = new Template("./tpl/message.tpl");
  $body->set("pagename", "Aliases: Edit");


  /* body */
  $msg = null;
  $error = null;

  /* connect to the database: */
  if (!@Mysql::getInstance()->connect()) {
    $error = "Cannot connect to MySQL: ".Mysql::getInstance()->getError()." !!";
    end_process();
  }

  $mid = getHTTPVar("mid");
  $aid = getHTTPVar("aid");
  $action = getHTTPVar("action");
  if (!isset($action)) $action = 1;

  if (isset($mid) && $mid) {
    $mono = new Monowall($mid);
    if (!$mono->fetchFromId()) {
      $error = "Cannot find specified device in database";
      end_process();
    }
  }
  $link = array("href" => "viewaliases.php?mid=".$mono->id, "label" => "Return to Alias list");

  $ok = 0;
  if ($action == 1) {
    if (isset($aid) && $aid) {
      $alias = new Alias($aid);
      if ($alias->fetchFromId()) {
        $ok = 1;
	$action = 3;
      } else {
        $error = "Cannot find alias to edit";
	end_process();
      }
    } else {
      $alias = new Alias();
      $alias->id = 0;
      $ok = 1;
      $action = 2;
    }
    $body = new Template("./tpl/aliasmod.tpl");
    if (!$aid) {
      $body->set("pagename", "Aliases: Add");
    } else {
      $body->set("pagename", "Aliases: Edit");
    }
    $body->set("alias", array($alias));
    $body->set("mono", array($mono));
    if (isset($action)) {
      $body->set("action", $action);
    }
    end_process();
    
  } else if ($action == 3) { /* mod */

    unset($action);
    $body = new Template("./tpl/message.tpl");
    $body->set("pagename", "Aliases: Edit");
   
    $ok = 0;
    if (isset($aid) && $aid) {
      $alias = new Alias($aid);
      if ($alias->fetchFromId()) {
        $ok = 1;
      } else {
        $error = "Cannot find the alias to modify in the database";
	end_process();
      }
    } else {
	$error = "No alias to modify specified";
	end_process();
    }
    $mod = 0;

    if (checkPost("name") && $_POST["name"] != $alias->name) {
      $alias->name = $_POST["name"];
      $mod = 1;
    }

    if (checkPost("descr") && $alias->description != $_POST["descr"]) {
      $alias->description = $_POST["descr"];
      $mod = 1;
    }

    if (checkPost("type") && $_POST["type"] == "host") {
      if (checkPost("address") && $_POST["address"] != $alias->address) {
        $alias->address = $_POST["address"];
        $mod = 1;
      }
    } else if (checkPost("type") && $_POST["type"] == "network") {
      if (checkPost("address") && checkPost("address_subnet") && $_POST["address"]."/".$_POST["address_subnet"] != $alias->address) {
        $alias->address = $_POST["address"]."/".$_POST["address_subnet"];
        $mod = 1;
      }
    } else {
      $error = "Missing fields";
    }

    if ($mod) {
       if ($alias->update()) {
         $msg = "Alias updated.<br/>";
       } else {
         $msg = "Failed to update Alias.<br/>";
         $error = "Failed";
	 
       }
     } else $msg = "Nothing to update.<br/>";

    end_process();

  } else if ($action == 2) { /* add */

    unset($action);
    $body = new Template("./tpl/message.tpl");
    $body->set("pagename", "Aliases: Add");

    $alias = new Alias();
    $alias->idhost = $mono->id;
    $err = 0;

    if (checkPost("name"))
      $alias->name = $_POST["name"];
    else $err = 1;
 
    if (checkPost("descr"))
      $alias->description = $_POST["descr"];

    if (checkPost("type")) {
      if ($_POST["type"] == "host" && checkPost("address"))
        $alias->address = $_POST["address"];
      else if ($_POST["type"] == "network" && checkPost("address") && checkPost("address_subnet"))
        $alias->address = $_POST["address"]."/".$_POST["address_subnet"];
      else $err = 1;
    } else $err = 1;
 

    if (!$err) {
      if ($alias->existsDb()) $msg = "Alias already in database...<br/>";
      else {
        if ($alias->insert()) {
	  $mono->updateChanged();
          $msg = "Alias inserted.<br/>";
        } else {
 	  $msg = "Failed insert alias.<br/>";
	  $error = "Unable to insert alias into database..<br/>";
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
