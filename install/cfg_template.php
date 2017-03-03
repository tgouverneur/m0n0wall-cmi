<?php
/**
 * File used to store application settings
 * @author Gouverneur Thomas <tgouverneur@be.tiauto.com>
 * @copyright Copyright (c) 2007-2008, Gouverneur Thomas - TI Automotive
 * @version 1.0
 * @package includes
 * @subpackage config
 * @category config
 */

/* MySQL */
/**
 * @access private
 */
$config['mysql']['host'] = "__DBHOST__";
$config['mysql']['user'] = "__DBUSER__";
$config['mysql']['pass'] = "__DBPASS__";
$config['mysql']['port'] = "__DBPORT__";

$config['mysql']['db'] = "__DBNAME__";

$config['curl']['timeout'] = 10;

/* XML */

$config['xml']['rootobj'] = "m0n0wall";


$config['version'] = "1.0-CVS";
$config['installed'] = TRUE;


?>
