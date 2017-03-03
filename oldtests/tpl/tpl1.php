<?php
require_once("./tpl/template.obj.php");

$page = new Template("./tpl/main.tpl");

$head = new Template("./tpl/head.tpl");
$head->set("title", "Title");
$head->set("subtitle", "Subtitle");
$head->set("pagename", "PAGE NAME");

$menu = new Template("./tpl/menu.tpl");

$foot = new Template("./tpl/foot.tpl");

$body = new Template("./tpl/bumod.tpl");
$body->set("link", array("href" => "busers.php", "label" => "return to backup user list"));
$body->set("error", "test erreur");

/* form */
$form["action"] = 1337;
$form["bid"] = 42;
$form["username"] = "user";
$form["description"] = "ma description";

$body->set("form", $form);

$page->set("head", $head);
$page->set("menu", $menu);
$page->set("foot", $foot);
$page->set("body", $body);

echo $page->fetch();
?>
