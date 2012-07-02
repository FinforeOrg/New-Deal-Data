<?php
/*************
sng:19/nov/2011
Clear the case study from all flags

check if admin is logged in or not
The return is in JSON
*********/
require_once("../../include/global.php");
require_once("classes/class.account.php");
require_once("classes/class.transaction.php");

$json = array();
$json['err'] = "";
$json['cleared'] = 'n';

if(!$g_account->is_admin_logged()){
	$json['cleared'] = 'n';
	$json['err'] = "You need to login first";
	echo json_encode($json);
	exit;
}


$success = $g_trans->clear_flagged_case_study($_POST['case_study_id']);
if(!$success){
	$json['err'] = "Error clearing case study";
	$json['cleared'] = 'n';
	echo json_encode($json);
	exit;
}
$json['err'] = "";
$json['cleared'] = 'y';
echo json_encode($json);
exit;