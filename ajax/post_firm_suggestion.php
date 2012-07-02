<?php
/******************************
sng:7/dec/2011

used to store data for new bank/law firm suggestion suggestion

**********************/
require_once("../include/global.php");
require_once("classes/class.account.php");
require_once("classes/class.company.php");
require_once("classes/db.php");

$json = array();
$json['posted'] = 'n';
$json['msg'] = "";
$json['err_arr'] = array();

/*********************
even though this is called in ajax, we check if the member has logged in or not.
*************************/
if(!$g_account->is_site_member_logged()){
	$json['posted'] = 'n';
	$json['msg'] = "You need to login to post suggestion";
	echo json_encode($json);
	exit;
}
/*******************************
now try to add the bank/law firm suggestion
********************************/
$validation_passed = false;
$success = $g_company->ajax_front_new_firm_suggestion($_POST,$validation_passed,$json['msg'],$json['err_arr']);
if(!$success){
	$json['posted'] = 'n';
	$json['msg'] = "Server error";
	echo json_encode($json);
	exit;
}
if(!$validation_passed){
	$json['posted'] = 'n';
	//msg is set inside the method
	echo json_encode($json);
	exit;
}
$json['posted'] = 'y';
$json['msg'] = "Your suggestion has been posted";
echo json_encode($json);
exit;
?>