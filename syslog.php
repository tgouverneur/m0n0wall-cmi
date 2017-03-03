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
  $head->set("subtitle", "Syslog View");
  $head->set("pagename", "Syslog: View");
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

  $ok = 0;
  if (getHTTPVar("mid")) {
    $mono = new Monowall(getHTTPVar("mid"));
    if ($mono->fetchFromId() ) {
      $mono->fetchSyslog();
      $ok = 1;
    } else {
      $error = "Unable to fetch m0n0wall with provided ID.";
    }
  }

  if (!$ok) {
    $body = new Template("./tpl/select.tpl");
    $body->set("pagename", "Syslog: View");
    $body->set("action", "syslog.php");
    $main = Main::getInstance();
    $main->fetchMonoId();
    $main->fetchMonoDetails();
    $select = array(	"title" => "m0n0wall",
			"name" => "mid",
			"desc" => "Please select the m0n0wall you want to see syslog settings"
		);
    $list = array();
    foreach ($main->monowall as $mono) {
      $item = array("value" => $mono->id,
		    "label" => $mono->hostname.".".$mono->domain
		   );
      array_push($list, $item);
    }
    $body->set("list", $list);
    $body->set("select", $select);
    end_process();
  }

  $body = new Template("./tpl/list.tpl");
  $body->set("pagename", "Syslog: View");

  $list = array();
  $cols = array("Remote?", "Server IP", "Num entries", "Reverse order?", "What?", "Log packet blocked?", "Show raw?", "Resolve IP?", "Edit");
  $list[0] = $cols;

  $i = 1;

  $msg = "Listing of syslog settings for ".$mono->hostname.".".$mono->domain;
  
  $list[$i++] = array((empty($mono->syslog->remoteserver))?"false":"true",
                      (!empty($mono->syslog->remoteserver))?$mono->syslog->remoteserver:"-",
                      $mono->syslog->nentries,
		      $mono->syslog->reverse,
		      array(
			($mono->syslog->dhcp)?"dhcp<br/>":"&nbsp;",
                        ($mono->syslog->system)?"system<br/>":"&nbsp;",
                        ($mono->syslog->portalauth)?"portal<br/>":"&nbsp;",
                        ($mono->syslog->vpn)?"vpn<br/>":"&nbsp;",
			),
		      ($mono->syslog->nologdefaultblock)?"0":"1",
		      "not impl",
		      $mono->syslog->resolve,
                      array("href" => "sysmod.php?mid=".$mono->id, "label" => "X")
                     );


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
