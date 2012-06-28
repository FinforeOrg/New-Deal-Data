<?php
include("../include/global.php");
require_once ("sa/checklogin.php");
require_once("classes/class.account.php");
///////////////////////////////////////////////////////
$g_view['err'] = array();
$g_view['msg'] = "";
///////////////////////////////////////
if(isset($_POST['action'])&&($_POST['action']=="change")){
	$validation_passed = false;
	$success = $g_account->change_sa_password($_POST['password'],$_POST['newpassword'],$_POST['renewpassword'],$validation_passed,$g_view['err']);
	if(!$success){
		die("Cannot change super admin password");
	}
	if($validation_passed){
		$g_view['msg'] = "Super admin password changed";
	}
}
///////////////////////////////////////////////////////////
$g_view['heading'] = "Change Password";
$g_view['content_view'] = "sa/changepassword_view.php";
include("sa/content_view.php");
?>