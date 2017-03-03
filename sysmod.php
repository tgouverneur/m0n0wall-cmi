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
  $head->set("subtitle", "Syslog");
  $head->set("pagename", "Syslog: Edit");
  $menu = new Template("./tpl/menu.tpl");
  $menu->set("version", $config["version"]);
  $foot = new Template("./tpl/foot.tpl");
  $body = new Template("./tpl/message.tpl");
  $body->set("pagename", "Syslog: Edit");


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
    $mono->fetchSyslog();
    $syslog = $mono->syslog;
  } else {
    $error = "No device specified";
    end_process();
  }
  $link = array("href" => "syslog.php?mid=".$mono->id, "label" => "Return to syslog settings");

  $ok = 0;
  if ($action == 1) {

    $body = new Template("./tpl/sysmod.tpl");
    $body->set("pagename", "Syslog: Edit");
    $body->set("mono", array($mono));
    $body->set("syslog", array($syslog));
    if (isset($action)) {
      $action = 3;
      $body->set("action", $action);
    }
    end_process();
    
  } else if ($action == 3) { /* mod */

    unset($action);
    $body = new Template("./tpl/message.tpl");
    $body->set("pagename", "Syslog: Edit");
   
    $ok = 0;
    $mod = 0;

     if (checkPost("reverse") && $_POST["reverse"] == yes && !$syslog->reverse) {
       $syslog->reverse = 1;
       $mod = 1;
      } else {
       if ($syslog->reverse == 1 && !checkPost("reverse")) {
        $syslog->reverse = 0;
        $mod = 1;
       }
      }
     
      if (checkPost("nentries") && $_POST["nentries"] != $syslog->nentries) {
       $syslog->nentries = $_POST["nentries"];
       $mod = 1;
      }
     
       if (checkPost("rawfilter") && $_POST["rawfilter"] == "yes" && !$syslog->rawfilter) {
       $syslog->rawfilter = 1;
       $mod = 1;
      } else {
       if ($syslog->rawfilter == 1 && !checkPost("rawfilter")) {
        $syslog->rawfilter = 0;
        $mod = 1;
       }
      }
      if (checkPost("resolve") && $_POST["resolve"] == "yes" && !$syslog->resolve) {
       $syslog->resolve= 1;
       $mod = 1;
      } else {
       if ($syslog->resolve == 1 && !checkPost("resolve")) {
        $syslog->resolve = 0;
        $mod = 1;
       }
      }
     
      if (checkPost("enable") && $_POST["enable"] == "yes" && !$syslog->enable) {
       $syslog->enable = 1;
       $mod = 1;
      } else {
       if ($syslog->enable == 1 && !checkPost("enable")) {
        $syslog->enable = 0;
        $mod = 1;
       }
      }
     
      if (checkPost("remoteserver") && $_POST["remoteserver"] != $syslog->remoteserver) {
       $syslog->remoteserver = $_POST["remoteserver"];
       $mod = 1;
      }
      if (checkPost("system") && $_POST["system"] == "yes" && !$syslog->system) {
       $syslog->system = 1;
       $mod = 1;
      } else {
       if ($syslog->system == 1 && !checkPost("system")) {
        $syslog->system = 0;
        $mod = 1;
       }
      }
     
      if (checkPost("filter") && $_POST["filter"] == "yes" && !$syslog->filter) {
       $syslog->filter = 1;
       $mod = 1;
      } else {
       if ($syslog->filter == 1 && !checkPost("filter")) {
        $syslog->filter = 0;
        $mod = 1;
       }
      }
      if (checkPost("dhcp") && $_POST["dhcp"] == "yes" && !$syslog->dhcp) {
       $syslog->dhcp = 1;
       $mod = 1;
      } else {
       if ($syslog->dhcp == 1 && !checkPost("dhcp")) {
        $syslog->dhcp = 0;
        $mod = 1;
       }
      }
     
      if (checkPost("portalauth") && $_POST["portalauth"] == "yes" && !$syslog->portalauth) {
       $syslog->portalauth= 1;
       $mod = 1;
      } else {
       if ($syslog->portalauth== 1 && !checkPost("portalauth")) {
        $syslog->portalauth= 0;
        $mod = 1;
       }
      }
      if (checkPost("vpn") && $_POST["vpn"] == "yes" && !$syslog->vpn) {
       $syslog->vpn= 1;
       $mod = 1;
      } else {
       if ($syslog->vpn == 1 && !checkPost("vpn")) {
        $syslog->vpn = 0;
        $mod = 1;
       }
      }
     
    if ($mod) {
       if ($syslog->update()) {
         $msg = "Syslog settings updated.<br/>";
       } else {
         $msg = "Failed to update syslog settings.<br/>";
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
