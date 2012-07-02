<?php
/*************************************
sng:10/jan/2011
Year/month division can now be quarterly, yearly, half yearly and the user can select the starting quarter
half year or year
q: quarterly
h: half yearly
y: yearly
************************/
include("../include/global.php");
require_once ("admin/checklogin.php");
require_once("classes/class.transaction.php");
require_once("classes/class.country.php");
require_once("classes/class.company.php");
require_once("classes/class.magic_quote.php");
require_once("classes/class.statistics.php");
///////////////////////////////////////////////////////
$g_view['err'] = array();
$g_view['msg'] = "";
$g_view['input'] = array();
///////////////////////////////////////////////////////////
if(isset($_POST['action'])&&($_POST['action']=="create")){
     
	$validation_passed = false;
	$success = $g_stat->generate_issuance_page_chart_image($_POST,$validation_passed,$g_view['err']);
	
	if(!$success){
		die("Cannot create chart image");
	}
	if($validation_passed){
		$g_view['msg'] = "Chart created";
	}else{
		//the form is to be shown with data just entered
		$g_view['input']['name'] = $g_mc->view_to_view($_POST['name']);
		$g_view['input']['deal_cat_name'] = $_POST['deal_cat_name'];
		$g_view['input']['deal_subcat1_name'] = $_POST['deal_subcat1_name'];
		$g_view['input']['deal_subcat2_name'] = $_POST['deal_subcat2_name'];
		$g_view['input']['region'] = $_POST['region'];
		$g_view['input']['country'] = $_POST['country'];
		$g_view['input']['sector'] = $_POST['sector'];
		$g_view['input']['industry'] = $_POST['industry'];
		
		$g_view['input']['deal_size'] = $_POST['deal_size'];
		$g_view['input']['month_division'] = $_POST['month_division'];
		$g_view['input']['month_division_list'] = $_POST['month_division_list'];
	}
}
//////////////////////////////////////////////////////////
//fetch Category names
$g_view['cat_list'] = array();
$g_view['cat_count'] = 0;
//$success = $g_trans->get_all_category_list($g_view['cat_list'],$g_view['cat_count']);
$success = $g_trans->get_all_category_type("type",$g_view['cat_list'],$g_view['cat_count']);
if(!$success){
	die("Cannot get category list");
}
//////////////////////////////////////////////////////
//fetch subcategories for this category
$g_view['subcat_list'] = array();
$g_view['subcat_count'] = 0;
//$success = $g_trans->get_all_category_list($g_view['cat_list'],$g_view['cat_count']);
$success = $g_trans->get_all_category_subtype1_for_category_type($g_view['input']['deal_cat_name'],$g_view['subcat_list'],$g_view['subcat_count']);
if(!$success){
	die("Cannot get sub category list");
}
////////////////////////////////////////////////////////////
//fetch sub subcategories for this category
$g_view['sub_subcat_list'] = array();
$g_view['sub_subcat_count'] = 0;
//$success = $g_trans->get_all_category_list($g_view['cat_list'],$g_view['cat_count']);
$success = $g_trans->get_all_category_subtype2_for_category_type($g_view['input']['deal_cat_name'],$g_view['input']['deal_subcat1_name'],$g_view['sub_subcat_list'],$g_view['sub_subcat_count']);
if(!$success){
	die("Cannot get sub sub category list");
}
///////////////////////////////////////////////////////////////////////
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
//////////////////////////////////////////////////
/***
sng:20/may/2010
we need sector and industry support
******/
//fetch sector names
$g_view['sector_list'] = array();
$g_view['sector_count'] = 0;
//$success = $g_trans->get_all_category_list($g_view['cat_list'],$g_view['cat_count']);
$success = $g_company->get_all_sector_list($g_view['sector_list'],$g_view['sector_count']);
if(!$success){
	die("Cannot get sector list");
}
//////////////////////////////////////////////////////
//fetch industries for this sector
$g_view['industry_list'] = array();
$g_view['industry_count'] = 0;
//$success = $g_trans->get_all_category_list($g_view['cat_list'],$g_view['cat_count']);
$success = $g_company->get_all_industry_for_sector($g_view['input']['sector'],$g_view['industry_list'],$g_view['industry_count']);
if(!$success){
	die("Cannot get industry list");
}
////////////////////////////////////////////////////
/***
sng:23/july/2010
There is another filter on deal size
*******/
$g_view['deal_size_filter_list'] = array();
$g_view['deal_size_filter_list_count'] = 0;
$success = $g_trans->front_get_deal_size_filter_list($g_view['deal_size_filter_list'],$g_view['deal_size_filter_list_count']);
if(!$success){
	die("Cannot get deal size filter list");
}
///////////////////////////////////////////////////////////
//by default, let us have the grouping to half year
if(!isset($_POST['month_division'])||($_POST['month_division']=="")){
	$g_view['input']['month_division'] = "h";
}
//get the month div list based on the month div selected
$g_view['month_div'] = array();
$g_view['month_div']['value_arr'] = NULL;
$g_view['month_div']['label_arr'] = NULL;
$g_view['month_div_cnt'] = 0;
$g_stat_h->volume_get_month_div_entries($g_view['input']['month_division'],$g_view['month_div']['value_arr'],$g_view['month_div']['label_arr']);
$g_view['month_div_cnt'] = count($g_view['month_div']['value_arr']);
//By default, the first value of the list is to be selected
if(!isset($_POST['month_division_list'])||($_POST['month_division_list']=="")){
	$g_view['input']['month_division_list'] = $g_view['month_div']['value_arr'][0];
}
///////////////////////////////////////////////////////////
$g_view['heading'] = "Add Issuance Page Chart";
$g_view['content_view'] = "admin/issuance_page_chart_add_view.php";
include("admin/content_view.php");
?>