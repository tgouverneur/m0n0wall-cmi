<?php
 /**
  * Base file for HTML processing
  *
  * @author Gouverneur Thomas <tgouverneur@be.tiauto.com>
  * @copyright Copyright (c) 2007-2008, Gouverneur Thomas - TI Automotive
  * @version 1.0
  * @package html
  * @subpackage base
  * @category html
  * @filesource
  */
?>
<?php
 require_once("./inc/config.inc.php");
 require_once("./lib/autoload.lib.php");

 /* connect to the database: */
 if (!Mysql::getInstance()->connect())
 { echo "Cannot connect to mysql database<br/>\n"; die(); }

 $main = Main::getInstance();
 $main->fetchRulesId();
 $main->fetchRulesDetails();
 $main->fetchGAliases();
 $main->fetchGAliasesDetails();

  if (isset($_GET["mid"])) $mid = $_GET["mid"];
  if (isset($_POST["mid"])) $mid = $_POST["mid"];
  if ($mid) {
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
    header("Content-Type: application/octet-stream"); 
    header("Content-Disposition: attachment; filename=".$mono->hostname.".".$mono->domain."-".time().".xml");
    header("Content-Length: ".strlen($mono->config->rawconfig));
    echo $mono->config->rawconfig;

  } else { die("Cannot be self-called"); }

?>
