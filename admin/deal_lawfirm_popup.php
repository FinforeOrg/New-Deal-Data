<?php
include("../include/global.php");
require_once ("admin/checklogin.php");
require_once("classes/class.company.php");
require_once("classes/class.transaction.php");
require_once("classes/class.magic_quote.php");
require_once("classes/class.deal_support.php");
require_once("classes/class.transaction_support.php");
$deal_support = new deal_support();
$trans_support = new transaction_support();
///////////////////////////////////////////////////////
$g_view['err'] = array();
$g_view['msg'] = "";
$g_view['input'] = array();
///////////////////////////////////////
if(isset($_POST['action'])&&($_POST['action']=="add")){
	$validation_passed = false;
	$success = $g_trans->add_partner($_POST,"law firm",$validation_passed,$g_view['err']);
	if(!$success){
		die("Cannot add law firm to the deal");
	}
	if($validation_passed){
		$g_view['msg'] = "Law firm added";
	}else{
		//the form is to be shown with data just entered
		$g_view['input']['partner_id'] = $_POST['partner_id'];
		$g_view['input']['firm_name'] = $g_mc->view_to_view($_POST['firm_name']);
		$g_view['input']['transaction_id'] = $_POST['transaction_id'];
	}
}
if(isset($_POST['action'])&&($_POST['action']=="delete")){
	
	$success = $g_trans->remove_partner($_POST,"law firm",$g_view['msg']);
	if(!$success){
		die("Cannot remove the law firm from the deal");
	}
	
}
/************************************************************
sng:17/jun/2011
to set sellside flag
*******************/
if(isset($_POST['action'])&&($_POST['action']=="flip_sellside_status")){
	
	$success = $g_trans->partner_sellside_flag($_POST,"law firm",$g_view['msg']);
	if(!$success){
		die("Cannot set sellside flag");
	}
	
}
/***************************************************************************/
/************************************************************
sng:27/sep/2011
to set is_insignificant flag
*******************/
if(isset($_POST['action'])&&($_POST['action']=="flip_is_insignificant_status")){
	
	$success = $g_trans->partner_is_insignificant_flag($_POST,"law firm",$g_view['msg']);
	if(!$success){
		die("Cannot set insignificant flag");
	}
	
}
/***************************************************************************/
/*************************************************************
sng:27/sep/2011
to set the role
*****************/
if(isset($_POST['action'])&&($_POST['action']=="role")){
	
	$success = $deal_support->set_deal_partner_role($_POST,"law firm",$g_view['msg']);
	if(!$success){
		die("Cannot set role of the partner");
	}
	
}
/***************************************************************/
//get_all_partner_name_list
$g_view['data_count'] = 0;
$g_view['data'] = NULL;
$success = $g_trans->get_all_partner($_REQUEST['transaction_id'],"law firm",$g_view['data'],$g_view['data_count']);
if(!$success){
	die("Cannot get partner data");
}
////////////////////////////////////////////////
/***
sng:20/may/2010
No need to fetch list of law firms as we implement ajax hint
*******/
///////////////////////////////////////////////////////////
/***********************
sng:16/sep/2011
We need the type of deal, because based on that, we will show the 'sellside advisor' checkbox
*************************/
$g_view['deal_type'] = NULL;
$success = $trans_support->get_deal_type($_REQUEST['transaction_id'],$g_view['deal_type']);
if(!$success){
	die("Cannot get deal type");
}
/**********************************
sng:27/sep/2011
we need the role names, based on deal type
***************************************/
$g_view['roles'] = NULL;
$g_view['roles_count'] = 0;

$success = $deal_support->front_get_deal_partner_roles("law firm",$g_view['deal_type']['deal_cat_name'],$g_view['roles'],$g_view['roles_count']);
if(!$success){
	die("Cannot get the partner roles");
}
include("admin/deal_lawfirm_popup_view.php");
?>