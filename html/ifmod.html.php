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
  * @todo If all vlan and all physical interface are already assign, forbid interface addition.
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



 if (isset($_GET["action"])) $action = $_GET["action"];
 if (isset($_POST["action"])) $action = $_POST["action"];
 if (!isset($action)) $action = 1;

 if (isset($_GET["mid"])) $mid = $_GET["mid"];
 if (isset($_POST["mid"])) $mid = $_POST["mid"];
 if (isset($_GET["iid"])) $iid = $_GET["iid"];
 if (isset($_POST["iid"])) $iid = $_POST["iid"];
 
 if (isset($mid) && $mid) {
   $mono = new Monowall($mid);
   $mono->fetchFromId();
   $mono->fetchIfaces();
   $mono->fetchIfacesDetails();
   $mono->fetchVlans();
   $mono->fetchVlansDetails();
   $mono->fetchHw();

   if (isset($iid) && $iid) {
   $if = new Iface($iid);
   $if->fetchFromId();
   } 
  } else die("No group or monowall found.");

?>
<p class="pgtitle">Interface: Edit</p>


<?php if ($action == 1) {
 if (isset($if))
  $type = $if->type;
 else
  $type = "opt";
 switch($type) {
   case "lan": 
?>
<script language="JavaScript">
<!--
function gen_bits(ipaddr) {
    if (ipaddr.search(/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}$/) != -1) {
        var adr = ipaddr.split(/\./);
        if (adr[0] > 255 || adr[1] > 255 || adr[2] > 255 || adr[3] > 255)
            return "";
        if (adr[0] == 0 && adr[1] == 0 && adr[2] == 0 && adr[3] == 0)
            return "";
		
		if (adr[0] <= 127)
			return "8";
		else if (adr[0] <= 191)
			return "16";
		else
			return "24";
    }
    else
        return "";
}
function ipaddr_change() {
	document.iform.subnet.value = gen_bits(document.iform.ipaddr.value);
}
// -->
</script>
            <form action="ifmod.php" method="post" name="iform" id="iform">
              <table width="100%" border="0" cellpadding="6" cellspacing="0">
                <tr> 
		  <td width="22%" valign="top" class="vncellreq">Interface</td>
		  <td width="78%" class="vtable">
 		  <select name="iface" class="formfld" id="iface">
		     <?php
				$ifaces = array();
				foreach ($mono->hwInt as $hw) {
					$ifaces[$hw->name] = $hw->name." (".$hw->mac.")";
				}
				foreach ($mono->vlans as $vlan) {
					$ifaces["vlan".$vlan->order] = "VLAN ".$vlan->tag." ON ".$vlan->if;
				}
				foreach($ifaces as $name => $desc) {
					?>
					<option value="<?php echo $name;?>" <?php if ($name == $if->if) echo "selected"; ?>><?php echo $desc;?></option>
					<?php
				}
			?>
		  </select>
		  </td>
		</tr>
		<tr>	
                  <td width="22%" valign="top" class="vncellreq">IP address</td>
                  <td width="78%" class="vtable"> 
                    <input name="ipaddr" type="text" class="formfld" id="ipaddr" size="20" value="<?php echo htmlspecialchars($if->ipaddr);?>" onchange="ipaddr_change()">
                    / 
                    <select name="subnet" class="formfld" id="subnet">
                      <?php for ($i = 31; $i > 0; $i--): ?>
                      <option value="<?php echo $i;?>" <?php if ($i == $if->subnet) echo "selected"; ?>>
                      <?php echo $i;?>
                      </option>
                      <?php endfor; ?>
                    </select></td>
                </tr>
                <tr> 
                  <td width="22%" valign="top">&nbsp;</td>
                  <td width="78%"> 
		<input name="mid" type="hidden" value="<?php echo $mono->id;?>">
		<?php if (isset($iid) && $iid && isset($if) && $if) { ?>
                  <input name="iid" type="hidden" value="<?php echo $if->id;?>">
                  <input name="action" type="hidden" value="3">
                  <?php } else { ?>
                  <input name="action" type="hidden" value="2">
                  <?php } ?>

                    <input name="Submit" type="submit" class="formbtn" value="Save"> 
                  </td>
                </tr>
                <tr> 
                  <td width="22%" valign="top">&nbsp;</td>
                  <td width="78%"><span class="vexpl"><span class="red"><strong>Warning:<br>
                    </strong></span>after you click &quot;Save&quot;, you must 
                    reboot your firewall for changes to take effect. You may also 
                    have to do one or more of the following steps before you can 
                    access your firewall again: 
                    <ul>
                      <li>change the IP address of your computer</li>
                      <li>renew its DHCP lease</li>
                      <li>access the webGUI with the new IP address</li>
                    </ul>
                    </span></td>
                </tr>
              </table>
</form>
<?php
   break;
   case "wan": ?>

<script language="JavaScript">
<!--
function enable_change(enable_change) {
	if (document.iform.pppoe_dialondemand.checked || enable_change) {
		document.iform.pppoe_idletimeout.disabled = 0;
	} else {
		document.iform.pppoe_idletimeout.disabled = 1;
	}
}

function enable_change_pptp(enable_change_pptp) {
	if (document.iform.pptp_dialondemand.checked || enable_change_pptp) {
		document.iform.pptp_idletimeout.disabled = 0;
		document.iform.pptp_local.disabled = 0;
		document.iform.pptp_remote.disabled = 0;
	} else {
		document.iform.pptp_idletimeout.disabled = 1;
	}
}

function type_change(enable_change,enable_change_pptp) {
	switch (document.iform.type.selectedIndex) {
		case 0:
			document.iform.username.disabled = 1;
			document.iform.password.disabled = 1;
			document.iform.provider.disabled = 1;
			document.iform.pppoe_dialondemand.disabled = 1;
			document.iform.pppoe_idletimeout.disabled = 1;
			document.iform.ipaddr.disabled = 0;
			document.iform.subnet.disabled = 0;
			document.iform.gateway.disabled = 0;
			document.iform.pptp_username.disabled = 1;
			document.iform.pptp_password.disabled = 1;
			document.iform.pptp_local.disabled = 1;
			document.iform.pptp_subnet.disabled = 1;
			document.iform.pptp_remote.disabled = 1;
			document.iform.pptp_dialondemand.disabled = 1;
			document.iform.pptp_idletimeout.disabled = 1;
			document.iform.bigpond_username.disabled = 1;
			document.iform.bigpond_password.disabled = 1;
			document.iform.bigpond_authserver.disabled = 1;
			document.iform.bigpond_authdomain.disabled = 1;
			document.iform.bigpond_minheartbeatinterval.disabled = 1;
			document.iform.dhcphostname.disabled = 1;
			break;
		case 1:
			document.iform.username.disabled = 1;
			document.iform.password.disabled = 1;
			document.iform.provider.disabled = 1;
			document.iform.pppoe_dialondemand.disabled = 1;
			document.iform.pppoe_idletimeout.disabled = 1;
			document.iform.ipaddr.disabled = 1;
			document.iform.subnet.disabled = 1;
			document.iform.gateway.disabled = 1;
			document.iform.pptp_username.disabled = 1;
			document.iform.pptp_password.disabled = 1;
			document.iform.pptp_local.disabled = 1;
			document.iform.pptp_subnet.disabled = 1;
			document.iform.pptp_remote.disabled = 1;
			document.iform.pptp_dialondemand.disabled = 1;
			document.iform.pptp_idletimeout.disabled = 1;
			document.iform.bigpond_username.disabled = 1;
			document.iform.bigpond_password.disabled = 1;
			document.iform.bigpond_authserver.disabled = 1;
			document.iform.bigpond_authdomain.disabled = 1;
			document.iform.bigpond_minheartbeatinterval.disabled = 1;
			document.iform.dhcphostname.disabled = 0;
			break;
		case 2:
			document.iform.username.disabled = 0;
			document.iform.password.disabled = 0;
			document.iform.provider.disabled = 0;
			document.iform.pppoe_dialondemand.disabled = 0;
			if (document.iform.pppoe_dialondemand.checked || enable_change) {
				document.iform.pppoe_idletimeout.disabled = 0;
			} else {
				document.iform.pppoe_idletimeout.disabled = 1;
			}
			document.iform.ipaddr.disabled = 1;
			document.iform.subnet.disabled = 1;
			document.iform.gateway.disabled = 1;
			document.iform.pptp_username.disabled = 1;
			document.iform.pptp_password.disabled = 1;
			document.iform.pptp_local.disabled = 1;
			document.iform.pptp_subnet.disabled = 1;
			document.iform.pptp_remote.disabled = 1;
			document.iform.pptp_dialondemand.disabled = 1;
			document.iform.pptp_idletimeout.disabled = 1;
			document.iform.bigpond_username.disabled = 1;
			document.iform.bigpond_password.disabled = 1;
			document.iform.bigpond_authserver.disabled = 1;
			document.iform.bigpond_authdomain.disabled = 1;
			document.iform.bigpond_minheartbeatinterval.disabled = 1;
			document.iform.dhcphostname.disabled = 1;
			break;
		case 3:
			document.iform.username.disabled = 1;
			document.iform.password.disabled = 1;
			document.iform.provider.disabled = 1;
			document.iform.pppoe_dialondemand.disabled = 1;
			document.iform.pppoe_idletimeout.disabled = 1;
			document.iform.ipaddr.disabled = 1;
			document.iform.subnet.disabled = 1;
			document.iform.gateway.disabled = 1;
			document.iform.pptp_username.disabled = 0;
			document.iform.pptp_password.disabled = 0;
			document.iform.pptp_local.disabled = 0;
			document.iform.pptp_subnet.disabled = 0;
			document.iform.pptp_remote.disabled = 0;
			document.iform.pptp_dialondemand.disabled = 0;
			if (document.iform.pptp_dialondemand.checked || enable_change_pptp) {
				document.iform.pptp_idletimeout.disabled = 0;
			} else {
				document.iform.pptp_idletimeout.disabled = 1;
			}
			document.iform.bigpond_username.disabled = 1;
			document.iform.bigpond_password.disabled = 1;
			document.iform.bigpond_authserver.disabled = 1;
			document.iform.bigpond_authdomain.disabled = 1;
			document.iform.bigpond_minheartbeatinterval.disabled = 1;
			document.iform.dhcphostname.disabled = 1;
			break;
		case 4:
			document.iform.username.disabled = 1;
			document.iform.password.disabled = 1;
			document.iform.provider.disabled = 1;
			document.iform.pppoe_dialondemand.disabled = 1;
			document.iform.pppoe_idletimeout.disabled = 1;
			document.iform.ipaddr.disabled = 1;
			document.iform.subnet.disabled = 1;
			document.iform.gateway.disabled = 1;
			document.iform.pptp_username.disabled = 1;
			document.iform.pptp_password.disabled = 1;
			document.iform.pptp_local.disabled = 1;
			document.iform.pptp_subnet.disabled = 1;
			document.iform.pptp_remote.disabled = 1;
			document.iform.pptp_dialondemand.disabled = 1;
			document.iform.pptp_idletimeout.disabled = 1;
			document.iform.bigpond_username.disabled = 0;
			document.iform.bigpond_password.disabled = 0;
			document.iform.bigpond_authserver.disabled = 0;
			document.iform.bigpond_authdomain.disabled = 0;
			document.iform.bigpond_minheartbeatinterval.disabled = 0;
			document.iform.dhcphostname.disabled = 1;
			break;
	}
}
//-->
</script>
            <form action="ifmod.php" method="post" name="iform" id="iform">
              <table width="100%" border="0" cellpadding="6" cellspacing="0">
            <tr> 
		  <td width="22%" valign="top" class="vncellreq">Interface</td>
		  <td width="78%" class="vtable">
 		  <select name="iface" class="formfld" id="iface">
		     <?php
				$ifaces = array();
				foreach ($mono->hwInt as $hw) {
					$ifaces[$hw->name] = $hw->name." (".$hw->mac.")";
				}
				foreach ($mono->vlans as $vlan) {
					$ifaces["vlan".$vlan->order] = "VLAN ".$vlan->tag." ON ".$vlan->if;
				}
				foreach($ifaces as $name => $desc) {
					?>
					<option value="<?php echo $name;?>" <?php if ($name == $if->if) echo "selected"; ?>><?php echo $desc;?></option>
					<?php
				}
			?>
		  </select>
		  </td>
		</tr>

                <tr> 
                  <td valign="middle"><strong>Type</strong></td>
                  <td><select name="type" class="formfld" id="type" onchange="type_change()">
                      <?php //$opts = split(" ", "Static DHCP PPPoE PPTP BigPond");
                            $opts = split(" ", "Static DHCP");
				foreach ($opts as $opt): ?>
                      <option <?php if (($opt == "DHCP" && $if->dhcp != "") || ($opt == "Static" && $if->dhcp == "")) echo "selected";?>> 
                      <?php echo htmlspecialchars($opt);?>
                      </option>
                      <?php endforeach; ?>
                    </select></td>
                </tr>
                <tr> 
                  <td colspan="2" valign="top" height="4"></td>
                </tr>
                <tr> 
                  <td colspan="2" valign="top" class="listtopic">General configuration</td>
                </tr>
                <tr> 
                  <td valign="top" class="vncell">MAC address</td>
                  <td class="vtable"> <input name="spoofmac" type="text" class="formfld" id="spoofmac" size="30" value="<?php echo htmlspecialchars($if->spoofmac);?>"> 
                    <br>
                    This field can be used to modify (&quot;spoof&quot;) the MAC 
                    address of the WAN interface<br>
                    (may be required with some cable connections)<br>
                    Enter a MAC address in the following format: xx:xx:xx:xx:xx:xx 
                    or leave blank</td>
                </tr>
                <tr> 
                  <td valign="top" class="vncell">MTU</td>
                  <td class="vtable"> <input name="mtu" type="text" class="formfld" id="mtu" size="8" value="<?php echo htmlspecialchars($if->mtu);?>"> 
                    <br>
                    If you enter a value in this field, then MSS clamping for 
                    TCP connections to the value entered above minus 40 (TCP/IP 
                    header size) will be in effect. If you leave this field blank, 
                    an MTU of 1492 bytes for PPPoE and 1500 bytes for all other 
                    connection types will be assumed.</td>
                </tr>
                <tr> 
                  <td colspan="2" valign="top" height="16"></td>
                </tr>
                <tr> 
                  <td colspan="2" valign="top" class="listtopic">Static IP configuration</td>
                </tr>
                <tr> 
                  <td width="100" valign="top" class="vncellreq">IP address</td>
                  <td class="vtable"><input name="ipaddr" type="text" class="formfld" id="ipaddr" size="20" value="<?php echo htmlspecialchars($if->ipaddr);?>">
                    / 
                    <select name="subnet" class="formfld" id="subnet">
                    <?php
                     $snmax = 31;
                      for ($i = $snmax; $i > 0; $i--): ?>
                      <option value="<?php echo $i;?>" <?php if ($i == $if->subnet) echo "selected"; ?>> 
                      <?php echo $i;?>
                      </option>
                      <?php endfor; ?>
                    </select></td>
                </tr>
                <tr> 
                  <td valign="top" class="vncellreq">Gateway</td>
                  <td class="vtable"><input name="gateway" type="text" class="formfld" id="gateway" size="20" value="<?php echo htmlspecialchars($if->gateway);?>"> 
                  </td>
                </tr>
                <tr> 
                  <td colspan="2" valign="top" height="16"></td>
                </tr>
                <tr> 
                  <td colspan="2" valign="top" class="listtopic">DHCP client configuration</td>
                </tr>
                <tr> 
                  <td valign="top" class="vncell">Hostname</td>
                  <td class="vtable"> <input name="dhcphostname" type="text" class="formfld" id="dhcphostname" size="40" value="<?php echo htmlspecialchars($if->dhcp);?>">
                    <br>
                    The value in this field is sent as the DHCP client identifier 
                    and hostname when requesting a DHCP lease. Some ISPs may require 
                    this (for client identification).</td>
                </tr>
                <tr> 
                  <td colspan="2" valign="top" height="16"></td>
                </tr>
                <tr> 
                <tr> 
                  <td valign="middle">&nbsp;</td>
                  <td class="vtable">
                <a name="rfc1918"></a><input name="blockpriv" type="checkbox" id="blockpriv" value="yes" <?php if ($if->blockpriv) echo "checked"; ?>> 
                    <strong>Block private networks</strong><br>
					When set, this option blocks traffic from IP addresses
					that are reserved for private networks as per RFC 1918
					(10/8, 172.16/12, 192.168/16) as well as loopback addresses
					(127/8). You should generally leave this option turned on, 
					unless your WAN network lies in such a private address space,
					too.</td>
                </tr>
                <tr> 
                  <td width="100" valign="top">&nbsp;</td>
		<input name="mid" type="hidden" value="<?php echo $mono->id;?>">
		<?php if (isset($iid) && $iid && isset($if) && $if) { ?>
                  <input name="iid" type="hidden" value="<?php echo $if->id;?>">
                  <input name="action" type="hidden" value="3">
                  <?php } else { ?>
                  <input name="action" type="hidden" value="2">
                  <?php } ?>

                  <td> &nbsp;<br> <input name="Submit" type="submit" class="formbtn" value="Save" onClick="enable_change_pptp(true)&&enable_change(true)"> 
                  </td>
                </tr>
              </table>
</form>
<script language="JavaScript">
<!--
type_change();
//-->
</script>

<?php
   break;
   case "opt": ?>
 
<script language="JavaScript">
<!--
function enable_change(enable_over) {
	var endis;
	endis = !(document.iform.enable.checked || enable_over);
	document.iform.descr.disabled = endis;
	document.iform.ipaddr.disabled = endis;
	document.iform.subnet.disabled = endis;
	document.iform.bridge.disabled = endis;

	if (document.iform.mode) {
		 document.iform.mode.disabled = endis;
		 document.iform.ssid.disabled = endis;
		 document.iform.channel.disabled = endis;
		 document.iform.stationname.disabled = endis;
		 document.iform.wep_enable.disabled = endis;
		 document.iform.key1.disabled = endis;
		 document.iform.key2.disabled = endis;
		 document.iform.key3.disabled = endis;
		 document.iform.key4.disabled = endis;
	}
}
function bridge_change(enable_over) {
	var endis;

	if (document.iform.enable.checked || enable_over) {
		endis = !((document.iform.bridge.selectedIndex == 0) || enable_over);
	} else {
		endis = true;
	}

	document.iform.ipaddr.disabled = endis;
	document.iform.subnet.disabled = endis;
}
function gen_bits(ipaddr) {
    if (ipaddr.search(/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}$/) != -1) {
        var adr = ipaddr.split(/\./);
        if (adr[0] > 255 || adr[1] > 255 || adr[2] > 255 || adr[3] > 255)
            return 0;
        if (adr[0] == 0 && adr[1] == 0 && adr[2] == 0 && adr[3] == 0)
            return 0;
		
		if (adr[0] <= 127)
			return 23;
		else if (adr[0] <= 191)
			return 15;
		else
			return 7;
    }
    else
        return 0;
}
function ipaddr_change() {
	document.iform.subnet.selectedIndex = gen_bits(document.iform.ipaddr.value);
}
//-->
</script>
<form action="ifmod.php" method="post" name="iform" id="iform">
              <table width="100%" border="0" cellpadding="6" cellspacing="0">
            <tr> 
		  <td width="22%" valign="top" class="vncellreq">Interface</td>
		  <td width="78%" class="vtable">
 		  <select name="iface" class="formfld" id="iface">
		     <?php
				$ifaces = array();
				foreach ($mono->hwInt as $hw) {
					$ifaces[$hw->name] = $hw->name." (".$hw->mac.")";
				}
				foreach ($mono->vlans as $vlan) {
					$ifaces["vlan".$vlan->order] = "VLAN ".$vlan->tag." ON ".$vlan->if;
				}
				foreach($ifaces as $name => $desc) {
					?>
					<option value="<?php echo $name;?>" <?php if ($name == $if->if) echo "selected"; ?>><?php echo $desc;?></option>
					<?php
				}
			?>
		  </select>
		  </td>
		</tr>

                <tr> 
                  <td width="22%" valign="top" class="vtable">&nbsp;</td>
                  <td width="78%" class="vtable">
