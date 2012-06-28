<?php
/*****************
sng:3/aug/2011

A hack to support the viewing of tombstones of competitors utilizing the existing code
This basically opens the data entry form which was there is showcase_firm_view_savedSearches.php

This is accessible only to bankers and lawyers

sng:9/Nov/2011
let us make this open
********************/
require_once("include/global.php");
require_once("classes/class.company.php");
//$_SESSION['after_login'] = "competitor_credentials.php";
//require_once("check_mem_login.php");

//if(($_SESSION['member_type']!="banker")&&($_SESSION['member_type']!="lawyer")){
//	$g_view['page_content'] = "This section is only for bankers and lawyers";
//	require("not_authorised.php");
//	exit; 
//}
/**************************
sng:10/nov/2011
We need to show the top banks also
We show the firms from dfault category
**************************/
$g_view['top_firm_cat_id'] = "";
//get the top banks
$g_view['bank_data'] = array();
$g_view['bank_data_count'] = 0;
$success = $g_company->front_get_top_firms_list_by_type("bank",$g_view['top_firm_cat_id'],$g_view['bank_data'],$g_view['bank_data_count']);
if(!$success){
	die("Cannot get top banks");
}
//////////////////////////////////////
//get the top law firms
$g_view['lawfirm_data'] = array();
$g_view['lawfirm_data_count'] = 0;
$success = $g_company->front_get_top_firms_list_by_type("law firm",$g_view['top_firm_cat_id'],$g_view['lawfirm_data'],$g_view['lawfirm_data_count']);
if(!$success){
	die("Cannot get top law firms");
}

require_once("default_metatags.php");
$g_view['page_heading'] = "";
//no heading since this contains a form that will send to another page (that has its own heading) and then user go to another
//page (that has its own heading)
/****************************************
sng:21/nov/2011
We have the default search and set the dropdown option to 'bank' if top_search_area is not set
***/
if(!isset($_POST['top_search_area'])){
	$_POST['top_search_area'] = "bank";
}
/**************************************************/
$g_view['content_view'] = "competitor_credentials_view.php";
require("content_view.php");
?>