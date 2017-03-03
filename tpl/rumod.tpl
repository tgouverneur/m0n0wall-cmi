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
<p class="pgtitle"><?php echo $pagename; ?></p>

<?php if (isset($error)): ?>
<table width="100%" border="0" cellpadding="6" cellspacing="0">
 <tr>
  <td class="allbr">
    <span class="redcolor"><?php echo $error; ?></span>
  </td>
 </tr>
</table>

<br/><br/>

<?php endif; ?>
<form action="rumod.php" method="post" name="iform" id="iform">

 <?php if (isset($action)): ?>
 <input type="hidden" name="action" value="<?php echo $action; ?>">
 <?php endif; ?>
 <?php if (isset($rule)): ?>
 <input type="hidden" name="rid" value="<?php echo $rule[0]->id; ?>">
 <?php endif; ?>
 <?php if (isset($mono)): ?>
 <input type="hidden" name="mid" value="<?php echo $mono[0]->id; ?>">
 <?php endif; ?>
 <?php if (isset($rpos)): ?>
 <input type="hidden" name="mid" value="<?php echo $rpos; ?>">
 <?php endif; ?>
 <?php if (isset($if)): ?>
 <input type="hidden" name="mid" value="<?php echo $if[0]->id; ?>">
 <?php endif; ?>
