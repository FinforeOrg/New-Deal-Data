<?php
include("../include/global.php");
require_once ("admin/checklogin.php");
require_once("classes/class.company.php");
require_once("classes/class.magic_quote.php");
///////////////////////////////////////////////////////
$g_view['err'] = array();
$g_view['msg'] = "";
$g_view['input'] = array();

$g_view['cat_id'] = $_GET['cat_id'];
$g_view['firm_type'] = $_GET['firm_type'];
///////////////////////////////////////
if(isset($_POST['action'])&&($_POST['action']=="add")){
	$validation_passed = false;
	$success = $g_company->add_top_firm($_POST,$g_view['cat_id'],$g_view['firm_type'],$validation_passed,$g_view['err']);
	if(!$success){
		die("Cannot add firm");
	}
	if($validation_passed){
		$g_view['msg'] = "Firm added";
	}else{
		//the form is to be shown with data just entered
		$g_view['input']['company_id'] = $_POST['company_id'];
		$g_view['input']['firm_name'] = $g_mc->view_to_view($_POST['firm_name']);
	}
}
if(isset($_POST['action'])&&($_POST['action']=="delete")){
	
	$success = $g_company->remove_top_firm($_POST['id'],$g_view['msg']);
	if(!$success){
		die("Cannot remove the firm");
	}
	
}
/////////////////////////////////////////////////////////////
//get the banks / law firms for this category and firm type
$g_view['data_count'] = 0;
$g_view['data'] = NULL;
$success = $g_company->admin_get_top_firms($g_view['cat_id'],$g_view['firm_type'],$g_view['data'],$g_view['data_count']);
if(!$success){
	die("Cannot get top firms data");
}
////////////////////////////////////////////
include("admin/top_firm_popup_view.php");
?>