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
 require_once("./lib/html.lib.php");

 /* sanitize _GET and _POST */
 sanitizeArray($_GET);
 sanitizeArray($_POST);

 /* connect to the database: */
 if (!Mysql::getInstance()->connect())
 { echo "Cannot connect to mysql database<br/>\n"; die(); }

$rule = new Rule();
 if (isset($_GET["mid"])) $mid = $_GET["mid"];
 if (isset($_POST["mid"])) $mid = $_POST["mid"];
 
 $main = Main::getInstance();
 $main->fetchMonoId();
 $main->fetchMonoDetails();
 $main->fetchRulesId();
 $main->fetchRulesDetails();

 if (isset($mid) && $mid) {
  $mono = new Monowall($mid);
  $mono->fetchFromId();
  $mono->fetchIfaces();
  $mono->fetchIfacesDetails();
  $mono->fetchRules();

  if (isset($_GET["iid"])) {
    $iid = mysql_escape_string($_GET["iid"]);
    foreach($mono->ifaces as $iface) {
      if ($iface->id == $iid) {
       $if = $iface->type;
       if ($if == "opt") $if .= $iface->num;
       $ifa = $iface;
       break;
      }
    }
  }
  else {
   foreach($mono->ifaces as $iface) {
     if ($iface->type == "wan") { $ifwan = $iface; break; }
   }
   $if = "wan";
   $ifa = $ifwan;
   $iid = $ifa->id;
  }
 }
?>
<script language="JavaScript">
<!--
function fr_toggle(id) {
	var checkbox = document.getElementById('frc' + id);
	checkbox.checked = !checkbox.checked;
	fr_bgcolor(id);
}
function fr_bgcolor(id) {
	var row = document.getElementById('fr' + id);
	var checkbox = document.getElementById('frc' + id);
	var cells = row.getElementsByTagName("td");
	
	for (i = 2; i <= 6; i++) {
		cells[i].style.backgroundColor = checkbox.checked ? "#FFFFBB" : "#FFFFFF";
	}
	cells[7].style.backgroundColor = checkbox.checked ? "#FFFFBB" : "#D9DEE8";
}
function fr_insline(id, on) {
	var row = document.getElementById('fr' + id);
	var prevrow;
	if (id != 0) {
		prevrow = document.getElementById('fr' + (id-1));
	} else {
		if (false) {
			prevrow = document.getElementById('frrfc1918');
		} else {
			prevrow = document.getElementById('frheader');
		}
	}
	
	var cells = row.getElementsByTagName("td");
	var prevcells = prevrow.getElementsByTagName("td");
	
	for (i = 2; i <= 7; i++) {
		if (on) {
			prevcells[i].style.borderBottom = "3px solid #999999";
			prevcells[i].style.paddingBottom = (id != 0) ? 2 : 3;
		} else {
			prevcells[i].style.borderBottomWidth = "1px";
			prevcells[i].style.paddingBottom = (id != 0) ? 4 : 5;
		}
	}
	
	for (i = 2; i <= 7; i++) {
		if (on) {
			cells[i].style.borderTop = "2px solid #999999";
			cells[i].style.paddingTop = 2;
		} else {
			cells[i].style.borderTopWidth = 0;
			cells[i].style.paddingTop = 4;
		}
	}
}
// -->
</script>

