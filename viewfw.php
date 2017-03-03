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
 /*
    m0n0wall Central Management Interface
    Copyright (C) 2007  Gouverneur Thomas

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
  */

  require_once("./inc/config.inc.php");

  /* HTML variables */

  $html["title"] = "m0n0wall Central Management Interface - Firewall View";
  $html["pagen"] = "view firewall";

  /* HTML STUFF */
  require_once("./html/head.html.php");
  require_once("./html/menu.html.php");
  require_once("./html/viewfw.html.php");
  require_once("./html/foot.html.php");

  /* ... */
?>
