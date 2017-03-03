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
<form action="import.php" method="post">
<input type="hidden" value="<?php echo $action; ?>" name="action">
<table width="100%" border="0" cellpadding="6" cellspacing="0">
<tr>
 <td width="22%" valign="top" class="vncellreq">Hostname</td>
 <td width="78%" class="vtable"><input name="hostname" type="text" class="formfld" id="hostname" size="40" value="">
 <br> <span class="vexpl">name of the firewall host, without
  domain part<br>
  e.g. <em>firewall</em></span></td>
</tr>
<tr>
 <td width="22%" valign="top" class="vncellreq">Domain</td>
 <td width="78%" class="vtable"><input name="domain" type="text" class="formfld" id="domain" size="40" value="">
  <br> <span class="vexpl">e.g. <em>mycorp.com</em> </span></td>
</tr>
<tr>
 <td width="22%" valign="top" class="vncellreq">IP Address</td>
 <td width="78%" class="vtable"><input name="ip" type="text" class="formfld" id="ip" size="40" value="">
  <br> <span class="vexpl">IP Address to use if the DNS is not resolvable</span>
  <br/>
  <input name="use_ip" type="checkbox" id="use_ip" value="1" >
  <strong>Use ip address to backup/restore configuration of monowall device</strong>
 </td>
</tr>
<tr>
 <td width="22%" valign="top" class="vncellreq">Port</td>
 <td width="78%" class="vtable"><input name="port" type="text" class="formfld" id="port" size="10" value="443">
  <br> <span class="vexpl">Port to use to connect to monowwall device</span>
  <br/>
  <input name="https" type="checkbox" id="https" value="1" checked>
  <strong>Use HTTPS</strong>
 </td>
</tr>

  <tr>
   <td width="22%" valign="top" class="vncellreq">Backup user</td>
   <td width="78%" class="vtable"><select name="buser" id="buser">
   <?php
     foreach ($busers as $buser) {
   ?>
       <option value="<?php echo $buser->id; ?>"><?php echo $buser->login; ?></option>
   <?php
     }
    ?>
     </select>
     <br/> <span class="vexpl">Select the backup user to use together with this monowall<br/></span></td>
  </tr>
 <tr>
  <td width="22%" valign="top">&nbsp;</td>
  <td width="78%"> <input name="Submit" type="submit" class="formbtn" value="Import">
  </td>
 </tr>
</table>
</form>


<br/><br/>

<?php if (isset($link)): ?>
<a href="<?php echo $link["href"]; ?>"><?php echo $link["label"]; ?></a><br/>
<?php endif; ?>
