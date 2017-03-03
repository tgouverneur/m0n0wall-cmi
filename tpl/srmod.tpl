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
<form action="srmod.php" method="post" name="iform" id="iform">

 <?php if (isset($action)): ?>
 <input type="hidden" name="action" value="<?php echo $action; ?>">
 <?php endif; ?>
 <?php if (isset($sroute)): ?>
 <input type="hidden" name="rid" value="<?php echo $ro[0]->id; ?>">
 <?php endif; ?>
 <?php if (isset($mono)): ?>
 <input type="hidden" name="mid" value="<?php echo $mono[0]->id; ?>">
 <?php endif; ?>
              <table width="100%" border="0" cellpadding="6" cellspacing="0">
                <tr>
                  <td width="22%" valign="top" class="vncellreq">Interface</td>
                  <td width="78%" class="vtable">
                                        <select name="interface" class="formfld">
   <?php $interfaces = array('wan' => 'WAN', 'lan' => 'LAN', 'pptp' => 'PPTP');
         foreach($mono[0]->ifaces as $if) { if ($if->type == "opt") $interfaces[$if->type.$if->num] = $if->description; }
         foreach ($interfaces as $iface => $ifacename): ?>
           <option value="<?php echo $iface;?>" <?php if ($iface == $ro[0]->if) echo "selected"; ?>>
            <?php echo htmlspecialchars($ifacename);?>
           </option>
       <?php endforeach; ?>
                    </select> <br>
                    <span class="vexpl">Choose which interface this route applies to.</span></td>
                </tr>
                <tr>
                  <td width="22%" valign="top" class="vncellreq">Destination network</td>
                  <td width="78%" class="vtable">
<?php
 if (isset($ro[0])) {
   if ($ro[0]->network == "") $ro[0]->network = "/32";
   $netw = explode('/', $ro[0]->network);
   $net = $netw[0];
   $sub = $netw[1];
 }
 else { $net = ""; $sub = 32; }
?>
                    <input name="network" type="text" class="formfld" id="network" size="20" value="<?php echo htmlspecialchars($net);?>">
                                  /
                    <select name="network_subnet" class="formfld" id="network_subnet">
                      <?php for ($i = 32; $i >= 1; $i--): ?>
                      <option value="<?php echo $i;?>" <?php if ($i == $sub) echo "selected"; ?>>
                      <?php echo $i;?>
                      </option>
                      <?php endfor; ?>
                    </select>
                    <br> <span class="vexpl">Destination network for this static route</span></td>
                </tr>
                                <tr>
                  <td width="22%" valign="top" class="vncellreq">Gateway</td>
                  <td width="78%" class="vtable">
                    <input name="gateway" type="text" class="formfld" id="gateway" size="40" value="<?php echo htmlspecialchars($ro[0]->gateway);?>">
                    <br> <span class="vexpl">Gateway to be used to reach the destination network</span></td>
                </tr>
                                <tr>
                  <td width="22%" valign="top" class="vncell">Description</td>
                  <td width="78%" class="vtable">
                    <input name="descr" type="text" class="formfld" id="descr" size="40" value="<?php echo htmlspecialchars($ro[0]->description);?>">
                    <br> <span class="vexpl">You may enter a description here
                    for your reference (not parsed).</span></td>
                </tr>
                <tr>
                  <td width="22%" valign="top">&nbsp;</td>
                  <td width="78%">
                    <input name="Submit" type="submit" class="formbtn" value="Save">
                  </td>
                </tr>
              </table>
 </form>

<br/><br/>

<?php if (isset($link)): ?>
<a href="<?php echo $link["href"]; ?>"><?php echo $link["label"]; ?></a><br/>
<?php endif; ?>
