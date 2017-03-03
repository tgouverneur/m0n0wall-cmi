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

<?php if (isset($message)): ?>
<table width="100%" border="0" cellpadding=0" cellspacing="0">
 <tr>
  <td>
   <?php echo $message; ?>
  </td>
 </tr>
</table>
<?php endif; ?>

<br/><br/>

<?php if (isset($list) && is_array($list)): ?>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
 <tr id="frheader">
 <?php   
         foreach ($list[0] as $col): 
 ?>
  <td class="listhdrr"><center><?php echo $col; ?></center></td>
 <?php   
         endforeach; 
	 unset($list[0]); 
 ?>
 </tr>
<?php    
	foreach ($list as $entry) { 
	 echo "<tr>";
 	 $i=0;
	 foreach ($entry as $col) {
	
?>
  <td class="<?php echo ($i)?"listr":"listlr"; ?>"><center>
  <?php 
	$this->parseArray($col);
  ?>
  </center></td>
<?php    
	 $i++;
	 }
 	 echo "</tr>";
       }
?>
</table>
<?php endif; ?>

<br/><br/>

<?php 
 if (isset($link)) {
   $this->parseArray($link);
 }
?>