<input name="enable" type="checkbox" value="yes" <?php if ($if->enable) echo "checked"; ?> onClick="enable_change(false);bridge_change(false)">
                    <strong>Enable Optional <?php echo $if->num;?> interface</strong></td>
				</tr>
                <tr> 
                  <td width="22%" valign="top" class="vncell">Description</td>
                  <td width="78%" class="vtable"> 
                    <input name="descr" type="text" class="formfld" id="descr" size="30" value="<?php echo htmlspecialchars($if->description);?>">
					<br> <span class="vexpl">Enter a description (name) for the interface here.</span>
				 </td>
				</tr>
                <tr> 
                  <td colspan="2" valign="top" height="16"></td>
				</tr>
				<tr> 
                  <td colspan="2" valign="top" class="listtopic">IP configuration</td>
				</tr>
				<tr> 
                  <td width="22%" valign="top" class="vncellreq">Bridge with</td>
                  <td width="78%" class="vtable">
					<select name="bridge" class="formfld" id="bridge" onChange="bridge_change(false)">
				  	<option <?php if (!$if->bridge) echo "selected";?> value="">none</option>
			                      <?php $opts = array('lan' => "LAN", 'wan' => "WAN");
						
						foreach ($mono->ifaces as $iface) {
							if ($if->num != $iface->num && $if->type == "opt") {
								$opts['opt' . $iface->num] = "Optional " . $iface->num . " (" . $iface->description . ")";
							}
						}
					foreach ($opts as $opt => $optname): ?>
                      <option <?php if ($opt == $if->bridge) echo "selected";?> value="<?php echo htmlspecialchars($opt);?>"> 
                      <?php echo htmlspecialchars($optname);?>
                      </option>
                      <?php endforeach; ?>
                    </select> </td>
				</tr>
                <tr> 
                  <td width="22%" valign="top" class="vncellreq">IP address</td>
                  <td width="78%" class="vtable"> 
                    <input name="ipaddr" type="text" class="formfld" id="ipaddr" size="20" value="<?php echo htmlspecialchars($if->ipaddr);?>" onchange="ipaddr_change()">
                    /
                	<select name="subnet" class="formfld" id="subnet">
					<?php for ($i = 31; $i > 0; $i--): ?>
					<option value="<?php echo $i;?>" <?php if ($i == $if->subnet) echo "selected"; ?>><?php echo $i;?></option>
					<?php endfor; ?>
                    </select>
				 </td>
				</tr>
                <tr> 
                  <td width="22%" valign="top">&nbsp;</td>
                  <td width="78%"> 
		<input name="mid" type="hidden" value="<?php echo $mono->id;?>">
		<?php if (isset($iid) && $iid && isset($if) && $if) { ?>
                  <input name="iid" type="hidden" value="<?php echo $if->id;?>">
                  <input name="action" type="hidden" value="3">
                  <?php } else { ?>
                  <input name="action" type="hidden" value="2">
                  <?php } ?>
				  <input name="Submit" type="submit" class="formbtn" value="Save" onclick="enable_change(true);bridge_change(true)"> 
                  </td>
                </tr>
                <tr> 
                  <td width="22%" valign="top">&nbsp;</td>
                  <td width="78%"><span class="vexpl"><span class="red"><strong>Note:<br>
                    </strong></span>be sure to add firewall rules to permit traffic 
                    through the interface. Firewall rules for an interface in 
                    bridged mode have no effect on packets to hosts other than 
                    m0n0wall itself, unless &quot;Enable filtering bridge&quot; 
                    is checked on the <a href="system_advanced.php">System: 
                    Advanced functions</a> page.</span></td>
                </tr>
              </table>
