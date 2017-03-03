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
<?php

?>
	<strong>Menu</strong><br/>
	<a href="javascript:showhide('General','tri_General')">
		<img src="./img/tri_o.gif" id="tri_General" width="14" height="10" border="0">
	</a>

	<strong>
	<a href="javascript:showhide('diag','tri_General')" class="navlnk">General</a>
	</strong><br>
	<span id="General">
	  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="index.php" class="navlnk">Main Page</a><br>
	  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="stats.php" class="navlnk">Statistics</a><br>
	  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="busers.php" class="navlnk">Backup users</a><br>
        </span>

	<a href="javascript:showhide('network','tri_m0n0wall')">
		<img src="./img/tri_o.gif" id="tri_m0n0wall" width="14" height="10" border="0">
	</a>

	<strong>
	<a href="javascript:showhide('diag','tri_m0n0wall')" class="navlnk">m0n0wall</a>
	</strong><br>
	<span id="network">
	  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="users.php" class="navlnk">Users</a><br>
	  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="groups.php" class="navlnk">Groups</a><br>
	  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="galiases.php" class="navlnk">Global Aliases</a><br>
	  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="viewaliases.php" class="navlnk">Local Aliases</a><br>
	  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="interfaces.php" class="navlnk">Interfaces</a><br>
	  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="viewfw.php" class="navlnk">FW Rules</a><br>
	  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="viewnat.php" class="navlnk">NAT Rules</a><br>
	  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="proxyarp.php" class="navlnk">Proxy ARP</a><br>
	  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="sroutes.php" class="navlnk">Static Routes</a><br>
	  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="vlans.php" class="navlnk">VLANs</a><br>
	  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="syslog.php" class="navlnk">Syslog</a><br>
	  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="snmp.php" class="navlnk">SNMP</a><br>
        </span>

	<a href="javascript:showhide('others','tri_others')">
		<img src="./img/tri_o.gif" id="tri_others" width="14" height="10" border="0">
	</a>

	<strong>
	<a href="javascript:showhide('diag','tri_others')" class="navlnk">Others</a>
	</strong><br>
	<span id="others">
	  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="dumpxml.php" class="navlnk">Dump XML</a><br>
	  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="save2mono.php" class="navlnk">Save To m0n0wall</a><br>
	  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="import.php" class="navlnk">Import new m0n0wall</a><br>
	  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="importxml.php" class="navlnk">Import from XML</a><br>
	  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="http://m0n0wall-cmi.sourceforge.net/docs/" class="navlnk">Documentation</a><br>
        </span>

  </font></span>
            </td>
        </tr>
	<tr><td>
	<hr/>
	<center><span class="navlnk"><font color="#FFFFFF">v<?php echo $config['version']; ?></font></span></center>
	</td></tr>
	</table></td>
    <td width="600"><table width="100%" border="0" cellpadding="10" cellspacing="0">
        <tr><td>
