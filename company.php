<?php
/****
This page appears when the companies link is clicked. By default, it shows company
description and recent deals of a featured company (of type company)

sng:29/apr/2010
we may get the company id from POST or GET, so

sng:2/sep/2010
support for extended company search via filters
****/
require_once("include/global.php");
/**********
sng:23/nov/2010
This now require login

sng:9/nov/2011
Here we keep it open
*******/
//$_SESSION['after_login'] = "company.php";
//require_once("check_mem_login.php");

require_once("classes/class.company.php");
require_once("classes/class.transaction.php");
require_once('classes/class.account.php');
//////////////////////////////////////////////////////////////////
/*****************
sng:7/sep/2011
factored out the common code to get the recent deals
*********************/
if(isset($_REQUEST['show_company_id'])&&($_REQUEST['show_company_id']!="")){
	$g_view['company_data'] = NULL;
	$success = $g_company->get_company($_REQUEST['show_company_id'],$g_view['company_data']);
	if(!$success){
		die("Cannot get company data");
	}
	$g_view['curr_company_id'] = $g_view['company_data']['company_id'];
	$g_view['company_heading'] = $g_view['company_data']['name'];
	/*********************
	sng:22/nov/2011
	since I am now seeing the company I want, let us not show the extended search dropdowns. This means
	the page heading should also change
	*******************/
	$g_view['show_search'] = false;
	$g_view['page_heading'] = "Company Detail";
	
}else{
	//by default we show the featured company page
	$g_view['company_data'] = NULL;
	$success = $g_company->get_featured_company($g_view['company_data']);
	if(!$success){
		die("Cannot get fetatured company data");
	}
	$g_view['curr_company_id'] = $g_view['company_data']['company_id'];
	$g_view['company_heading'] = "Featured Company: ".$g_view['company_data']['name'];
	/*********************
	sng:22/nov/2011
	since I am not seeing any company, show the serch dropdowns
	*******************/
	$g_view['show_search'] = true;
	$g_view['page_heading'] = "Search for Companies";
}
/////////////////////////////////////////////////////////////////////////////////////////////
//get the 10 most recent deals
$g_view['deal_data'] = array();
$g_view['deal_count'] = 0;
$g_view['max_deals'] = 10;
$success = $g_trans->front_get_recent_transactions($g_view['curr_company_id'],$g_view['max_deals'],$g_view['deal_data'],$g_view['deal_count']);
if(!$success){
	die("Cannot get deal data");
}
/*******************
sng:7/sep/2011
Get the identifiers

sng:21/nov/2011
Now we need the value for all the identifiers, even if it is not set. We use front_get_company_identifiers instead of get_company_identifiers
**************/
$g_view['identifiers'] = NULL;
$g_view['identifiers_cnt'] = 0;
$success = $g_company->front_get_company_identifiers($g_view['curr_company_id'],$g_view['identifiers'],$g_view['identifiers_cnt']);
if(!$success){
	die("Cannot get the identifiers");
}
/////////////////////////////////////////////////////////////////////////////////////////////
require_once("company_extended_search_support.php");
//////////////////////////////////////////////////////////////////////////
require_once("default_metatags.php");
/////////////////////////////////////////////////////////////////////////////////
/**
sng:19/may/2010
The default search will be used to search for company

sng:21/nov/2011
We have the default search and set the dropdown option to 'company' if top_search_area is not set
***/
if(!isset($_POST['top_search_area'])){
	$_POST['top_search_area'] = "company";
}

/***********************
sng:22/nov/2011
The heading change based on whether I am viewing a specific company or just came to the page to search.
******************/
$g_view['show_help'] = true;
$g_view['content_view'] = "company_view.php";
require("content_view.php");
?>