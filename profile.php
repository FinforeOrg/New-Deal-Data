<?php
/***
sng:23/apr/2010
This is to see someone else's profile

sng:28/apr/2010
If this is a profile for company representative, we show fewer data

sng:1/jun/2010
It has been decided that visitors should not be able to see banker/lawyer data. So, we show
message if the user is not logged in
********/
require_once("include/global.php");
////////////////////////////////////////////
require_once("classes/class.member.php");
require_once("classes/class.statistics.php");
require_once("classes/class.transaction.php");
require_once("classes/class.account.php");
///////////////////////
if(!$g_account->is_site_member_logged()){
	$g_view['page_heading'] = "Member Profile";
	$g_view['content_view'] = "restricted.php";
	require("content_view.php");
	exit;
}
////////////////////////////////////////////////////////////
$g_view['member_id'] = $_REQUEST['mem_id'];
////////////////////////////////////////////////////////////
$g_view['data'] = NULL;
$success = $g_mem->front_get_profile_data($g_view['member_id'],$g_view['data']);
if(!$success){
	die("Failed to fetch profile data");
}
/////////////////////////////
if($g_view['data']['member_type'] == "company rep"){
	require("company_rep_profile.php");
	return;
}
/*****************
smg:5/apr/2011
support for data partner role
*****************/
if($g_view['data']['member_type'] == "data partner"){
	require("data_partner_profile.php");
	return;
}
/**********************
sng:12/nov/2011
No need to show tombstone points since we are not implementing deal team yet
**********************/
/*************************************************************************************
get the previous work records, if any
********/
$g_view['prev_work_data'] = array();
$g_view['prev_work_count'] = 0;
$success = $g_mem->front_prev_work_list($g_view['member_id'],$g_view['prev_work_data'],$g_view['prev_work_count']);
if(!$success){
	die("Cannot fetch prev work data");
}
/*************************************************************************************
get list of members recommended by this member
********/
$g_view['recommended_data'] = array();
$g_view['recommended_count'] = 0;
$success = $g_mem->front_recommended_colleague_list($g_view['member_id'],$g_view['recommended_data'],$g_view['recommended_count']);
if(!$success){
	die("Cannot fetch members recommended");
}
/*************************************************************************************
get list of members admired by this member
********/
$g_view['admired_data'] = array();
$g_view['admired_count'] = 0;
$success = $g_mem->front_admired_competitor_list($g_view['member_id'],$g_view['admired_data'],$g_view['admired_count']);
if(!$success){
	die("Cannot fetch members admired");
}
/*************************************************************************************
get list of members recommended this member
********/
$g_view['recommended_by_data'] = array();
$g_view['recommended_by_count'] = 0;
$success = $g_mem->front_recommended_by_list($g_view['member_id'],$g_view['recommended_by_data'],$g_view['recommended_by_count']);
if(!$success){
	die("Cannot fetch members who recommended this member");
}
/*************************************************************************************
get list of members admire this member
********/
$g_view['admired_by_data'] = array();
$g_view['admired_by_count'] = 0;
$success = $g_mem->front_admired_by_list($g_view['member_id'],$g_view['admired_by_data'],$g_view['admired_by_count']);
if(!$success){
	die("Cannot fetch members who admired this member");
}
/*************************************************************************************
get the last 4 deals for tombstones
********
$g_view['tombstone_data'] = array();
$g_view['tombstone_data_count'] = 0;
$success = $g_trans->front_get_recent_deals_of_member($g_view['member_id'],4,$g_view['tombstone_data'],$g_view['tombstone_data_count']);
if(!$success){
	die("Cannot fetch tombstone data");
}*/
/*************************************************************************************
get the last 25 deals
********/
$g_view['deal_data'] = array();
$g_view['deal_count'] = 0;
$success = $g_trans->front_get_recent_deals_of_member($g_view['member_id'],25,$g_view['deal_data'],$g_view['deal_count']);
if(!$success){
	die("Cannot fetch deal data");
}

/******
sng:7/may/2010
if the viewer is logged in, we might show the recommend colleague/admire competitor
assuming I am not a company rep and I am not seeing my own profile
and I am seeing the profile of a same type of member

sng:5/apr/2011
We do not show recommend colleague/admire competitor if I am a data partner
**********/
$g_view['show_recommend_colleague'] = false;
$g_view['show_admire_competitor'] = false;

if($g_account->is_site_member_logged()){
	//logged
	if(($_SESSION['member_type']!="company rep")&&($_SESSION['member_type']!="data partner")){
		//lawyer or banker
		if($_SESSION['mem_id']!=$g_view['member_id']){
			//I am not seeing my own profile
			if($_SESSION['member_type']==$g_view['data']['member_type']){
				//seeing a same type
				if($_SESSION['company_id']==$g_view['data']['company_id']){
					//same company, so colleague
					$g_view['show_recommend_colleague'] = true;
				}else{
					//different company, so competitor
					$g_view['show_admire_competitor'] = true;
				}
			}
		}
	}
}
////////////////////////////
require_once("default_metatags.php");
$g_view['content_view'] = "profile_view.php";
require("content_view.php");
?>