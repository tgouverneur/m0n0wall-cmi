<?php
  $html["title"] = "m0n0wall Central Management Interface - Installation";
  $html["pagen"] = "Installation";
  require_once("../inc/config.inc.php");
  require_once("../html/head.html.php");
?>
  </tr></table></td>
  <td width="600"><table width="100%" border="0" cellpadding="10" cellspacing="0">
  <tr><td>
<?php
  if (isset($config['installed']) && $config['installed'] == TRUE) {
    die("Nothing to do here");
  } else {
  $error = 0;
?>
<p class="pgtitle">m0n0wall-CMI : Installation</p>
<h2>Requirements</h2>
<ul>
<?php
 /* write permission into tmp/ dir */
 if (($fp = @fopen("../tmp/config.xml", "w")) === FALSE) {
   ?><li><span class="redcolor">ERROR</span>: Cannot write in tmp/ directory.</li><?php
 } else {
   ?><li><span class="greencolor">OK</span>: write permission in tmp/ are correct.</li><?php
   fclose($fp);
   unlink("../tmp/config.xml");
 }
 /* access to ../inc/config.inc.php with write permission */
 if (($fp = @fopen("../inc/config.inc.php", "a")) === FALSE) {
   ?><li><span class="redcolor">ERROR</span>: Unable to open inc/config.inc.php for writting, please check file permission.</li><?php
   $error++;
 } else {
   fclose($fp);
   ?><li><span class="greencolor">OK</span>: correct inc/config.inc.php permissions.</li><?php
 }
 /* CURL extension available */
 if (@function_exists("curl_init")) { 
   ?><li><span class="greencolor">OK</span>: CURL available</li><?php
 } else {
   $error++;
   ?><li><span class="redcolor">ERROR</span>: No CURL extensions detected</li><?php
 }

 /* need php 5 to get all OO things */
 if (version_compare(phpversion(), "5.0.0", "gt")) {
   ?><li><span class="greencolor">OK</span>: version of php correct (<?php echo phpversion(); ?>)</li><?php
 } else {
   $error++;
   ?><li><span class="redcolor">ERROR</span>: version of php incorrect (<?php echo phpversion(); ?>). Needed: > 5.0.0</li><?php
 }
?>
</ul>
<br/><br/>
<?php if (!$error) { ?>
<h2>Database setup</h2>
<form action="install2.php" method="post">
<table width="100%" border="0" cellpadding="6" cellspacing="0">
<tr> 
 <td width="22%" valign="top" class="vncellreq">DB hostname</td>
 <td width="78%" class="vtable"><input name="dbhostname" type="text" class="formfld" id="dbhostname" size="40" value=""> 
 <br> <span class="vexpl">Hostname of the database server<br>
  </span></td>
</tr>
<tr> 
 <td width="22%" valign="top" class="vncellreq">DB name</td>
 <td width="78%" class="vtable"><input name="dbname" type="text" class="formfld" id="dbname" size="40" value=""> 
 <br> <span class="vexpl">Name of the database to use<br>
  </span></td>
</tr>
<tr> 
 <td width="22%" valign="top" class="vncellreq">Username</td>
 <td width="78%" class="vtable"><input name="username" type="text" class="formfld" id="username" size="40" value=""> 
 <br> <span class="vexpl">Username used to connect to DB<br>
  </span></td>
</tr>
<tr> 
 <td width="22%" valign="top" class="vncellreq">Password</td>
 <td width="78%" class="vtable"><input name="password" type="password" class="formfld" id="password" size="40" value=""> 
 <br> <span class="vexpl">Password used to connect to DB<br>
  </span></td>
</tr>
 <tr> 
  <td width="22%" valign="top">&nbsp;</td>

  <td width="78%"> <input name="Submit" type="submit" class="formbtn" value="Install!"> 
  </td>
 </tr>
</table>
</form>

<?php
  } else echo "Please fix requirements before installation...<br/>";
  }
  require_once("../html/foot.html.php");
?>
