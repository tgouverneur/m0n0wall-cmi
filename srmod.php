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
  $head->set("subtitle", "Static Routes");
  $head->set("pagename", "Static Routes: Edit");
  $menu = new Template("./tpl/menu.tpl");
  $menu->set("version", $config["version"]);
  $foot = new Template("./tpl/foot.tpl");
  $body = new Template("./tpl/message.tpl");
  $body->set("pagename", "Static Routes: Edit");


  /* body */
  $msg = null;
  $error = null;

  /* connect to the database: */
  if (!@Mysql::getInstance()->connect()) {
    $error = "Cannot connect to MySQL: ".Mysql::getInstance()->getError()." !!";
    end_process();
  }

  $mid = getHTTPVar("mid");
  $vid = getHTTPVar("rid");
  $action = getHTTPVar("action");
  if (!isset($action)) $action = 1;

  if (isset($mid) && $mid) {
    $mono = new Monowall($mid);
    if (!$mono->fetchFromId()) {
      $error = "Cannot find specified device in database";
      end_process();
    }
    $mono->fetchIfaces();
    $mono->fetchIfacesDetails();
    $mono->fetchRoutes();
    $mono->fetchRoutesDetails();
  }
  $link = array("href" => "sroutes.php?mid=".$mono->id, "label" => "Return to Static routes list");

  $ok = 0;
  if ($action == 1) {
    if (isset($rid) && $rid) {
      $ro = new StaticRoute(rid);
      if ($ro->fetchFromId()) {
        $ok = 1;
	$action = 3;
      } else {
        $error = "Cannot find static route to edit";
	end_process();
      }
    } else {
      $ro = new StaticRoute();
      $ro ->id = 0;
      $ok = 1;
      $action = 2;
    }
    $body = new Template("./tpl/srmod.tpl");
    if (!$rid) {
      $body->set("pagename", "Static Route: Add");
    } else {
      $body->set("pagename", "Static Route: Edit");
    }
    $body->set("ro", array($ro));
    $body->set("mono", array($mono));
    if (isset($action)) {
      $body->set("action", $action);
    }
    end_process();
    
  } else if ($action == 3) { /* mod */

    unset($action);
    $body = new Template("./tpl/message.tpl");
    $body->set("pagename", "Static Route: Edit");
   
    $ok = 0;
    if (isset($rid) && $rid) {
      $ro = new StaticRoute($rid);
      if ($ro->fetchFromId()) {
        $ok = 1;
      } else {
        $error = "Cannot find the static route to modify in the database";
	end_process();
      }
    } else {
	$error = "No static route to modify specified";
	end_process();
    }
    $mod = 0;

    if (checkPost("interface") && checkPost("network") && checkPost("network_subnet") && checkPost("gateway")) {
      $mod = 0;
      if ($ro->if != $_POST["interface"]) {
        $ro->if = $_POST["interface"];
        $mod = 1;
      }
      if ($ro->network != $_POST["network"]."/".$_POST["network_subnet"]) {
        $ro->network = $_POST["network"]."/".$_POST["network_subnet"];
        $mod = 1;
      }
      if ($ro->gateway != $_POST["gateway"]) {
        $ro->gateway = $_POST["gateway"];
        $mod = 1;
      }
      if (checkPost("descr") && $ro->description != $_POST["descr"]) {
       $ro->description = $_POST["descr"];
       $mod = 1;
      }
    } else {
      $error = "missing field in form..";
      end_process();
    }

    if ($mod) {
       if ($ro->update()) {
         $msg = "Static Route updated.";
       } else {
         $msg = "Failed to update Static Route.";
         $error = "Failed";
	 
       }
     } else $msg = "Nothing to update.";

    end_process();

  } else if ($action == 2) { /* add */

    unset($action);
    $body = new Template("./tpl/message.tpl");
    $body->set("pagename", "Static Route: Add");

    $ro = new StaticRoute();
    $ro->idhost = $mono->id;
    $err = 0;

    if (checkPost("interface") && checkPost("network") && checkPost("network_subnet") && checkPost("gateway")) {
     $ro->if = $_POST["interface"];
     $ro->network = $_POST["network"]."/".$_POST["network_subnet"];
     $ro->gateway = $_POST["gateway"];
     if (checkPost("descr")) {
      $ro->description = $_POST["descr"];
     }
   } else {
     $error = "Missing field...";
     end_process();
   }
    if (!$err) {
      if ($ro->existsDb()) $msg = "Static Route already in database...<br/>";
      else {
        if ($ro->insert()) {
	  $mono->updateChanged();
          $msg = "Route inserted.<br/>";
        } else {
 	  $msg = "Failed insert route.<br/>";
	  $error = "Unable to insert route into database..<br/>";
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
