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
  $head->set("subtitle", "Interfaces View");
  $head->set("pagename", "Interfaces: View");
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
      $mono->fetchIfaces();
      $mono->fetchIfacesDetails();
      $ok = 1;
    } else {
      $error = "Unable to fetch m0n0wall with provided ID.";
    }
  }

  if (!$ok) {
    $body = new Template("./tpl/select.tpl");
    $body->set("pagename", "Interfaces: View");
    $body->set("action", "interfaces.php");
    $main = Main::getInstance();
    $main->fetchMonoId();
    $main->fetchMonoDetails();
    $select = array(	"title" => "m0n0wall",
			"name" => "mid",
			"desc" => "Please select the m0n0wall you want to see interfaces"
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
  $body->set("pagename", "Interfaces: View");

  $list = array();
  $cols = array("Name", "Iface", "ENA", "IP", "Subnet", "GW", "Media", "Mopts", "DHCP", "Bridge", "MTU", "SpoofMAC","Edit", "Del");
  $list[0] = $cols;

  $i = 1;

  $msg = "Listing of interfaces for ".$mono->hostname.".".$mono->domain;

  foreach ($mono->ifaces as $if) {
    $desc = (!empty($alias->description))?$alias->description:"-";
    $list[$i++] = array(array($if->type, ($if->type=="opt")?$if->num:""),
			$if->if,
			$if->enable,
			empty($if->ipaddr)?"-":$if->ipaddr,
			empty($if->subnet)?"-":$if->subnet,
			empty($if->gateway)?"-":$if->gateway,
			empty($if->media)?"-":$if->media,
			empty($if->mediaopt)?"-":$if->mediaopt,
			empty($if->dhcp)?"-":$if->dhcp,
			empty($if->bridge)?"-":$if->bridge,
			empty($if->mtu)?"-":$if->mtu,
			empty($if->spoofmac)?"-":$if->spoofmac,
                        array("href" => "ifmod.php?mid=".$mono->id."&iid=".$if->id, "label" => "X"),
                        ($if->type == "opt")?array("href" => "ifrm.php?mid=".$mono->id."&iid=".$if->id, "label" => "X"):"X"
                       );
  }

  $link = array("href" => "ifmod.php?mid=".$mono->id, "label" => "Add new interface");

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
