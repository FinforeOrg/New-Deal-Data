<?php
include("../include/global.php");
require_once ("sa/checklogin.php");
require_once("classes/class.account.php");
///////////////////////////////////////////////////////
$g_view['err'] = array();
$g_view['msg'] = "";
$g_view['data'] = array();
///////////////////////////////////////
if(isset($_POST['action'])&&($_POST['action']=="change")){
	$validation_passed = false;
	$success = $g_account->change_email_of_sa($_POST,$validation_passed,$g_view['err']);
	if(!$success){
		die("Cannot change email of super admin user");
	}
	if($validation_passed){
		$g_view['msg'] = "Email changed";
	}
}
///////////////////////////////////////////////////////////
//get the current data
$success = $g_account->get_email_of_sa($g_view['data']);
if(!$success){
	die("Cannot get email of super admin user");
}
///////////////////////////////////////////////////////////////////
$g_view['heading'] = "Edit Super Admin Email";
$g_view['content_view'] = "sa/changesaemail_view.php";
include("sa/content_view.php");
?>