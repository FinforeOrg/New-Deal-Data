<?php
/*************
check if admin is logged in or not
The return is in JSON
*********/
include("../../include/global.php");
require_once("classes/class.account.php");
require_once("classes/class.transaction.php");

$json = array();
$json['err'] = "";
$json['rejected'] = 'n';

if(!$g_account->is_admin_logged()){
	$json['rejected'] = 'n';
	$json['err'] = "You need to login first";
	echo json_encode($json);
	exit;
}
$case_study_rejected = false;

$success = $g_trans->reject_case_study($_POST,$case_study_rejected,$json['err']);
if(!$success){
	$json['err'] = "Error rejecting case study";
	$json['rejected'] = 'n';
	echo json_encode($json);
	exit;
}
if($case_study_rejected){
	$json['rejected'] = 'y';
}else{
	$json['rejected'] = 'n';
}
echo json_encode($json);
exit;