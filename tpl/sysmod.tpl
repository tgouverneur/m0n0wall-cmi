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
function enable_change(enable_over) {
        if (document.iform.enable.checked || enable_over) {
                document.iform.remoteserver.disabled = 0;
                document.iform.filter.disabled = 0;
                document.iform.dhcp.disabled = 0;
                document.iform.portalauth.disabled = 0;
                document.iform.vpn.disabled = 0;
                document.iform.system.disabled = 0;
        } else {
                document.iform.remoteserver.disabled = 1;
                document.iform.filter.disabled = 1;
                document.iform.dhcp.disabled = 1;
                document.iform.portalauth.disabled = 1;
                document.iform.vpn.disabled = 1;
                document.iform.system.disabled = 1;
        }
}
// -->
</script>
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
<form action="sysmod.php" method="post" name="iform" id="iform">
 <?php if (isset($action)): ?>
 <input type="hidden" name="action" value="<?php echo $action; ?>">
 <?php endif; ?>
 <?php if (isset($mono)): ?>
 <input type="hidden" name="mid" value="<?php echo $mono[0]->id; ?>">
 <?php endif; ?>

<table width="100%" border="0" cellpadding="6" cellspacing="0">
                      <tr>
                        <td width="22%" valign="top" class="vtable">&nbsp;</td>
                        <td width="78%" class="vtable"> <input name="reverse" type="checkbox" id="reverse" value="yes" <?php if ($syslog[0]->reverse) echo "checked"; ?>>
                          <strong>Show log entries in reverse order (newest entries
                          on top)</strong></td>
                      </tr>
                      <tr>
                        <td width="22%" valign="top" class="vtable">&nbsp;</td>
                        <td width="78%" class="vtable">Number of log entries to
                          show:
                          <input name="nentries" id="nentries" type="text" class="formfld" size="4" value="<?php echo htmlspecialchars($syslog[0]->nentries);?>"></td>
                      </tr>
                      <tr>
                        <td valign="top" class="vtable">&nbsp;</td>
                        <td class="vtable"> <input name="logdefaultblock" type="checkbox" id="logdefaultblock" value="yes" <?php if (!$syslog[0]->nologdefaultblock) echo "checked"; ?>>
                          <strong>Log packets blocked by the default rule</strong><br>
                          Hint: packets that are blocked by the
                          implicit default block rule will not be logged anymore
                          if you uncheck this option. Per-rule logging options are not affected.</td>
                      </tr>
                      <tr>
                        <td valign="top" class="vtable">&nbsp;</td>
                        <td class="vtable"> <input name="rawfilter" type="checkbox" id="rawfilter" value="yes" <?php if ($syslog[0]->rawfilter) echo "checked"; ?>>
                          <strong>Show raw filter logs</strong><br>
                          Hint: If this is checked, filter logs are shown as generated by the packet filter, without any formatting. This will reveal more detailed information. </td>
                      </tr>
                      <tr>
                        <td valign="top" class="vtable">&nbsp;</td>
                        <td class="vtable"> <input name="resolve" type="checkbox" id="resolve" value="yes" <?php if ($syslog[0]->resolve) echo "checked"; ?>>
                          <strong>Resolve IP addresses to hostnames</strong><br>
                          Hint: If this is checked, IP addresses in firewall logs are resolved to real hostnames where possible.<br>
                          Warning: This can cause a huge delay in loading the firewall log page!</td>
                      </tr>
                      <tr>
                        <td width="22%" valign="top" class="vtable">&nbsp;</td>
                        <td width="78%" class="vtable"> <input name="enable" type="checkbox" id="enable" value="yes" <?php if ($syslog[0]->enable) echo "checked"; ?> onClick="enable_change(false)">
                          <strong>Enable syslog'ing to remote syslog server</strong></td>
                      </tr>
                      <tr>
                        <td width="22%" valign="top" class="vncell">Remote syslog
                          server</td>
                        <td width="78%" class="vtable"> <input name="remoteserver" id="remoteserver" type="text" class="formfld" size="20" value="<?php echo htmlspecialchars($syslog[0]->remoteserver);?>">
                          <br>
                          IP address of remote syslog server<br> <br>
                                                  <input name="system" id="system" type="checkbox" value="yes" onclick="enable_change(false)" <?php if ($syslog[0]->system) echo "checked"; ?>>
                          system events <br>
                                                  <input name="filter" id="filter" type="checkbox" value="yes" <?php if ($syslog[0]->filter) echo "checked"; ?>>
                          firewall events<br>
                                                  <input name="dhcp" id="dhcp" type="checkbox" value="yes" <?php if ($syslog[0]->dhcp) echo "checked"; ?>>
                          DHCP service events<br>
                                                  <input name="portalauth" id="portalauth" type="checkbox" value="yes" <?php if ($syslog[0]->portalauth) echo "checked"; ?>>
                          Captive portal<br>
                                                  <input name="vpn" id="vpn" type="checkbox" value="yes" <?php if ($syslog[0]->vpn) echo "checked"; ?>>
                          PPTP VPN events</td>
                      </tr>
                      <tr>
                        <td width="22%" valign="top">&nbsp;</td>
                        <td width="78%"> <input name="Submit" type="submit" class="formbtn" value="Save" onclick="enable_change(true)">
                        </td>
                      </tr>
                      <tr>
                        <td width="22%" valign="top">&nbsp;</td>
                        <td width="78%"><strong><span class="red">Note:</span></strong><br>
                          syslog sends UDP datagrams to port 514 on the specified
                          remote syslog server. Be sure to set syslogd on the
                          remote server to accept syslog messages from m0n0wall.
                        </td>
                      </tr>
</table>
</form>
<script language="JavaScript">
<!--
enable_change();
//-->
</script>
<br/><br/>

<?php if (isset($link)): ?>
<a href="<?php echo $link["href"]; ?>"><?php echo $link["label"]; ?></a><br/>
<?php endif; ?>
