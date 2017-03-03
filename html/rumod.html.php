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
<script language="JavaScript">
<!--
var portsenabled = 1;

function ext_change() {
    if ((document.iform.srcbeginport.selectedIndex == 0) && portsenabled) {
        document.iform.srcbeginport_cust.disabled = 0;
    } else {
        document.iform.srcbeginport_cust.value = "";
        document.iform.srcbeginport_cust.disabled = 1;
    }
    if ((document.iform.srcendport.selectedIndex == 0) && portsenabled) {
        document.iform.srcendport_cust.disabled = 0;
    } else {
        document.iform.srcendport_cust.value = "";
        document.iform.srcendport_cust.disabled = 1;
    }
    if ((document.iform.dstbeginport.selectedIndex == 0) && portsenabled) {
        document.iform.dstbeginport_cust.disabled = 0;
    } else {
        document.iform.dstbeginport_cust.value = "";
        document.iform.dstbeginport_cust.disabled = 1;
    }
    if ((document.iform.dstendport.selectedIndex == 0) && portsenabled) {
        document.iform.dstendport_cust.disabled = 0;
    } else {
        document.iform.dstendport_cust.value = "";
        document.iform.dstendport_cust.disabled = 1;
    }
    
    if (!portsenabled) {
        document.iform.srcbeginport.disabled = 1;
        document.iform.srcendport.disabled = 1;
        document.iform.dstbeginport.disabled = 1;
        document.iform.dstendport.disabled = 1;
    } else {
        document.iform.srcbeginport.disabled = 0;
        document.iform.srcendport.disabled = 0;
        document.iform.dstbeginport.disabled = 0;
        document.iform.dstendport.disabled = 0;
    }
}

function typesel_change() {
    switch (document.iform.srctype.selectedIndex) {
        case 1:    /* single */
            document.iform.src.disabled = 0;
            document.iform.srcmask.value = "";
            document.iform.srcmask.disabled = 1;
            break;
        case 2:    /* network */
            document.iform.src.disabled = 0;
            document.iform.srcmask.disabled = 0;
            break;
        default:
            document.iform.src.value = "";
            document.iform.src.disabled = 1;
            document.iform.srcmask.value = "";
            document.iform.srcmask.disabled = 1;
            break;
    }
    switch (document.iform.dsttype.selectedIndex) {
        case 1:    /* single */
            document.iform.dst.disabled = 0;
            document.iform.dstmask.value = "";
            document.iform.dstmask.disabled = 1;
            break;
        case 2:    /* network */
            document.iform.dst.disabled = 0;
            document.iform.dstmask.disabled = 0;
            break;
        default:
            document.iform.dst.value = "";
            document.iform.dst.disabled = 1;
            document.iform.dstmask.value = "";
            document.iform.dstmask.disabled = 1;
            break;
    }
}

function proto_change() {
    if (document.iform.proto.selectedIndex < 3) {
        portsenabled = 1;
    } else {
        portsenabled = 0;
    }
    
    if (document.iform.proto.selectedIndex == 3) {
        document.iform.icmptype.disabled = 0;
    } else {
        document.iform.icmptype.disabled = 1;
    }
    
    ext_change();
}

