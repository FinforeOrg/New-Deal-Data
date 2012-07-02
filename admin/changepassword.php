<?php
include("../include/global.php");
require_once ("admin/checklogin.php");
require_once("classes/class.account.php");
///////////////////////////////////////////////////////
$g_view['err'] = array();
$g_view['msg'] = "";
///////////////////////////////////////
if(isset($_POST['action'])&&($_POST['action']=="change")){
	$validation_passed = false;
	$success = $g_account->change_admin_password($_SESSION['admin_id'],$_POST['password'],$_POST['newpassword'],$_POST['renewpassword'],$validation_passed,$g_view['err']);
	if(!$success){
		die("Cannot change admin password");
	}
	if($validation_passed){
		$g_view['msg'] = "Admin password changed";
	}
}
///////////////////////////////////////////////////////////
$g_view['heading'] = "Change Password";
$g_view['content_view'] = "admin/changepassword_view.php";
include("admin/content_view.php");
?>