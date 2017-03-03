<?php
/**
 * Library for autoloading object dependancies at runtime
 *
 * @author Gouverneur Thomas <tgouverneur@be.tiauto.com>
 * @copyright Copyright (c) 2007-2008, Gouverneur Thomas - TI Automotive
 * @version 1.0
 * @package libs
 * @subpackage various
 * @category libs
 * @filesource
 */

  function __autoload($name) {
    $name = strtolower($name);
    $file = "./lib/".$name.".obj.php";
    if (file_exists($file)) {
      require_once($file);
    } else {
      trigger_error("Cannot load $file...<br/>\n", E_USER_ERROR);
    }
  }
?>
