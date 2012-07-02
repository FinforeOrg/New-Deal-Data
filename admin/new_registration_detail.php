<?php
/***
sng:7/apr/2010
we have changed the method names, now it it accept_registration_request and reject_registration_request
***********/
include("../include/global.php");
require_once ("admin/checklogin.php");
require_once("classes/class.country.php");
require_once("classes/class.member.php");
require_once("classes/class.company.php");
require_once("classes/class.magic_quote.php");
///////////////////////////////////////////////////////
$g_view['err'] = array();
$g_view['msg'] = "";
///////////////////////////////////////
if(isset($_POST['action'])&&($_POST['action']=="accept")){
	
	$success = $g_mem->accept_registration_request($_POST);
	if(!$success){
		die("Could not accept registration request");
	}
	else
	{
		$g_view['msg'] = "Registration request accepted successfully";
	}	
	
}
///////////////////////////reject member/////////////////////////////////////
if(isset($_POST['action'])&&($_POST['action']=="reject")){
	
	
	$success = $g_mem->reject_registration_request($_POST['uid'],$_POST['reject_reason']);
	if(!$success){
		die("Cannot reject registration request data");
	}
	else
	{
	  header("location:member_newreg_list.php");
	}
	
}
/////////////////////////////////////////////////////////////////////////
//get the request data

$g_view['data'] = NULL;
$success = $g_mem->get_new_reg_req($_POST['uid'],$g_view['data']);
if(!$success){
	die("Cannot get reg req data");
}
/////////////////////////////////////////////////////////////
/***
sng:6/apr/2010
check if the company name is given properly or not
If the name is not found, then a near match is tried.
If there is only one match, then no ambiguity else ambiguity
If found then no problem
***/
$g_view['company_ambiguous'] = false;
if($g_view['data']['member_type']=='banker') $company_type="bank";
elseif($g_view['data']['member_type']=='lawyer') $company_type="law firm";
elseif($g_view['data']['member_type']=='company rep') $company_type="company";
/*********************
sng:5/apr/2011
Data partners are all associated with company. Were they wanted to be assiciated with bank, they would have selected bank as member type
**************************/
elseif($g_view['data']['member_type'] == 'data partner') $company_type = 'company';
//////////////////////////////////////////////////////////////////////////////////////
//try to get the company id by doing an exact match
$comp_id_q = "SELECT name,company_id FROM ".TP."company WHERE type='".$company_type."' and name='".$g_view['data']['company_name']."'";
$comp_id_q_res = mysql_query($comp_id_q) or die("Cannot get company id");
//the company record may or may not be there
$comp_id_q_res_cnt = mysql_num_rows($comp_id_q_res);
if(0 == $comp_id_q_res_cnt){
	//not found, check for a near match
	$near_id_q = "select name,company_id FROM ".TP."company WHERE type='".$company_type."' and name like '".$g_view['data']['company_name']."%'";
	$near_id_q_res = mysql_query($near_id_q) or die("Cannot get near match");
	$near_id_q_res_cnt = mysql_num_rows($near_id_q_res);
	if(0 == $near_id_q_res_cnt){
		//no exact match, no near match
		$g_view['company_status'] = "This company was not found in the database";
		$g_view['company_id'] = 0;
	}else{
		//there is a near match, is it single
		if(1 == $near_id_q_res_cnt){
			$near_id_q_res_row = mysql_fetch_assoc($near_id_q_res);
			$g_view['company_status'] = "Not found but matched ".$g_mc->db_to_view($near_id_q_res_row['name']);
			$g_view['company_id'] = $near_id_q_res_row['company_id'];
		}else{
			//more than one match
			$g_view['company_ambiguous'] = true;
			$g_view['company_status'] = "Ambiguous, more than one match";
			$g_view['company_id'] = 0;
		}
	}	
}else{
	//found in the database
	$comp_id_q_res_row = mysql_fetch_assoc($comp_id_q_res);
	$g_view['company_status'] = "Present";
	$g_view['company_id'] = $comp_id_q_res_row['company_id'];
}

		     

///////////////////////////////////////////////////////////
$g_view['heading'] = "Member ragistration request data";
$g_view['content_view'] = "admin/new_registration_detail_view.php";
include("admin/content_view.php");
?>