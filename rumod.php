<?php
 /**
  * Base file for HTML processing
  *
  * @author Gouverneur Thomas <tgouverneur@be.tiauto.com>
  * @copyright Copyright (c) 2007-2008, Gouverneur Thomas - TI Automotive
  * @version 1.0
  * @package html
  * @subpackage base
  * @category html
  * @filesource
  */
?>
<?php
  require_once("./inc/config.inc.php");

  /* HTML variables */

  $html["title"] = "m0n0wall Central Management Interface - Rule Edit";
  $html["pagen"] = "Rule Edit";

  /* HTML STUFF */
  require_once("./html/head.html.php");
  require_once("./html/menu.html.php");
  require_once("./html/rumod.html.php");
  require_once("./html/foot.html.php");

  /* ... */
?>
