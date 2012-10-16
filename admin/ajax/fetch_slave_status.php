<?php
/********************
sng:16/oct/2012
*********/
require_once("../../include/global.php");
require_once("classes/class.account.php");
require_once("classes/class.background_slave_controller.php");
$master = new background_slave_controller();

$slave_name = $_GET['slave_name'];

$status_arr = array();
$status_arr['err_flag'] = 0;
$status_arr['still_running'] = "y";


if(!$g_account->is_admin_logged()){
	$status_arr['err_flag'] = 1;
	$status_arr['still_running'] = 'n';
	echo json_encode($status_arr);
	exit;
}
$still_running = false;
$ok = $master->is_running($slave_name,$still_running);
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
}
echo json_encode($status_arr);
exit;
?>