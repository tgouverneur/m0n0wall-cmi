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
<?php endif; ?>

<br/><br/>

<?php if (isset($list) && isset($select)) { ?>

<form action="<?php echo $action; ?>" method="post">
<?php if (isset($hidden)) {
 foreach ($hidden as $hf) {
  echo "<input type=\"hidden\" name=\"".$hf["name"]."\" value=\"".$hf["value"]."\">";
 }
}
?>
<table width="100%" border="0" cellpadding="6" cellspacing="0">
 <tr>
  <td width="22%" valign="top" class="vncellreq"><?php echo $select["title"]; ?></td>
  <td width="78%" class="vtable"><select name="<?php echo $select["name"]; ?>">
   <?php foreach ($list as $item): ?>
   <option value="<?php echo $item["value"]; ?>"><?php echo $item["label"]; ?></option>
   <?php endforeach; ?>
     </select><br><span class="vexpl"><?php echo $select["desc"]; ?><br/></span></td>
 </tr>
 <tr>
  <td width="22%" valign="top">&nbsp;</td>
  <td width="78%"> <input name="Submit" type="submit" class="formbtn" value="Continue">

  </td>
 </tr>
</table>
</form>
<?php } ?>

<br/><br/>

<?php if (isset($link)): ?>
<a href="<?php echo $link["href"]; ?>"><?php echo $link["label"]; ?></a><br/>
<?php endif; ?>
