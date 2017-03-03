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
<script language="JavaScript">
<!--
function typesel_change() {
        switch (document.iform.type.selectedIndex) {
                case 0: /* host */
                        document.iform.address_subnet.disabled = 1;
                        document.iform.address_subnet.value = "";
                        break;
                case 1: /* network */
                        document.iform.address_subnet.disabled = 0;
                        break;
        }
}
//-->
</script>
<form action="galiasmod.php" method="post" name="iform" id="iform">

 <?php if (isset($action)): ?>
 <input type="hidden" name="action" value="<?php echo $action; ?>">
 <?php endif; ?>
 <?php if (isset($alias)): ?>
 <input type="hidden" name="gaid" value="<?php echo $alias[0]->id; ?>">
 <?php endif; ?>

<table width="100%" border="0" cellpadding="6" cellspacing="0">
<tr>
 <td valign="top" class="vncellreq">Name</td>
 <td class="vtable"><input name="name" type="text" class="formfld" id="name" size="40" value="<?php echo $alias[0]->name; ?>">
 <br> <span class="vexpl">The name of the alias may only consist
 of the characters a-z, A-Z and 0-9.</span></td>
</tr>
<tr>
 <td valign="top" class="vncellreq">Type</td>
 <td class="vtable">
  <select name="type" class="formfld" id="type" onChange="typesel_change()">
   <option value="host" <?php if (!strpos($alias[0]->address, "/")) echo "selected"; ?>>Host</option>
   <option value="network" <?php if (strpos($alias[0]->address, "/")) echo "selected"; ?>>Network</option>
  </select>
 </td>
</tr>
<tr>
 <td width="22%" valign="top" class="vncellreq">Address</td>
<?php if (strpos($alias[0]->address, "/")) { $a = explode('/', $alias[0]->address); $addr = $a[0]; $net = $a[1]; }
      else { $addr = $alias[0]->address; $net = 0; } ?>
 <td width="78%" class="vtable"><input name="address" type="text" class="formfld" id="address" size="20" value="<? echo $addr;?>">
 /
 <select name="address_subnet" class="formfld" id="address_subnet">
 <?php for ($i = 32; $i >= 1; $i--): ?>
 <option value="<?php echo $i; ?>" <?php if ($i == $net) echo "selected"; ?>>
 <?php echo $i; ?>
 </option>
 <?php endfor; ?>
 </select> <br> <span class="vexpl">The address that this alias
 represents.</span></td>
</tr>
<tr>
 <td width="22%" valign="top" class="vncell">Description</td>
 <td width="78%" class="vtable"> <input name="descr" type="text" class="formfld" id="descr" size="40" value="<? echo $alias[0]->description;?>">
 <br> <span class="vexpl">You may enter a description here
 for your reference (not parsed).</span></td>
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
