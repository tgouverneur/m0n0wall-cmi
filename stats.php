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
$head->set("subtitle", "Statistics");
$head->set("pagename", "Statistics");
$menu = new Template("./tpl/menu.tpl");
$menu->set("version", $config["version"]);
$foot = new Template("./tpl/foot.tpl");
$body = new Template("./tpl/stats.tpl");
$body->set("pagename", "General: Statistics");

/* body */
$msg = null;
$error = null;

/* connect to the database: */
if (!@Mysql::getInstance()->connect()) {
  $error = "Cannot connect to MySQL: ".Mysql::getInstance()->getError()." !!";
  end_process();
}

$main = Main::getInstance();

$list = array(
		array("label" => "Number of monowall devices in the database", "value" => Mysql::getInstance()->count("hosts")),
		array("label" => "Number of interfaces in the databases", "value" => Mysql::getInstance()->count("interfaces")),
		array("label" => "Number of firewall rules in the databases", "value" => Mysql::getInstance()->count("rules")),
		array("label" => "Number of NAT rules in the databases", "value" => Mysql::getInstance()->count("nat-srv")+Mysql::getInstance()->count("nat-rules")+Mysql::getInstance()->count("nat-advout")+Mysql::getInstance()->count("nat-one2one")),
		array("label" => "Number of Aliases in the databases", "value" => Mysql::getInstance()->count("alias")),
		array("label" => "Number of Global Aliases in the databases", "value" => Mysql::getInstance()->count("galias")),
		array("label" => "Number of Users in the databases", "value" => Mysql::getInstance()->count("user")),
		array("label" => "Number of Groups in the databases", "value" => Mysql::getInstance()->count("group")),
		array("label" => "Number of ProxyARP in the databases", "value" => Mysql::getInstance()->count("proxyarp")),
		array("label" => "Number of Static Routes in the databases", "value" => Mysql::getInstance()->count("staticroutes")),
		array("label" => "Number of VLANs in the databases", "value" => Mysql::getInstance()->count("vlans"))
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
