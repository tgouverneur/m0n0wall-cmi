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
 require_once("./lib/autoload.lib.php");
 require_once("./lib/html.lib.php");

 /* sanitize _GET and _POST */
 sanitizeArray($_GET);
 sanitizeArray($_POST);

 /* connect to the database: */
 if (!Mysql::getInstance()->connect())
 { echo "Cannot connect to mysql database<br/>\n"; die(); }

 if (isset($_GET["mid"])) $mid = $_GET["mid"];
 if (isset($_POST["mid"])) $mid = $_POST["mid"];
 if (isset($_GET["type"])) $type = $_GET["type"];
 if (isset($_POST["type"])) $type = $_POST["type"];
 if (!isset($type)) $type = 1;

 $main = Main::getInstance();
 $main->fetchMonoId();
 $main->fetchMonoDetails();

 if (isset($mid) && $mid) {
  $mono = new Monowall($mid);
  $mono->fetchFromId();
  $mono->fetchIfaces();
  $mono->fetchIfacesDetails();
  $mono->fetchAllNat();
  $mono->fetchAllNatDetails();
  if (isset($_POST["action"]) && $_POST["action"] = "advout") { 
   if (isset($_POST["enable"]) && $_POST["enable"] == "yes") {
     if ($mono->enablednat == 0) {
       $mono->enablednat = 1;
       $mono->update();
     }
   } else if (!isset($_POST["enable"]) || $_POST["enable"] != "yes") {
     if ($mono->enablednat == 1) {
       $mono->enablednat = 0;
       $mono->update();
     }
   }
  }
 } 
