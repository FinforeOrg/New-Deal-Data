<?php
include("include/global.php");
require_once("check_mem_login.php");
////////////////////////////////////////////////////////////
require_once("default_metatags.php");
require_once("classes/class.statistics.php");
require_once("classes/class.member.php");
require_once("classes/class.transaction.php");
//////////////////////////////////////////////////////////////////
$g_view['page_heading'] = "";
$g_view['my_total_points'] = 0;
$success = $g_stat->front_get_total_deal_value_of_member($_SESSION['mem_id'],$g_view['my_total_points'],false);
if(!$success){
	die("Cannot fetch total points for the member");
}
/***
sng:13/may/2010
we convert to million in the view file
**/
//////////////////////////////////////////////////////
$g_view['my_last_3_months_total_points'] = 0;
$success = $g_stat->front_get_total_deal_value_of_member($_SESSION['mem_id'],$g_view['my_last_3_months_total_points'],true);
if(!$success){
	die("Cannot fetch total points for the member");
}
/***
sng:13/may/2010
we convert to million in the view file
**/
////////////////////////////////////////////////////
/*************
sng:22/july/2010
We show 8 recent tombstones of this firm

sng:23/july/2010
client sqaid 4 tombstones so that it is in one row
***********/
$g_view['data'] = array();
$g_view['data_count'] = 0;
$success = $g_trans->get_recent_deal_ids_of_firm($_SESSION['company_id'],4,$g_view['data'],$g_view['data_count']);
if(!$success){
	die("Cannot get tombstones");
}
///////////////////////////////////////////////////
//collegue
$g_view['collegue_data'] = NULL;
$g_view['collegue_count'] = 0;
$success = $g_mem->front_get_random_collegue($_SESSION['mem_id'],$g_view['collegue_data'],$g_view['collegue_count']);
if(!$success){
	die("Failed to fetch collegue");
}
//get collegue tombstone points
$g_view['collegue_total_points'] = 0;
$success = $g_stat->front_get_total_deal_value_of_member($g_view['collegue_data']['mem_id'],$g_view['collegue_total_points'],false);
if(!$success){
	die("Cannot fetch total points of collegue");
}
/***
sng:13/may/2010
we convert to million in the view file
**/
//////////////////////////////////////////////////////
$g_view['collegue_last_3_months_total_points'] = 0;
$success = $g_stat->front_get_total_deal_value_of_member($g_view['collegue_data']['mem_id'],$g_view['collegue_last_3_months_total_points'],true);
if(!$success){
	die("Cannot fetch total points for collegue");
}
/***
sng:13/may/2010
we convert to million in the view file
**/
//////////////////////////////////////////////////////
//get 3 recent tombstones. for that, we need 3 deals in which this colleague was a part
/*****
sng:19/oct/2010
Client just want to show the tombstones of my firm and not the tombstones of any collegue or competitor
***/
/****
$g_view['colleague_last_3_tombstone'] = array();
$g_view['colleague_last_3_tombstone_count'] = 0;
$success = $g_trans->front_get_recent_deals_of_member($g_view['collegue_data']['mem_id'],3,$g_view['colleague_last_3_tombstone'],$g_view['colleague_last_3_tombstone_count']);
if(!$success){
	die("Cannot get last 3 deals of colleague");
}
*****/
///////////////////////////////////////////////////////
//competitor
$g_view['competitor_data'] = NULL;
$g_view['competitor_count'] = 0;
$success = $g_mem->front_get_random_competitor($_SESSION['mem_id'],$g_view['competitor_data'],$g_view['competitor_count']);
if(!$success){
	die("Failed to fetch competitor");
}
//get competitor tombstone points
$g_view['competitor_total_points'] = 0;
$success = $g_stat->front_get_total_deal_value_of_member($g_view['competitor_data']['mem_id'],$g_view['competitor_total_points'],false);
if(!$success){
	die("Cannot fetch total points of competitor");
}
/***
sng:13/may/2010
we convert to million in the view file
**/
//////////////////////////////////////////////////////
$g_view['competitor_last_3_months_total_points'] = 0;
$success = $g_stat->front_get_total_deal_value_of_member($g_view['competitor_data']['mem_id'],$g_view['competitor_last_3_months_total_points'],true);
if(!$success){
	die("Cannot fetch total points for competitor");
}
/***
sng:13/may/2010
we convert to million in the view file
**/
//////////////////////////////////////////////////////////
//get 3 recent tombstones. for that, we need 3 deals in which this competitor was a part
/*****
sng:19/oct/2010
Client just want to show the tombstones of my firm and not the tombstones of any collegue or competitor
***/
/*****
$g_view['competitor_last_3_tombstone'] = array();
$g_view['competitor_last_3_tombstone_count'] = 0;
$success = $g_trans->front_get_recent_deals_of_member($g_view['competitor_data']['mem_id'],3,$g_view['competitor_last_3_tombstone'],$g_view['competitor_last_3_tombstone_count']);
if(!$success){
	die("Cannot get last 3 deals of competitor");
}
********/
/////////////////////////////////////////////////////////
$g_view['content_view'] = "member_home_view.php";
require("content_view.php");
?>