<?php
/**
 * Various HTML related stuff
 *
 * @author Gouverneur Thomas <tgouverneur@be.tiauto.com>
 * @copyright Copyright (c) 2007-2008, Gouverneur Thomas - TI Automotive
 * @version 1.0
 * @package libs
 * @subpackage various
 * @category libs
 * @filesource
 */

	static $wkports = array(
		"FTP" => 21,
		"SSH" => 22,
		"Telnet" => 23,
		"SMTP" => 25,
		"DNS" => 53,
		"HTTP" => 80,
		"POP3" => 110,
		"IMAP" => 143,
                "HTTPS" => 443
        );

function specialport($port) {
 global $wkports;
 if (strtolower($port) == "") return 1;
 if (strpos($port, "-")) return 0;
 foreach($wkports as $p) {
  if ($port == $p) {
   return 1;
  }
 }
 return 0;
}

function specialnet($mono, $str) {
 
  $special = array("wanip", "lan", "pptp", "any");
  foreach ($mono->ifaces as $if) {
   if ($if->type == "opt") {
    $ifn = $if->type.$if->num;
    if ($ifn == $str)
     return 1;
   }
  }
  foreach ($special as $sp) {
   if (strtolower($sp) == strtolower($str))
    return 1;
  }
  return 0;
}

function checkPost($name) {
  global $_POST;
  return isset($_POST[$name]);
}

function getHTTPVar($name) {
 global $_GET, $_POST;
 
 /* first check POST, then fallback on GET */
 if (isset($_POST[$name])) return $_POST[$name];
 if (isset($_GET[$name])) return $_GET[$name];
 return NULL;
}

function sanitizeVar($var) {
  return mysql_escape_string($var);
}

function sanitizeArray($var) {
  foreach($var as $name => $value) {
    if (is_array($value)) { sanitizeArray($value); continue; }
    $var[$name] = mysql_escape_string($value);
  }
}


function checkHTTPVars($names) {

}

function getcurlerror($ret) {
global $error, $mono;
$ok = 0;
switch($ret) {
         case 0:
          $error = $mono->config->error;
          break;
         case 401:
          $error = "401 Unauthorized";
          break;
         case 403:
          $error = "403 Forbidden";
          break;
         case 402:
         case 400:
         case 404:
         case 405:
         case 406:
         case 407:
         case 408:
         case 409:
         case 410:
         case 411:
         case 412:
         case 413:
         case 414:
         case 415:
         case 416:
         case 417:
         case 422:
         case 423:
         case 424:
          $error = "4XX Client Error";
         break;
         case 500:
         case 501:
         case 502:
         case 503:
         case 504:
         case 505:
         case 507:
          $error = "5XX Server error";
         break;
        default:
          echo "ok<br/>";
          echo "HTTP code returned: ".$ret."<br/>";
          $ok = 1;
          break;
       }
        return $ok;
}



?>
