<?php
/********************
sng:16/oct/2012

If running then we send still_running=y
if currently not running, we send still_running=n and msg: the time when the slave was invoked last
*********/
require_once("../../include/global.php");
require_once("classes/class.account.php");
require_once("classes/class.background_slave_controller.php");
$master = new background_slave_controller();

$slave_name = $_GET['slave_name'];

$status_arr = array();
$status_arr['err_flag'] = 0;
$status_arr['still_running'] = "y";
$status_arr['msg'] = "";


if(!$g_account->is_admin_logged()){
	$status_arr['err_flag'] = 1;
	$status_arr['still_running'] = 'n';
	echo json_encode($status_arr);
	exit;
}
$still_running = false;
$last_triggered = "";
$ok = $master->is_running($slave_name,$still_running,$last_triggered);
if(!$ok){
	$status_arr['err_flag'] = 1;
	$status_arr['still_running'] = 'n';
	echo json_encode($status_arr);
	exit;
}
//all ok
$status_arr['err_flag'] = 0;
if($still_running){
	$status_arr['still_running'] = 'y';
}else{
	$status_arr['still_running'] = 'n';
	/**********
	sng:30/oct/2012
	we need to know when it ran last
	***********/
	if($last_triggered==="0000-00-00 00:00:00"){
		$status_arr['msg'] = "Yet to run";
	}else{
		$status_arr['msg'] = "Last searched ".date("Y-m-d H:i:s");
	}
}
echo json_encode($status_arr);
exit;
?>