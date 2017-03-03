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
  $head->set("subtitle", "Global Aliases Edit");
  $head->set("pagename", "Global Aliases: Edit");
  $menu = new Template("./tpl/menu.tpl");
  $menu->set("version", $config["version"]);
  $foot = new Template("./tpl/foot.tpl");

  /* body */
  $msg = null;
  $error = null;

  /* connect to the database: */
  if (!@Mysql::getInstance()->connect()) {
    $error = "Cannot connect to MySQL: ".Mysql::getInstance()->getError()." !!";
    end_process();
  }

  $gaid = getHTTPVar("gaid");
  $action = getHTTPVar("action");
  if (!isset($action)) $action = 1;

  $ok = 0;
  if ($action == 1) {
    if (isset($gaid) && $gaid) {
      $alias = new GAlias($gaid);
      if ($alias->fetchFromId()) {
        $ok = 1;
	$action = 3;
      }
    } else {
      $alias = new GAlias();
      $alias->id = 0;
      $ok = 1;
      $action = 2;
    }
    if (!$ok) {
      $body = new Template("./tpl/message.tpl");
      $body->set("pagename", "Global Aliases: Edit");
      $error = "Cannot find alias from ID providen";
      $link = array("href" => "galiases.php", "label" => "Return to Global Alias list");
      end_process();
    }
    $body = new Template("./tpl/galiasmod.tpl");
    $body->set("pagename", "Global Aliases: Edit");
    $body->set("alias", array($alias));
    if (isset($action)) {
      $body->set("action", $action);
    }
    $link = array("href" => "galiases.php", "label" => "Return to Global Alias list");
    end_process();
    
  } else if ($action == 3) { /* mod */

    unset($action);
    $body = new Template("./tpl/message.tpl");
    $body->set("pagename", "Global Aliases: Edit");
    $link = array("href" => "galiases.php", "label" => "Return to Global Alias list");
   
    $ok = 0;
    if (isset($gaid) && $gaid) {
      $alias = new GAlias($gaid);
      if ($alias->fetchFromId()) {
        $ok = 1;
      }
    }
    if (!$ok) {
      $body = new Template("./tpl/message.tpl");
      $body->set("pagename", "Global Aliases: Edit");
      $error = "Cannot find alias from ID providen";
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
         $msg = "Failed to update Global Alias.<br/>";
         $erro = "Failed";
	 
       }
     } else $msg = "Nothing to update.<br/>";

    end_process();

  } else if ($action == 2) { /* add */

    unset($action);
    $body = new Template("./tpl/message.tpl");
    $body->set("pagename", "Global Aliases: Add");
    $link = array("href" => "galiases.php", "label" => "Return to Global Alias list");

    $alias = new GAlias();
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
