<?php
include("../include/global.php");
require_once ("admin/checklogin.php");
require_once("classes/class.member.php");
require_once("classes/class.magic_quote.php");
///////////////////////////////////////////////////////
$g_view['err'] = array();
$g_view['msg'] = "";
$g_view['input'] = array();
///////////////////////////////////////
if(isset($_POST['action'])&&($_POST['action']=="add")){
     
	$validation_passed = false;
	$success = $g_mem->add_registration_special_email($_POST,$validation_passed,$g_view['err']);
	if(!$success){
		die("Cannot add email suffix");
	}
	if($validation_passed){
		$g_view['msg'] = "Email suffix added";
	}else{
		//the form is to be shown with data just entered
		$g_view['input']['company_type'] = $_POST['company_type'];
		$g_view['input']['company_id'] = $_POST['company_id'];
		$g_view['input']['firm_name'] = $_POST['firm_name'];
		$g_view['input']['email_suffix'] = $_POST['email_suffix'];
	}
}
/////////////////////////////////////////////////

$g_view['heading'] = "Add Favoured Email";
$g_view['content_view'] = "admin/favoured_email_add_view.php";
include("admin/content_view.php");
?>