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
  $head->set("subtitle", "Device edit");
  $head->set("pagename", "Device: Edit");
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

  $mid = getHTTPVar("mid");
  $action = getHTTPVar("action");
  if (!isset($action)) $action = 1;

  $ok = 0;
  if ($action == 1) {
    if (isset($mid) && $mid) {
      $mono = new Monowall($mid);
      if ($mono->fetchFromId()) {
        $ok = 1;
	$action = 3;
      }
    } else {
      $mono = new Monowall();
      $mono->id = 0;
      $ok = 1;
      $action = 2;
    }
    if (!$ok) {
      $body = new Template("./tpl/message.tpl");
      $body->set("pagename", "Device: Edit");
      $error = "Cannot find device from ID providen";
      $link = array("href" => "hlist.php", "label" => "Return to device list");
      end_process();
    }
    $body = new Template("./tpl/hmod.tpl");
    $body->set("pagename", "Device: Edit");
    $body->set("mono", array($mono));
    if (isset($action)) {
      $body->set("action", $action);
    }
    $link = array("href" => "hlist.php", "label" => "Return to device list");
    end_process();
    
  } else if ($action == 3) { /* mod */

    unset($action);
    $body = new Template("./tpl/message.tpl");
    $body->set("pagename", "Device: Edit");
    $link = array("href" => "hlist.php", "label" => "Return to device list");
   
    $ok = 0;
    if (isset($mid) && $mid) {
      $mono = new Monowall($mid);
      if ($mono->fetchFromId()) {
        $ok = 1;
      }
    }

    if (!$ok) {
      $body = new Template("./tpl/message.tpl");
      $body->set("pagename", "Device: Edit");
      $error = "Cannot find device from ID providen";
      end_process();
    }
    
    $mod = 0;

    if (checkPost("hostname") && !empty($_POST["hostname"]) && $mono->hostname != $_POST["hostname"]) {
      $mono->hostname = $_POST["hostname"];
      $mod = 1;
    } else if (empty($_POST["hostname"])){
      $error = "Hostname can not be blank!";
      end_process();
    } 
 
    if (checkPost("domain") && $mono->domain != $_POST["domain"]) {
      $mono->domain = $_POST["domain"];
      $mod = 1;
    }
 
    if (checkPost("ip") && !empty($_POST["ip"]) && $mono->ip != $_POST["ip"]) { 
      $mono->ip = $_POST["ip"];
      $mod = 1;
    }

    if (checkPost("use_ip") && $_POST["use_ip"] == 1 && $mono->use_ip != $_POST["use_ip"]) {
      $mono->use_ip = 1;
      $mod = 1;
      if (empty($mono->ip)) {
        $error = "Error, you must set IP address if you want to use it!";
        end_process();
      }
    } else if ((!checkPost("use_ip") || $_POST["use_ip"] == 0) && $mono->use_ip != $_POST["use_ip"]) {
      $mono->use_ip = 0;
      $mod = 1;
    }

    if (checkPost("port") && !empty($_POST["port"]) && $_POST["port"] != $mono->port) { 
      $mono->port = $_POST["port"]; 
      $mod = 1;
    } else if ($mono->port != 443) {
      $mono->port = 443;
      $mod = 1;
    }

    if (checkPost("https") && $_POST["https"] == 1 && $mono->https != 1) { 
      $mono->https = 1; 
      $mod = 1;
    } else if ($mono->https != 0) {
      $mono->https = 0;
      $mod = 1;
    }

   $dns = "";
   if (checkPost("dns1") && !empty($_POST["dns1"])) 
     $dns .= $_POST["dns1"].";";
   if (checkPost("dns2") && !empty($_POST["dns2"])) 
     $dns .= $_POST["dns2"].";";
   if (checkPost("dns3") && !empty($_POST["dns3"])) 
     $dns .= $_POST["dns3"].";";
   if ($mono->dnsserver != $dns) {
     $mono->dnsserver = $dns;
     $mod = 1;
   }

   if (checkPost("dnsallowoverride") 
       && $_POST["dnsallowoverride"] == 1 
       && $mono->dnsoverride == 0) {
     $mono->dnsoverride = 1;
     $mod = 1;
   } else if ($mono->dnsoverride == 1 && !checkPost("dnsallowoverride")) {
     $mono->dnsoverride = 0;
     $mod = 1;
   }

   if (checkPost("username") && !empty($_POST["username"]) && $mono->username != $_POST["username"]) { 
     $mono->username = $_POST["username"];
     $mod = 1;
   }

   if (checkPost("password") && checkPost("password2") 
       && !empty($_POST["password"]) 
       && ($_POST["password"] == $_POST["password2"])
       && $mono->password != crypt($_POST["password"])) {

     $mono->password = crypt($_POST["password"]);
     $mod = 1;

   } else if ($_POST["password"] != $_POST["password2"]) { 
     $error = "Error with password and his confirmation (not the same)."; 
     end_process();
   }

   if (checkPost("timezone") && !empty($_POST["timezone"]) && $mono->timezone != $_POST["timezone"]) { 
     $mono->timezone = $_POST["timezone"];
     $mod = 1;
   }

   if (checkPost("timeupdateinterval") && !empty($_POST["timeupdateinterval"]) && $mono->ntpinterval != $_POST["timeupdateinterval"]) {
    $mono->ntpinterval = $_POST["timeupdateinterval"];
    $mod = 1;
   }

   if (checkPost("timeservers") && !empty($_POST["timeservers"]) && $mono->ntpserver != $_POST["timeservers"]) {
     $mono->ntpserver = $_POST["timeservers"];
     $mod = 1;
   }

    if ($mod) {
       if ($mono->update()) {
         $msg = "Device updated.<br/>";
       } else {
         $msg = "Failed to update Device.<br/>";
         $erro = "Failed";
	 
       }
     } else $msg = "Nothing to update.<br/>";

    end_process();

  } else if ($action == 2) { /* add */

    unset($action);
    $body = new Template("./tpl/message.tpl");
    $body->set("pagename", "Device: Add");
    $link = array("href" => "hlist.php", "label" => "Return to device list");

    $mono = new Monowall();

    $err = 0;

    if (checkPost("hostname") && !empty($_POST["hostname"]))
      $mono->hostname = $_POST["hostname"];
    else $err = 1;
 
    if (checkPost("domain") && !empty($_POST["domain"]))
      $mono->domain = $_POST["domain"];
 
    if (checkPost("ip") && !empty($_POST["ip"])) 
      $mono->ip = $_POST["ip"];

    if (checkPost("use_ip") && $_POST["use_ip"] == 1) {
      $mono->use_ip = 1;
      if (empty($mono->ip)) {
        $error = "Error, you must set IP address if you want to use it!";
        end_process();
      }
    } else if (!checkPost("use_ip") || $_POST["use_ip"] == 0) {
      $mono->use_ip = 0;
    }

    if (checkPost("port")) { 
      $mono->port = $_POST["port"]; 
    } else {
      $mono->port = 443;
    }

    if (checkPost("https") && $_POST["https"] == 1) { 
      $mono->https = 1; 
    } else {
      $mono->https = 0;
    }

   $dns = "";
   if (checkPost("dns1") && !empty($_POST["dns1"])) 
     $dns .= $_POST["dns1"].";";
   if (checkPost("dns2") && !empty($_POST["dns2"])) 
     $dns .= $_POST["dns2"].";";
   if (checkPost("dns3") && !empty($_POST["dns3"])) 
     $dns .= $_POST["dns3"].";";
   $mono->dnsserver = $dns;

   if (checkPost("dnsallowoverride") 
       && $_POST["dnsallowoverride"] == 1 
       && $mono->dnsoverride == 0) {
     $mono->dnsoverride = 1;
   } else if ($mono->dnsoverride == 1 && !checkPost("dnsallowoverride")) {
     $mono->dnsoverride = 0;
   }

   if (checkPost("username") && !empty($_POST["username"])) 
     $mono->username = $_POST["username"];

   if (checkPost("password") && checkPost("password2") 
       && !empty($_POST["password"]) 
       && ($_POST["password"] == $_POST["password2"])) {

     $mono->password = crypt($_POST["password"]);

   } else if ($_POST["password"] != $_POST["password2"]) { 
     $error = "Error with password and his confirmation (not the same)."; 
     end_process();
   }

   if (checkPost("timezone") && !empty($_POST["timezone"])) 
     $mono->timezone = $_POST["timezone"];

   if (checkPost("timeupdateinterval") && !empty($_POST["timeupdateinterval"])) {
    $mono->ntpinterval = $_POST["timeupdateinterval"];
   }

   if (checkPost("timeservers") && !empty($_POST["timeservers"])) {
     $mono->ntpserver = $_POST["timeservers"];
   }
    if (!$err) {
      if ($mono->existsDb()) 
        $msg = "Device already in database...";
      else {
        if ($mono->insert()) {
          $msg = "Device inserted.";

          $mono->id = Mysql::getInstance()->getLastId();
          $syslog = new Syslog();
          $syslog->idhost = $mono->id;
          $snmp = new Snmp();
          $snmp->idhost = $mono->id;
          $syslog->insert();
          $mono->idsyslog = Mysql::getInstance()->getLastId();
          $snmp->insert();
          $mono->idsnmp = Mysql::getInstance()->getLastId();
          $lan = new Iface();
          $lan->type = "lan";
          $lan->idhost = $mono->id;

          $wan = new Iface();
          $wan->type = "wan";
          $wan->idhost = $mono->id;

          $lan->insert();
          $wan->insert();


          $mono->update();
          $mono->updateChanged(); 

        } else {
 	  $msg = "Failed insert device.";
	  $error = "Unable to insert device into database..";
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
