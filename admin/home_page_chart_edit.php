<?php
include("../include/global.php");
require_once ("admin/checklogin.php");
require_once("classes/class.magic_quote.php");
require_once("classes/class.statistics.php");
require_once("classes/class.transaction.php");
require_once("classes/class.country.php");
require_once("classes/class.company.php");
///////////////////////////////////////////////////////
$g_view['err'] = array();
$g_view['msg'] = "";
$g_view['data'] = array();
$g_view['id'] = $_POST['id'];
/***
sng:26/may/2010
pagination support, so that we can go back to the correct location in listing
******/
$g_view['start'] = 0;
if(isset($_REQUEST['start'])&&($_REQUEST['start']!="")){
	$g_view['start'] = $_REQUEST['start'];
}
///////////////////////////////////////////////////////////
if(isset($_POST['action'])&&($_POST['action']=="edit")){
     
	$validation_passed = false;
	$success = $g_stat->update_home_page_chart_image($g_view['id'],$_POST,$validation_passed,$g_view['err']);
	
	if(!$success){
		die("Cannot update chart image");
	}
	if($validation_passed){
		$g_view['msg'] = "Chart updated";
	}
}
///////////////////////////////////////////////////////
if(isset($_POST['action'])&&($_POST['action']=="assign_to_firm")){
	$validation_passed = false;
	$success = $g_stat->assign_chart_to_firm($g_view['id'],$_POST,$validation_passed,$g_view['err']);
	
	if(!$success){
		die("Cannot assign chart to firm");
	}
	if($validation_passed){
		$g_view['msg'] = "Chart assigned to firm";
	}
}
///////////////////////////////////////////////////////////
/****************
26/sep/2011
admin will be able to remove an associated firm
*******************/
if(isset($_POST['action'])&&($_POST['action']=="remove_firm")){
	$validation_passed = false;
	$success = $g_stat->remove_firm_from_chart($_POST['firm_assoc_id']);
	
	if(!$success){
		die("Cannot remove firm from chart");
	}
	if($validation_passed){
		$g_view['msg'] = "Firm removed from chart";
	}
}
//////////////////////////////////////////////////////
//get the data
$success = $g_stat->get_home_page_chart_data($g_view['id'],$g_view['data']);
if(!$success){
	die("Cannot get home page chart data");
}
////////////////////////////////////////////////////
//fetch Category names
$g_view['cat_list'] = array();
$g_view['cat_count'] = 0;
//$success = $g_trans->get_all_category_list($g_view['cat_list'],$g_view['cat_count']);
$success = $g_trans->get_all_category_type("type",$g_view['cat_list'],$g_view['cat_count']);
if(!$success){
	die("Cannot get category list");
}
//////////////////////////////////////////////
//fetch subcategories for this category
$g_view['subcat_list'] = array();
$g_view['subcat_count'] = 0;
$success = $g_trans->get_all_category_subtype1_for_category_type($g_view['data']['deal_cat_name'],$g_view['subcat_list'],$g_view['subcat_count']);
if(!$success){
	die("Cannot get sub category list");
}
//////////////////////////////////////////////////////
//fetch sub subcategories for this category
$g_view['sub_subcat_list'] = array();
$g_view['sub_subcat_count'] = 0;
$success = $g_trans->get_all_category_subtype2_for_category_type($g_view['data']['deal_cat_name'],$g_view['data']['deal_subcat1_name'],$g_view['sub_subcat_list'],$g_view['sub_subcat_count']);
if(!$success){
	die("Cannot get sub sub category list");
}
/////////////////////////////////////////////////////
//fetch regions
$g_view['region_list'] = array();
$g_view['region_count'] = 0;
$success = $g_country->get_all_region_list($g_view['region_list'],$g_view['region_count']);
if(!$success){
	die("Cannot get region list");
}
//////////////////////////////////////////////////////
//fetch countries
$g_view['country_list'] = array();
$g_view['country_count'] = 0;
$success = $g_country->get_all_country_list($g_view['country_list'],$g_view['country_count']);
if(!$success){
	die("Cannot get country list");
}
/////////////////////////////////////////////////////////
//fetch sector names
$g_view['sector_list'] = array();
$g_view['sector_count'] = 0;
//$success = $g_trans->get_all_category_list($g_view['cat_list'],$g_view['cat_count']);
$success = $g_company->get_all_sector_list($g_view['sector_list'],$g_view['sector_count']);
if(!$success){
	die("Cannot get sector list");
}
//////////////////////////////////////////////
//fetch industries for this sector
$g_view['industry_list'] = array();
$g_view['industry_count'] = 0;
$success = $g_company->get_all_industry_for_sector($g_view['data']['sector'],$g_view['industry_list'],$g_view['industry_count']);
if(!$success){
	die("Cannot get industry list");
}
//////////////////////////////////////////////////////
/****
sng:29/sep/2010
now the years will be a list of ranges. The ranges are defined in a master table
we store the range id
*********/
$g_view['date_list'] = array();
$g_view['date_count'] = 0;
$success = $g_trans->front_get_all_date_range($g_view['date_list'],$g_view['date_count']);
if(!$success){
	die("Cannot get date list");
}
////////////////////////////////////////////////////
/************************************************************
sng:22/sep/2011
we now show the firms associated with the chart
*************/
$g_view['firm_list'] = NULL;
$g_view['firm_count'] = 0;
$success = $g_stat->firms_associated_with_chart($g_view['id'],$g_view['firm_list'],$g_view['firm_count']);
if(!$success){
	die("Cannot get firm list");
}
/************************************************************/
$g_view['heading'] = "Edit Home Page Chart";
$g_view['content_view'] = "admin/home_page_chart_edit_view.php";
include("admin/content_view.php");
?>