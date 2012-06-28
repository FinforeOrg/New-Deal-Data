<?php
/****
this is the page that opens when deal menu item is clicked.
This is not to be confused with deal detail page.
******/
include("include/global.php");
/**********
sng:23/nov/2010
This now require login

sng:10/nov/2011
Let us make this open in data-cx
*******/
//$_SESSION['after_login'] = "deal.php";
//require_once("check_mem_login.php");

require_once("classes/class.account.php");
require_once("classes/class.transaction.php");
require_once("classes/class.company.php");
require_once("classes/class.country.php");
require_once("classes/class.deal_support.php");
$deal_support = new deal_support();
////////////////////////////////////
/**************************
sng:11/nov/2011
client want to remove the featured deal section
$g_view['featured_deal_found'] = false;
$g_view['featured_deal_data'] = array();
$success = $g_trans->front_get_random_deal_data($g_view['featured_deal_data'],$g_view['featured_deal_found']);
if(!$success){
die("Cannot get the deal");
}
*******************************/
/////////////////////////////////////////////////////////////////////////
//we need to get the data from master tables to show in dropdowns
//fetch Category names
$g_view['cat_list'] = array();
$g_view['cat_count'] = 0;
$success = $g_trans->get_all_category_type("type",$g_view['cat_list'],$g_view['cat_count']);
if(!$success){
	die("Cannot get category list");
}
//////////////////////////////////////////////////////////
//fetch regions
$g_view['region_list'] = array();
$g_view['region_count'] = 0;
$success = $g_country->get_all_region_list($g_view['region_list'],$g_view['region_count']);
if(!$success){
	die("Cannot get region list");
}
//////////////////////////////////////////////////////////
//fetch countries
$g_view['country_list'] = array();
$g_view['country_count'] = 0;
$success = $g_country->get_all_country_list($g_view['country_list'],$g_view['country_count']);
if(!$success){
	die("Cannot get country list");
}
////////////////////////////////////////////////////////
/***
sng:19/may/2010
We show sector and fetch industry as per the sector selected
*******/
//fetch sector types
$g_view['sector_list'] = array();
$g_view['sector_count'] = 0;
$success = $g_company->get_all_sector_list($g_view['sector_list'],$g_view['sector_count']);
if(!$success){
	die("Cannot get sector list");
}
//////////////////////////////////////////////
/***********************************************************
sng:27/oct/2011
We need the deal size options like in league table
***/
$g_view['deal_size_filter_list'] = array();
$g_view['deal_size_filter_list_count'] = 0;
$success = $deal_support->front_get_deal_value_range_list($g_view['deal_size_filter_list'],$g_view['deal_size_filter_list_count']);
if(!$success){
	die("Cannot get deal size filter list");
}
/****************************************************************
sng: 2/nov/2011
client want to preselect the 2010-2011 by default
The dropdown is in deal_search_filter_form_view.php, which is included in deal_view.php
In deal_search_filter_form_view.php, the code checks for $_POST['year'].
Problem is, we cannot hardcode 2010-2011. The figures are created on the fly based on current year.
It is (curr_year-1)-(curr_year)
***************/
$y = date("Y");
$_POST['year'] = sprintf('%s-%s',$y-1,$y);
/*******************************************************************/
require_once("default_metatags.php");
$g_view['page_heading'] = "Search for a Deal";
$g_view['show_help'] = true;
$g_view['content_view'] = "deal_view.php";
$categories = $g_trans->getCategoryTree();
require("content_view.php");
?>