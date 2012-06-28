<?php
/****
deal team page for a partner associated with a deal
Here we also show the deal data also

sng:27/may/2010
Client want to show team data only to logged in members
******/
include("include/global.php");
require_once("check_mem_login.php");
///////////////////////////////////////////////////
require_once("classes/class.transaction.php");
require_once("classes/class.company.php");
/////////////////////////////////////////////////////
//get the deal data
$g_view['deal_id'] = $_REQUEST['deal_id'];
$g_view['deal_found'] = false;
$g_view['deal_data'] = array();
$success = $g_trans->front_get_deal_detail($g_view['deal_id'],$g_view['deal_data'],$g_view['deal_found']);
if(!$success){
die("Cannot get the deal");
}
//////////////////////////////////////////
//get deal partner data
$g_view['deal_partner_id'] = $_REQUEST['partner_id'];
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
/////////////////////////////////////////////
/********
sng:5/may/2010
we need the adjusted value for this partner company.
Now, from deal_partner_data, we get the type of the partner, then from deal data, we get adjusted value
for bank/law firm
*************/
if($g_view['deal_partner_data']['type']=="bank") $g_view['adjusted_value_for_firm_in_billion'] = $g_view['deal_data']['banks'][0]['adjusted_value_in_billion'];
if($g_view['deal_partner_data']['type']=="law firm") $g_view['adjusted_value_for_firm_in_billion'] = $g_view['deal_data']['law_firms'][0]['adjusted_value_in_billion'];
//////////////////////////////////////////////
require_once("default_metatags.php");
/**
sng:19/may/2010
we use the default search
****/
$g_view['page_heading'] = "";
$g_view['content_view'] = "deal_team_view.php";
require("content_view.php");
?>