<?php
/*************
check if admin is logged in or not
The return is in JSON
*********/
require_once("../../include/global.php");
require_once("classes/class.account.php");
require_once("classes/class.transaction.php");

$json = array();
$json['err'] = "";
$json['approved'] = 'n';

if(!$g_account->is_admin_logged()){
	$json['approved'] = 'n';
	$json['err'] = "You need to login first";
	echo json_encode($json);
	exit;
}


$success = $g_trans->approve_case_study($_POST['case_study_id']);
if(!$success){
	$json['err'] = "Error rejecting case study";
	$json['approved'] = 'n';
	echo json_encode($json);
	exit;
}
$json['err'] = "";
$json['approved'] = 'y';
echo json_encode($json);
exit;