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
  $head->set("subtitle", "VLANs");
  $head->set("pagename", "VLANs: Edit");
  $menu = new Template("./tpl/menu.tpl");
  $menu->set("version", $config["version"]);
  $foot = new Template("./tpl/foot.tpl");
  $body = new Template("./tpl/message.tpl");
  $body->set("pagename", "VLANs: Edit");


  /* body */
  $msg = null;
  $error = null;

  /* connect to the database: */
  if (!@Mysql::getInstance()->connect()) {
    $error = "Cannot connect to MySQL: ".Mysql::getInstance()->getError()." !!";
    end_process();
  }

  $mid = getHTTPVar("mid");
  $vid = getHTTPVar("vid");
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
    $mono->fetchVlans();
    $mono->fetchVlansDetails();
    $mono->fetchHw();
  }
  $link = array("href" => "vlans.php?mid=".$mono->id, "label" => "Return to VLAN list");

  $ok = 0;
  if ($action == 1) {
    if (isset($vid) && $vid) {
      $vlan = new Vlan($vid);
      if ($vlan->fetchFromId()) {
        $ok = 1;
	$action = 3;
      } else {
        $error = "Cannot find vlan to edit";
	end_process();
      }
    } else {
      $vlan = new VLAN();
      $vlan->id = 0;
      $ok = 1;
      $action = 2;
    }
    $body = new Template("./tpl/vlmod.tpl");
    if (!$vid) {
      $body->set("pagename", "VLANs: Add");
    } else {
      $body->set("pagename", "VLANs: Edit");
    }
    $body->set("vlan", array($vlan));
    $body->set("mono", array($mono));
    if (isset($action)) {
      $body->set("action", $action);
    }
    end_process();
    
  } else if ($action == 3) { /* mod */

    unset($action);
    $body = new Template("./tpl/message.tpl");
    $body->set("pagename", "VLANs: Edit");
   
    $ok = 0;
    if (isset($vid) && $vid) {
      $vlan = new VLAN($vid);
      if ($vlan->fetchFromId()) {
        $ok = 1;
      } else {
        $error = "Cannot find the vlan to modify in the database";
	end_process();
      }
    } else {
	$error = "No vlan to modify specified";
	end_process();
    }
    $mod = 0;

    if (checkPost("if")) {
      foreach ($mono->hwInt as $if) {
       if ($if->id == $_POST["if"]) {
        if ($if->name != $vlan->if) {
         $vlan->if = $if->name;
         $mod = 1;
         break;
        }
       }
      }
    }
    if (checkPost("tag") && $_POST["tag"] != $vlan->tag) {
      $vlan->tag = $_POST["tag"];
      $mod = 1;
    }
    if (checkPost("descr") && $_POST["descr"] != $vlan->description) {
      $vlan->description = $_POST["descr"];
      $mod = 1;
    }

    if ($mod) {
       if ($vlan->update()) {
         $msg = "VLAN updated.<br/>";
       } else {
         $msg = "Failed to update VLAN.<br/>";
         $error = "Failed";
	 
       }
     } else $msg = "Nothing to update.<br/>";

    end_process();

  } else if ($action == 2) { /* add */

    unset($action);
    $body = new Template("./tpl/message.tpl");
    $body->set("pagename", "VLANs: Add");

    $vlan = new VLAN();
    $vlan->idhost = $mono->id;
    $err = 0;

    if (checkPost("if")) {

      $vlan->if = "";
      foreach ($mono->hwInt as $if) {
        if ($if->id == $_POST["if"]) {
          $vlan->if = $if->name;
          break;
        }
      }
    }
    if (checkPost("tag")) {
    
      $vlan->tag = $_POST["tag"];
    }
    if (checkPost("descr"))
      $vlan->description = $_POST["descr"];

    if (!$err) {
      if ($vlan->existsDb()) $msg = "VLAN already in database...<br/>";
      else {
        if ($vlan->insert()) {
	  $mono->updateChanged();
          $msg = "VLAN inserted.<br/>";
        } else {
 	  $msg = "Failed insert vlan.<br/>";
	  $error = "Unable to insert vlan into database..<br/>";
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
