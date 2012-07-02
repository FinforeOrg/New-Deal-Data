<?php
/****
even though this is called in ajax, we need to check if the caller has logged in or not
The return data is in json
*******/
include("../include/global.php");
require_once("classes/class.account.php");
require_once("classes/class.transaction.php");

$json = array();
$json['error'] = "";
$json['uploaded'] = 'n';
///////////////
if(!$g_account->is_site_member_logged()){
	$json['uploaded'] = 'n';
	$json['error'] = "You need to login first";
	echo json_encode($json);
	exit;
}
/////////////////
$err = array();
$validation_passed = false;
/*********
sng:18/nov/2011
We are now sending the access rule code. Just a hack.
***************/
$success = $g_trans->add_case_study_via_file($_POST['transaction_id'],$_SESSION['mem_id'],$_POST['partner_id'],$_POST['partner_type'],$_POST['caption'],$_POST['access_rule_code'],"case_study_file",CASE_STUDY_PATH,'n',$validation_passed,$err);
if(!$success){
	$json['uploaded'] = 'n';
	$json['error'] = "Error uploading case study";
	echo json_encode($json);
	exit;
}
////////////////////
if($validation_passed){
	$json['uploaded'] = 'y';
	echo json_encode($json);
	exit;
}
////////////////////
$json['uploaded'] = 'n';
$json['error'] = "Error uploading case study";
echo json_encode($json);
exit;
?>