?>
<p class="pgtitle">NAT: View</p>
<?php
  if (!isset($mono)) {
?>
<form action="viewnat.php" method="post">
<table width="100%" border="0" cellpadding="6" cellspacing="0">
 <tr>
  <td width="22%" valign="top" class="vncellreq">m0n0wall</td>
  <td width="78%" class="vtable"><select name="mid">
   <?php
     foreach ($main->monowall as $mono) {
      ?><option value="<?php echo $mono->id; ?>"><?php echo $mono->hostname.".".$mono->domain; ?></option>
   <?php }
   ?>
  </select><br><span class="vexpl">Select the m0n0wall which you want to manage NAT rules<br/></span></td>
 </tr>
 <tr>
  <td width="22%" valign="top">&nbsp;</td>
  <td width="78%"> <input name="Submit" type="submit" class="formbtn" value="View NAT">
  </td>
 </tr>
</table>
</form>
<?php } else { ?>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr><td class="tabnavtbl">
  <ul id="tabnav">
<?php
   	$tabs = array('Inbound' => '1',
           		  'Server NAT' => '2',
           		  '1:1' => '3',
           		  'Outbound' => '4');
        foreach($tabs as $desc => $link) {
          if ($link == $type) { 
            echo '<li class="tabact">'.$desc.'</li>';
          } else
            echo '<li class="tabinact"><a href="?mid='.$mid.'&type='.$link.'">'.$desc.'</a></li>';
        }
?>
  </ul>
  </td></tr>
  <tr> 
    <td class="tabcont">
<?php
switch($type) {
  case 1: /* NAT */ ?>
              <table width="100%" border="0" cellpadding="0" cellspacing="0">
                <tr> 
                  <td width="5%" class="listhdrr">If</td>
                  <td width="5%" class="listhdrr">Proto</td>
                  <td width="20%" class="listhdrr">Ext. port range</td>
                  <td width="20%" class="listhdrr">NAT IP</td>
                  <td width="20%" class="listhdrr">Int. port range</td>
                  <td width="20%" class="listhdr">Description</td>
                  <td width="5%" class="list"></td>
				</tr>
			  <?php $i = 0; foreach ($mono->nat as $enat): ?>
                <tr valign="top"> 
		  <td class="listlr">
                  <?php echo $enat->if;?>
                  </td>
                  <td class="listr"> 
                  <?php echo $enat->proto; ?>
                  </td>
                  <td class="listr">
                  <?php echo $enat->eport;?>
                  </td>
                  <td class="listr"> 
		  <?php echo $enat->target." (external: ".$enat->external.")"; ?>
                  </td>
                  <td class="listr"> 
		  <?php echo $enat->lport;?>
                  </td>
                  <td class="listbg"> 
		  <?php echo $enat->description;?>
                  </td>
                  <td valign="middle" class="list" nowrap> <a href="natmod.php?mid=<?php echo $mono->id;?>&type=<?php echo $type;?>&nid=<?php echo $enat->id;?>"><img src="img/e.gif" title="edit rule" width="17" height="17" border="0"></a>
                     &nbsp;<a href="natrm.php?mid=<?php echo $mono->id;?>&type=<?php echo $type;?>&nid=<?php echo $enat->id;?>" onclick="return confirm('Do you really want to delete this rule?')"><img src="img/x.gif" title="delete rule" width="17" height="17" border="0"></a></td>
				</tr>
			  <?php $i++; endforeach; ?>
                <tr> 
                  <td class="list" colspan="6"></td>
                  <td class="list"> <a href="natmod.php?mid=<?php echo $mono->id;?>&type=<?php echo $type;?>"><img src="img/plus.gif" title="add rule" width="17" height="17" border="0"></a></td>
				</tr>
              </table><br>
                    <span class="vexpl"><span class="red"><strong>Note:<br>
                      </strong></span>It is not possible to access NATed services 
                      using the WAN IP address from within LAN (or an optional 
                      network).</span></td>
  </tr>
</table>
            </form>
<?php
  break;
  case 2: /* Server NAT */ ?>
  <table width="80%" border="0" cellpadding="0" cellspacing="0">
                <tr> 
                  <td width="40%" class="listhdrr">External IP address</td>
                  <td width="50%" class="listhdr">Description</td>
                  <td width="10%" class="list"></td>
				</tr>
			  <?php $i = 0; foreach ($mono->srvnat as $snat): ?>
                <tr> 
                  <td class="listlr"> 
                    <?php echo $snat->ipaddr;?>
                  </td>
                  <td class="listbg"> 
                    <?php echo $snat->description;?>
                  </td>
                  <td class="list" nowrap> <a href="natmod.php?mid=<?php echo $mono->id;?>&type=<?php echo $type;?>&nid=<?php echo $snat->id;?>"><img src="img/e.gif" title="edit entry" width="17" height="17" border="0"></a>
                     &nbsp;<a href="natrm.php?mid=<?php echo $mono->id;?>&type=<?php echo $type;?>&nid=<?php echo $snat->id;?>" onclick="return confirm('Do you really want to delete this entry?')"><img src="img/x.gif" title="delete entry" width="17" height="17" border="0"></a></td>
				</tr>
			  <?php $i++; endforeach; ?>
                <tr> 
                  <td class="list" colspan="2"></td>
                  <td class="list"> <a href="natmod.php?mid=<?php echo $mono->id;?>&type=<?php echo $type;?>"><img src="img/plus.gif" title="add entry" width="17" height="17" border="0"></a></td>
				</tr>
              </table><br>
			        <span class="vexpl"><span class="red"><strong>Note:<br>
                      </strong></span>The external IP addresses defined on this page may be used in <a href="viewnat.php?type=1">inbound NAT</a> mappings. Depending on the way your WAN connection is setup, you may also need <a href="services_proxyarp.php">proxy ARP</a>.</span>
</td>
  </tr>
</table>
            </form>


<?php
  break;
  case 3: /* 1:1 NAT */ ?>
  <table width="100%" border="0" cellpadding="0" cellspacing="0">
                <tr> 
				  <td width="10%" class="listhdrr">Interface</td>
                  <td width="20%" class="listhdrr">External IP</td>
                  <td width="20%" class="listhdrr">Internal IP</td>
                  <td width="40%" class="listhdr">Description</td>
                  <td width="10%" class="list"></td>
				</tr>
			  <?php $i = 0; foreach ($mono->o2onat as $onat): ?>
                <tr> 
		  <td class="listlr">
		  <?php echo $onat->if;?>
                  </td>
                  <td class="listr"> 
		  <?php echo $onat->external;?><?php if ($onat->subnet) echo "/".$onat->subnet; ?>
                  </td>
                  <td class="listr"> 
		  <?php echo $onat->internal;?><?php if ($onat->subnet) echo "/".$onat->subnet; ?>
                  </td>
                  <td class="listbg"> 
		  <?php echo $onat->description;?>
                  </td>
                  <td class="list" nowrap> <a href="natmod.php?mid=<?php echo $mono->id;?>&type=<?php echo $type;?>&nid=<?php echo $onat->id;?>"><img src="img/e.gif" title="edit mapping" width="17" height="17" border="0"></a>
                     &nbsp;<a href="natrm.php?mid=<?php echo $mono->id;?>&type=<?php echo $type;?>&nid=<?php echo $snat->id;?>" onclick="return confirm('Do you really want to delete this mapping?')"><img src="img/x.gif" title="delete mapping" width="17" height="17" border="0"></a></td>
				</tr>
			  <?php $i++; endforeach; ?>
                <tr> 
                  <td class="list" colspan="4"></td>
                  <td class="list"> <a href="natmod.php?mid=<?php echo $mono->id;?>&type=<?php echo $type;?>"><img src="img/plus.gif" title="add mapping" width="17" height="17" border="0"></a></td>
				</tr>
              </table><br>
			  	<span class="vexpl"><span class="red"><strong>Note:<br>
                </strong></span>Depending on the way your WAN connection is setup, you may also need <a href="services_proxyarp.php">proxy ARP</a>.</span>
</td>
</tr>
</table>
</form>


<?php
  break;
  case 4: /* Adv Outbound */ ?>

<table width="100%" border="0" cellpadding="6" cellspacing="0">
		<form action="viewnat.php" method="POST">
                <tr> 
                  <td class="vtable">
                      <input name="enable" type="checkbox" id="enable" value="yes" <?php if ($mono->enablednat) echo "checked";?>>
                      <strong>Enable advanced outbound NAT</strong></td>
                </tr>
                <tr> 
		       <input name="mid" type="hidden" value="<?php echo $mono->id; ?>">
		       <input name="action" type="hidden" value="advout">
                  <td> <input name="submit" type="submit" class="formbtn" value="Save"> </form>
                  </td>
                </tr>
                <tr>
                  <td><p><span class="vexpl"><span class="red"><strong>Note:<br>
                      </strong></span>If advanced outbound NAT is enabled, no outbound NAT
                      rules will be automatically generated anymore. Instead, only the mappings
                      you specify below will be used. With advanced outbound NAT disabled,
                      a mapping is automatically created for each interface's subnet
                      (except WAN) and any mappings specified below will be ignored.</span>
                      If you use target addresses other than the WAN interface's IP address,
                      then depending on<span class="vexpl"> the way your WAN connection is setup,
                      you may also need <a href="proxyarp.php">proxy ARP</a>.</span><br>
                      <br>
                      You may enter your own mappings below.</p>
                    </td>
                </tr>
              </table>
              <table width="100%" border="0" cellpadding="0" cellspacing="0">
                <tr> 
                  <td width="10%" class="listhdrr">Interface</td>
                  <td width="20%" class="listhdrr">Source</td>
                  <td width="20%" class="listhdrr">Destination</td>
                  <td width="20%" class="listhdrr">Target</td>
                  <td width="25%" class="listhdr">Description</td>
                  <td width="5%" class="list"></td>
                </tr>
              <?php $i = 0; foreach ($mono->advnat as $anat): ?>
                <tr valign="top"> 
                  <td class="listlr">
		  <?php echo $anat->if;?>
                  </td>
                  <td class="listr"> 
		  <?php echo $anat->source;?>
                  </td>
                  <td class="listr"> 
		  <?php if($anat->dnot) echo "! "; ?><?php echo $anat->destination;?>
                  </td>
                  <td class="listr"> 
		  <?php echo $anat->target;?><?php
                      if ($anat->noportmap)
                          echo "<br>(no portmap)";
                    ?>
                  </td>
                  <td class="listbg"> 
		  <?php echo $anat->description;?>
                  </td>
                  <td class="list" nowrap> <a href="natmod.php?mid=<?php echo $mono->id;?>&type=<?php echo $type;?>&nid=<?php echo $anat->id;?>"><img src="img/e.gif" title="edit mapping" width="17" height="17" border="0"></a>
                     &nbsp;<a href="natrm.php?mid=<?php echo $mono->id;?>&type=<?php echo $type;?>&nid=<?php echo $anat->id;?>" onclick="return confirm('Do you really want to delete this mapping?')"><img src="img/x.gif" title="delete mapping" width="17" height="17" border="0"></a></td>
                </tr>
              <?php $i++; endforeach; ?>
                <tr> 
                  <td class="list" colspan="5"></td>
                  <td class="list"> <a href="natmod.php?mid=<?php echo $mono->id;?>&type=<?php echo $type;?>"><img src="img/plus.gif" title="add mapping" width="17" height="17" border="0"></a></td>
                </tr>
              </table>
</td>
  </tr>
</table>
            </form>


<?php
  break;
  default: /* Unknown */ ?>

  Error in nat Type...<br/>
<?php
  break;
 }
?>
<?php
 }

 /* disconnect database */
 Mysql::getInstance()->disconnect();
?>
