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
$head->set("subtitle", "Device List");
$head->set("pagename", "Device List");
$menu = new Template("./tpl/menu.tpl");
$menu->set("version", $config["version"]);
$foot = new Template("./tpl/foot.tpl");
$body = new Template("./tpl/list.tpl");
$body->set("pagename", "Device list");

/* body */
$msg = null;
$error = null;

/* connect to the database: */
if (!@Mysql::getInstance()->connect()) {
  $error = "Cannot connect to MySQL: ".Mysql::getInstance()->getError()." !!";
  end_process();
}

$main = Main::getInstance();
$main->fetchMonoId();
$main->fetchMonoDetails();

$list = array();
$cols = array("Hostname", "Domain", "DNS Servers", "Settings", "Backup User", "HW", "Edit", "Del", "Status");
$list[0] = $cols;
$i = 1;
foreach ($main->monowall as $mono) {
    
    if (!empty($mono->fversion)) { $ver = "(v ".$mono->fversion; } else $ver = "";
    $dnsserver = (!empty($mono->dnsserver))?str_replace(";", "<br/>", $mono->dnsserver):"-";
    if ($mono->idbuser != -1) { 
       $mono->fetchBuser();
       $buser = $mono->buser->login;
    } else $buser = "Assign";
    if ($mono->lastchange < $mono->changed) {
      $lastchange = array("href" => "save2mono.php?mid=".$mono->id, "label" => "Click To Save!", "img" => "img/bred.png");
    } else {
      $lastchange = array("label" => "No changes to save...", "img" => "img/bgreen.png");
    }

    $list[$i++] = array(array($mono->hostname, "<br/>", $ver),
                        $mono->domain,
			$dnsserver,
			array(
				array("href" => "viewfw.php?mid=".$mono->id, "label" => "Firewall"),
				"<br/>",
				array("href" => "viewnat.php?mid=".$mono->id, "label" => "NAT"),
				"<br/>",
				array("href" => "viewaliases.php?mid=".$mono->id, "label" => "Aliases"),
				"<br/>",
				array("href" => "users.php?mid=".$mono->id, "label" => "Users"),
				"<br/>",
				array("href" => "groups.php?mid=".$mono->id, "label" => "Groups"),
				"<br/>",
				array("href" => "interfaces.php?mid=".$mono->id, "label" => "Interfaces"),
				"<br/>",
				array("href" => "proxyarp.php?mid=".$mono->id, "label" => "ProxyARP"),
				"<br/>",
				array("href" => "sroutes.php?mid=".$mono->id, "label" => "Static Routes"),
				"<br/>",
				array("href" => "vlans.php?mid=".$mono->id, "label" => "VLANs"),
				"<br/>",
				array("href" => "syslog.php?mid=".$mono->id, "label" => "Syslog"),
				"<br/>",
				array("href" => "snmp.php?mid=".$mono->id, "label" => "SNMP"),
			),
			array("href" => "buser.php?mid=".$mono->id, "label" => $buser),
			array("href" => "hwupdate.php?mid=".$mono->id, "label" => "Update HW List"),
			array("href" => "hmod.php?mid=".$mono->id, "label" => "X"),
			array("href" => "hrm.php?mid=".$mono->id, "label" => "X"),
			$lastchange
                       );
}

$msg .= "Warning: if you remove a m0n0wall, ALL object linked to this m0n0wall will be deleted as well!";
$link = array(
	 array("href" => "hmod.php?action=2", "label" => "Add new device"),
	 " | ",
	 array("href" => "import.php", "label" => "Import new device"),
	 "<br/>",
	 array("href" => "luupdate.php", "label" => "Check last update time of all device"),
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
