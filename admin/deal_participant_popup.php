<?php
require_once("../include/global.php");
require_once ("admin/checklogin.php");
require_once("classes/class.transaction_support.php");
require_once("classes/class.transaction_company.php");
$trans_support = new transaction_support();
$deal_comp = new transaction_company();

$g_view['deal_id'] = $_GET['transaction_id'];

$g_view['err'] = array();
$g_view['msg'] = "";
$g_view['input'] = array();
///////////////////////////////////////
if(isset($_POST['action'])&&($_POST['action']=="add")){
	$validation_passed = false;
	$success = $deal_comp->admin_add_participant_for_deal($g_view['deal_id'],$_POST,$validation_passed,$g_view['err']);
	if(!$success){
		die("Cannot add participant to the deal");
	}
	if($validation_passed){
		$g_view['msg'] = "Participant company added";
	}else{
		//the form is to be shown with data just entered
		$g_view['input']['company_id'] = $_POST['company_id'];
		$g_view['input']['company_name'] = $_POST['company_name'];
		$g_view['input']['role_id'] = $_POST['role_id'];
		$g_view['input']['footnote'] = $_POST['footnote'];
	}
}
/**********************************************************************************/
if(isset($_POST['action'])&&($_POST['action']=="update")){
	$success = $deal_comp->admin_update_participant_for_deal($g_view['deal_id'],$_POST);
	if(!$success){
		die("Cannot update participant for the deal");
	}
	$g_view['msg'] = "Participant updated";
}
/**********************************************************************************/
if(isset($_POST['action'])&&($_POST['action']=="remove")){
	$success = $deal_comp->admin_remove_participant_for_deal($g_view['deal_id'],$_POST['company_id']);
	if(!$success){
		die("Cannot remove participant for the deal");
	}
	$g_view['msg'] = "Participant removed";
}
/*******************************
get deal type
********************************/
$g_view['deal_type'] = NULL;
$success = $trans_support->get_deal_type($_REQUEST['transaction_id'],$g_view['deal_type']);
if(!$success){
	die("Cannot get deal type");
}
/********************************
get the roles for this deal type
*******************************/
$g_view['roles'] = NULL;
$g_view['role_count'] = 0;
$success = $deal_comp->get_all_deal_company_roles_for_deal_type($g_view['deal_type']['deal_cat_name'],$g_view['roles'],$g_view['role_count']);
if(!$success){
	die("Cannot get roles");
}
/*********************************
get the participants
*********************************/
$g_view['data_count'] = 0;
$g_view['data'] = NULL;
$success = $deal_comp->admin_get_participants_for_deal($g_view['deal_id'],$g_view['data'],$g_view['data_count']);
if(!$success){
	die("Cannot get participant data");
}

include("admin/deal_participant_popup_view.php");
?>