function src_rep_change() {
    document.iform.srcendport.selectedIndex = document.iform.srcbeginport.selectedIndex;
}
function dst_rep_change() {
    document.iform.dstendport.selectedIndex = document.iform.dstbeginport.selectedIndex;
}
//-->
</script>
<?php
 require_once("./lib/autoload.lib.php");
 require_once("./lib/html.lib.php");
 require_once("./lib/positions.lib.php");

 /* sanitize _GET and _POST */
 sanitizeArray($_GET);
 sanitizeArray($_POST);

 /* connect to the database: */
 if (!Mysql::getInstance()->connect())
 { echo "Cannot connect to mysql database<br/>\n"; die(); }

 if (isset($_GET["mid"])) $mid = $_GET["mid"];
 if (isset($_POST["mid"])) $mid = $_POST["mid"];

 if (isset($_GET["rpos"])) $rpos = $_GET["rpos"];
 if (isset($_POST["rpos"])) $rpos = $_POST["rpos"];

 if (isset($_GET["iid"])) $iid = $_GET["iid"];
 if (isset($_POST["iid"])) $iid = $_POST["iid"];

 if (isset($_GET["rid"])) $rid = $_GET["rid"];
 if (isset($_POST["rid"])) $rid = $_POST["rid"];

 $action = 0;
 if (isset($_GET["action"])) $action = $_GET["action"];
 if (isset($_POST["action"])) $action = $_POST["action"];

 $main = Main::getInstance();
 $main->fetchRulesId();
 $main->fetchRulesDetails();

 if ($mid) {
  $mono = new Monowall($mid);
  if (!$mono->fetchFromId()) { die("Error, m0n0wall not found."); }
  $mono->fetchIfaces();
  $mono->fetchIfacesDetails();
  $mono->fetchRules();
 } else {
   // cannot add rule without monowall...
   die("Error, no m0n0wall selected..");
 }

 if ($iid) {
  $ifn = NULL;
  $ifa = NULL;
  foreach ($mono->ifaces as $if) {
    if ($if->id == $iid) {
      $ifn = $if->type;
      if ($ifn == "opt") $ifn.=$if->num;
      $ifa = $if;
      break;
    }
  }
  if (!$ifa) die("Cannot find interface.");
 } else {
  die("Error, no interface selected..");
 }

 switch ($action) {
  case 0: /* Add new rule */
   $action = 3;
   $rule = new Rule();
   $ri = new RuleInt();
//   $rpos = count($ifa->rulesint);
//   $rpos->position = $ifa->rulesint[count($ifa->rulesint) - 1]->position + 1; BUGGY ?
   $rpos = $ifa->rulesint[count($ifa->rulesint) - 1]->position + 1;
  break;
  case 1: /* edit rule */
   $action = 4;
   if (isset($rpos)) {
     foreach($ifa->rulesint as $ru) {
       if ($rpos == $ru->position) {
         $rule = $ru->rule;
         $ri = $ru;
         break;
       }
     }
     if(!$rule) die("Cannot edit non-existant rule..");
   } else {
     die("No rule selected..");
   }
  break;
  case 2: /* duplicate rule */
    $action = 3;
    if (isset($rpos)) {
      foreach($ifa->rulesint as $ru) {
        if ($rpos == $ru->position) {
          $rule = $ru->rule;
          $ri = $ru;
          break;
        }
      }
      if(!$rule) die("Cannot edit non-existant rule..");
    } else {
      die("No rule selected..");
    }
  break;
  case 3: /* add new rule (POST) */
    $action = 5;
    $rule = new Rule();
    $ri = new RuleInt();
  break;
  case 4: /* edit rule (POST) */
   $action = 6;

   if (isset($rid)) {
     foreach($ifa->rulesint as $ru) {
       if ($ru->rule->id == $rid) {
         $rule = $ru->rule;
         $ri = $ru;
         break;
       }
     }
     if(!$rule) die("Cannot edit non-existant rule..");
   } else {
     die("No rule selected..");
   }
  break;
  case 7: /* toggle enable/disable */
    $action = 8;
    if (isset($rpos)) {
      foreach($ifa->rulesint as $ru) {
        if ($rpos == $ru->position) {
          $rule = $ru->rule;
          $ri = $ru;
          break;
        }
      }
      if(!$rule) die("Cannot edit non-existant rule..");
    } else {
      die("No rule selected..");
    }
  break;
  default:
    $action = -1;
  break; 
 }

?>

<p class="pgtitle">Rule: Edit</p>

