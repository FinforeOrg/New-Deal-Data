<?php
/**********************
sng:27/oct/2012

Minimum files needed to run a script.
This is used by cron codes who does not require session and run in background mode

NOT FOR NORMAL FILES
**************************/
$conn = mysql_connect($g_config['db_host'], $g_config['db_user'], $g_config['db_password']);
if(!$conn){
	exit;
}
$ok = mysql_select_db($g_config['db_name'], $conn);
if(!$ok){
	exit;
}
require_once(FILE_PATH."/classes/db.php");
?>