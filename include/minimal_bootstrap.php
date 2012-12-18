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
/*************
sng:17/dec/2012
We will not use the db class here. It does not use the connection resource while running the queries.
Since these codes can run in parallel to other codes, we require the use of connection resource so that
insert and last_insert_id give correct result

But then some codes use it, so
*******************/
require_once(FILE_PATH."/classes/db.php");
?>