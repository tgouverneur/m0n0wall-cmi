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
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title><?php echo $html["title"]; ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="./css/gui.css" rel="stylesheet" type="text/css">
</head>

<body link="#0000CC" vlink="#0000CC" alink="#0000CC">
<script language="javascript">
<!--
var tri_open = "";
var tri_closed = "";

window.onload = preload;

function preload() {
	if (document.images) {
		tri_open = new Image(14,10);
		tri_closed = new Image(14,10);
		tri_open.src = "./img/tri_o.gif";
		tri_closed.src = "./img/tri_c.gif";
	}
}

function showhide(tspan, tri) {
	tspanel = document.getElementById(tspan);
	triel = document.getElementById(tri);
	if (tspanel.style.display == 'none') {
		tspanel.style.display = '';
		triel.src = "./img/tri_o.gif";
	} else {
		tspanel.style.display = 'none';
		triel.src = "./img/tri_c.gif";
	}
}
-->
</script>
<table width="90%" border="0" cellspacing="0" cellpadding="2">
  <tr valign="bottom"> 
    <td width="20%" height="65" align="center" valign="middle"> <strong><a href="http://m0n0.ch/wall" target="_blank"><img src="./img/logo.gif" width="150" height="47" border="0"></a></strong></td>
    <td height="80%" bgcolor="#435370">
	<table border="0" cellspacing="0" cellpadding="0" width="100%">
	<tr><td align="left" valign="bottom"><span class="tfrtitle">&nbsp;Central Management Interface
	</span></td>
	  <td align="right" valign="bottom">
	  <span class="hostname"><?php echo $html["pagen"]; ?>&nbsp;</span>
	  </td></tr></table>
	</td>
  </tr>
  <tr valign="top"> 
    <td width="150" bgcolor="#9D9D9D">
	<table width="100%" border="0" cellpadding="6" cellspacing="0">
        <tr>
          <td><span class="navlnk"><font color="#FFFFFF"> 

