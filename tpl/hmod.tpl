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
<form action="hmod.php" method="post" name="iform" id="iform">

 <?php if (isset($action)): ?>
 <input type="hidden" name="action" value="<?php echo $action; ?>">
 <?php endif; ?>
 <?php if (isset($mono)): ?>
 <input type="hidden" name="mid" value="<?php echo $mono[0]->id; ?>">
 <?php endif; ?>
<table width="100%" border="0" cellpadding="6" cellspacing="0">
<tr>
 <td width="22%" valign="top" class="vncellreq">Hostname</td>
 <td width="78%" class="vtable"><input name="hostname" type="text" class="formfld" id="hostname" size="40" value="<?php echo $mono[0]->hostname; ?>">
 <br> <span class="vexpl">name of the firewall host, without
  domain part<br>
  e.g. <em>firewall</em></span></td>
</tr>
<tr>
 <td width="22%" valign="top" class="vncellreq">Domain</td>
 <td width="78%" class="vtable"><input name="domain" type="text" class="formfld" id="domain" size="40" value="<?php echo $mono[0]->domain; ?>">
  <br> <span class="vexpl">e.g. <em>mycorp.com</em> </span></td>
</tr>
<tr>
 <td width="22%" valign="top" class="vncellreq">IP Address</td>
 <td width="78%" class="vtable"><input name="ip" type="text" class="formfld" id="ip" size="40" value="<?php echo $mono[0]->ip; ?>">
  <br> <span class="vexpl">IP Address to use if the DNS is not resolvable</span>
  <br/>
  <input name="use_ip" type="checkbox" id="use_ip" value="1" <?php if ($mono[0]->use_ip) echo "checked"; ?>>
  <strong>Use ip address to backup/restore configuration of monowall device</strong>
 </td>
</tr>
<tr>
 <td width="22%" valign="top" class="vncellreq">Port</td>
 <td width="78%" class="vtable"><input name="port" type="text" class="formfld" id="ip" size="10" value="<?php echo $mono[0]->port; ?>">
  <br> <span class="vexpl">Port to access the interface</span>
  <br/>
  <input name="https" type="checkbox" id="https" value="1" <?php if ($mono[0]->https) echo "checked"; ?>>
  <strong>Use HTTPS</strong>
 </td>
</tr>
<tr>
 <td width="22%" valign="top" class="vncell">DNS servers</td>
 <td width="78%" class="vtable">
  <?php $dns = explode(";", $mono[0]->dnsserver); ?>
  <input name="dns1" type="text" class="formfld" id="dns1" size="20" value="<?php echo $dns[0]; ?>">
  <br>
  <input name="dns2" type="text" class="formfld" id="dns2" size="20" value="<?php echo $dns[1]; ?>">
  <br>
  <input name="dns3" type="text" class="formfld" id="dns3" size="20" value="<?php echo $dns[2]; ?>">
  <br>
  <span class="vexpl">IP addresses; these are also used for
  the DHCP service, DNS forwarder and for PPTP VPN clients<br>
  <br>
  <input name="dnsallowoverride" type="checkbox" id="dnsallowoverride" value="1" <?php if ($mono[0]->dnsoverride) echo "checked"; ?> >
  <strong>Allow DNS server list to be overridden by DHCP/PPP
  on WAN</strong><br>
  If this option is set, m0n0wall will use DNS servers assigned
  by a DHCP/PPP server on WAN for its own purposes (including
  the DNS forwarder). They will not be assigned to DHCP and
  PPTP VPN clients, though.</span></td>
 </tr>
 <tr>
  <td valign="top" class="vncell">Username</td>
  <td class="vtable"> <input name="username" type="text" class="formfld" id="username" size="20" value="<?php echo $mono[0]->username; ?>">
  <br>
  <span class="vexpl">If you want
  to change the username for accessing the webGUI, enter it
  here.</span></td>
 </tr>
 <tr>
  <td width="22%" valign="top" class="vncell">Password</td>
  <td width="78%" class="vtable"> <input name="password" type="password" class="formfld" id="password" size="20">
  <br> <input name="password2" type="password" class="formfld" id="password2" size="20">
  &nbsp;(confirmation) <br> <span class="vexpl">If you want
  to change the password for accessing the webGUI, enter it
  here twice.</span></td>
 </tr>
<!--
 <tr>
  <td width="22%" valign="top" class="vncell">webGUI protocol</td>
  <td width="78%" class="vtable"> <input name="webguiproto" type="radio" value="http" >
   HTTP &nbsp;&nbsp;&nbsp; <input type="radio" name="webguiproto" value="https" checked>
   HTTPS</td>
 </tr>
 <tr>
  <td valign="top" class="vncell">webGUI port</td>
  <td class="vtable"> <input name="webguiport" type="text" class="formfld" id="webguiport" size="5" value="">
  <br>
  <span class="vexpl">Enter a custom port number for the webGUI
  above if you want to override the default (80 for HTTP, 443
  for HTTPS).</span></td>
 </tr>
-->
 <tr>
  <td width="22%" valign="top" class="vncell">Time zone</td>
  <td width="78%" class="vtable"> <select name="timezone" id="timezone">
<?php
   $fp = fopen ("./inc/timezone.txt", "r");
   if ($fp) {
     while($line = fgets($fp)) {
       $line = trim($line);
       ?><option value="<?php echo $line; ?>"<?php if ($mono[0]->timezone == $line) echo " selected"; ?>><?php echo $line; ?></option>
       <?php
     }
     fclose($fp);
   }
?>
   </select> <br> <span class="vexpl">Select the location closest
   to you</span></td>
 </tr>
 <tr>
  <td width="22%" valign="top" class="vncell">Time update interval</td>
  <td width="78%" class="vtable"> <input name="timeupdateinterval" type="text" class="formfld" id="timeupdateinterval" size="4" value="<?php echo $mono[0]->ntpinterval; ?>">
  <br> <span class="vexpl">Minutes between network time sync.;
  300 recommended, or 0 to disable </span></td>
 </tr>
 <tr>
  <td width="22%" valign="top" class="vncell">NTP time server</td>
  <td width="78%" class="vtable"> <input name="timeservers" type="text" class="formfld" id="timeservers" size="40" value="<?php echo $mono[0]->ntpserver; ?>">
  <br> <span class="vexpl">Use a space to separate multiple
  hosts (only one required). Remember to set up at least one
  DNS server if you enter a host name here!</span></td>
 </tr>
 <tr>
  <td width="22%" valign="top">&nbsp;</td>
  <td width="78%"> <input name="Submit" type="submit" class="formbtn" value="Save">
  </td>
 </tr>
</table>
 </form>

<br/><br/>

<?php if (isset($link)): ?>
<a href="<?php echo $link["href"]; ?>"><?php echo $link["label"]; ?></a><br/>
<?php endif; ?>
