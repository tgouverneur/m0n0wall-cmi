<?php
 /**
  * Position management for RuleInt object
  *
  * @author Gouverneur Thomas <tgouverneur@be.tiauto.com>
  * @copyright Copyright (c) 2007-2008, Gouverneur Thomas - TI Automotive
  * @version 1.0
  * @package libs
  * @subpackage various
  * @category libs
  * @filesource
  * @todo code cleaning in this file, make an object with all this mess.
  */

function cmp_rulesint($a, $b) {
  if ($a->position == $b->position) return 0;
  else if ($a->position < $b->position) return -1;
  else if ($a->position > $b->position) return 1;
  /* could not happen ! */
}

function sort_rulesint(&$list) {
  usort($list, "cmp_rulesint");
}

function org_index(&$list) {

  $newlist = array();
  $i = 0;
  foreach ($list as $elem) {
   $newlist[$i++] = $elem;
  }
  $list = $newlist;
}

function add_ri(&$list, $elem) {

 /* re-organize list */
 sort_rulesint($list);

 foreach ($list as $ri) {
   if ($ri->position < $elem->position)
     continue;
   $ri->position++;
 }
 $list[] = $elem;

 /* re-organize list */
 sort_rulesint($list);
}

function del_ri(&$list, $elem) {

 /* re-organize list */
 sort_rulesint($list);

 $done = 0;
 foreach ($list as $key => $ri) {
   if (!$done && $ri->idrule == $elem->idrule &&
       $ri->idint == $elem->idint &&
       $ri->position == $elem->position &&
       $ri->enabled == $elem->enabled) {

      $done++;
      unset($list[$key]);
   }
   if ($done) $ri->position--;
 }
 org_index($list);

 /* re-organize list */
 sort_rulesint($list);
}

function move_ri(&$list, &$elem, $newpos) {

 if ($elem->position < $newpos) $newpos--; /* if rule goes down, newpos should be decremented */
 del_ri($list, $elem);

 $done = 0;
 foreach($list as $key => $ri) {
   if ($ri->position == $newpos && !$done) {
     $elem->position = $ri->position;
     $ri->position++;
     $done++;
     continue;
   }
   if ($done) $ri->position++;
 }
 if (!$done) { /* probably added to the end */
  $elem->position = $newpos;
 }
 $list[] = $elem;

 /* re-organize list */
 sort_rulesint($list);
}


?>
