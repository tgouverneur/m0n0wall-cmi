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
function enable_change(enable_change) {
        var endis;
        endis = !(document.iform.enable.checked || enable_change);
        document.iform.syslocation.disabled = endis;
        document.iform.syscontact.disabled = endis;
        document.iform.rocommunity.disabled = endis;
        document.iform.bindlan.disabled = endis;
}
//-->
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
<form action="snmmod.php" method="post" name="iform" id="iform">
 <?php if (isset($action)): ?>
 <input type="hidden" name="action" value="<?php echo $action; ?>">
 <?php endif; ?>
 <?php if (isset($mono)): ?>
 <input type="hidden" name="mid" value="<?php echo $mono[0]->id; ?>">
 <?php endif; ?>
              <table width="100%" border="0" cellpadding="6" cellspacing="0">
                <tr>
                  <td width="22%" valign="top" class="vtable">&nbsp;</td>
                  <td width="78%" class="vtable">
<input name="enable" type="checkbox" value="yes" <?php if ($snmp[0]->enable) echo "checked"; ?> onClick="enable_change(false)">
                    <strong>Enable SNMP agent</strong></td>
                </tr>
                <tr>
                  <td width="22%" valign="top" class="vncell">System location</td>
                  <td width="78%" class="vtable">
                    <input name="syslocation" type="text" class="formfld" id="syslocation" size="40" value="<?php echo htmlspecialchars($snmp[0]->syslocation);?>">
                  </td>
                </tr>
                <tr>
                  <td width="22%" valign="top" class="vncell">System contact</td>
                  <td width="78%" class="vtable">
                    <input name="syscontact" type="text" class="formfld" id="syscontact" size="40" value="<?php echo htmlspecialchars($snmp[0]->syscontact);?>">
                  </td>
                </tr>
                <tr>
                  <td width="22%" valign="top" class="vncellreq">Community</td>
                  <td width="78%" class="vtable">
                    <?php echo $mandfldhtml;?><input name="rocommunity" type="text" class="formfld" id="rocommunity" size="40" value="<?php echo htmlspecialchars($snmp[0]->rocommunity);?>">
                    <br>
                    In most cases, &quot;public&quot; is used here</td>
                </tr>
                <tr>
                  <td width="22%" valign="top" class="vtable"></td>
                  <td width="78%" class="vtable">
                    <input name="bindlan" type="checkbox" value="yes" <?php if ($snmp[0]->bindlan) echo "checked"; ?>> <strong>Bind to LAN interface only</strong>
                    <br>
                    This option can be useful when trying to access the SNMP agent
                    by the LAN interface's IP address through a VPN tunnel terminated on the WAN interface.</td>
                </tr>
                <tr>
                  <td width="22%" valign="top">&nbsp;</td>
                  <td width="78%">
                    <input name="Submit" type="submit" class="formbtn" value="Save" onClick="enable_change(true)">
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
