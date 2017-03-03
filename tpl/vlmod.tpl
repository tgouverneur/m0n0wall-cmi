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
<form action="vlmod.php" method="post" name="iform" id="iform">

 <?php if (isset($action)): ?>
 <input type="hidden" name="action" value="<?php echo $action; ?>">
 <?php endif; ?>
 <?php if (isset($vlan)): ?>
 <input type="hidden" name="vid" value="<?php echo $vlan[0]->id; ?>">
 <?php endif; ?>
 <?php if (isset($mono)): ?>
 <input type="hidden" name="mid" value="<?php echo $mono[0]->id; ?>">
 <?php endif; ?>
              <table width="100%" border="0" cellpadding="6" cellspacing="0">
                                <tr>
                  <td width="22%" valign="top" class="vncellreq">Parent interface</td>
                  <td width="78%" class="vtable">
                    <select name="if" class="formfld">
                      <?php
                                          foreach ($mono[0]->hwInt as $if): ?>
                      <option value="<?php echo $if->id;?>" <?php if ($vlan[0]->if == $if->name) echo "selected"; ?>>
                      <?php echo htmlspecialchars($if->name . " ($if->mac)");?>
                      </option>
                      <?php endforeach; ?>
                    </select></td>
                </tr>
                                <tr>
                  <td valign="top" class="vncellreq">VLAN tag </td>
                  <td class="vtable">
                    <?php echo $mandfldhtml;?><input name="tag" type="text" class="formfld" id="tag" size="6" value="<?php echo htmlspecialchars($vlan[0]->tag);?>">
                    <br>
                    <span class="vexpl">802.1Q VLAN tag (between 1 and 4094) </span></td>
                            </tr>
                                <tr>
                  <td width="22%" valign="top" class="vncell">Description</td>
                  <td width="78%" class="vtable">
                   <input name="descr" type="text" class="formfld" id="descr" size="40" value="<?php echo htmlspecialchars($vlan[0]->description);?>">
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

<br/><br/>

<?php if (isset($link)): ?>
<a href="<?php echo $link["href"]; ?>"><?php echo $link["label"]; ?></a><br/>
<?php endif; ?>
