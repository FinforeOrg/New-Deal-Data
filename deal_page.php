<?php
/***********************************************************
The new deal detail page

sng:27/may/2010
Some feature requires that the user is logged in, else those are not visible

sng:8/apr/2011
deals can have discussion. It will not be visible to every one
************************************************************/
require_once("include/global.php");
require_once("classes/class.transaction.php");
require_once("classes/class.deal_support.php");
require_once("classes/class.account.php");

require_once("classes/class.country.php");
require_once("classes/class.company.php");

require_once("classes/class.transaction_discussion.php");
require_once("classes/class.transaction_verification.php");

$deal_support = new deal_support();
$trans_verification = new transaction_verification();

$g_view['deal_id'] = $_REQUEST['deal_id'];
$g_view['deal_found'] = false;
$g_view['deal_data'] = array();
$success = $g_trans->front_get_deal_detail_extra($g_view['deal_id'],$g_view['deal_data'],$g_view['deal_found']);
if(!$success){
	die("Cannot get the deal");
}
/**********************************************************************
fetch headquarter_country names
***/
$g_view['country_list'] = array();
$g_view['country_count'] = 0;
$success = $g_country->get_all_country_list($g_view['country_list'],$g_view['country_count']);
if(!$success){
	die("Cannot get country list");
}
/**************************************************************
fetch sector list
**************/
$g_view['sector_list'] = array();
$g_view['sector_count'] = 0;
$success = $g_company->get_all_sector_list($g_view['sector_list'],$g_view['sector_count']);
if(!$success){
	die("Cannot get sector list");
}
/**********************************
sng:17/nov/2011
Get the accepted file extensions for case studies. This returns an array
of name value pairs.
*************************************/
$temp_data = $g_trans->file_extensions_for_case_studies();
$g_view['valid_file_extensions_for_case_study_doc'] = array_keys($temp_data);
/************************************
sng:18/nov/2011
Get the access rules for case study (who can see a case study)
***/
$g_view['case_study_view_rules'] = $g_trans->access_rules_for_case_studies();

/***********************************
sng:17/nov/2011
Now we load the case studies via ajax, when we view the case study tab
***************************************/


$g_view['can_upload_case_study'] = false;
/***************
sng:27/sep/2011
let us have a var $g_view['is_mem_firm_associated'] so that case study code and mark bank insignificant code
can take decision
**********************/
$g_view['is_mem_firm_associated'] = false;
$success = $g_trans->is_firm_associated_with_deal($g_view['deal_id'],$_SESSION['company_id'],$g_view['is_mem_firm_associated']);
if(!$success){
	die("Cannot determine if the firm is associated with the deal");
}
//if this member's firm is associated with the deal, this fellow can upload
$g_view['can_upload_case_study'] = $g_view['is_mem_firm_associated'];
/******************************************************************************/
/**************************************************************************
deal discussion
*****/
//check access
$g_view['show_discussion'] = false;
$success = $g_deal_disc->can_see($g_view['deal_id'],$g_view['show_discussion']);
if(!$success){
	die("Cannot determine whether the user can access deal discussion or not");
}
/*********
we fetch the comments via ajax
*********/
/*******************
sng:6/mar/2012
We need to fetch the varification summery list for this deal
It will be like
Verified by 2 Bankers at Morgan Stanley
Verified by 1 Lawyer at Freshfields
*************/
$g_view['verify_count'] = 0;
$g_view['verify_data'] = NULL;
$ok = $trans_verification->member_verification_summery($g_view['deal_id'],$g_view['verify_data'],$g_view['verify_count']);
if(!ok){
	die("Cannot get verification summery for the deal");
}
/****************************************/
require_once("default_metatags.php");
/**
sng:19/may/2010
we use the default search
****/

$g_view['page_heading'] = "";
$g_view['content_view'] = "deal_page_view.php";
require("content_view.php");
?>