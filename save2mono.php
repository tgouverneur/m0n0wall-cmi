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
  $head->set("subtitle", "Save to m0n0wall");
  $head->set("pagename", "Save to m0n0wall");
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
      $ok = 1;
    } else {
      $error = "Unable to fetch m0n0wall with provided ID.";
    }
  }

  if (!$ok) {
    $body = new Template("./tpl/select.tpl");
    $body->set("pagename", "Save to m0n0wall");
    $body->set("action", "save2mono.php");
    $main = Main::getInstance();
    $main->fetchMonoId();
    $main->fetchMonoDetails();
    $select = array(	"title" => "m0n0wall",
			"name" => "mid",
			"desc" => "Please select the m0n0wall you want to save config to"
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

  $body = new Template("./tpl/message.tpl");
  $body->set("pagename", "Save to m0n0wall");


  $main = Main::getInstance();
  $main->fetchRulesId();
  $main->fetchRulesDetails();
  $main->fetchGAliases();
  $main->fetchGAliasesDetails();

  $mono = new Monowall($mid);
  $mono->fetchFromId();
  $mono->fetchBuser();
  $mono->fetchProp();
  $mono->fetchIfaces();
  $mono->fetchIfacesDetails();
  $mono->fetchSnmp();
  $mono->fetchSyslog();
  $mono->fetchRoutes();
  $mono->fetchRoutesDetails();
  $mono->fetchGroups();
  $mono->fetchUsers();
  $mono->fetchAlias();
  $mono->fetchAliasDetails();
  $mono->fetchProxyArp();
  $mono->fetchProxyArpDetails();
  $mono->fetchVlans();
  $mono->fetchVlansDetails();
  $mono->fetchAllNat();
  $mono->fetchAllNatDetails();
  $mono->fetchRules();
  $mono->fetchUnknown();
  /* fetch of all object regarding monowall complete */
  $mono->config->dbToLocal();
  $mono->config->XML();

  $res = $mono->config->restoreConfig();
  $ok = getcurlerror($res);
  if ($ok || $res == 1) {
    $msg = "Configuration saved to ".$mono->hostname.".".$mono->domain;
  } else {
    $error = "Error while restore: ".$error;
  }

  $link = array("href" => "index.php", "label" => "Return to device list");

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
