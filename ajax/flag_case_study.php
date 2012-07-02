<?php
/****
even though this is called in ajax, we need to check if the caller has logged in or not
The return data is in json
*******/
require_once("../include/global.php");
require_once("classes/class.account.php");
require_once("classes/class.transaction.php");

$json = array();
$json['error'] = "";
$json['submitted'] = 'n';
/**************************************************/
if(!$g_account->is_site_member_logged()){
	$json['submitted'] = 'n';
	$json['error'] = "You need to login first";
	echo json_encode($json);
	exit;
}
/******************************************************/
$success = $g_trans->flag_case_study($_POST['case_study_id'],$_SESSION['mem_id'],$_POST['flag_reason']);
if(!$success){
	$json['submitted'] = 'n';
	$json['error'] = "Could not flag the case study";
	echo json_encode($json);
	exit;
}
$json['submitted'] = 'y';
$json['error'] = "";
echo json_encode($json);
exit;