<?php if ($action == 3 || $action == 4) { ?>

<form action="rumod.php" method="post" name="iform" id="iform">
<table width="100%" border="0" cellpadding="6" cellspacing="0">
 <tr> 
  <td width="22%" valign="top" class="vncellreq">Action</td>
  <td width="78%" class="vtable">
   <select name="type" class="formfld">
    <?php $types = explode(" ", "Pass Block Reject"); foreach ($types as $type): ?>
     <option value="<?php echo strtolower($type); ?>" <?php if (strtolower($type) == strtolower($rule->type)) echo "selected"; ?>>
     <?php echo htmlspecialchars($type); ?>
     </option>
    <?php endforeach; ?>
   </select> <br>
   <span class="vexpl">Choose what to do with packets that match
   the criteria specified below.<br>
   Hint: the difference between block and reject is that with reject, a packet (TCP RST or ICMP port unreachable for UDP) is returned to the sender, whereas with block the packet is dropped silently. In either case, the original packet is discarded. Reject only works when the protocol is set to either TCP or UDP (but not &quot;TCP/UDP&quot;) below.</span></td>
 </tr>
 <tr> 
  <td width="22%" valign="top" class="vncellreq">Disabled</td>
  <td width="78%" class="vtable"> 
   <input name="disabled" type="checkbox" id="disabled" value="yes" <?php if (!$ri->enabled) echo "checked"; ?>>
   <strong>Disable this rule</strong><br>
   <span class="vexpl">Set this option to disable this rule without
    removing it from the list.</span></td>
 </tr>
 <tr> 
  <td width="22%" valign="top" class="vncellreq">Interface</td>
  <td width="78%" class="vtable">
   <select name="interface" class="formfld">
   <?php $interfaces = array('wan' => 'WAN', 'lan' => 'LAN', 'pptp' => 'PPTP');
         foreach($mono->ifaces as $if) { if ($if->type == "opt") $interfaces[$if->type.$if->num] = $if->description; }
         foreach ($interfaces as $iface => $ifacename): ?>
           <option value="<?php echo $iface;?>" <?php if ($iface == $ifn) echo "selected"; ?>>
            <?php echo htmlspecialchars($ifacename);?>
           </option>
       <?php endforeach; ?>
   </select> <br>
   <span class="vexpl">Choose on which interface packets must 
   come in to match this rule.</span></td>
 </tr>
 <tr> 
  <td width="22%" valign="top" class="vncellreq">Protocol</td>
  <td width="78%" class="vtable">
   <select name="proto" class="formfld" onchange="proto_change()">
     <?php $protocols = explode(" ", "TCP UDP TCP/UDP ICMP ESP AH GRE IPv6 IGMP any"); foreach ($protocols as $proto): ?>
     <?php if ($rule->protocol == "") $rule->protocol = "any"; ?>
     <option value="<?php echo strtolower($proto);?>" <?php if (strtolower($proto) == strtolower($rule->protocol)) echo "selected"; ?>>
     <?php echo htmlspecialchars($proto);?>
     </option>
     <?php endforeach; ?>
     </select> <br>
     <span class="vexpl">Choose which IP protocol this rule should 
     match.<br>
     Hint: in most cases, you should specify <em>TCP</em> &nbsp;here.</span></td>
 </tr>
 <tr>
  <td valign="top" class="vncell">ICMP type</td>
  <td class="vtable">
   <select name="icmptype" class="formfld">
<?php
                      
                      $icmptypes = array(
                          "" => "any",
                          "unreach" => "Destination unreachable",
                        "echo" => "Echo",
                        "echorep" => "Echo reply",
                        "squench" => "Source quench",
                        "redir" => "Redirect",
                        "timex" => "Time exceeded",
                        "paramprob" => "Parameter problem",
                        "timest" => "Timestamp",
                        "timestrep" => "Timestamp reply",
                        "inforeq" => "Information request",
                        "inforep" => "Information reply",
                        "maskreq" => "Address mask request",
                        "maskrep" => "Address mask reply"
                      );
                      
                      foreach ($icmptypes as $icmptype => $descr): ?>
                      <option value="<?php echo $icmptype;?>" <?php if ($icmptype == $rule->icmptype) echo "selected"; ?>>
                      <?php echo htmlspecialchars($descr);?>
                      </option>
                      <?php endforeach; ?>

    </select>
    <br>
   <span class="vexpl">If you selected ICMP for the protocol above, you may specify an ICMP type here.</span></td>
 </tr>
 <tr> 
  <td width="22%" valign="top" class="vncellreq">Source</td>
  <td width="78%" class="vtable">
   <input name="srcnot" type="checkbox" id="srcnot" value="yes" <?php if ($rule->snot) echo "checked"; ?>>
   <strong>not</strong><br>
   Use this option to invert the sense of the match.<br>
   <br>
   <table border="0" cellspacing="0" cellpadding="0">
    <tr> 
     <td>Type:&nbsp;&nbsp;</td>
     <td></td>
     <td><select name="srctype" class="formfld" onChange="typesel_change()">
      <option value="any" <?php if (strtolower($rule->source) == "any") echo "selected"; ?>>
      any</option>
      <option value="single" <?php if (!strpos($rule->source, "/") && !specialnet($mono, $rule->source)) echo "selected"; ?>>
      Single host or alias</option>
      <option value="network" <?php if (strpos($rule->source, "/")) echo "selected"; ?>>
      Network</option>
      <option value="wanip" <?php if ($rule->source == "wanip") echo "selected"; ?>>
      WAN address</option>
      <option value="lan" <?php if ($rule->source == "lan") echo "selected"; ?>>
      LAN subnet</option>
      <option value="pptp" <?php if ($rule->source == "pptp") echo "selected"; ?>>
      PPTP clients</option>
      <?php foreach($mono->ifaces as $if) {
        if ($if->type == "opt") {
         ?><option value="<?php echo $if->type.$if->num; ?>" <?php if ($if->type.$if->num == $rule->source) echo "selected"; ?>><?php echo $if->type.$if->num; ?> subnet</option>
      <?php  }
      } ?>
      </select></td>
    </tr>
    <tr> 
     <td>Address:&nbsp;&nbsp;</td>
     <td></td>
     <?php
      $spe = specialnet($mono, $rule->source);
      if (!$spe && !strpos($rule->source, "/")) {
       $srcaddr = $rule->source;
       $srcmask = "";
      } else if ($spe) {
       $srcaddr = "";
       $srcmask = "";
      } else if (strpos($rule->source, "/")) {
       $src = explode("/", $rule->source);
       $srcaddr = $src[0];
       $srcmask = $src[1];
      }
     ?>
     <td><input name="src" type="text" class="formfldalias" id="src" size="20" value="<?php echo $srcaddr; ?>">
     /
      <select name="srcmask" class="formfld" id="srcmask">
<?php for ($i=31; $i; $i--) { ?>
<option value="<?php echo $i; ?>" <?php if ($srcmask == $i) echo "selected"; ?>><?php echo $i; ?></option>
<?php }?>
  </select>
</td>
</tr>
</table></td>
</tr>
<tr> 
<td width="22%" valign="top" class="vncellreq">Source port range 
</td>
<td width="78%" class="vtable"> 
<table border="0" cellspacing="0" cellpadding="0">
<tr> 
<td>from:&nbsp;&nbsp;</td>
<?php
 if (!specialport($rule->sport)) {
  if (strpos($rule->sport, "-")) {
   $ports = explode("-", $rule->sport);
   $sport1 = $ports[0];
   $sport2 = $ports[1];
  }
  else { $sport1 = $rule->sport; $sport2 = $rule->sport; }
 } else { $sport1 = ""; $sport2 = ""; }
?>
<td><select name="srcbeginport" class="formfld" onchange="src_rep_change();ext_change()">
                            <option value="" <?php if (!specialport($rule->sport)) echo "selected"; ?>>(other)</option>
                            <option value="any" <?php if (strtolower($rule->sport) == "") echo "selected"; ?>>any</option>
                          <?php foreach ($wkports as $port => $value) { ?>
                                <option value="<?php echo $value; ?>"<?php if ($rule->sport == $value) echo "selected"; ?>><?php echo $port; ?></option>
            <?php } ?>
</select> <input name="srcbeginport_cust" type="text" size="5" value="<?php echo $sport1; ?>"></td>
</tr>
<tr> 
<td>to:</td>
<td><select name="srcendport" class="formfld" onchange="ext_change()">
                            <option value="" <?php if (!specialport($rule->sport)) echo "selected"; ?>>(other)</option>
                            <option value="any" <?php if (strtolower($rule->sport) == "") echo "selected"; ?>>any</option>
                          <?php foreach ($wkports as $port => $value) { ?>
                                <option value="<?php echo $value; ?>"<?php if ($rule->sport == $value) echo "selected"; ?>><?php echo $port; ?></option>
            <?php } ?>
</select> <input name="srcendport_cust" type="text" size="5" value="<?php echo $sport2; ?>"></td>
</tr>
</table>

<br> 
<span class="vexpl">Specify the port or port range for 
the source of the packet for this rule. This is usually not equal to the destination port range (and is often &quot;any&quot;). <br>
Hint: you can leave the <em>'to'</em> field empty if you only 
want to filter a single port</span></td>
<tr> 
<td width="22%" valign="top" class="vncellreq">Destination</td>
<td width="78%" class="vtable"> 
<input name="dstnot" type="checkbox" id="dstnot" value="yes" <?php if ($rule->dnot) echo "checked"; ?>> 
<strong>not</strong><br>
Use this option to invert the sense of the match.<br>
<br>
<table border="0" cellspacing="0" cellpadding="0">
<tr> 
<td>Type:&nbsp;&nbsp;</td>
<td></td>
<td><select name="dsttype" class="formfld" onChange="typesel_change()">
<option value="any" <?php if (strtolower($rule->destination) == "any") echo "selected"; ?>>
      any</option>
      <option value="single" <?php if (!strpos($rule->destination, "/") && !specialnet($mono, $rule->destination)) echo "selected"; ?>>
      Single host or alias</option>
      <option value="network" <?php if (strpos($rule->destination, "/")) echo "selected"; ?>>
      Network</option>
      <option value="wanip" <?php if ($rule->destination== "wanip") echo "selected"; ?>>
      WAN address</option>
      <option value="lan" <?php if ($rule->destination == "lan") echo "selected"; ?>>
      LAN subnet</option>
      <option value="pptp" <?php if ($rule->destination == "pptp") echo "selected"; ?>>
      PPTP clients</option>
      <?php foreach($mono->ifaces as $if) {
        if ($if->type == "opt") {
         ?><option value="<?php echo $if->type.$if->num; ?>" <?php if ($if->type.$if->num == $rule->destination) echo "selected"; ?>><?php echo $if->type.$if->num; ?> subnet</option>
      <?php  }
      } ?>
</select></td>
</tr>
<tr> 
<td>Address:&nbsp;&nbsp;</td>
<td></td>
     <?php
      $spe = specialnet($mono, $rule->destination);
      if (!$spe && !strpos($rule->destination, "/")) {
       $dstaddr = $rule->destination;
       $dstmask = "";
      } else if ($spe) {
       $dstaddr = "";
       $dstmask = "";
      } else if (strpos($rule->destination, "/")) {
       $dst = explode("/", $rule->destination);
       $dstaddr = $dst[0];
       $dstmask = $dst[1];
      }
     ?>
     <td><input name="dst" type="text" class="formfldalias" id="dst" size="20" value="<?php echo $dstaddr; ?>">
     /
      <select name="dstmask" class="formfld" id="dstmask">
<?php for ($i=31; $i; $i--) { ?>
<option value="<?php echo $i; ?>" <?php if ($dstmask == $i) echo "selected"; ?>><?php echo $i; ?></option>
<?php }?>
</select></td>
</tr>
</table></td>
</tr>
<tr> 
<td width="22%" valign="top" class="vncellreq">Destination port 
range </td>
<td width="78%" class="vtable"> 
                    <table border="0" cellspacing="0" cellpadding="0">
                      <tr> 
                        <td>from:&nbsp;&nbsp;</td>
<?php
 if (!specialport($rule->dport)) {
  if (strpos($rule->dport, "-")) {
   $ports = explode("-", $rule->dport);
   $dport1 = $ports[0];
   $dport2 = $ports[1];
  }
  else { $dport1 = $rule->dport; $dport2 = $rule->dport; }
 } else { $dport1 = ""; $dport2 = ""; }
?>

                        <td><select name="dstbeginport" class="formfld" onchange="dst_rep_change();ext_change()">
                            <option value="" <?php if (!specialport($rule->dport)) echo "selected"; ?>>(other)</option>
                            <option value="any" <?php if (strtolower($rule->dport) == "") echo "selected"; ?>>any</option>
                          <?php foreach ($wkports as $port => $value) { ?>
                <option value="<?php echo $value; ?>"<?php if ($rule->dport == $value) echo "selected"; ?>><?php echo $port; ?></option>
            <?php } ?>
                                                      </select> <input name="dstbeginport_cust" type="text" size="5" value="<?php echo $dport1; ?>"></td>
                      </tr>
                      <tr> 
                        <td>to:</td>
                        <td><select name="dstendport" class="formfld" onchange="ext_change()">
            <option value="" <?php if (!specialport($rule->dport)) echo "selected"; ?>>(other)</option>
                            <option value="any" <?php if (strtolower($rule->dport) == "") echo "selected"; ?>>any</option>
                          <?php foreach ($wkports as $port => $value) { ?>
                                <option value="<?php echo $value; ?>"<?php if ($rule->dport == $value) echo "selected"; ?>><?php echo $port; ?></option>
            <?php } ?>
                                                      </select> <input name="dstendport_cust" type="text" size="5" value="<?php echo $dport2; ?>"></td>
                      </tr>

                    </table>
                    <br> <span class="vexpl">Specify the port or port range for 
                    the destination of the packet for this rule.<br>
                    Hint: you can leave the <em>'to'</em> field empty if you only 
                    want to filter a single port</span></td>
                
                <tr> 
                  <td width="22%" valign="top" class="vncellreq">Fragments</td>
                  <td width="78%" class="vtable"> 
                    <input name="frags" type="checkbox" id="frags" value="yes" <?php if ($rule->frags) echo "checked"; ?>>

                    <strong>Allow fragmented packets</strong><br>
                    <span class="vexpl">Hint: this option puts additional load 
                    on the firewall and may make it vulnerable to DoS attacks. 
                    In most cases, it is not needed. Try enabling it if you have 
                    troubles connecting to certain sites.</span></td>
                </tr>
                <tr> 
                  <td width="22%" valign="top" class="vncellreq">Log</td>
                  <td width="78%" class="vtable"> 
                    <input name="log" type="checkbox" id="log" value="yes" <?php if ($rule->log) echo "checked"; ?>>
                    <strong>Log packets that are handled by this rule</strong><br>

                    <span class="vexpl">Hint: the firewall has limited local log 
                    space. Don't turn on logging for everything. If you want to 
                    do a lot of logging, consider using a remote syslog server 
                    (see the <a href="diag_logs_settings.php">Diagnostics: System 
                    logs: Settings</a> page).</span></td>
                </tr>
                <tr> 
                  <td width="22%" valign="top" class="vncell">Description</td>
                  <td width="78%" class="vtable"> 
                    <input name="descr" type="text" class="formfld" id="descr" size="40" value="<?php echo $rule->description; ?>"> 
                    <br> <span class="vexpl">You may enter a description here 
                    for your reference (not parsed).</span></td>

                </tr>
                <tr> 
                  <td width="22%" valign="top">&nbsp;</td>
                  <td width="78%"> 
                    <input name="Submit" type="submit" class="formbtn" value="Save"> 
                                        <input name="mid" type="hidden" value="<?php echo $mono->id; ?>"> 
                                        <input name="iid" type="hidden" value="<?php echo $ifa->id; ?>"> 
                                        <input name="rid" type="hidden" value="<?php echo $rule->id; ?>"> 
<?php if ($action == 3) { ?>        <input name="rpos" type="hidden" value="<?php echo $rpos; ?>"> <?php } ?>
                                        <input name="action" type="hidden" value="<?php echo $action;?>"> 
                  </td>
                </tr>
              </table>
</form>
<script language="JavaScript">
<!--
ext_change();
typesel_change();
proto_change();
//-->

</script>

<?php } else if ($action == 5) { /* add rule (POST) */ ?>

<?php

if (isset($_POST["type"])) {
 $rule->type = $_POST["type"];
}

if (isset($_POST["disabled"])) {
    $ri->enabled = 0;
} else {
   $ri->enabled = 1;
}

if (isset($_POST["interface"])) {
  $newif = $mono->getIface(mysql_escape_string($_POST["interface"]));
  if ($newif) {
   $ri->idint = $newif->id;
   $ri->iface = $newif;
   $rule->if = mysql_escape_string($_POST["interface"]);
   
  } else { ?>
    Interface specified not found on this monowall...<br/>
  <?php 
  }
}

if (isset($_POST["proto"])) {
  $rule->protocol = $_POST["proto"];
}

if (isset($_POST["icmptype"])) {
  $rule->icmptype = mysql_escape_string($_POST["icmptype"]);
}

if (isset($_POST["srcnot"])) {
  $rule->snot = 1;
} else {
  if ($rule->snot) {
   $rule->snot = 0;
  }
}

if (isset($_POST["dstnot"])) {
  $rule->dnot = 1;
} else {
 if ($rule->dnot) {
   $rule->dnot = 0;
 }
}

if (isset($_POST["srctype"])) {

 if ($_POST["srctype"] == "any" && $rule->source != "ANY") {
   $rule->source = "ANY";
 }
 else if ($_POST["srctype"] == "single" && isset($_POST["src"])) {
  $rule->source = mysql_escape_string($_POST["src"]);
 } else if ($_POST["srctype"] == "network" && isset($_POST["src"])) {
   $rule->source = mysql_escape_string($_POST["src"])."/".mysql_escape_string($_POST["srcmask"]);
 } else {
   $rule->source = mysql_escape_string($_POST["srctype"]);
 }
}

if (isset($_POST["dsttype"])) {

 if ($_POST["dsttype"] == "any") {
   $rule->destination = "ANY";
 }
 else if ($_POST["dsttype"] == "single" && isset($_POST["dst"])) {
  $rule->destination = mysql_escape_string($_POST["dst"]);
 } else if ($_POST["dsttype"] == "network" && isset($_POST["dst"])) {
   $rule->destination = mysql_escape_string($_POST["dst"])."/".mysql_escape_string($_POST["dstmask"]);
 } else {
   $rule->destination = mysql_escape_string($_POST["dsttype"]);
 }
}

if (isset($_POST["srcbeginport"])) {

 if (mysql_escape_string($_POST["srcbeginport"]) == "any" && mysql_escape_string($_POST["srcendport"]) == "any") {
   
     $rule->sport = "";
 } else if (mysql_escape_string($_POST["srcbeginport"]) == "" && mysql_escape_string($_POST["srcendport"]) == "") {
   if (mysql_escape_string($_POST["srcbeginport_cust"]) == mysql_escape_string($_POST["srcendport_cust"])) {
       $rule->sport = mysql_escape_string($_POST["srcbeginport_cust"]);
   } else {
     $p = mysql_escape_string($_POST["srcbeginport_cust"])."-".mysql_escape_string($_POST["srcendport_cust"]);
     $rule->sport = $p;
   }
 } else if (mysql_escape_string($_POST["srcbeginport"]) ==  mysql_escape_string($_POST["srcendport"])) {
   $rule->sport = mysql_escape_string($_POST["srcbeginport"]);

 } else {
   // huh ?!
   ;
 }
}

if (isset($_POST["dstbeginport"])) {

 if (mysql_escape_string($_POST["dstbeginport"]) == "any" && mysql_escape_string($_POST["dstendport"]) == "any") {
   
   $rule->dport = "";
 } else if (mysql_escape_string($_POST["dstbeginport"]) == "" && mysql_escape_string($_POST["dstendport"]) == "") {
   if (mysql_escape_string($_POST["dstbeginport_cust"]) == mysql_escape_string($_POST["dstendport_cust"])) {
     $rule->dport = mysql_escape_string($_POST["dstbeginport_cust"]);
   } else {
     $p = mysql_escape_string($_POST["dstbeginport_cust"])."-".mysql_escape_string($_POST["dstendport_cust"]);
     $rule->dport = $p;
   }
 } else if (mysql_escape_string($_POST["dstbeginport"]) ==  mysql_escape_string($_POST["dstendport"])) {
   $rule->dport = mysql_escape_string($_POST["dstbeginport"]);
 } else {
   // huh ?!
   ;
 }
}

if (isset($_POST["descr"])) {

  $rule->description = mysql_escape_string($_POST["descr"]);
}

if (isset($_POST["frags"]) && mysql_escape_string($_POST["frags"]) == "yes") {
  $rule->frags = 1;
} else {
  $rule->frags = 0;
}

if (isset($_POST["log"]) && mysql_escape_string($_POST["log"]) == "yes") {
  $rule->log = 1;
} else {
  $rule->log = 0;
}

if ($rule->protocol != "tcp" && $rule->protocol != "udp" && $rule->protocol != "tcp/udp") {
 $rule->dport = "";
 $rule->sport = "";
}

if ($rule->insert()) {
  $mono->updateChanged();
  $rule->id = Mysql::getInstance()->getLastId();
  $ri->idrule = $rule->id;
  if ($ri->insert()) {
   if (!isset($rpos)) { $ri->position = count($newif->rulesint); }
   else $ri->position = $rpos;
   add_ri($newif->rulesint, $ri);
   
   foreach($newif->rulesint as $rui) {
     if ($rui->isChanged())
     {
       $rui->update();
        $mono->updateChanged();
     }
   }
?>
  Rule has been correctly added.<br/>
  <a href="viewfw.php?mid=<?php echo $mid;?>&iid=<?php echo $iid;?>">Return to firewall view</a>
  <?php
  } else {
  ?>
  Error while updating rule...<br/>
  <a href="viewfw.php?mid=<?php echo $mid;?>&iid=<?php echo $iid;?>">Return to firewall view</a>
  <?php
  echo Mysql::getInstance()->getError()."<br/>";
 }
} else {
?>
  Error while updating rule...<br/>
  <a href="viewfw.php?mid=<?php echo $mid;?>&iid=<?php echo $iid;?>">Return to firewall view</a>
  <?php
  echo Mysql::getInstance()->getError()."<br/>";
 }
?>

<?php } else if ($action == 6) { /* edit rule (POST) */ ?>

<?php
$mod = 0;

if (isset($_POST["type"]) && mysql_escape_string($_POST["type"]) != $rule->type) {
 $rule->type = mysql_escape_string($_POST["type"]);
 $mod = 1;
}

if (isset($_POST["disabled"])) {
  if ($ri->enabled && $_POST["disabled"] == "yes") {
    $ri->enabled = 0;
    $mod = 1;
  } else if (!$ri->enabled && $_POST["disabled"] != "yes") {
    $ri->enabled = 1;
    $mod = 1;
  }
} else {
 if (!$ri->enabled) {
   $ri->enabled = 1;
   $mod = 1;
 }
}

if (isset($_POST["proto"]) && $_POST["proto"] != $rule->protocol) {
  $rule->protocol = $_POST["proto"];
  $mod = 1;
}

if (isset($_POST["icmptype"]) && mysql_escape_string($_POST["icmptype"]) != $rule->icmptype) {
  $rule->icmptype = mysql_escape_string($_POST["icmptype"]);
  $mod = 1;
}

if (isset($_POST["srcnot"]) && mysql_escape_string($_POST["srcnot"]) == "yes" && !$rule->snot) {
  $rule->snot = 1;
  $mod = 1;
} else {
  if ($rule->snot) {
   $rule->snot = 0;
   $mod = 1;
  }
}

if (isset($_POST["dstnot"]) && mysql_escape_string($_POST["dstnot"]) == "yes" && !$rule->dnot) {
  $rule->dnot = 1;
  $mod = 1;
} else {
 if ($rule->dnot) {
   $rule->dnot = 0;
   $mod = 1;
 }
}

if (isset($_POST["srctype"]) && !empty($_POST["srctype"])) {

 if ($_POST["srctype"] == "any" && $rule->source != "ANY") {
   $rule->source = "ANY";
   $mod = 1;
 }
 else if ($_POST["srctype"] == "single") {
   if (isset($_POST["src"]) && mysql_escape_string($_POST["src"]) != $rule->source) {
     $rule->source = mysql_escape_string( $_POST["src"]);
     $mod = 1;
   }
 } else if ($_POST["srctype"] == "network" && isset($_POST["src"])) {
   
   $src = explode('/', $rule->source);
   if(mysql_escape_string($_POST["src"]) != $src[0] || mysql_escape_string($_POST["srcmask"]) != $src[1]) {
     $rule->source = mysql_escape_string($_POST["src"])."/".mysql_escape_string($_POST["srcmask"]);
     $mod = 1;
   }
 } else if (mysql_escape_string($_POST["srctype"]) != $rule->source) {
   $rule->source = mysql_escape_string($_POST["srctype"]);
   $mod = 1;
 } else {
  // error
  ;
 }
}

if (isset($_POST["dsttype"]) && !empty($_POST["dsttype"])) {

 if ($_POST["dsttype"] == "any" && $rule->destination != "ANY") {
   $rule->destination = "ANY";
   $mod = 1;
 }
 else if ($_POST["dsttype"] == "single") {
  if (isset($_POST["dst"]) && mysql_escape_string( $_POST["dst"]) != $rule->destination) {
    $rule->destination = mysql_escape_string( $_POST["dst"]);
    $mod = 1;
  }
 } else if ($_POST["dsttype"] == "network" && isset($_POST["dst"])) {
   
   $dst = explode('/', $rule->destination);
   if(mysql_escape_string($_POST["dst"]) != $dst[0] || mysql_escape_string($_POST["dstmask"]) != $dst[1]) {
     $rule->destination = mysql_escape_string($_POST["dst"])."/".mysql_escape_string($_POST["dstmask"]);
     $mod = 1;
   }
 } else if (mysql_escape_string($_POST["dsttype"]) != $rule->destination) {
   $rule->destination = mysql_escape_string($_POST["dsttype"]);
   $mod = 1;
 } else {
  // error
  ;
 }
}

if (isset($_POST["srcbeginport"])) {

 if (mysql_escape_string($_POST["srcbeginport"]) == "any" && mysql_escape_string($_POST["srcendport"]) == "any") {
   
   if ($rule->sport != "") {
     $rule->sport = "";
     $mod = 1;
   }
 } else if (mysql_escape_string($_POST["srcbeginport"]) == "" && mysql_escape_string($_POST["srcendport"]) == "") {
   if (mysql_escape_string($_POST["srcbeginport_cust"]) == mysql_escape_string($_POST["srcendport_cust"])) {
     if ($rule->sport != mysql_escape_string($_POST["srcbeginport_cust"])) {
       $rule->sport = mysql_escape_string($_POST["srcbeginport_cust"]);
       $mod = 1;
     }
   } else {
     $p = mysql_escape_string($_POST["srcbeginport_cust"])."-".mysql_escape_string($_POST["srcendport_cust"]);
     if ($rule->sport != $p) {
       $rule->sport = $p;
       $mod = 1;
     }
   }
 } else if (mysql_escape_string($_POST["srcbeginport"]) ==  mysql_escape_string($_POST["srcendport"])) {
   if ($rule->sport != mysql_escape_string($_POST["srcbeginport"])) {
     $rule->sport = mysql_escape_string($_POST["srcbeginport"]);
     $mod = 1;
   }

 } else {
   // huh ?!
   ;
 }
}

if (isset($_POST["dstbeginport"])) {

 if (mysql_escape_string($_POST["dstbeginport"]) == "any" && mysql_escape_string($_POST["dstendport"]) == "any") {
   
   if ($rule->dport != "") {
     $rule->dport = "";
     $mod = 1;
   }
 } else if (mysql_escape_string($_POST["dstbeginport"]) == "" && mysql_escape_string($_POST["dstendport"]) == "") {
   if (mysql_escape_string($_POST["dstbeginport_cust"]) == mysql_escape_string($_POST["dstendport_cust"])) {
     if ($rule->dport != mysql_escape_string($_POST["dstbeginport_cust"])) {
       $rule->dport = mysql_escape_string($_POST["dstbeginport_cust"]);
       $mod = 1;
     }
   } else {
     $p = mysql_escape_string($_POST["dstbeginport_cust"])."-".mysql_escape_string($_POST["dstendport_cust"]);
     if ($rule->dport != $p) {
       $rule->dport = $p;
       $mod = 1;
     }
   }
 } else if (mysql_escape_string($_POST["dstbeginport"]) ==  mysql_escape_string($_POST["dstendport"])) {
   if ($rule->dport != mysql_escape_string($_POST["dstbeginport"])) {
     $rule->dport = mysql_escape_string($_POST["dstbeginport"]);
     $mod = 1;
   }
 } else {
   // huh ?!
   ;
 }
}

if (isset($_POST["descr"]) && mysql_escape_string($_POST["descr"]) != $rule->description) {

  $rule->description = mysql_escape_string($_POST["descr"]);
  $mod = 1;
}

if (isset($_POST["frags"]) && mysql_escape_string($_POST["frags"]) == "yes" && !$rule->frags) {
  $rule->frags = 1;
  $mod = 1;
} else {
  if ($rule->frags) {
   $rule->frags = 0;
   $mod = 1;
 }
}

if (isset($_POST["log"]) && mysql_escape_string($_POST["log"]) == "yes" && !$rule->log) {
  $rule->log = 1;
  $mod = 1;
} else {
  if ($rule->log) {
   $rule->log = 0;
   $mod = 1;
 }
}

if ($rule->protocol != "tcp" && $rule->protocol != "udp" && $rule->protocol != "tcp/udp") {
  $rule->dport = "";
  $rule->sport = "";
}



if (isset($_POST["interface"]) && mysql_escape_string($_POST["interface"]) != $rule->if) {
  $newif = $mono->getIface(mysql_escape_string($_POST["interface"]));
  if ($newif) {
   $oldif = $mono->getIface($rule->if);
   del_ri($oldif->rulesint, $ri);
   $ri->delete();
   $ri->position = $newif->rulesint[count($newif->rulesint) - 1]->position + 1;
   $ri->idint = $newif->id;
   $ri->iface = $newif;
   $ri->insert();
   $mono->updateChanged();
   add_ri($newif->rulesint, $ri);
   $rule->if = mysql_escape_string($_POST["interface"]);
   
   foreach($oldif->rulesint as $rui)  {
     if ($rui->isChanged()) {
       $rui->update();
       $mono->updateChanged();
     }
   }
 
   foreach($newif->rulesint as $rui) {
     if ($rui->isChanged()) {
       $rui->update();
       $mono->updateChanged();
     }
   }

   $mod = 1;
  } else { ?>
    Interface specified not found on this monowall...<br/>
  <?php 
  }
}

if ($mod) { /* duplicate rule */
  $index = "`idrule`";
  $table = "rules-int";
  $where = "WHERE `idrule`='".$ri->idrule."'";
  $m = Mysql::getInstance();
  $data = $m->select("idrule", $table, $where);
  if (count($data) > 1) {

    $if = $mono->getIface($rule->if);
//    del_ri($if->rulesint, $ri);
    $ri->delete();
    $rule->insert();
    $rule->id = Mysql::getInstance()->getLastId();
    $ri->idrule = $rule->id;
    $ri->insert();
//    add_ri($if->rulesint, $ri);
/*    foreach($if->rulesint as $rui)  {
      if ($rui->isChanged()) {
        $rui->update();
        $mono->updateChanged();
      }
    }
*/
  }
}

if ($mod) {

 if ($ri->update() && $rule->update()) {
  $mono->updateChanged();
  ?>
  Rule has been correctly modified.<br/>
  <a href="viewfw.php?mid=<?php echo $mid;?>&iid=<?php echo $iid;?>">Return to firewall view</a>
  <?php
 } else { ?>
  Error while updating rule...<br/>
  <a href="viewfw.php?mid=<?php echo $mid;?>&iid=<?php echo $iid;?>">Return to firewall view</a>
  <?php
  echo Mysql::getInstance()->getError()."<br/>";
 }
} else {
?>
Nothing to modify in rule.<br/>
<a href="viewfw.php?mid=<?php echo $mid;?>&iid=<?php echo $iid;?>">Return to firewall view</a>
<?php
}

?>

<?php } else if ($action == 8) { /* ena/dis rule */ ?>
<?php
  if ($ri->enabled) $ri->enabled = 0; else $ri->enabled = 1;
  if ($ri->update()) echo "Rule updated successfully<br/>"; else echo "Error while updating rule..<br/>";
  ?>
  <a href="viewfw.php?mid=<?php echo $mono->id;?>&iid=<?php echo $ifa->id;?>">Return to FW View</a>
<?php } else { ?>

Incorrect action.

<?php } ?>
<?php
 /* disconnect database */
 Mysql::getInstance()->disconnect();
?>
