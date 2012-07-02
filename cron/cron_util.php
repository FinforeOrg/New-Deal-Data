<?php


function exec_in_background($php_code,$args){
	//$cmd = "E:\\xampp\\php\\php.exe -f E:\\xampp\\htdocs\\tombstone\\cron\\".$php_code." ".$args;
	//pclose(popen("start /B".$cmd, "r"));
	//exec($cmd);
	$cmd = "/usr/bin/php -f /var/www/home/cron/".$php_code." ".$args;
	exec($cmd . " > /dev/null &");
	/*********************************************************
	for debugging
	**/
	//print_r(passthru($cmd,$output));
	/*****************************************************/
}
?>
