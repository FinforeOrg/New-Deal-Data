<?php
require_once("../include/global.php");
require_once ("admin/checklogin.php");
require_once("classes/class.transaction_company.php");
require_once("classes/class.transaction.php");
$trans_com = new transaction_company();
///////////////////////////////////////////////////////
$g_view['err'] = array();
$g_view['msg'] = "";
$g_view['input'] = array();
//initialize
$g_view['input']['role_name'] = "";
$g_view['input']['for_deal_type'] = "";
$g_view['input']['partner_type'] = "";
///////////////////////////////////////
if(isset($_POST['action'])&&($_POST['action']=="add")){
	$validation_passed = false;
	$success = $trans_com->admin_add_deal_company_role($_POST,$validation_passed,$g_view['err']);
	if(!$success){
		die("Cannot add role");
	}
	if($validation_passed){
		$g_view['msg'] = "Role added";
	}else{
		//the form is to be shown with data just entered
		$g_view['input']['role_name'] = $_POST['role_name'];
		$g_view['input']['for_deal_type'] = $_POST['for_deal_type'];
	}
}

if(isset($_POST['myaction'])&&($_POST['myaction']=="update")){
	$validation_passed = false;
	$success = $trans_com->admin_update_deal_company_role($_POST['role_id'],$_POST,$validation_passed,$g_view['msg']);
	if(!$success){
		die("Cannot edit role");
	}
	if($validation_passed){
		$g_view['msg'] = "Role updated";
	}else{
		//the message is in the variable
	}
}
/*******************************************************************
get the list of roles
********/
$g_view['role_count'] = 0;
$g_view['role'] = NULL;
$success = $trans_com->admin_get_all_deal_company_roles($g_view['role'],$g_view['role_count']);
if(!$success){
	die("Cannot get data");
}
/***********************************************************
get the list of deal types
*******/
$g_view['deal_types'] = array();
$g_view['deal_types_count'] = 0;
$success = $g_trans->get_all_category_type("type",$g_view['deal_types'],$g_view['deal_types_count']);
if(!$success){
	die("Cannot get the deal types");
}
/****************************************************************/
$g_view['heading'] = "List of Deal Company Roles";
$g_view['content_view'] = "admin/deal_company_role_list_view.php";
include("admin/content_view.php");
?>