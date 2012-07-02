<?php
include("../include/global.php");
require_once ("admin/checklogin.php");
require_once("classes/class.account.php");
///////////////////////////////////////////////////////
$g_view['err'] = array();
$g_view['msg'] = "";
$g_view['data'] = array();
///////////////////////////////////////
if(isset($_POST['action'])&&($_POST['action']=="change")){
	$validation_passed = false;
	$success = $g_account->change_email_of_admin($_SESSION['admin_id'],$_POST,$validation_passed,$g_view['err']);
	if(!$success){
		die("Cannot change email of admin user");
	}
	if($validation_passed){
		$g_view['msg'] = "Email changed";
	}
}
///////////////////////////////////////////////////////////
//get the current data
$success = $g_account->get_email_of_admin($_SESSION['admin_id'],$g_view['data']);
if(!$success){
	die("Cannot get email of admin user");
}
///////////////////////////////////////////////////////////////////
$g_view['heading'] = "Edit Admin Email";
$g_view['content_view'] = "admin/changesaemail_view.php";
include("admin/content_view.php");
?>