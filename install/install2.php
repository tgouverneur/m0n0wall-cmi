<?php
  $html["title"] = "m0n0wall Central Management Interface - Installation";
  $html["pagen"] = "Installation";
  require_once("../inc/config.inc.php");
  require_once("../html/head.html.php");
  /* ripped from PHP.NET user comment */
  function mysql_exec_batch ($p_query, $link, $p_transaction_safe = true) {
  if ($p_transaction_safe) {
      $p_query = 'START TRANSACTION;' . $p_query . '; COMMIT;';
    };
  $query_split = preg_split ("/[;]+/", $p_query);
  foreach ($query_split as $command_line) {
    $command_line = trim($command_line);
    if ($command_line != '') {
      $query_result = mysql_query($command_line, $link);
      if ($query_result == 0) {
        break;
      };
    };
  };
  return $query_result;
 }
?>
  </tr></table></td>
  <td width="600"><table width="100%" border="0" cellpadding="10" cellspacing="0">
  <tr><td>
<?php
  if (isset($config['installed']) && $config['installed'] == TRUE) {
    die("Nothing to do here");
  }
?>
<p class="pgtitle">m0n0wall-CMI : Installation</p>
<?php
 if (!isset($_POST["Submit"])) {
   echo "Cannot be self-called";
 }
 else {
   $error = 0;
   if (isset($_POST["dbhostname"]) && isset($_POST["dbname"]) && isset($_POST["username"]) && isset($_POST["password"]) &&
       !empty($_POST["dbhostname"]) && !empty($_POST["dbname"]) && !empty($_POST["username"]) && !empty($_POST["password"])) {

     ?><ul><?php
      if (($l = mysql_connect($_POST["dbhostname"], $_POST["username"], $_POST["password"])) === FALSE) {
        ?><li><span class="redcolor">ERROR</span>: Cannot connect to MySQL database...</li><?php
        $error++;
      } else {
        ?><li><span class="greencolor">OK</span>: Connected to MySQL database...</li><?php
        if (mysql_select_db($_POST["dbname"], $l) === FALSE) {
          ?><li><span class="redcolor">ERROR</span>: Cannot select specified database</li><?php
          $error++;
        } else {
          ?><li><span class="greencolor">OK</span>: Database <?php echo $_POST["dbname"];?> selected...</li><?php
          /* create tables */
          $sqlf = glob("./sql/*.sql");   
 	  if (count($sqlf)) {
	    foreach ($sqlf as $sqlfile) {
	      $fp = fopen($sqlfile, "r");
	      $sqlq = fread($fp, 4096);
	      $table = explode('.', basename($sqlfile));
	      $table = $table[0];
	      if (mysql_exec_batch($sqlq, $l) === FALSE) {
	         ?><li><span class="redcolor">ERROR</span>: Cannot create <?php echo $table;?></li><?php
	         $error++;
	      } else {
		 ?><li><span class="greencolor">OK</span>: Created table <?php echo $table;?>...</li><?php
	      }
 	    }
	  }
	  /* write config.inc.php */
	  mysql_close($l);
	  if (($fp = fopen("../inc/config.inc.php", "w")) === FALSE) {
 	    ?><li><span class="redcolor">ERROR</span>: Cannot open config.inc.php file...</li><?php
	    $error++;
	  }
	  else {
	    ?><li><span class="greencolor">OK</span>: config.inc.php opened</li><?php
            $ftpl = fopen("./cfg_template.php", "r");
	    $content = fread($ftpl, 4096);
	    $content = str_replace("__DBHOST__", $_POST["dbhostname"], $content);
	    $content = str_replace("__DBUSER__", $_POST["username"], $content);
	    $content = str_replace("__DBPASS__", $_POST["password"], $content);
	    $content = str_replace("__DBNAME__", $_POST["dbname"], $content);
	    $content = str_replace("__DBPORT__", 3306, $content);
            fwrite($fp, $content);
	    fclose($fp);
	    ?><li><span class="greencolor">OK</span>: config.inc.php written</li><?php
	  }
        }
     }
     ?></ul><?php
      if ($error) {

	echo "WARNING: some error were detected while installing, please check your installation...<br/>";
      } else {
	echo "Installation went successful<br/>";
	echo "You can remove the install/ directory and restrict permissions on config.inc.php<br/>";
      }
   }
   else echo "Missing one field or field empty...<br/>";
 }
 require_once("../html/foot.html.php");
?>
