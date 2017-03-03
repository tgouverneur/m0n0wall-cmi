<?php
require_once("./tpl/template.obj.php");

$page = new Template("./tpl/main.tpl");

$head = new Template("./tpl/head.tpl");
$head->set("title", "Title");
$head->set("subtitle", "Subtitle");
$head->set("pagename", "PAGE NAME");
$menu = new Template("./tpl/menu.tpl");
$foot = new Template("./tpl/foot.tpl");

$body = new Template("./tpl/message.tpl");
$body->set("error", "test erreur");
$body->set("link", array("href" => "mapage.php", "label" => "return to mapage"));

$msg = "Doing something...";

// blabla

$msg .= "done<br/>";
$msg .= "Doing some other thing...";

$msg .= "done<br/>";

$body->set("message", $msg); 


$page->set("head", $head);
$page->set("menu", $menu);
$page->set("foot", $foot);
$page->set("body", $body);

echo $page->fetch();


?>
