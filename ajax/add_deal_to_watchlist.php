<?php
require_once("../include/global.php");
require_once("classes/class.account.php");
require_once("classes/class.deal_support.php");
$deal_support = new deal_support();

$result = array();
//0 is error, 1 is success
$result['status'] = 0;
$result['reason'] = "";

if(!$g_account->is_site_member_logged()){
	$result['status'] = 0;
	$result['reason'] = "You need to login";
	echo json_encode($result);
	exit;
}
if(!isset($_GET['deal_id'])||($_GET['deal_id']=="")){
	$result['status'] = 0;
	$result['reason'] = "Please specify the deal";
	echo json_encode($result);
	exit;
}
$deal_id = $_GET['deal_id'];
$validation_passed = false;
$err_msg = "";
$success = $deal_support->ajax_add_deal_to_watch_list($_SESSION['mem_id'],$deal_id,$validation_passed,$err_msg);
if(!$success){
	$result['status'] = 0;
	$result['reason'] = "Db error";
	echo json_encode($result);
	exit;
}
if($validation_passed){
	$result['status'] = 1;
	$result['reason'] = "";
}else{
	$result['status'] = 0;
	$result['reason'] = $err_msg;
}
echo json_encode($result);
exit;
?>