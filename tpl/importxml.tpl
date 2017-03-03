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
 <form action="importxml.php" method="post">

 <table width="100%" border="0" cellpadding="6" cellspacing="0">
 <tr>
  <td width="22%" valign="top" class="vncellreq">Hostname</td>
  <td width="78%" class="vtable"><input name="hostname" type="text" class="formfld" id="hostname" size="40" value="">
  <br/> <span class="vexpl">Enter hostname for the m0n0wall, left empty to keep the original.<br/></span></td>
 </tr>
 <tr>
  <td width="22%" valign="top" class="vncellreq">Domain</td>
  <td width="78%" class="vtable"><input name="domain" type="text" class="formfld" id="domain" size="40" value="">
  <br/> <span class="vexpl">Enter hostname for the m0n0wall, left empty to keep the original.<br/></span></td>
 </tr>
 <tr>
  <td width="22%" valign="top" class="vncellreq">XML</td>
  <td width="78%" class="vtable"><textarea name="xml" class="formfld" rows="50" cols="60"></textarea>
  <br/> <span class="vexpl">Content of the XML to be imported<br/></span></td>
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
