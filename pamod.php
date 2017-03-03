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
  $head->set("subtitle", "ProxyARP");
  $head->set("pagename", "ProxyARP: Edit");
  $menu = new Template("./tpl/menu.tpl");
  $menu->set("version", $config["version"]);
  $foot = new Template("./tpl/foot.tpl");
  $body = new Template("./tpl/message.tpl");
  $body->set("pagename", "ProxyARP: Edit");


  /* body */
  $msg = null;
  $error = null;

  /* connect to the database: */
  if (!@Mysql::getInstance()->connect()) {
    $error = "Cannot connect to MySQL: ".Mysql::getInstance()->getError()." !!";
    end_process();
  }

  $mid = getHTTPVar("mid");
  $pid = getHTTPVar("pid");
  $action = getHTTPVar("action");
  if (!isset($action)) $action = 1;

  if (isset($mid) && $mid) {
    $mono = new Monowall($mid);
    if (!$mono->fetchFromId()) {
      $error = "Cannot find specified device in database";
      end_process();
    }
  }
  $link = array("href" => "proxyarp.php?mid=".$mono->id, "label" => "Return to ProxyArp list");

  $ok = 0;
  if ($action == 1) {
    if (isset($pid) && $pid) {
      $pa = new ProxyArp($pid);
      if ($pa->fetchFromId()) {
        $ok = 1;
	$action = 3;
      } else {
        $error = "Cannot find proxyarp to edit";
	end_process();
      }
    } else {
      $pa = new ProxyARP();
      $alias->id = 0;
      $ok = 1;
      $action = 2;
    }
    $body = new Template("./tpl/pamod.tpl");
    if (!$aid) {
      $body->set("pagename", "ProxyArp: Add");
    } else {
      $body->set("pagename", "ProxyArp: Edit");
    }
    $body->set("pa", array($pa));
    $body->set("mono", array($mono));
    if (isset($action)) {
      $body->set("action", $action);
    }
    end_process();
    
  } else if ($action == 3) { /* mod */

    unset($action);
    $body = new Template("./tpl/message.tpl");
    $body->set("pagename", "ProxyArp: Edit");
   
    $ok = 0;
    if (isset($pid) && $pid) {
      $pa = new ProxyArp($pid);
      if ($pa->fetchFromId()) {
        $ok = 1;
      } else {
        $error = "Cannot find the proxyarp to modify in the database";
	end_process();
      }
    } else {
	$error = "No proxyarp to modify specified";
	end_process();
    }
    $mod = 0;

    if (checkPost("interface") && $_POST["interface"] != $pa->if) {
      $pa->if = $_POST["interface"];
      $mod = 1;
    }

    if (checkPost("type")) {
      if ($_POST["type"] == "single" 
          && checkPost("subnet") && $pa->network != $_POST["subnet"]."/32") {

        $pa->network = $_POST["subnet"]."/32";
        $pa->from = "";
        $pa->to = "";
        $mod = 1;

      }
      else if ($_POST["type"] == "network" && checkPost("subnet") && checkPost("subnet_bits") && $pa->network != $_POST["subnet"]."/".$_POST["subnet_bits"]) {

        $pa->network = $_POST["subnet"]."/".$_POST["subnet_bits"];
        $pa->from = "";
        $pa->to = "";
        $mod = 1;

      } else if ($_POST["type"] == "range" && checkPost("range_from") && checkPost("range_to") && ($pa->from != $_POST["range_from"] || $pa->to != $_POST["range_to"])) {

        $pa->network = "";
        $pa->from = $_POST["range_from"];
        $pa->to = $_POST["range_to"];
        $mod = 1;
      }
    }

    if (checkPost("descr") && $pa->description != $_POST["descr"]) {
      $pa->description = $_POST["descr"];
      $mod = 1;
    }

    if ($mod) {
       if ($pa->update()) {
         $msg = "ProxyARP updated.<br/>";
       } else {
         $msg = "Failed to update ProxyARP.<br/>";
         $error = "Failed";
	 
       }
     } else $msg = "Nothing to update.<br/>";

    end_process();

  } else if ($action == 2) { /* add */

    unset($action);
    $body = new Template("./tpl/message.tpl");
    $body->set("pagename", "ProxyARP: Add");

    $pa = new ProxyArp();
    $pa->idhost = $mono->id;
    $err = 0;

    if (checkPost("interface")) {
      $pa->if = $_POST["interface"];
    }

    if (checkPost("type")) {
      if ($_POST["type"] == "single" && checkPost("subnet")) {

        $pa->network = $_POST["subnet"]."/32";
    
      }
      else if ($_POST["type"] == "network" && checkPost("subnet") && checkPost("subnet_bits")) {

        $pa->network = $_POST["subnet"]."/".$_POST["subnet_bits"];

      } else if ($_POST["type"] == "range" && checkPost("range_from") && checkPost("range_to")) {

        $pa->from = $_POST["range_from"];
        $pa->to = $_POST["range_to"];
      }
    }

    if (checkPost("descr")) {
      $pa->description = $_POST["descr"];
    }
     

    if (!$err) {
      if ($pa->existsDb()) $msg = "ProxyARP already in database...";
      else {
        if ($pa->insert()) {
	  $mono->updateChanged();
          $msg = "ProxyARP inserted.";
        } else {
 	  $msg = "Failed insert proxy ARP.";
	  $error = "Unable to insert proxyarp into database..";
        }
      }
    } else $error = "Error, missing or incorrect field.";

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
