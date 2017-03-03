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
 require_once("./lib/positions.lib.php");

 /* sanitize _GET and _POST */
 sanitizeArray($_GET);
 sanitizeArray($_POST);

 /* connect to the database: */
 if (!Mysql::getInstance()->connect())
 { echo "Cannot connect to mysql database<br/>\n"; die(); }

 if (isset($_GET["mid"])) $mid = $_GET["mid"];
 if (isset($_POST["mid"])) $mid = $_POST["mid"];
 if (isset($_GET["type"])) $type = $_GET["type"];
 if (isset($_POST["type"])) $type = $_POST["type"];
 if (isset($_GET["nid"])) $nid = $_GET["nid"];
 if (isset($_POST["nid"])) $nid = $_POST["nid"];
 if (!isset($type)) $type = 1;

 if (isset($_GET["action"])) $action = $_GET["action"];
 if (isset($_POST["action"])) $action = $_POST["action"]; 
 if (!isset($action)) $action = 1;

 $main = Main::getInstance();
 $main->fetchMonoId();
 $main->fetchMonoDetails();

 if (isset($mid) && $mid) {
  $mono = new Monowall($mid);
  $mono->fetchFromId();
  $mono->fetchIfaces();
  $mono->fetchIfacesDetails();
  $mono->fetchAllNat();
  $mono->fetchAllNatDetails();
 } 

