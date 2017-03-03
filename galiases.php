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
  $head->set("subtitle", "Global Aliases");
  $head->set("pagename", "Global Aliases");
  $menu = new Template("./tpl/menu.tpl");
  $menu->set("version", $config["version"]);
  $foot = new Template("./tpl/foot.tpl");
  $body = new Template("./tpl/list.tpl");
  $body->set("pagename", "Global Aliases: View");

  /* body */
  $msg = null;
  $error = null;

  /* connect to the database: */
  if (!@Mysql::getInstance()->connect()) {
    $error = "Cannot connect to MySQL: ".Mysql::getInstance()->getError()." !!";
    end_process();
  }

  $main = Main::getInstance();
  if (!$main->fetchGAliases()) {
    $error = "Unable to fetch Aliases";
    end_process();
  }
  if (!$main->fetchGAliasesDetails()) {
    $error = "Unable to fetch Aliases details";
    end_process();
  }

  $list = array();
  $cols = array("Name", "Address", "Description", "Edit", "Del");
  $list[0] = $cols;

  $i = 1;

  $msg = "Listing of global aliases";


  foreach ($main->galiases as $ga) {
    $desc = (!empty($ga->description))?$ga->description:"-";
    $list[$i++] = array($ga->name,
			$ga->address,
                        $desc,
                        array("href" => "galiasmod.php?gaid=".$ga->id, "label" => "X"),
                        array("href" => "galiasrm.php?gaid=".$ga->id, "label" => "X")
                       );
  }

  $link = array("href" => "galiasmod.php", "label" => "Add new global alias");

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
