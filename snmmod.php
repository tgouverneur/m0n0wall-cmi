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
  $head->set("subtitle", "SNMP");
  $head->set("pagename", "SNMP: Edit");
  $menu = new Template("./tpl/menu.tpl");
  $menu->set("version", $config["version"]);
  $foot = new Template("./tpl/foot.tpl");
  $body = new Template("./tpl/message.tpl");
  $body->set("pagename", "SNMP: Edit");


  /* body */
  $msg = null;
  $error = null;

  /* connect to the database: */
  if (!@Mysql::getInstance()->connect()) {
    $error = "Cannot connect to MySQL: ".Mysql::getInstance()->getError()." !!";
    end_process();
  }

  $mid = getHTTPVar("mid");
  $action = getHTTPVar("action");
  if (!isset($action)) $action = 1;

  if (isset($mid) && $mid) {
    $mono = new Monowall($mid);
    if (!$mono->fetchFromId()) {
      $error = "Cannot find specified device in database";
      end_process();
    }
    $mono->fetchSNMP();
    $snmp = $mono->snmp;
  } else {
    $error = "No device specified";
    end_process();
  }
  $link = array("href" => "snmp.php?mid=".$mono->id, "label" => "Return to SNMP settings");

  $ok = 0;
  if ($action == 1) {

    $body = new Template("./tpl/snmmod.tpl");
    $body->set("pagename", "SNMP: Edit");
    $body->set("mono", array($mono));
    $body->set("snmp", array($snmp));
    if (isset($action)) {
      $action = 3;
      $body->set("action", $action);
    }
    end_process();
    
  } else if ($action == 3) { /* mod */

    unset($action);
    $body = new Template("./tpl/message.tpl");
    $body->set("pagename", "SNMP: Edit");
   
    $ok = 0;
    $mod = 0;
     
    if (checkPost("enable") && $_POST["enable"] == yes && !$snmp->enable) {
     $snmp->enable = 1;
     $mod = 1;
    } else {
     if ($snmp->enable == 1 && !checkPost("enable")) {
      $snmp->enable = 0;
      $mod = 1;
     }
    }
   
    if (checkPost("bindlan") && $_POST["bindlan"] == yes && !$snmp->bindlan) {
     $snmp->bindlan = 1;
     $mod = 1;
    } else {
     if ($snmp->bindlan == 1 && !checkPost("bindlan")) {
      $snmp->bindlan = 0;
      $mod = 1;
     }
    }
   
    if (checkPost("syslocation") && $_POST["syslocation"] != $snmp->syslocation) {
     $snmp->syslocation = $_POST["syslocation"];
     $mod = 1;
    }
    if (checkPost("syscontact") && $_POST["syscontact"] != $snmp->syscontact) {
     $snmp->syscontact = $_POST["syscontact"];
     $mod = 1;
    }
   
    if (checkPost("rocommunity") && $_POST["rocommunity"] != $snmp->rocommunity) {
     $snmp->rocommunity = $_POST["rocommunity"];
     $mod = 1;
    }

    if ($mod) {
       if ($snmp->update()) {
         $msg = "SNMP settings updated.<br/>";
       } else {
         $msg = "Failed to update syslog SNMP.<br/>";
         $error = "Failed";
	 
       }
     } else $msg = "Nothing to update.<br/>";

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
