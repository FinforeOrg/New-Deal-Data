<?php
include("../include/global.php");
require_once ("sa/checklogin.php");
require_once("classes/class.account.php");
///////////////////////////////////////////////////////
$g_view['err'] = array();
$g_view['msg'] = "";
$g_view['input'] = array();
///////////////////////////////////////
if(isset($_POST['action'])&&($_POST['action']=="add")){
	$validation_passed = false;
	$success = $g_account->create_admin_user($_POST,$validation_passed,$g_view['err']);
	if(!$success){
		die("Cannot create admin user");
	}
	if($validation_passed){
		$g_view['msg'] = "Admin user created";
	}else{
		//the form is to be shown with data just entered
		$g_view['input']['name'] = $_POST['name'];
		$g_view['input']['login_name'] = $_POST['login_name'];
		$g_view['input']['email'] = $_POST['email'];
	}
}
///////////////////////////////////////////////////////////
$g_view['heading'] = "Add Admin User";
$g_view['content_view'] = "sa/add_admin_user_view.php";
include("sa/content_view.php");
?>