</form>
<script language="JavaScript">
<!--
enable_change(false);
bridge_change(false);
//-->
</script>

<?php
   break;
 }

} else if ($action == 2) /* add */ {

/* add an OPT interface */

/* must find an empty number first */
$num = 1;
foreach ($mono->ifaces as $if) if ($if->num > $num) $num = $if->num;
$num++;

$iface = new Iface();
$iface->idhost = $mono->id;
$iface->num = $num;
$iface->type = "opt";
$iface->enable = 0;
if (checkPost("iface") && !empty($_POST["iface"])) {
 $iface->if = $_POST["iface"];
 if (checkPost("ipaddr") && checkPost("subnet")) {
   $iface->ipaddr = $_POST["ipaddr"];
   $iface->subnet = $_POST["subnet"];
 }
 if (checkPost("descr")) {
   $iface->description = $_POST["descr"];
 }
 if (checkPost("bridge")) {
   $iface->bridge = $_POST["bridge"];
 }
 if (checkPost("enable") && $_POST["enable"] == "yes") {
   $iface->enable = 1;
 }

 if (!$iface->existsDb()) {
   $iface->insert();
 $mono->updateChanged();
   echo "Interface added into database";
 } else {
  echo "Interface already in database";
 }
 }


} else if ($action == 3) /* mod */ {
  $mod = 0;
  switch($if->type) {
    case "lan":

    if (checkPost("iface") && $_POST["iface"] != $if->if) {
     $if->if = $_POST["iface"];
     $mod = 1;
    }

    if (checkPost("ipaddr") && $_POST["ipaddr"] != $if->ipaddr) {
     $if->ipaddr = $_POST["ipaddr"];
      $mod = 1;
    }
    if (checkPost("subnet") && $_POST["subnet"] != $if->subnet) {
     $if->subnet = $_POST["subnet"];
      $mod = 1;
    }
    if ($mod) {
      $if->update();
 $mono->updateChanged();
      echo "Updated interface into database...<br/>";
    } else {
      echo "No modification to apply...<br/>";
    }
    break;
    case "wan":
    if (checkPost("iface") && $_POST["iface"] != $if->if) {
     $if->if = $_POST["iface"];
     $mod = 1;
    }
    if (checkPost("spoofmac") && $_POST["spoofmac"] != $if->spoofmac) {
      $if->spoofmac = $_POST["spoofmac"];
      $mod = 1;
    }

    if (checkPost("mtu") && $_POST["mtu"] != $if->mtu) {
      $if->mtu = $_POST["mtu"];
      $mod = 1;
    }
    
    if (checkPost("ipaddr") && $_POST["ipaddr"] != $if->ipaddr) {
      $if->ipaddr = $_POST["ipaddr"];
      $mod = 1;
    }
       
    if (checkPost("subnet") && $_POST["subnet"] != $if->subnet) {
      $if->subnet = $_POST["subnet"];
      $mod = 1;
    }

    if (checkPost("gateway") && $_POST["gateway"] != $if->gateway) {
      $if->gateway = $_POST["gateway"];
      $mod = 1;
    }

    if (checkPost("dhcphostname") && $_POST["dhcphostname"] != $if->dhcp) {
      $if->dhcp = $_POST["dhcphostname"];
      $mod = 1;
    }

    if (checkPost("blockpriv") && $_POST["blockpriv"] == "yes" && !$if->blockpriv) {
      $if->blockpriv = "yes";
      $mod = 1;
    } else if ($if->blockpriv == "yes" && (!checkPost("blockpriv") || $_POST["blockpriv"] != "yes")) {
      $if->blockpriv = "no";
      $mod = 1;
    }


    if ($mod) {
      $if->update();
 $mono->updateChanged();
      echo "Interface updated in database..<br/>";
    } else
      echo "Nothing to update in database";

    break;
    case "opt":
    if (checkPost("iface") && $_POST["iface"] != $if->if) {
     $if->if = $_POST["iface"];
     $mod = 1;
    }
    if (checkPost("enable") && $_POST["enable"] == "yes" && !$if->enable) {
      $if->enable = 1;
      $mod = 1;
    } else if ($if->enable == 1 && (!checkPost("enable") || $_POST["enable"] != "yes")) {
      $if->enable = 0;
      $mod = 1;
    }

    if (checkPost("descr") && $_POST["descr"] != $if->description) {
      $if->description = $_POST["descr"];
      $mod = 1;
    }
    if (checkPost("ipaddr") && $_POST["ipaddr"] != $if->ipaddr) {
      $if->ipaddr = $_POST["ipaddr"];
      $mod = 1;
    }
    if (checkPost("subnet") && $_POST["subnet"] != $if->subnet) {
      $if->subnet = $_POST["subnet"];
      $mod = 1;
    }
    if (checkPost("bridge") && $_POST["bridge"] != $if->bridge) {
      $if->bridge = $_POST["bridge"];
      $mod = 1;
    }
    if ($mod) {
      $if->update();
 $mono->updateChanged();
      echo "Interface updated in database..<br/>";
    } else
      echo "Nothing to update in database";
    break;
  }

}
?>

<?php
 /* disconnect database */
 Mysql::getInstance()->disconnect();
?>