<p class="pgtitle">Firewall: View</p>
<?php
  if (!isset($mono)) {
?>
<form action="viewfw.php" method="post">
<table width="100%" border="0" cellpadding="6" cellspacing="0">
 <tr>
  <td width="22%" valign="top" class="vncellreq">m0n0wall</td>
  <td width="78%" class="vtable"><select name="mid">
   <?php
     foreach ($main->monowall as $mono) {
      ?><option value="<?php echo $mono->id; ?>"><?php echo $mono->hostname.".".$mono->domain; ?></option>
   <?php }
   ?>
  </select><br><span class="vexpl">Select the m0n0wall which you want to manage Firewall<br/></span></td>
 </tr>
 <tr>
  <td width="22%" valign="top">&nbsp;</td>
  <td width="78%"> <input name="Submit" type="submit" class="formbtn" value="View Firewall">
  </td>
 </tr>
</table>
</form>
<?php } else { ?>
<?php echo $mono->hostname.".".$mono->domain."<br/>"; ?>
<form action="modfw.php" method="post">
<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr><td class="tabnavtbl">
  <ul id="tabnav">
<?php $i=0; foreach ($mono->ifaces as $iface) {
      $ifn = $iface->type;
      if ($ifn == "opt") $ifn .= $iface->num;
      if ($ifn == $if) { ?>
  <li class="tabact"><?php echo $ifn; ?></li>
<?php } else { ?>
  <li class="<?php if ($i == 0) echo "tabinact1"; else echo "tabinact";?>"><a href="viewfw.php?mid=<?php echo $mono->id; ?>&iid=<?php echo $iface->id; ?>"><?php echo $ifn; ?></a></li>
<?php } $i++; 
  } ?>
  </ul>
  </td></tr>
  <tr> 
   <td class="tabcont">
    <table width="100%" border="0" cellpadding="0" cellspacing="0">
    <tr id="frheader">
     <td width="3%" class="list">&nbsp;</td>
     <td width="5%" class="list">&nbsp;</td>
     <td width="10%" class="listhdrr">Proto</td>
     <td width="15%" class="listhdrr">Source</td>
     <td width="10%" class="listhdrr">Port</td>
     <td width="15%" class="listhdrr">Destination</td>
     <td width="10%" class="listhdrr">Port</td>
     <td width="22%" class="listhdr">Description</td>
     <td width="10%" class="list"></td>
    </tr>

<?php $nrules = 0; $i=0; foreach ($ifa->rulesint as $ru) {
$nrules = $ru->position;
$i = $ru->position;
?>

<tr valign="top" id="fr<?php echo $nrules;?>">
 <td class="listt"><input type="checkbox" id="frc<?php echo $nrules;?>" name="rule[]" value="<?php echo $ru->rule->id;?>" onClick="fr_bgcolor('<?php echo $nrules;?>')" style="margin: 0; padding: 0; width: 15px; height: 15px;"></td>
 <td class="listt" align="center">
<?php if ($ru->rule->type == "block")
	 $iconfn = "block";
      else if ($ru->rule->type == "reject") {
	  if ($ru->rule->protocol == "tcp" || $ru->rule->protocol == "udp")
          $iconfn = "reject";
        else
          $iconfn = "block";
      } else
        $iconfn = "pass";
      if (!$ru->enabled) {
        $textss = "<span class=\"gray\">";
        $textse = "</span>";
        $iconfn .= "_d";
      } else {
        $textss = $textse = "";
      }
?>
  <a href="rumod.php?action=7&mid=<?php echo $mono->id;?>&iid=<?php echo $ifa->id; ?>&rpos=<?php echo $ru->position; ?>"><img src="img/<?php echo $iconfn;?>.gif" width="11" height="11" border="0" title="click to toggle enabled/disabled status"></a>
<?php if ($ru->rule->log):
        $iconfn = "log_s";
        if (!$ru->enabled)
          $iconfn .= "_d";
?>
  <br><img src="img/<?php echo $iconfn;?>.gif" width="11" height="15" border="0">
  <?php endif; ?>
  </td>
  <td class="listlr" onClick="fr_toggle(<?php echo $nrules;?>)"> 
    <?php echo $textss;?><?php if (!empty($ru->rule->protocol)) echo strtoupper($ru->rule->protocol); else echo "*"; ?><?php echo $textse;?>
  </td>
  <td class="listr" onClick="fr_toggle(<?php echo $nrules;?>)">
   <?php echo $textss;?><?php if(!empty($ru->rule->source)) echo htmlspecialchars($ru->rule->source); else echo "*"; ?><?php echo $textse;?>
  </td>
  <td class="listr" onClick="fr_toggle(<?php echo $nrules;?>)">
   <?php echo $textss;?><?php if(!empty($ru->rule->sport)) echo htmlspecialchars($ru->rule->sport); else echo "*"; ?><?php echo $textse;?>
  </td>
  <td class="listr" onClick="fr_toggle(<?php echo $nrules;?>)"> 
   <?php echo $textss;?><?php if(!empty($ru->rule->destination)) echo htmlspecialchars($ru->rule->destination); else echo "*"; ?><?php echo $textse;?>
  </td>
  <td class="listr" onClick="fr_toggle(<?php echo $nrules;?>)"> 
   <?php echo $textss;?><?php if(!empty($ru->rule->dport)) echo htmlspecialchars($ru->rule->dport); else echo "*"; ?><?php echo $textse;?>
  </td>
  <td class="listbg" onClick="fr_toggle(<?php echo $nrules;?>)"> 
   <?php echo $textss;?><?php echo htmlspecialchars($ru->rule->description);?>&nbsp;<?php echo $textse;?>
  </td>
  <td valign="middle" nowrap class="list">
   <table border="0" cellspacing="0" cellpadding="1">
    <tr>
     <td><input name="move[<?php echo $ru->position;?>]" value="<?php echo $ru->position;?>" type="image" src="img/left.gif" width="17" height="17" title="move selected rules before this rule" onMouseOver="fr_insline(<?php echo $nrules;?>, true)" onMouseOut="fr_insline(<?php echo $nrules;?>, false)"></td>
     <td><a href="rumod.php?action=1&iid=<?php echo $ifa->id; ?>&rpos=<?php echo $ru->position; ?>&mid=<?php echo $mono->id; ?>"><img src="img/e.gif" title="edit rule" width="17" height="17" border="0"></a></td>
    </tr>
    <tr>
     <td align="center" valign="middle"></td>
     <td><a href="rumod.php?action=2&rid=<?php echo $ru->rule->id;?>&rpos=<?php echo $ru->position;?>&iid=<?php echo $ifa->id; ?>&mid=<?php echo $mono->id; ?>"><img src="img/plus.gif" title="add a new rule based on this one" width="17" height="17" border="0"></a></td>
    </tr>
   </table>
  </td>
 </tr>

<?php } ?>
<?php if (!count($ifa->rulesint)) { ?>
<td class="listt"></td>
<td class="listt"></td>
<td class="listlr" colspan="6" align="center" valign="middle">
<span class="gray">
No rules are currently defined for this interface.<br>
All incoming connections on this interface will be blocked until you add pass rules.<br><br>
Click the <a href="rumod.php?mid=<?php echo $mono->id?>&iid=<?php echo $ifa->id;?>"><img src="img/plus.gif" title="add new rule" border="0" width="17" height="17" align="absmiddle"></a> button to add a new rule.</span>
</td>
<?php } ?>

<tr id="fr<?php echo $nrules;?>"> 
 <td class="list"></td>
 <td class="list"></td>
 <td class="list">&nbsp;</td>
 <td class="list">&nbsp;</td>
 <td class="list">&nbsp;</td>
 <td class="list">&nbsp;</td>
 <td class="list">&nbsp;</td>
 <td class="list">&nbsp;</td>
 <td class="list">
  <table border="0" cellspacing="0" cellpadding="1">
   <tr>
    <td>
     <?php if ($nrules == 0): ?><img src="img/left_d.gif" width="17" height="17" title="move selected rules to end" border="0"><?php else: ?><input name="move" value="<?php echo $i+1;?>" type="image" src="img/left.gif" width="17" height="17" title="move selected rules to end" onMouseOver="fr_insline(<?php echo $nrules+1;?>, true)" onMouseOut="fr_insline(<?php echo $nrules+1;?>, false)"><?php endif; ?></td>
    <td></td>
   </tr>
   <tr>
    <td><?php if ($nrules == 0): ?><img src="img/x_d.gif" width="17" height="17" title="delete selected rules" border="0"><?php else: ?><input name="del[]" type="image" src="img/x.gif" width="17" height="17" value="yes" title="delete selected rules" onclick="return confirm('Do you really want to delete the selected rules?')"><?php endif; ?></td>
    <td><a href="rumod.php?mid=<?php echo $mono->id;?>&iid=<?php echo $ifa->id;?>"><img src="img/plus.gif" title="add new rule" width="17" height="17" border="0"></a></td>
   </tr>
  </table>
 </td>
</tr>
</table>
<table border="0" cellspacing="0" cellpadding="0">
 <tr> 
  <td width="16"><img src="img/pass.gif" width="11" height="11"></td>
  <td>pass</td>
  <td width="14"></td>
  <td width="16"><img src="img/block.gif" width="11" height="11"></td>
  <td>block</td>
  <td width="14"></td>
  <td width="16"><img src="img/reject.gif" width="11" height="11"></td>
  <td>reject</td>
  <td width="14"></td>
  <td width="16"><img src="img/log.gif" width="11" height="11"></td>
  <td>log</td>
 </tr>
 <tr>
  <td colspan="5" height="4"></td>
 </tr>
 <tr> 
  <td><img src="img/pass_d.gif" width="11" height="11"></td>
  <td>pass (disabled)</td>
  <td></td>
  <td><img src="img/block_d.gif" width="11" height="11"></td>
  <td>block (disabled)</td>
  <td></td>
  <td><img src="img/reject_d.gif" width="11" height="11"></td>
  <td>reject (disabled)</td>
  <td></td>
  <td width="16"><img src="img/log_d.gif" width="11" height="11"></td>
  <td>log (disabled)</td>
 </tr>
</table>
</td>
</tr>
</table><br>
<strong><span class="red">Hint:<br>
</span></strong>Rules are evaluated on a first-match basis (i.e. 
the action of the first rule to match a packet will be executed). 
This means that if you use block rules, you'll have to pay attention 
to the rule order. Everything that isn't explicitly passed is blocked 
by default.
<input type="hidden" name="iid" value="<?php echo $ifa->id;?>">
<input type="hidden" name="mid" value="<?php echo $mono->id;?>">


</form>

<?php
 }

 /* disconnect database */
 Mysql::getInstance()->disconnect();
?>
