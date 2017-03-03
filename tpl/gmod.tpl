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
<form action="gmod.php" method="post" name="iform" id="iform">

 <?php if (isset($action)): ?>
 <input type="hidden" name="action" value="<?php echo $action; ?>">
 <?php endif; ?>
 <?php if (isset($group)): ?>
 <input type="hidden" name="gid" value="<?php echo $group[0]->id; ?>">
 <?php endif; ?>
 <?php if (isset($mono)): ?>
 <input type="hidden" name="mid" value="<?php echo $mono[0]->id; ?>">
 <?php endif; ?>
 <table width="100%" border="0" cellpadding="6" cellspacing="0">
   <tr>
     <td width="22%" valign="top" class="vncellreq">Group name</td>
     <td width="78%" class="vtable">
      <input name="groupname" type="text" class="formfld" id="groupname" size="20" value="<?php echo $group[0]->name; ?>">
     </td>
   </tr>
   <tr>
     <td width="22%" valign="top" class="vncell">Description</td>
     <td width="78%" class="vtable">
       <input name="description" type="text" class="formfld" id="description" size="20" value="<?php echo $group[0]->description; ?>">
       <br>
       Group description, for your own information only</td>
     </tr>
     <tr>
       <td colspan="4"><br>&nbsp;Select that pages that this group may access.  Members of this group will be able to perform all actions that<br>&nbsp; are possible from each individual web page.  Ensure you set access levels appropriately.<br><br>
       <span class="vexpl"><span class="red"><strong>&nbsp;Note: </strong></span>Pages
       marked with an * are strongly recommended for every group.</span>
       </td>
     </tr>
     <tr>
       <td colspan="2">
       <table width="100%" border="0" cellpadding="0" cellspacing="0">
        <tr>
          <td class="listhdrr">&nbsp;</td>
          <td class="listhdrr">Page</td>
        </tr>
        <?php
        $pages = explode (';',$group[0]->pages);
              foreach ($lpages as $fname => $title) {
                $identifier = str_replace('.php','',$fname);
                ?>
                <tr><td class="listlr">
                <input name="<?php echo $identifier; ?>" type="checkbox" id="<?php echo $identifier; ?>" value="yes" <?php if (in_array($fname,$pages)) echo "checked"; ?>></td>
                <td class="listr"><?php echo $title; ?></td>
                </tr>
                <?
              } ?>
              </table>
              </td>
            </tr>
            <tr>
              <td width="22%" valign="top">&nbsp;</td>
              <td width="78%">
                <input name="save" type="submit" class="formbtn" value="Save">
                <input name="mid" type="hidden" value="<?php echo $mono[0]->id; ?>">
                <?php if (isset($group) && $group[0]->id) { ?>
                   <input name="gid" type="hidden" value="<?php echo $group[0]->id;?>">
                <?php } ?>
                <?php if (isset($action)) { ?>
                   <input name="action" type="hidden" value="<?php echo $action; ?>
		<?php } ?>
              </td>
            </tr>
          </table>
 </form>

<br/><br/>

<?php if (isset($link)): ?>
<a href="<?php echo $link["href"]; ?>"><?php echo $link["label"]; ?></a><br/>
<?php endif; ?>
