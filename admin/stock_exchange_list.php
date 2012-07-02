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
	$success = $g_deal_support->admin_add_stock_exchange($_POST,$validation_passed,$g_view['err']);
	if(!$success){
		die("Cannot add stock exchange");
	}
	if($validation_passed){
		$g_view['msg'] = "Stock exchange added";
	}else{
		//the form is to be shown with data just entered
		$g_view['input']['name'] = $_POST['name'];
	}
}

if(isset($_POST['myaction'])&&($_POST['myaction']=="update")){
	$validation_passed = false;
	$success = $g_deal_support->admin_edit_stock_exchange($_POST['id'],$_POST,$validation_passed);
	if(!$success){
		die("Cannot edit stock exchange");
	}
	if($validation_passed){
		$g_view['msg'] = "Stock exchange updated";
	}else{
		$g_view['msg'] = "Stock exchange not updated";
	}
}
///////////////////////////////////////////////////////////
//get the list of exchanges
$g_view['data_count'] = 0;
$g_view['data'] = NULL;
$success = $g_deal_support->admin_get_all_stock_exchange($g_view['data'],$g_view['data_count']);
if(!$success){
	die("Cannot get data");
}
////////////////////////////////////////////////
$g_view['heading'] = "List of Stock Exchanges";
$g_view['content_view'] = "admin/stock_exchange_list_view.php";
include("admin/content_view.php");
?>