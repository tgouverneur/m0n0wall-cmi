<?php
 /**
  * Base file for HTML processing
  *
  * @author Gouverneur Thomas <tgouverneur@be.tiauto.com>
  * @copyright Copyright (c) 2007-2008, Gouverneur Thomas - TI Automotive
  * @version 1.0
  * @package html
  * @subpackage html
  * @category html
  * @filesource
  */
?>
<?php
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
$head->set("subtitle", "Backup users list");
$head->set("pagename", "Backup users: List");
$menu = new Template("./tpl/menu.tpl");
$menu->set("version", $config["version"]);
$foot = new Template("./tpl/foot.tpl");
$body = new Template("./tpl/list.tpl");
$body->set("pagename", "Backup users: List");

/* body */
$msg = null;
$error = null;


/* connect to the database: */
if (!@Mysql::getInstance()->connect()) {
  $error = "Cannot connect to MySQL: ".Mysql::getInstance()->getError()." !!";
  end_process();
}

$main = Main::getInstance();
$main->fetchBusers();

$list = array();
$cols = array("Login", "Description", "Edit", "Del");
$list[0] = $cols;
$i = 1; 

foreach ($main->busers as $buser) {
    $desc = (!empty($buser->description))?$buser->description:"-";
    $list[$i++] = array($buser->login, 
		        $desc, 
			array("href" => "bumod.php?bid=".$buser->id, "label" => "X"),
			array("href" => "burm.php?bid=".$buser->id, "label" => "X")
		       );
}

$msg .= "Warning: if you remove a backup user that is still in use, all m0n0wall who are using this user will no longer work!";
$link = array("href" => "bumod.php", "label" => "Add new backup user");

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
