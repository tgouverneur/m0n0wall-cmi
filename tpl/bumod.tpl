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

 <form action="bumod.php" method="post">

 <?php if (isset($action)): ?>
 <input type="hidden" name="action" value="<?php echo $action; ?>">
 <?php endif; ?>
 <?php if (isset($buser[0]->id)): ?>
 <input type="hidden" name="bid" value="<?php echo $buser[0]->id; ?>">
 <?php endif; ?>

 <table width="100%" border="0" cellpadding="6" cellspacing="0">
 <tr>
  <td width="22%" valign="top" class="vncellreq">Username</td>
  <td width="78%" class="vtable"><input name="login" type="text" class="formfld" id="username" size="40" value="<?php echo $buser[0]->login; ?>">
  <br/> <span class="vexpl">Login of the backup user to get/put m0n0wall's configuration<br/></span></td>
 </tr>
 <tr>
  <td width="22%" valign="top" class="vncellreq">Password</td>
  <td width="78%" class="vtable"><input name="password" type="password" class="formfld" id="password" size="40" value="">
  <br/> <span class="vexpl">Password of the backup user (let empty not to change it)<br/></span></td>
 </tr>
 <tr>
  <td width="22%" valign="top" class="vncellreq">Description</td>
  <td width="78%" class="vtable"><textarea name="description" class="formfld" id="description" size="40"><?php echo $buser[0]->description; ?></textarea>
  <br/><span class="vexpl">Description for the backup user<br/></span></td>
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
