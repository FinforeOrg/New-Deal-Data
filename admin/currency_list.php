<?php
require_once("../include/global.php");
require_once ("admin/checklogin.php");
require_once("classes/class.deal_support.php");
$g_deal_support = new deal_support();
///////////////////////////////////////////////////////
$g_view['err'] = array();
$g_view['msg'] = "";
$g_view['input'] = array();
///////////////////////////////////////
if(isset($_POST['action'])&&($_POST['action']=="add")){
	$validation_passed = false;
	$success = $g_deal_support->admin_add_currency($_POST,$validation_passed,$g_view['err']);
	if(!$success){
		die("Cannot add currency");
	}
	if($validation_passed){
		$g_view['msg'] = "Currency added";
	}else{
		//the form is to be shown with data just entered
		$g_view['input']['code'] = $_POST['code'];
		$g_view['input']['name'] = $_POST['name'];
	}
}

if(isset($_POST['myaction'])&&($_POST['myaction']=="update")){
	$validation_passed = false;
	$success = $g_deal_support->admin_edit_currency($_POST['id'],$_POST,$validation_passed);
	if(!$success){
		die("Cannot edit currency");
	}
	if($validation_passed){
		$g_view['msg'] = "Currency updated";
	}else{
		$g_view['msg'] = "Currency not updated";
	}
}
///////////////////////////////////////////////////////////
//get the list of curencies
$g_view['data_count'] = 0;
$g_view['data'] = NULL;
$success = $g_deal_support->admin_get_all_currency($g_view['data'],$g_view['data_count']);
if(!$success){
	die("Cannot get data");
}
////////////////////////////////////////////////
$g_view['heading'] = "List of Currencies";
$g_view['content_view'] = "admin/currency_list_view.php";
include("admin/content_view.php");
?>