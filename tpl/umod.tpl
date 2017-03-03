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
<form action="umod.php" method="post" name="iform" id="iform">

 <?php if (isset($action)): ?>
 <input type="hidden" name="action" value="<?php echo $action; ?>">
 <?php endif; ?>
 <?php if (isset($user)): ?>
 <input type="hidden" name="uid" value="<?php echo $user[0]->id; ?>">
 <?php endif; ?>
 <?php if (isset($mono)): ?>
 <input type="hidden" name="mid" value="<?php echo $mono[0]->id; ?>">
 <?php endif; ?>
   <table width="100%" border="0" cellpadding="6" cellspacing="0">
  <tr>
   <td width="22%" valign="top" class="vncellreq">Username</td>
   <td width="78%" class="vtable">
        <input name="username" type="text" class="formfld" id="username" size="20" value="<?php echo $user[0]->name; ?>">
   </td>
  </tr>
  <tr>
   <td width="22%" valign="top" class="vncellreq">Password</td>
   <td width="78%" class="vtable">
   <input name="password" type="password" class="formfld" id="password" size="20" value=""> <br>
   <input name="password2" type="password" class="formfld" id="password2" size="20" value="">
&nbsp;(confirmation)                                    </td>
  </tr>
  <tr>
   <td width="22%" valign="top" class="vncell">Full name</td>
   <td width="78%" class="vtable">
   <input name="fullname" type="text" class="formfld" id="fullname" size="20" value="<?php echo $user[0]->fullname;?>">
    <br>
    User's full name, for your own information only</td>
  </tr>
  <tr>
   <td width="22%" valign="top" class="vncell">Group Name</td>
   <td width="78%" class="vtable">
    <select name="groupname" class="formfld" id="groupname">
<?php foreach($mono[0]->group as $grp) { ?>
     <option value="<?php echo $grp->id; ?>"<?php if ($grp->name == $user[0]->groupname) echo "selected"; ?>><?php echo $grp->name; ?></option>
<?php } ?>
    </select>
      <br>
      The admin group to which this user is assigned.</td>
  </tr>
  <tr>
   <td width="22%" valign="top">&nbsp;</td>
   <td width="78%">
<?php if (isset($action)) { ?>
   <input name="action" type="hidden" value="<?php echo $action; ?>">
<?php } ?>
   <input name="save" type="submit" class="formbtn" value="Save">
   </td>
  </tr>
 </table>

 </form>

<br/><br/>

<?php if (isset($link)): ?>
<a href="<?php echo $link["href"]; ?>"><?php echo $link["label"]; ?></a><br/>
<?php endif; ?>
