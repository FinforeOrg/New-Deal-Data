<?php
/**********************
sng:19/dec/2011

used to register using the quick register popup. Used when the user wants
to submit data but is not registered.
**************************/
require_once("../include/global.php");
require_once("classes/class.member.php");
require_once("classes/class.magic_quote.php");

$json = array();
$json['registered'] = 'n';
$json['msg'] = "";
$json['err_arr'] = array();

/*******************************************************************************
not used now
***/
echo json_encode($json);
exit;
/*******************************************************************************/

$req_id = "";
$validation_passed = false;
$success = $g_mem->new_membership_request($_POST,$_SESSION['security_code'],$validation_passed,$json['err_arr'],$req_id);

if(!$success){
	$json['registered'] = 'n';
	$json['msg'] = "Server error";
	echo json_encode($json);
	exit;
}

if(!$validation_passed){
	$json['registered'] = 'n';
	$json['msg'] = "";
	echo json_encode($json);
	exit;
}
/******************************
validation passed. Now, if this is a favoured email domain, activation code is sent then and there
and the user can activate the account.
else admin has to see the application, then accept and only then the activation code is sent. Which means, at this point of time
data cannot be submitted.
**********************************/
$json['registered'] = 'y';
//default message
$json['msg'] = "We are checking your registration details. Please <strong>DO NOT</strong> try and log-in until you have received the welcome email. Thank you for your patience.";

$is_favoured = false;

$success = $g_mem->is_work_email_favoured($_POST['type'],$_POST['work_email'],$_POST['firm_name'],$is_favoured);
if($success){
	if($is_favoured){
		$success = $g_mem->favour_accept_registration($req_id);
		if($success){
			//accepted, email sent, the message will be
			$json['msg'] = "We have sent the welcome email to your work email. It contains the activation link. You will have to click that link before you can log-in.";
		}
	}
}
echo json_encode($json);
exit;