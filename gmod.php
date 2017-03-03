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
  $head->set("subtitle", "Groups");
  $head->set("pagename", "Groups: Edit");
  $menu = new Template("./tpl/menu.tpl");
  $menu->set("version", $config["version"]);
  $foot = new Template("./tpl/foot.tpl");
  $body = new Template("./tpl/message.tpl");
  $body->set("pagename", "Groups: Edit");


  /* body */
  $msg = null;
  $error = null;

  /* connect to the database: */
  if (!@Mysql::getInstance()->connect()) {
    $error = "Cannot connect to MySQL: ".Mysql::getInstance()->getError()." !!";
    end_process();
  }

  $mid = getHTTPVar("mid");
  $gid = getHTTPVar("gid");
  $action = getHTTPVar("action");
  if (!isset($action)) $action = 1;

  if (isset($mid) && $mid) {
    $mono = new Monowall($mid);
    if (!$mono->fetchFromId()) {
      $error = "Cannot find specified device in database";
      end_process();
    }
  } else {
    $error = "No device specified.";
    end_process();
  }
  $link = array("href" => "groups.php?mid=".$mono->id, "label" => "Return to Group list");

  $ok = 0;
  if ($action == 1) {
    if (isset($gid) && $gid) {
      $group = new Group($gid);
      if ($group->fetchFromId()) {
        $ok = 1;
	$action = 3;
      } else {
        $error = "Cannot find group to edit";
	end_process();
      }
    } else {
      $group = new Group();
      $group->id = 0;
      $ok = 1;
      $action = 2;
    }
    $body = new Template("./tpl/gmod.tpl");
    if (!$gid) {
      $body->set("pagename", "Group: Add");
    } else {
      $body->set("pagename", "Group: Edit");
    }
    $body->set("group", array($group));
    $body->set("lpages", Group::$lpages);
    $body->set("mono", array($mono));
    if (isset($action)) {
      $body->set("action", $action);
    }
    end_process();
    
  } else if ($action == 3) { /* mod */

    unset($action);
    $body = new Template("./tpl/message.tpl");
    $body->set("pagename", "Group: Edit");
   
    $ok = 0;
    if (isset($gid) && $gid) {
      $group = new Group($gid);
      if ($group->fetchFromId()) {
        $ok = 1;
      } else {
        $error = "Cannot find the group to modify in the database";
	end_process();
      }
    } else {
	$error = "No group to modify specified";
	end_process();
    }
    $mod = 0;

    if (checkPost("groupname") && $_POST["groupname"] != $group->name) {
      $group->name = $_POST["groupname"];
      $mod = 1;
    }

    if (checkPost("description") && $group->description != $_POST["description"]) {
      $group->description = $_POST["description"];
      $mod = 1;
    }

    $pages = explode (';',$group->pages);
    foreach (Group::$lpages as $phpfile => $title) {
      $fname = str_replace('.php','',$phpfile);
      if (checkPost($fname)) {
        if ($_POST[$fname] == "yes") {
          if (!in_array($phpfile, $pages)) {
           $group->pages .= $phpfile.";";
           $pages[] = $phpfile;
           $mod = 1;
          }
        } else {
          if (in_array($phpfile, $pages)) {
            $group->pages = "";
            foreach ($pages as $k => $p) {
              if ($p != $phpfile && !empty($p)) $group->pages .= $p.";";
              else unset($pages[$k]);
            }
            $mod = 1;
          }
        }
      } else {
          if (in_array($phpfile, $pages)) {
            $group->pages = "";
            foreach ($pages as $k => $p) {
              if ($p != $phpfile && !empty($p)) $group->pages .= $p.";";
              else unset($pages[$k]);
            }
            $mod = 1;
          }
      }
    }

    if ($mod) {
       if ($group->update()) {
         $msg = "Group updated.<br/>";
 	 $mono->updateChanged();
       } else {
         $msg = "Failed to update Group.<br/>";
         $error = "Failed";
	 
       }
     } else $msg = "Nothing to update.<br/>";

    end_process();

  } else if ($action == 2) { /* add */

    unset($action);
    $body = new Template("./tpl/message.tpl");
    $body->set("pagename", "Group: Add");

    $group = new Group();
    $group->idhost = $mono->id;
    $err = 0;

    if (checkPost("groupname"))
      $group->name = $_POST["groupname"];
    else $err = 1;
 
    if (checkPost("description"))
      $group->description = $_POST["description"];

    foreach (Group::$lpages as $phpfile => $title) {
      $fname = str_replace('.php','',$phpfile);
      if (checkPost($fname) && $_POST[$fname] == "yes") {
        $group->pages .= $phpfile.";";
      }
    }

    if (!$err) {
      if ($group->existsDb()) $msg = "Group already in database...<br/>";
      else {
        if ($group->insert()) {
	  $mono->updateChanged();
          $msg = "Group inserted.<br/>";
        } else {
 	  $msg = "Failed insert group.<br/>";
	  $error = "Unable to insert group into database..<br/>";
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
