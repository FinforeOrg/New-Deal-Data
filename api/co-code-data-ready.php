<?php
/************************
sng:26/feb/2013

This is called by co-codes when all data dumping has been done.
This is to be called only once.
**************************/
require(dirname(dirname(__FILE__))."/include/minimal_bootstrap.php");
$op = array();
$code_file = "slave_fetch_from_co_codes.php";
exec('/usr/bin/php -f '.FILE_PATH.'/cron/'.$code_file.' > /dev/null 2>&1 & echo $!',$op);
$pid = (int)$op[0];
echo $pid."\r\n";
echo "OK\r\n";
?>