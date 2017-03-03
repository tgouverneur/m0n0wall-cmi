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
 require_once("./lib/autoload.lib.php");
 require_once("./lib/positions.lib.php");
 require_once("./lib/html.lib.php");

 /* sanitize _GET and _POST */
 sanitizeArray($_GET);
 sanitizeArray($_POST);

 /* connect to the database: */
 if (!Mysql::getInstance()->connect())
 { echo "Cannot connect to mysql database<br/>\n"; die(); }

 if (isset($_POST["mid"])) $mid = $_POST["mid"]; else { ?>
  Error, no m0n0wall selected<br/><?php die(); }
 if (isset($_POST["iid"])) $iid = $_POST["iid"]; else { ?>
  Error, no interface selected<br/><?php die(); }

 if (isset($_POST["move"])) {
   $action = "move";
   $pos = array_keys($_POST["move"]);
   $pos = $pos[0];
 //  $pos = $_POST["move"];
   if (isset($_POST["rule"])) {
     $rules = $_POST["rule"];
     if (!is_array($rules)) {
       ?>Error in rules number transmitted..<br/><?php die();
     }
   } else {
    ?>Error, no rules to move...<br/><?php die();
   }
 }
 else if (isset($_POST["del"])) {
  $action = "delete";
  if (isset($_POST["rule"])) {
    $rules = $_POST["rule"];
    if (!is_array($rules)) {
       ?>Error in rules number transmitted..<br/><?php die();
    }
  } else {
    ?>Error, no rules to delete...<br/><?php die();
  } 
 } else { $action = "nothing"; }

 $main = Main::getInstance();
 $main->fetchRulesId();
 $main->fetchRulesDetails();

 if (isset($mid) && $mid) {
  $mono = new Monowall($mid);
  $mono->fetchFromId();
  $mono->fetchIfaces();
  $mono->fetchIfacesDetails();
  $mono->fetchRules();
  if (isset($iid) && $iid) {
    foreach($mono->ifaces as $iface) {
      if ($iid == $iface->id)
      {
        $if = $iface;
        break;
      }
    }
  }
 } 

?>

<p class="pgtitle">Firewall: Modify</p>

<?php
  switch ($action) {
    case "delete":
      echo count($rules)." to delete..<br/>";
      if (count($rules)) {
        foreach($rules as $rule) {
          foreach($if->rulesint as $ri) {
            if ($ri->idrule == $rule) {
              $ri->delete();
              del_ri($if->rulesint, $ri);
              echo "Rule id ".$rule." has been removed from this iface<br/>";
              /* if rule isn't used anymore, delete it from db */
	      $index = "`idrule`";
	      $table = "rules-int";
	      $where = "WHERE `idrule`='".$ri->idrule."'";
	      $m = Mysql::getInstance();
	      $data = $m->select("idrule", $table, $where);
	      if (!count($data)) {
		$ru = new Rule($ri->idrule);
		$ru->fetchFromId();
		$ru->delete();
 		echo "Rule number ".$ru->id." deleted totally from database as there were no longer monowall using it...<br/>";
	      }
            }
          }
        }
        foreach($if->rulesint as $ri) {
          if ($ri->isChanged()) {
            $ri->update();
            $mono->updateChanged();
          }
        }
        ?>Rules order updated...<br/><?php
      }
    break;
    case "move":
      echo count($rules)." to move..<br/>";
      if (count($rules) == 1) {
        foreach($if->rulesint as $ri) {
          if ($ri->idrule == $rules[0]) { $rul = $ri; break; }
        }
        if ($rul)
        {
           echo "Moving rule ".$rul->rule->id." from ".$rul->position." to ".$pos."pos<br/>";
          move_ri($if->rulesint, $rul, $pos);
        }
      }
      else {
        $i = 0;
        foreach($rules as $num) {
          foreach($if->rulesint as $ri) {
           if ($ri->idrule == $num) { $rul = $ri; break; }
          }
          if ($rul)
          {
            echo "Moving rule ".$rul->rule->id." from ".$rul->position." to ".$pos."+". $i ."pos<br/>";
            move_ri($if->rulesint, $rul, $pos + $i);
            if ($i + $pos != count($if->rulesint)) $i++;
          }
        }
      }
      foreach($if->rulesint as $ri) {
        if ($ri->isChanged()) {
          $ri->update();
          $mono->updateChanged();
        }
      }
      ?>Rules order updated...<br/><?php
    break;
  }
?>
<a href="viewfw.php?mid=<?php echo $mid?>&iid=<?php echo $iid?>">Return to firewall view</a>
<?php
 /* disconnect database */
 Mysql::getInstance()->disconnect();
?>
