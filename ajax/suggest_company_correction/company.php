<?php
/*************************
sng:23/may/2012

This is called to submit corrective suggestion for companies.

Although this is called in ajax, we check whether the member is logged in or not.
*********/
require_once("../../include/global.php");
require_once("classes/class.account.php");

$result = array();
$result['msg'] = "";
$result['success'] = 'n';

if(!$g_account->is_site_member_logged()){
	$result['msg'] = "You need to login first";
	$result['success'] = 'n';
	echo json_encode($result);
	exit;
}

require_once("classes/class.company_suggestion.php");
$comp_suggestion = new company_suggestion();

$this_member = $_SESSION['mem_id'];
$company_id = $_POST['company_id'];
$validation_passed = false;


$success = $comp_suggestion->front_submit_company_corrective_suggestion($company_id,$this_member,$_POST,$validation_passed,$result['msg']);
if(!$success){
	$result['msg'] = "Could not send the company";
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
$result['msg'] = "Stored";
$result['success'] = 'y';
echo json_encode($result);
exit;
?>