<table width="100%" border="0" cellpadding="6" cellspacing="0">
 <tr>
  <td width="22%" valign="top" class="vncellreq">Action</td>
  <td width="78%" class="vtable">
   <select name="type" class="formfld">
    <?php $types = explode(" ", "Pass Block Reject"); foreach ($types as $type): ?>
     <option value="<?php echo strtolower($type); ?>" <?php if (strtolower($type) == strtolower($rule[0]->type)) echo "selected"; ?>>
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
   <input name="disabled" type="checkbox" id="disabled" value="yes" <?php if (!$ri[0]->enabled) echo "checked"; ?>>
   <strong>Disable this rule</strong><br>
   <span class="vexpl">Set this option to disable this rule without
    removing it from the list.</span></td>
 </tr>
 <tr>
  <td width="22%" valign="top" class="vncellreq">Interface</td>
  <td width="78%" class="vtable">
   <select name="interface" class="formfld">
   <?php $interfaces = array('wan' => 'WAN', 'lan' => 'LAN', 'pptp' => 'PPTP');
         foreach($mono[0]->ifaces as $if) { if ($if->type == "opt") $interfaces[$if->type.$if->num] = $if->description; }
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
     <?php if ($rule[0]->protocol == "") $rule[0]->protocol = "any"; ?>
     <option value="<?php echo strtolower($proto);?>" <?php if (strtolower($proto) == strtolower($rule[0]->protocol)) echo "selected"; ?>>
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
                      <option value="<?php echo $icmptype;?>" <?php if ($icmptype == $rule[0]->icmptype) echo "selected"; ?>>
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
   <input name="srcnot" type="checkbox" id="srcnot" value="yes" <?php if ($rule[0]->snot) echo "checked"; ?>>
   <strong>not</strong><br>
   Use this option to invert the sense of the match.<br>
   <br>
   <table border="0" cellspacing="0" cellpadding="0">
    <tr>
     <td>Type:&nbsp;&nbsp;</td>
     <td></td>
     <td><select name="srctype" class="formfld" onChange="typesel_change()">
      <option value="any" <?php if (strtolower($rule[0]->source) == "any") echo "selected"; ?>>
      any</option>
      <option value="single" <?php if (!strpos($rule[0]->source, "/") && !specialnet($mono[0], $rule[0]->source)) echo "selected"; ?>>
      Single host or alias</option>
      <option value="network" <?php if (strpos($rule[0]->source, "/")) echo "selected"; ?>>
      Network</option>
      <option value="wanip" <?php if ($rule[0]->source == "wanip") echo "selected"; ?>>
      WAN address</option>
      <option value="lan" <?php if ($rule[0]->source == "lan") echo "selected"; ?>>
      LAN subnet</option>
      <option value="pptp" <?php if ($rule[0]->source == "pptp") echo "selected"; ?>>
      PPTP clients</option>
      <?php foreach($mono[0]->ifaces as $if) {
        if ($if->type == "opt") {
         ?><option value="<?php echo $if->type.$if->num; ?>" <?php if ($if->type.$if->num == $rule[0]->source) echo "selected"; ?>><?php echo $if->type.$if->num; ?> subnet</option>
      <?php  }
      } ?>
      </select></td>
    </tr>
    <tr>
     <td>Address:&nbsp;&nbsp;</td>
     <td></td>
     <?php
      $spe = specialnet($mono[0], $rule[0]->source);
      if (!$spe && !strpos($rule[0]->source, "/")) {
       $srcaddr = $rule[0]->source;
       $srcmask = "";
      } else if ($spe) {
       $srcaddr = "";
       $srcmask = "";
      } else if (strpos($rule[0]->source, "/")) {
       $src = explode("/", $rule[0]->source);
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
 if (!specialport($rule[0]->sport)) {
  if (strpos($rule[0]->sport, "-")) {
   $ports = explode("-", $rule[0]->sport);
   $sport1 = $ports[0];
   $sport2 = $ports[1];
  }
  else { $sport1 = $rule[0]->sport; $sport2 = $rule[0]->sport; }
 } else { $sport1 = ""; $sport2 = ""; }
?>
<td><select name="srcbeginport" class="formfld" onchange="src_rep_change();ext_change()">
                            <option value="" <?php if (!specialport($rule[0]->sport)) echo "selected"; ?>>(other)</option>
                            <option value="any" <?php if (strtolower($rule[0]->sport) == "") echo "selected"; ?>>any</option>
                          <?php foreach ($wkports as $port => $value) { ?>
                                <option value="<?php echo $value; ?>"<?php if ($rule[0]->sport == $value) echo "selected"; ?>><?php echo $port; ?></option>
            <?php } ?>
</select> <input name="srcbeginport_cust" type="text" size="5" value="<?php echo $sport1; ?>"></td>
</tr>
<tr>
<td>to:</td>
<td><select name="srcendport" class="formfld" onchange="ext_change()">
                            <option value="" <?php if (!specialport($rule[0]->sport)) echo "selected"; ?>>(other)</option>
                            <option value="any" <?php if (strtolower($rule[0]->sport) == "") echo "selected"; ?>>any</option>
                          <?php foreach ($wkports as $port => $value) { ?>
                                <option value="<?php echo $value; ?>"<?php if ($rule[0]->sport == $value) echo "selected"; ?>><?php echo $port; ?></option>
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
<input name="dstnot" type="checkbox" id="dstnot" value="yes" <?php if ($rule[0]->dnot) echo "checked"; ?>>
<strong>not</strong><br>
Use this option to invert the sense of the match.<br>
<br>
<table border="0" cellspacing="0" cellpadding="0">
<tr>
<td>Type:&nbsp;&nbsp;</td>
<td></td>
<td><select name="dsttype" class="formfld" onChange="typesel_change()">
<option value="any" <?php if (strtolower($rule[0]->destination) == "any") echo "selected"; ?>>
      any</option>
      <option value="single" <?php if (!strpos($rule[0]->destination, "/") && !specialnet($mono[0], $rule[0]->destination)) echo "selected"; ?>>
      Single host or alias</option>
      <option value="network" <?php if (strpos($rule[0]->destination, "/")) echo "selected"; ?>>
      Network</option>
      <option value="wanip" <?php if ($rule[0]->destination== "wanip") echo "selected"; ?>>
      WAN address</option>
      <option value="lan" <?php if ($rule[0]->destination == "lan") echo "selected"; ?>>
      LAN subnet</option>
      <option value="pptp" <?php if ($rule[0]->destination == "pptp") echo "selected"; ?>>
      PPTP clients</option>
      <?php foreach($mono[0]->ifaces as $if) {
        if ($if->type == "opt") {
         ?><option value="<?php echo $if->type.$if->num; ?>" <?php if ($if->type.$if->num == $rule[0]->destination) echo "selected"; ?>><?php echo $if->type.$if->num; ?> subnet</option>
      <?php  }
      } ?>
</select></td>
</tr>
<tr>
<td>Address:&nbsp;&nbsp;</td>
<td></td>
     <?php
      $spe = specialnet($mono[0], $rule[0]->destination);
      if (!$spe && !strpos($rule[0]->destination, "/")) {
       $dstaddr = $rule[0]->destination;
       $dstmask = "";
      } else if ($spe) {
       $dstaddr = "";
       $dstmask = "";
      } else if (strpos($rule[0]->destination, "/")) {
       $dst = explode("/", $rule[0]->destination);
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
 if (!specialport($rule[0]->dport)) {
  if (strpos($rule[0]->dport, "-")) {
   $ports = explode("-", $rule[0]->dport);
   $dport1 = $ports[0];
   $dport2 = $ports[1];
  }
  else { $dport1 = $rule[0]->dport; $dport2 = $rule[0]->dport; }
 } else { $dport1 = ""; $dport2 = ""; }
?>

                        <td><select name="dstbeginport" class="formfld" onchange="dst_rep_change();ext_change()">
                            <option value="" <?php if (!specialport($rule[0]->dport)) echo "selected"; ?>>(other)</option>
                            <option value="any" <?php if (strtolower($rule[0]->dport) == "") echo "selected"; ?>>any</option>
                          <?php foreach ($wkports as $port => $value) { ?>
                <option value="<?php echo $value; ?>"<?php if ($rule[0]->dport == $value) echo "selected"; ?>><?php echo $port; ?></option>
            <?php } ?>
                                                      </select> <input name="dstbeginport_cust" type="text" size="5" value="<?php echo $dport1; ?>"></td>
                      </tr>
                      <tr>
                        <td>to:</td>
                        <td><select name="dstendport" class="formfld" onchange="ext_change()">
            <option value="" <?php if (!specialport($rule[0]->dport)) echo "selected"; ?>>(other)</option>
                            <option value="any" <?php if (strtolower($rule[0]->dport) == "") echo "selected"; ?>>any</option>
                          <?php foreach ($wkports as $port => $value) { ?>
                                <option value="<?php echo $value; ?>"<?php if ($rule[0]->dport == $value) echo "selected"; ?>><?php echo $port; ?></option>
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
                    <input name="frags" type="checkbox" id="frags" value="yes" <?php if ($rule[0]->frags) echo "checked"; ?>>

                    <strong>Allow fragmented packets</strong><br>
                    <span class="vexpl">Hint: this option puts additional load
                    on the firewall and may make it vulnerable to DoS attacks.
                    In most cases, it is not needed. Try enabling it if you have
                    troubles connecting to certain sites.</span></td>
                </tr>
                <tr>
                  <td width="22%" valign="top" class="vncellreq">Log</td>
                  <td width="78%" class="vtable">
                    <input name="log" type="checkbox" id="log" value="yes" <?php if ($rule[0]->log) echo "checked"; ?>>
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
                    <input name="descr" type="text" class="formfld" id="descr" size="40" value="<?php echo $rule[0]->description; ?>">
                    <br> <span class="vexpl">You may enter a description here
                    for your reference (not parsed).</span></td>

                </tr>
                <tr>
                  <td width="22%" valign="top">&nbsp;</td>
                  <td width="78%">
                    <input name="Submit" type="submit" class="formbtn" value="Save">
               </td>
                </tr>
              </table>
<script language="JavaScript">
<!--
ext_change();
typesel_change();
proto_change();
//-->

</script>
 </form>

<br/><br/>

<?php if (isset($link)): ?>
<a href="<?php echo $link["href"]; ?>"><?php echo $link["label"]; ?></a><br/>
<?php endif; ?>
