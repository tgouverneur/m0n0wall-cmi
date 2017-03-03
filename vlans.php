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
  $head->set("subtitle", "VLANs View");
  $head->set("pagename", "VLANs: View");
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
      $mono->fetchVlans();
      $mono->fetchVlansDetails();
      $ok = 1;
    } else {
      $error = "Unable to fetch m0n0wall with provided ID.";
    }
  }

  if (!$ok) {
    $body = new Template("./tpl/select.tpl");
    $body->set("pagename", "VLANs: View");
    $body->set("action", "vlans.php");
    $main = Main::getInstance();
    $main->fetchMonoId();
    $main->fetchMonoDetails();
    $select = array(	"title" => "m0n0wall",
			"name" => "mid",
			"desc" => "Please select the m0n0wall you want to see VLANs"
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
  $body->set("pagename", "VLANs: View");

  $list = array();
  $cols = array("Tag", "If", "Description", "Edit", "Del");
  $list[0] = $cols;

  $i = 1;

  $msg = "Listing of VLANs for ".$mono->hostname.".".$mono->domain;

  foreach ($mono->vlans as $vl) {
    $desc = (!empty($vl->description))?$vl->description:"-";
    $from = (!empty($vl->from))?$vl->from:"-";
    $to = (!empty($vl->to))?$vl->to:"-";
    $list[$i++] = array($vl->tag,
			$vl->if,
			empty($vl->description)?"-":$vl->description,
                        array("href" => "vlmod.php?mid=".$mono->id."&vid=".$vl->id, "label" => "X"),
                        array("href" => "vlrm.php?mid=".$mono->id."&vid=".$vl->id, "label" => "X")
                       );
  }

  $link = array("href" => "vlmod.php?mid=".$mono->id, "label" => "Add new VLAN");

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
