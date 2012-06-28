<?php
/****
deal team page edit page, requires login
******/
include("include/global.php");
require_once("check_mem_login.php");
/////////////////////////////////////////////////////////////
require_once("classes/class.transaction.php");
require_once("classes/class.company.php");
require_once("classes/class.member.php");
require_once("classes/class.magic_quote.php");
/////////////////////////////////////////////////////
$g_view['deal_id'] = $_REQUEST['deal_id'];
$g_view['deal_partner_id'] = $_REQUEST['partner_id'];
/////////////////////////////////////////////////////////////
$g_view['mem_added'] = false;
$g_view['msg'] = "";
$g_view['err'] = array();
$g_view['input'] = array();
////////////////////////////////////////////////////////////////
if(isset($_REQUEST['action'])&&($_REQUEST['action']=="add_self")){
	//add this member
	$success = $g_trans->add_deal_partner_team_member($g_view['deal_id'],$g_view['deal_partner_id'],$_SESSION['mem_id'],$g_view['mem_added'],$g_view['msg']);
	if(!$success){
		die("Cannot add to deal team");
	}
}
//////////////////////////////////////////////////////////////
$g_view['mem_removed'] = false;
if(isset($_REQUEST['action'])&&($_REQUEST['action']=="remove_self")){
	//add this member
	$success = $g_trans->remove_deal_partner_team_member($g_view['deal_id'],$g_view['deal_partner_id'],$_SESSION['mem_id'],$g_view['mem_removed'],$g_view['msg']);
	if(!$success){
		die("Cannot remove from deal team");
	}
}
//////////////////////////////////////////////////////////////
if(isset($_REQUEST['action'])&&($_REQUEST['action']=="add_team_member")){
	//add this member
	//the function we are using does not have one particular validation test, so we manually
	//do it here
	
	if($_POST['team_mem_id']==""){
		$g_view['err']['team_member_name'] = "Not found";
	}
	$success = $g_trans->add_deal_partner_team_member($g_view['deal_id'],$g_view['deal_partner_id'],$_POST['team_mem_id'],$g_view['mem_added'],$g_view['msg']);
	if(!$success){
		die("Cannot add to deal team");
	}
	////////////////////////////
	if(!$g_view['mem_added']){
		$g_view['input']['team_member_name'] = $g_mc->view_to_view($_POST['team_member_name']);
	}
}
///////////////////////////////////////////////////////////////
if(isset($_REQUEST['action'])&&($_REQUEST['action']=="create_and_add_colleague")){
	/****
	sng:7/jul/2010
	We store the inputs so that we can show
	***/
	$g_view['input']['f_name'] = $g_mc->view_to_view($_POST['f_name']);
	$g_view['input']['l_name'] = $g_mc->view_to_view($_POST['l_name']);
	$g_view['input']['work_email'] = $g_mc->view_to_view($_POST['work_email']);
	$g_view['input']['designation'] = $_POST['designation'];
	
	$validation_passed = false;
	$ghost_mem_id = 0;
	$success = $g_mem->create_ghost_account($_POST,$validation_passed,$ghost_mem_id,$g_view['err']);
	if(!$success){
		die("Cannot create colleague account");
	}
	if($validation_passed){
		//member created, so try to add this
		$success = $g_trans->add_deal_partner_team_member($g_view['deal_id'],$g_view['deal_partner_id'],$ghost_mem_id,$g_view['mem_added'],$g_view['msg']);
		if(!$success){
			die("Cannot add to deal team");
		}
	}
}
//////////////////////////////////////////////////////////////
//get the deal data

$g_view['deal_found'] = false;
$g_view['deal_data'] = array();
$success = $g_trans->front_get_deal_detail($g_view['deal_id'],$g_view['deal_data'],$g_view['deal_found']);
if(!$success){
die("Cannot get the deal");
}
/////////////////////////////////////////
//get deal partner data

$g_view['deal_partner_data'] = NULL;
$success = $g_company->get_company($g_view['deal_partner_id'],$g_view['deal_partner_data']);
if(!$success){
die("Cannot get the deal partner data");
}
/////////////////////////////////////////////
//get the team members with designation
$g_view['deal_partner_team_data'] = array();
$g_view['deal_partner_team_data_count'] = 0;
$success = $g_trans->get_deal_partner_team_data($g_view['deal_id'],$g_view['deal_partner_id'],$g_view['deal_partner_team_data'],$g_view['deal_partner_team_data_count']);
if(!$success){
die("Cannot get the deal partner team data");
}
//////////////////////////////////////////////
//get designations for the create colleague form
//the type is same as type of this member
$g_view['designation_list'] = array();
$g_view['designation_count'] = 0;
$success = $g_mem->get_all_designation_list_by_type($_SESSION['member_type'],$g_view['designation_list'],$g_view['designation_count']);
if(!$success){
	die("Cannot get designation list");
}

/////////////////////////////////////////////
require_once("default_metatags.php");
/**
sng:19/may/2010
we use the default search
****/
$g_view['page_heading'] = "";
$g_view['content_view'] = "deal_team_edit_view.php";
require("content_view.php");
?>