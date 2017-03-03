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
function typesel_change() {
    switch (document.iform.type.selectedIndex) {
        case 0: // single
            document.iform.subnet.disabled = 0;
            document.iform.subnet_bits.disabled = 1;
            document.iform.range_from.disabled = 1;
            document.iform.range_to.disabled = 1;
            break;
        case 1: // network
            document.iform.subnet.disabled = 0;
            document.iform.subnet_bits.disabled = 0;
            document.iform.range_from.disabled = 1;
            document.iform.range_to.disabled = 1;
            break;
        case 2: // range
            document.iform.subnet.disabled = 1;
            document.iform.subnet_bits.disabled = 1;
            document.iform.range_from.disabled = 0;
            document.iform.range_to.disabled = 0;
            break;
    }
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
<form action="pamod.php" method="post" name="iform" id="iform">

 <?php if (isset($action)): ?>
 <input type="hidden" name="action" value="<?php echo $action; ?>">
 <?php endif; ?>
 <?php if (isset($pa)): ?>
 <input type="hidden" name="pid" value="<?php echo $pa[0]->id; ?>">
 <?php endif; ?>
 <?php if (isset($mono)): ?>
 <input type="hidden" name="mid" value="<?php echo $mono[0]->id; ?>">
 <?php endif; ?>
              <table width="100%" border="0" cellpadding="6" cellspacing="0">
                <tr>
                  <td width="22%" valign="top" class="vncellreq">Interface</td>
                  <td width="78%" class="vtable">
                  <select name="interface" class="formfld">
<?php $interfaces = array('wan' => 'WAN', 'lan' => 'LAN', 'pptp' => 'PPTP');
         foreach($mono[0]->ifaces as $if) { if ($if->type == "opt") $interfaces[$if->type.$if->num] = $if->description; }
         foreach ($interfaces as $iface => $ifacename): ?>
           <option value="<?php echo $iface;?>" <?php if ($iface == $pa[0]->if) echo "selected"; ?>>
            <?php echo htmlspecialchars($ifacename);?>
           </option>
<?php endforeach; ?>
                    </select> </td>
                </tr>
                <tr>
                  <td valign="top" class="vncellreq">Network</td>
                  <td class="vtable">
                    <table border="0" cellspacing="0" cellpadding="0">
                      <tr>
                        <td>Type:&nbsp;&nbsp;</td>
                                                <td></td>
<?php
 $ar = explode('/', $pa[0]->network);
 $address = $ar[0];
 $subnet = $ar[1];
?>
                        <td><select name="type" class="formfld" onChange="typesel_change()">
                            <option value="single" <?php if (empty($pa[0]->from) && $subnet == 32) echo "selected"; ?>>
                            Single address</option>
                            <option value="network" <?php if (empty($pa[0]->from) && $subnet != 32) echo "selected"; ?>>
                            Network</option>
                            <option value="range" <?php if (!empty($pa[0]->from)) echo "selected"; ?>>
                            Range</option>
                          </select></td>
                      </tr>
                      <tr>
                        <td>Address:&nbsp;&nbsp;</td>
                                                <td></td>
                        <td><input name="subnet" type="text" class="formfld" id="subnet" size="20" value="<?php echo htmlspecialchars($address);?>">
                  /
                          <select name="subnet_bits" class="formfld" id="select">
                            <?php for ($i = 31; $i >= 0; $i--): ?>
                            <option value="<?php echo $i;?>" <?php if ($i == $subnet) echo "selected"; ?>>
                            <?php echo $i;?>
                      </option>
                            <?php endfor; ?>
                      </select>
 </td>
                      </tr>
                      <tr>
                        <td>Range:&nbsp;&nbsp;</td>
                                                <td></td>
                        <td><input name="range_from" type="text" class="formfld" id="range_from" size="20" value="<?php echo htmlspecialchars($pa[0]->from);?>">
-
                          <input name="range_to" type="text" class="formfld" id="range_to" size="20" value="<?php echo htmlspecialchars($pa[0]->to);?>">
                          </td>
                      </tr>
                    </table>
                  </td>
                </tr>
                                <tr>
                  <td width="22%" valign="top" class="vncell">Description</td>
                  <td width="78%" class="vtable">
                    <input name="descr" type="text" class="formfld" id="descr" size="40" value="<?php echo htmlspecialchars($pa[0]->description);?>">
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
 </form>
<script language="JavaScript">
<!--
typesel_change();
//-->
</script>
<br/><br/>

<?php if (isset($link)): ?>
<a href="<?php echo $link["href"]; ?>"><?php echo $link["label"]; ?></a><br/>
<?php endif; ?>