?>
<p class="pgtitle">NAT: Edit</p>
<?php
if ($action == 1) {

  if (isset($type)) {
    switch($type) {
      case "1": // inbound
      if (isset($nid) && $nid) {
       $nat = new RuleNat($nid);
       $nat->fetchFromId();
      } else $nat = new RuleNat();
?>
  <script language="JavaScript">
<!--
function ext_change() {
	if (document.iform.beginport.selectedIndex == 0) {
		document.iform.beginport_cust.disabled = 0;
	} else {
		document.iform.beginport_cust.value = "";
		document.iform.beginport_cust.disabled = 1;
	}
	if (document.iform.endport.selectedIndex == 0) {
		document.iform.endport_cust.disabled = 0;
	} else {
		document.iform.endport_cust.value = "";
		document.iform.endport_cust.disabled = 1;
	}
	if (document.iform.localbeginport.selectedIndex == 0) {
		document.iform.localbeginport_cust.disabled = 0;
	} else {
		document.iform.localbeginport_cust.value = "";
		document.iform.localbeginport_cust.disabled = 1;
	}
}
function ext_rep_change() {
	document.iform.endport.selectedIndex = document.iform.beginport.selectedIndex;
	document.iform.localbeginport.selectedIndex = document.iform.beginport.selectedIndex;
}
//-->
</script>
<form action="natmod.php" method="post" name="iform" id="iform">
              <table width="100%" border="0" cellpadding="6" cellspacing="0">
			  	<tr>
                  <td width="22%" valign="top" class="vncellreq">Interface</td>
                  <td width="78%" class="vtable">
					<select name="interface" class="formfld">
						<?php
						$interfaces = array('wan' => 'WAN');
						foreach ($mono->ifaces as $if) {
							if ($if->type == "lan") continue;
 							$name = $if->type; if ($if->type == "opt") $name.=$if->num;
							$interfaces[$name] = $if->description;
						}
						foreach ($interfaces as $iface => $ifacename): ?>
						<option value="<?php echo $iface;?>" <?php if ($iface == $nat->if) echo "selected"; ?>>
						<?php echo $iface;?> (<?php echo htmlspecialchars($ifacename);?>)
						</option>
						<?php endforeach; ?>
					</select><br>
                     <span class="vexpl">Choose which interface this rule applies to.<br>
                     Hint: in most cases, you'll want to use WAN here.</span></td>
                </tr>
    	        <tr> 
                  <td width="22%" valign="top" class="vncellreq">External address</td>
                  <td width="78%" class="vtable"> 
                    <select name="extaddr" class="formfld">
					  <option value="" <?php if (!$nat->external) echo "selected"; ?>>Interface address</option>
                      <?php
					  if (is_array($mono->srvnat)):
						  foreach ($mono->srvnat as $sn): ?>
                      <option value="<?php echo $sn->ipaddr;?>" <?php if ($sn->ipaddr == $nat->external) echo "selected"; ?>><?php echo htmlspecialchars("{$sn->ipaddr} ({$sn->description})");?></option>
                      <?php endforeach; endif; ?>
                    </select><br>
                    <span class="vexpl">
					If you want this rule to apply to another IP address than the IP address of the interface chosen above,
					select it here (you need to define IP addresses on the
					<a href="firewall_nat_server.php">Server NAT</a> page first).</span></td>
                </tr>
                <tr> 
                  <td width="22%" valign="top" class="vncellreq">Protocol</td>
                  <td width="78%" class="vtable"> 
                    <select name="proto" class="formfld">
                      <?php $protocols = explode(" ", "TCP UDP TCP/UDP"); foreach ($protocols as $proto): ?>
                      <option value="<?php echo strtolower($proto);?>" <?php if (strtolower($proto) == $nat->proto) echo "selected"; ?>><?php echo htmlspecialchars($proto);?></option>
                      <?php endforeach; ?>
                    </select> <br> <span class="vexpl">Choose which IP protocol 
                    this rule should match.<br>
                    Hint: in most cases, you should specify <em>TCP</em> &nbsp;here.</span></td>
                </tr>
                <tr> 
                  <td width="22%" valign="top" class="vncellreq">External port 
                    range </td>
                  <td width="78%" class="vtable"> 
                    <table border="0" cellspacing="0" cellpadding="0">
                      <tr> 
                        <td>from:&nbsp;&nbsp;</td>
                        <td><select name="beginport" class="formfld" onChange="ext_rep_change();ext_change()">
                            <option value="">(other)</option>
<?php if (!specialport($nat->eport)) {
  if (strpos($nat->eport, "-")) {
   $ports = explode("-", $nat->eport);
   $eport1 = $ports[0];
   $eport2 = $ports[1];
  }
  else { $eport1 = $nat->eport; $eport2 = $nat->eport; }
 } else { $eport1 = ""; $eport2 = ""; }
?>
		<?php foreach ($wkports as $port => $value) { ?>
                                <option value="<?php echo $value; ?>"<?php if ($nat->eport == $value) echo "selected"; ?>><?php echo $port; ?></option>
                        <?php } ?>
                          </select> <input name="beginport_cust" type="text" size="5" value="<?php echo $eport1; ?>"></td>
                      </tr>
                      <tr> 
                        <td>to:</td>
                        <td><select name="endport" class="formfld" onChange="ext_change()">
                            <option value="">(other)</option>
			<?php foreach ($wkports as $port => $value) { ?>
                                <option value="<?php echo $value; ?>"<?php if ($nat->eport == $value) echo "selected"; ?>><?php echo $port; ?></option>
                        <?php } ?>
                          </select> <input name="endport_cust" type="text" size="5" value="<?php echo $eport2; ?>"></td>
                      </tr>
                    </table>
                    <br> <span class="vexpl">Specify the port or port range on 
                    the firewall's external address for this mapping.<br>
                    Hint: you can leave the <em>'to'</em> field empty if you only 
                    want to map a single port</span></td>
                </tr>
                <tr> 
                  <td width="22%" valign="top" class="vncellreq">NAT IP</td>
                  <td width="78%" class="vtable"> 
                    <input name="localip" type="text" class="formfldalias" id="localip" size="20" value="<?php echo htmlspecialchars($nat->target);?>"> 
                    <br> <span class="vexpl">Enter the internal IP address of 
                    the server on which you want to map the ports.<br>
                    e.g. <em>192.168.1.12</em></span></td>
                </tr>
                <tr> 
<?php
 if(specialport($nat->lport)) $lport = ""; else $lport = $nat->lport;
?>
                  <td width="22%" valign="top" class="vncellreq">Local port</td>
                  <td width="78%" class="vtable"> 
                    <select name="localbeginport" class="formfld" onChange="ext_change()">
                      <option value="">(other)</option>
                        <?php foreach ($wkports as $port => $value) { ?>
                                <option value="<?php echo $value; ?>"<?php if ($nat->lport == $value) echo "selected"; ?>><?php echo $port; ?></option>
                        <?php } ?>
                    </select> <input name="localbeginport_cust" type="text" size="5" value="<?php echo $lport;?>"> 
                    <br>
                    <span class="vexpl">Specify the port on the machine with the 
                    IP address entered above. In case of a port range, specify 
                    the beginning port of the range (the end port will be calculated 
                    automatically).<br>
                    Hint: this is usually identical to the 'from' port above</span></td>
                </tr>
                <tr> 
                  <td width="22%" valign="top" class="vncell">Description</td>
                  <td width="78%" class="vtable"> 
                    <input name="descr" type="text" class="formfld" id="descr" size="40" value="<?php echo htmlspecialchars($nat->description);?>"> 
                    <br> <span class="vexpl">You may enter a description here 
                    for your reference (not parsed).</span></td>
                </tr><?php if (!(isset($nid) && $nid)): ?>
                <tr> 
                  <td width="22%" valign="top">&nbsp;</td>
                  <td width="78%"> 
                    <input name="autoadd" type="checkbox" id="autoadd" value="yes">
                    <strong>Auto-add a firewall rule to permit traffic through 
                    this NAT rule</strong></td>
                </tr><?php endif; ?>
                <tr> 
                  <td width="22%" valign="top">&nbsp;</td>
                  <td width="78%"> 
                    <input name="Submit" type="submit" class="formbtn" value="Save"> 
		  <input name="mid" type="hidden" value="<?php echo $mono->id;?>">
		  <input name="type" type="hidden" value="<?php echo $type;?>">
                  <?php if (isset($nid) && $nid && isset($nat) && $nat) { ?>
                  <input name="nid" type="hidden" value="<?php echo $nat->id;?>">
                  <input name="action" type="hidden" value="3">
                  <?php } else { ?>
                  <input name="action" type="hidden" value="2">
                  <?php } ?>
                  </td>
                </tr>
              </table>
</form>
<script language="JavaScript">
<!--
ext_change();
//-->
</script>

<?php
    break;
    case "2": // srv
     if (isset($nid) && $nid) {
      $nat = new SrvNat($nid);
      $nat->fetchFromId();
     } else $nat = new SrvNat();

?>
            <form action="natmod.php" method="post" name="iform" id="iform">
              <table width="100%" border="0" cellpadding="6" cellspacing="0">
                <tr> 
                  <td width="22%" valign="top" class="vncellreq">External IP address</td>
                  <td width="78%" class="vtable"> 
                    <input name="ipaddr" type="text" class="formfld" id="ipaddr" size="20" value="<?php echo htmlspecialchars($nat->ipaddr);?>">
                    </td>
                </tr>
                <tr> 
                  <td width="22%" valign="top" class="vncell">Description</td>
                  <td width="78%" class="vtable"> 
                    <input name="descr" type="text" class="formfld" id="descr" size="40" value="<?php echo htmlspecialchars($nat->description);?>"> 
                    <br> <span class="vexpl">You may enter a description here 
                    for your reference (not parsed).</span></td>
                </tr>
                <tr> 
                  <td width="22%" valign="top">&nbsp;</td>
                  <td width="78%"> 
                    <input name="Submit" type="submit" class="formbtn" value="Save"> 
                  </td>
                </tr>
     		  <input name="mid" type="hidden" value="<?php echo $mono->id;?>">
		  <input name="type" type="hidden" value="<?php echo $type;?>">
                  <?php if (isset($nid) && $nid && isset($nat) && $nat) { ?>
                  <input name="nid" type="hidden" value="<?php echo $nat->id;?>">
                  <input name="action" type="hidden" value="3">
                  <?php } else { ?>
                  <input name="action" type="hidden" value="2">
                  <?php } ?>
         </table>
</form>

<?php
    break;
    case "3": // 1:1
    if (isset($nid)) {
     $nat = new O2ONat($nid);
     $nat->fetchFromId();
    }
    else $nat = new O2ONat();

?>
            <form action="natmod.php" method="post" name="iform" id="iform">
              <table width="100%" border="0" cellpadding="6" cellspacing="0">
				<tr>
				  <td width="22%" valign="top" class="vncellreq">Interface</td>
				  <td width="78%" class="vtable">
					<select name="interface" class="formfld">
						<?php
						$interfaces = array('wan' => 'WAN');
                                                foreach ($mono->ifaces as $if) {
                                                        if ($if->type == "lan") continue;
                                                        $name = $if->type; if ($if->type == "opt") $name.=$if->num;
                                                        $interfaces[$name] = $if->description;
                                                }
                                                foreach ($interfaces as $iface => $ifacename): ?>
                                                <option value="<?php echo $iface;?>" <?php if ($iface == $nat->if) echo "selected"; ?>>
                                                <?php echo $iface;?> (<?php echo htmlspecialchars($ifacename);?>)
						</option>
						<?php endforeach; ?>
					</select><br>
				  <span class="vexpl">Choose which interface this rule applies to.<br>
				  Hint: in most cases, you'll want to use WAN here.</span></td>
				</tr>
                <tr> 
                  <td width="22%" valign="top" class="vncellreq">External subnet</td>
                  <td width="78%" class="vtable"> 
                    <input name="external" type="text" class="formfld" id="external" size="20" value="<?php echo htmlspecialchars($nat->external);?>">
                    / 
                    <select name="subnet" class="formfld" id="subnet">
                      <?php for ($i = 32; $i >= 0; $i--): ?>
                      <option value="<?php echo $i;?>" <?php if ($i == $nat->subnet) echo "selected"; ?>>
                      <?php echo $i;?>
                      </option>
                      <?php endfor; ?>
                    </select>
                    <br>
                    <span class="vexpl">Enter the external (WAN) subnet for the 1:1 mapping. You may map single IP addresses by specifying a /32 subnet.</span></td>
                </tr>
                <tr> 
                  <td width="22%" valign="top" class="vncellreq">Internal subnet</td>
                  <td width="78%" class="vtable"> 
                    <input name="internal" type="text" class="formfld" id="internal" size="20" value="<?php echo htmlspecialchars($nat->internal);?>"> 
                    <br>
                     <span class="vexpl">Enter the internal (LAN) subnet for the 1:1 mapping. The subnet size specified for the external subnet also applies to the internal subnet (they  have to be the same).</span></td>
                </tr>
                <tr> 
                  <td width="22%" valign="top" class="vncell">Description</td>
                  <td width="78%" class="vtable"> 
                    <input name="descr" type="text" class="formfld" id="descr" size="40" value="<?php echo htmlspecialchars($nat->description);?>"> 
                    <br> <span class="vexpl">You may enter a description here 
                    for your reference (not parsed).</span></td>
                </tr>
<?php if (!isset($nid)): ?>
		<tr> 
		  <td width="22%" valign="top">&nbsp;</td>
		  <td width="78%"> 
		    <input name="autoaddproxy" type="checkbox" id="autoaddproxy" value="yes" checked="checked">
		    <strong>Auto-add a <a href="proxyarp.php">proxy ARP</a> entry to this interface
		    </strong></td>
		</tr>
<?php endif; ?>
                <tr> 
                  <td width="22%" valign="top">&nbsp;</td>
                  <td width="78%"> 
                    <input name="Submit" type="submit" class="formbtn" value="Save"> 
		  <input name="mid" type="hidden" value="<?php echo $mono->id;?>">
                  <input name="type" type="hidden" value="<?php echo $type;?>">
                  <?php if (isset($nid) && $nid && isset($nat) && $nat) { ?>
                  <input name="nid" type="hidden" value="<?php echo $nat->id;?>">
                  <input name="action" type="hidden" value="3">
                  <?php } else { ?>
                  <input name="action" type="hidden" value="2">
                  <?php } ?>    
                  </td>
                </tr>
              </table>
</form>

<?php
    break;
    case "4": // outbound
     if (isset($nid) && $nid) {
       $nat = new AdvNat($nid);
       $nat->fetchFromId();
     } else $nat = new AdvNat();

?>
<script language="JavaScript">
<!--
function typesel_change() {
    switch (document.iform.destination_type.selectedIndex) {
        case 1: // network
            document.iform.destination.disabled = 0;
            document.iform.destination_subnet.disabled = 0;
            break;
        default:
            document.iform.destination.value = "";
            document.iform.destination.disabled = 1;
            document.iform.destination_subnet.value = "24";
            document.iform.destination_subnet.disabled = 1;
            break;
    }
}
//-->
</script>
 <form action="natmod.php" method="post" name="iform" id="iform">
              <table width="100%" border="0" cellpadding="6" cellspacing="0">
			      <tr>
                  <td width="22%" valign="top" class="vncellreq">Interface</td>
                  <td width="78%" class="vtable">
					<select name="interface" class="formfld">
						<?php
						$interfaces = array('wan' => 'WAN');
                                                foreach ($mono->ifaces as $if) {
                                                        if ($if->type == "lan") continue;
                                                        $name = $if->type; if ($if->type == "opt") $name.=$if->num;
                                                        $interfaces[$name] = $if->description;
                                                }
                                                foreach ($interfaces as $iface => $ifacename): ?>
                                                <option value="<?php echo $iface;?>" <?php if ($iface == $nat->if) echo "selected"; ?>>
                                                <?php echo $iface;?> (<?php echo htmlspecialchars($ifacename);?>)
						</option>
						<?php endforeach; ?>
					</select><br>
                     <span class="vexpl">Choose which interface this rule applies to.<br>
                     Hint: in most cases, you'll want to use WAN here.</span></td>
                </tr>
                <tr> 
                  <td width="22%" valign="top" class="vncellreq">Source</td>
                  <td width="78%" class="vtable">
<?php $src = explode('/', $nat->source); ?>
					<input name="source" type="text" class="formfld" id="source" size="20" value="<?php echo htmlspecialchars($src[0]);?>">
                     
                  / 
                    <select name="source_subnet" class="formfld" id="source_subnet">
                      <?php for ($i = 32; $i >= 0; $i--): ?>
                      <option value="<?php echo $i;?>" <?php if (isset($src[1]) && $i == $src[1]) echo "selected"; ?>>
                      <?php echo $i;?>
                      </option>
                      <?php endfor; ?>
                    </select>
                    <br>
                     <span class="vexpl">Enter the source network for the outbound NAT mapping.</span></td>
                </tr>
                <tr> 
                  <td width="22%" valign="top" class="vncellreq">Destination</td>
                  <td width="78%" class="vtable">
				<input name="destination_not" type="checkbox" id="destination_not" value="yes" <?php if ($nat->dnot) echo "checked"; ?>>
                    <strong>not</strong><br>
                    Use this option to invert the sense of the match.<br>
                    <br>
                    <table border="0" cellspacing="0" cellpadding="0">
                      <tr> 
                        <td>Type:&nbsp;&nbsp;</td>
						<td></td>
                        <td><select name="destination_type" class="formfld" onChange="typesel_change()">
                            <option value="any" <?php if (strtolower($nat->destination) == "any") echo "selected"; ?>> 
                            any</option>
                            <option value="network" <?php if (strtolower($nat->destination) != "any") echo "selected"; ?>> 
                            Network</option>
                          </select></td>
                      </tr>
                      <tr> 
                        <td>Address:&nbsp;&nbsp;</td>
						<td></td>
<?php if (strtolower($nat->destination) == "any") { $dst = ""; $dstn = ""; } else { $dsta = explode('/', $nat->destination); $dst = $dsta[0]; $dstn = isset($dsta[1])?$dsta[1]:""; } ?>
                        <td><input name="destination" type="text" class="formfld" id="destination" size="20" value="<?php echo htmlspecialchars($dst);?>">
                          / 
                          <select name="destination_subnet" class="formfld" id="destination_subnet">
                            <?php for ($i = 32; $i >= 0; $i--): ?>
                            <option value="<?php echo $i;?>" <?php if ($i == $dstn) echo "selected"; ?>> 
                            <?php echo $i;?>
                            </option>
                            <?php endfor; ?>
                          </select> </td>
                      </tr>
                      <tr>
                        <td>&nbsp;</td>
						<td></td>
                        <td><span class="vexpl">Enter the destination network for 
                          the outbound NAT mapping.</span></td>
                      </tr>
                    </table></td>
                </tr>
                <tr> 
                  <td valign="top" class="vncell">Target</td>
                  <td class="vtable">
<input name="target" type="text" class="formfld" id="target" size="20" value="<?php echo htmlspecialchars($nat->target);?>">
                    <br>
                     <span class="vexpl">Packets matching this rule will be mapped to the IP address given here. Leave blank to use the selected interface's IP address.</span></td>
                </tr>
                <tr> 
                  <td width="22%" valign="top" class="vncell">Portmap</td>
                  <td width="78%" class="vtable">
					<input name="noportmap" type="checkbox" id="noportmap" value="1" <?php if ($nat->noportmap) echo "checked"; ?>> <strong>Disable port mapping</strong>
                    <br>
                     <span class="vexpl">This option disables remapping of the source port number for outbound packets. This may help with software
                     	that insists on the source ports being left unchanged when applying NAT (such as some IPsec VPN gateways). However,
                     	with this option enabled, two clients behind NAT cannot communicate with the same server at the same time using the
                     	same source ports.</span></td>
                </tr>
                <tr> 
                  <td width="22%" valign="top" class="vncell">Description</td>
                  <td width="78%" class="vtable"> 
                    <input name="descr" type="text" class="formfld" id="descr" size="40" value="<?php echo htmlspecialchars($nat->description);?>"> 
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
		  <input name="mid" type="hidden" value="<?php echo $mono->id;?>">
                  <input name="type" type="hidden" value="<?php echo $type;?>">
                  <?php if (isset($nid) && $nid && isset($nat) && $nat) { ?>
                  <input name="nid" type="hidden" value="<?php echo $nat->id;?>"> 
                  <input name="action" type="hidden" value="3">
                  <?php } else { ?>
                  <input name="action" type="hidden" value="2">
                  <?php } ?>
</form>
<script language="JavaScript">
<!--
typesel_change();
//-->
</script>

<?php
    break;
    default:
     die("No such NAT type...");
    break;
  }
 }
} else if ($action == 2) /* add */ {

switch($type) {
    case "1": // inbound
     $nat = new RuleNat();
     $nat->idhost = $mono->id;

     if (checkPost("interface")) {
       $nat->if = $_POST["interface"];
     }
     
     if (checkPost("extaddr")) {
       $nat->external = $_POST["extaddr"];
     }

     if (checkPost("proto")) {
       $nat->proto = $_POST["proto"];
     }

     if (checkPost("beginport") && checkPost("endport")) {
       if (empty($_POST["beginport"]) && empty($_POST["endport"])) {
         $nat->eport = $_POST["beginport_cust"]."-".$_POST["endport_cust"];
       }
       else {
         $nat->eport = $_POST["beginport"];
       }
     }

     if (checkPost("localip")) {
       $nat->target = $_POST["localip"];
     }

     if (checkPost("localbeginport")) {
       if (empty($_POST["localbeginport"])) {
         $nat->lport = $_POST["localbeginport_cust"];
       }
       else {
         $nat->lport = $_POST["localbeginport"];
       }
     }

     if (checkPost("descr")) {
       $nat->description = $_POST["descr"];
     }


     if ($nat->existsDb()) {
       echo "NAT Entry already in database.<br/>";
     } else {
	/* prepare fw rule if autoadd is checked */
	if (checkPost("autoadd") && $_POST["autoadd"] == "yes") {
         $fwr = new Rule();
	 $fwr->description = "Auto added rule by NAT rule";
         $fwr->if = $nat->if;
	 $fwr->protocol = $nat->proto;
	 $fwr->source = "ANY";
	 $fwr->destination = $nat->target;
	 $fwr->dport = $nat->eport;
 	 $fwr->insert();
	 $fwri = new RuleInt();
	 $fwri->idrule = Mysql::getInstance()->getLastId();
	 $fwr->id = $fwri->idrule;
	 $iface = $mono->getIface($fwr->if);
	 $fwri->idint = $iface->id;
	 $fwri->enabled = 1;
         if ($fwri->insert()) {
           $fwri->position = count($iface->rulesint);
           add_ri($iface->rulesint, $fwri);
    
           foreach($iface->rulesint as $rui) {
	     if ($rui->isChanged())
             {
               $rui->update();
             }
  	   }
         }
       }
       $nat->insert();
       $mono->updateChanged();
       echo "NAT Entry correctly inserted in database<br/>";
     }

    break;
    case "2": // srv
     $nat = new SrvNat();
     $nat->idhost = $mono->id;

     if (checkPost("ipaddr")) {
       $nat->ipaddr = $_POST["ipaddr"];
     }
     if (checkPost("descr")) {
       $nat->description = $_POST["descr"];
     }
     if ($nat->existsDb()) {
       echo "Entry already exists<br/>";
     } else {
       $nat->insert();
       $mono->updateChanged();
       echo "NAT Entry inserted.<br/>";
     }

    break;
    case "3": // 1:1
     $nat = new O2ONat();
     $nat->idhost = $mono->id;

      if (checkPost("interface") && checkPost("external") && checkPost("subnet") && checkPost("internal")) {

	$nat->if = $_POST["interface"];
	$nat->internal = $_POST["internal"];
	$nat->external = $_POST["external"];
	$nat->subnet = $_POST["subnet"];
      }
      if (checkPost("descr")) {
        $nat->description = $_POST["descr"];
      }
      if ($nat->existsDb()) {
       echo "Nat entry already in database<br/>";
      } else {
	if (checkPost("autoaddproxy") && $_POST["autoaddproxy"] == "yes") {
	  $pa = new ProxyArp();
	  $pa->idhost = $mono->id;
	  $pa->if = $nat->if;
	  $pa->network = $nat->external."/".$nat->subnet;
	  $pa->description = "Added with NAT rules";
	  $pa->insert();
	}
	
        $nat->insert();
        $mono->updateChanged();
        echo "NAT Entry inserted.<br/>";
      }

    break;
    case "4": // outbound
     $nat = new AdvNat();
     $nat->idhost = $mono->id;
     
     if (checkPost("interface") && checkPost("source") && checkPost("source_subnet") && checkPost("destination_type")) {
	$nat->if = $_POST["interface"];
	$nat->source = $_POST["source"]."/".$_POST["source_subnet"];
	if ($_POST["destination_type"] == "any") {
		$nat->destination = "ANY";
	} else {
		$nat->destination = $_POST["destination"]."/".$_POST["destination_subnet"];
	}
	if (checkPost("target")) {
		$nat->target = $_POST["target"];
	}
	if (checkPost("descr")) {
		$nat->description = $_POST["descr"];
	}
	if (checkPost("noportmap") && $_POST["noportmap"] == "1") {
		$nat->noportmap = 1;
	} else $nat->noportmap = 0;
	if (checkPost("destination_not") && $_POST["destination_not"] == "yes") {
		$nat->dnot = 1;
	} else $nat->dnot = 0;
        if ($nat->existsDb()) {
          echo "Nat entry already in database<br/>";
        } else {
          $nat->insert();
          $mono->updateChanged();
          echo "NAT Entry inserted.<br/>";
        }
     } else {
       echo "Missing field in form<br/>";
     }
    break;
}



} else if ($action == 3) /* mod */ {

  switch($type) {
      case "1": // inbound
      if (isset($nid) && $nid) {
        $nat = new RuleNat($nid);
        $nat->fetchFromId();
	$mod = 0;

	if (checkPost("interface") && $_POST["interface"] != $nat->if) {
	  $mod = 1;
	  $nat->if = $_POST["interface"];
        }
	if (checkPost("extaddr") && $_POST["extaddr"] != $nat->external) {
	  $mod = 1;
	  $nat->external = $_POST["extaddr"];
	}
	if (checkPost("proto") && $_POST["proto"] != $nat->proto) {
	  $mod = 1;
	  $nat->proto = $_POST["proto"];
	}
     	if (checkPost("beginport") && checkPost("endport")) {
          if (empty($_POST["beginport"]) && empty($_POST["endport"])) {
 	   if ($nat->eport != $_POST["beginport_cust"]."-".$_POST["endport_cust"]) {
             $nat->eport = $_POST["beginport_cust"]."-".$_POST["endport_cust"];
	     $mod = 1;
	   }
          }
          else if ($nat->eport != $_POST["beginport"]) {
            $nat->eport = $_POST["beginport"];
	    $mod = 1;
          }
        }
    
        if (checkPost("localip") && $_POST["localip"] != $nat->target) {
          $nat->target = $_POST["localip"];
	  $mod = 1;
        } 

	if (checkPost("localbeginport")) {
         if (empty($_POST["localbeginport"])) {
	   if ($nat->lport != $_POST["localbeginport_cust"]) {
             $nat->lport = $_POST["localbeginport_cust"];
	     $mod = 1;
	   }
         }
         else if ($nat->lport != $_POST["localbeginport"]) {
           $nat->lport = $_POST["localbeginport"];
	   $mod = 1;
         }
       }

       if (checkPost("descr") && $nat->description != $_POST["descr"]) { 
         $nat->description = $_POST["descr"];
	 $mod = 1;
       }

       if ($mod) {
	 $nat->update();
         $mono->updateChanged();
	 echo "NAT entry updated in database...<br/>";
       }
       else echo "Nothing to update in database..<br/>";

     }
    break;
    case "2": // srv
     if (isset($nid) && $nid) {
      $nat = new SrvNat($nid);
      $nat->fetchFromId();
      $mod = 0;

      if (checkPost("ipaddr") && $nat->ipaddr != $_POST["ipaddr"]) {
        $nat->ipaddr = $_POST["ipaddr"];
	$mod = 1;
      }
      if (checkPost("descr") && $nat->description != $_POST["descr"]) {
        $nat->description = $_POST["descr"];
	$mod = 1;
      } 

      if ($mod) {
	 $nat->update();
         $mono->updateChanged();
	 echo "NAT entry updated in database...<br/>";
       }
       else echo "Nothing to update in database..<br/>";
     }
    break;
    case "3": // 1:1
    if (isset($nid)) {
     $nat = new O2ONat($nid);
     $nat->fetchFromId();
     $mod = 0;

      if (checkPost("interface") && checkPost("external") && checkPost("subnet") && checkPost("internal")) {

	if ($nat->if != $_POST["interface"]) {
	  $nat->if = $_POST["interface"];
	  $mod = 1;
	}
	if ($nat->internal != $_POST["internal"]) {
	  $mod = 1;
	  $nat->internal = $_POST["internal"];
	}
	if ($nat->external != $_POST["external"]) {
	  $nat->external = $_POST["external"];
	  $mod = 1;
	}
	if ($nat->subnet != $_POST["subnet"]) {
	  $mod = 1;
	  $nat->subnet = $_POST["subnet"];
	}
      }
      if (checkPost("descr") && $_POST["descr"] != $nat->description) {
        $nat->description = $_POST["descr"];
	$mod = 1;
      }
     if ($mod) {
       $nat->update();
       $mono->updateChanged();
       echo "NAT entry updated in database...<br/>";
     }
     else echo "Nothing to update in database..<br/>";
    }
    break;
    case "4": // outbound
     if (isset($nid) && $nid) {
       $nat = new AdvNat($nid);
       $nat->fetchFromId();
       $mod = 0;

     if (checkPost("interface") && checkPost("source") && checkPost("source_subnet") && checkPost("destination_type")) {
	
	if ($nat->if != $_POST["interface"]) {
	  $mod = 1;
	  $nat->if = $_POST["interface"];
	}
	if ($nat->source != $_POST["source"]."/".$_POST["source_subnet"]) {
	  $mod = 1;
	  $nat->source = $_POST["source"]."/".$_POST["source_subnet"];
	}
	
	if ($_POST["destination_type"] == "any") {
		if ($nat->destination != "ANY") {
		  $nat->destination = "ANY";
		  $mod = 1;
		}
	} else {
		if ($nat->destination != $_POST["destination"]."/".$_POST["destination_subnet"]) {
		  $nat->destination = $_POST["destination"]."/".$_POST["destination_subnet"];
		  $mod = 1;
		}
	}
	if (checkPost("target") && $nat->target != $_POST["target"]) {
		$nat->target = $_POST["target"];
		$mod = 1;
	}
	if (checkPost("descr") && $nat->description != $_POST["descr"]) {
		$nat->description = $_POST["descr"];
		$mod = 1;
	}
	if (checkPost("noportmap") && $_POST["noportmap"] == "1") {
		if (!$nat->noportmap) {
		  $nat->noportmap = 1;
		  $mod = 1;
		}
	} else {
		if ($nat->noportmap) {
		  $nat->noportmap = 0;
		  $mod = 1;
		}
	}
	if (checkPost("destination_not") && $_POST["destination_not"] == "yes") {
		if (!$nat->dnot) {
		  $nat->dnot = 1;
		  $mod = 1;
		}
	} else {
		if ($nat->dnot) {
		  $mod = 1;
		  $nat->dnot = 0;
		}
	}
      }

      if ($mod) {
	 $nat->update();
         $mono->updateChanged();
	 echo "NAT entry updated in database...<br/>";
      }
      else echo "Nothing to update in database..<br/>";
     }
    break;
  }
}

?>
<?php
 /* disconnect database */
 Mysql::getInstance()->disconnect();
?>
