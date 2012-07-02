<?php
/******************************
sng:8/dec/2011

used to store suggested correction data for bank/law firm

**********************/
require_once("../include/global.php");
require_once("classes/class.account.php");
require_once("classes/class.company.php");
require_once("classes/db.php");

$json = array();
$json['posted'] = 'n';
$json['msg'] = "";

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
$success = $g_company->ajax_front_firm_correction($_POST,$json['msg']);
if(!$success){
	$json['posted'] = 'n';
	$json['msg'] = "Server error";
	echo json_encode($json);
	exit;
}

$json['posted'] = 'y';
$json['msg'] = "Your correction has been posted";
echo json_encode($json);
exit;
?>