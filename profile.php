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

sng:12/nov/2011
No need for recommend / admire for now since in the current data-cx, no one can see profile of another
**********************/
/************************************
//get the last 25 deals
$g_view['deal_data'] = array();
$g_view['deal_count'] = 0;
$success = $g_trans->front_get_recent_deals_of_member($g_view['member_id'],25,$g_view['deal_data'],$g_view['deal_count']);
if(!$success){
	die("Cannot fetch deal data");
}
**********************************************/
////////////////////////////////////
//get the previous work records, if any
$g_view['prev_work_data'] = array();
$g_view['prev_work_count'] = 0;
$success = $g_mem->front_prev_work_list($g_view['member_id'],$g_view['prev_work_data'],$g_view['prev_work_count']);
if(!$success){
	die("Cannot fetch prev work data");
}
////////////////////////////////////
/**********************************************
//get the last 4 deals for tombstones
$g_view['tombstone_data'] = array();
$g_view['tombstone_data_count'] = 0;
$success = $g_trans->front_get_recent_deals_of_member($g_view['member_id'],4,$g_view['tombstone_data'],$g_view['tombstone_data_count']);
if(!$success){
	die("Cannot fetch tombstone data");
}
***********************************************/
////////////////////////////////////
/*************************************
sng:12/nov/2011
No need for recommend / admire for now since in the current data-cx, no one can see profile of another
**********************/
////////////////////////////
require_once("default_metatags.php");
$g_view['content_view'] = "profile_view.php";
require("content_view.php");
?>