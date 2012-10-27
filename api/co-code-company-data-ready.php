<?php
/**************
sng:26/oct/2012
Quick and dirty method so that the cron job at co-codes.com can notify us that it has dumped the data

This start the background task and send OK message to the caller.
Since this will be called as REST, there is no session and we do not use the global.php
Any errors are discarded silently

We load the db class since it is required by the background_slave_controller
We also need to connect to the database. If that fails, we still send ok but nothing gets done
*****************/
header("Content-type: text/plain");

require_once("../include/config.php");

$conn = mysql_connect($g_config['db_host'], $g_config['db_user'], $g_config['db_password']);
if(!$conn){
	echo "OK\r\n";
	exit;
}

$ok = mysql_select_db($g_config['db_name'], $conn);
if(!$ok){
	echo "OK\r\n";
	exit;
}

require_once(FILE_PATH."/classes/db.php");
require_once(FILE_PATH."/classes/class.background_slave_controller.php");

$master = new background_slave_controller();
$slave_to_run = "fetch_company_data_co_codes";
$started = false;
$msg = "";

$ok = $master->trigger_slave($slave_to_run,$started,$msg);
if(!$ok){
	//cannot do a thing
	//maybe log
}else{
	//the worker has been triggered in the background
}
echo "OK\r\n";
exit;
?>