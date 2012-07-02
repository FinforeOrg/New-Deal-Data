<?php
/****
sng:13/apr/2012
even though this is called in ajax, we need to check if the caller has logged in or not
We use json to send the result
*******/
$result = array();
$result['msg'] = "";
$result['success'] = 'n';


require_once("../../include/global.php");
require_once("classes/class.account.php");

if(!$g_account->is_site_member_logged()){
	$result['msg'] = "You need to login first";
	$result['success'] = 'n';
	echo json_encode($result);
	exit;
}

require_once("classes/class.transaction_suggestion.php");
$trans_suggestion = new transaction_suggestion();

$this_member = $_SESSION['mem_id'];
$deal_id = $_POST['deal_id'];
$validation_passed = false;

$success = $trans_suggestion->front_submit_partners($deal_id,$_POST['partner_type'],$this_member,$_POST,$validation_passed,$result['msg']);
if(!$success){
	$result['msg'] = "Could not send the partners";
	$result['success'] = 'n';
	echo json_encode($result);
	exit;
}

if(!$validation_passed){
	//the message is set inside the function
	$result['success'] = 'n';
	echo json_encode($result);
	exit;
}
//no error
$result['msg'] = "Updated";
$result['success'] = 'y';
echo json_encode($result);
exit;
?>