<?php
include("../include/global.php");
require_once ("admin/checklogin.php");
require_once("classes/class.country.php");
require_once("classes/class.company.php");
require_once("classes/class.transaction.php");
require_once("classes/class.magic_quote.php");
require_once("classes/class.deal_support.php");
$deal_support = new deal_support();
///////////////////////////////////////////////////////
$g_view['msg'] = "";
/*******************
sng:28/sep/2012
We have changed the signature of the admin_search_for_deal. We now support pagination.
However, we are not using any pagination here. We just get the first 100 matching records
********************/
$g_view['start_offset'] = 0;
$g_view['num_to_fetch'] = 100;
/////////////////////////////////////////////
if(isset($_POST['action'])&&($_POST['action']=="del")){
	//first delete the transaction
	$success = $g_trans->delete_transaction($_POST['deal_id']);
	if(!$success){
		die("Cannot delete the transaction");
	}
	//now do the search result again
	$g_view['data_count'] = 0;
	$g_view['data'] = NULL;
	$success = $g_trans->admin_search_for_deal($_POST,$g_view['start_offset'],$g_view['num_to_fetch'],$g_view['data'],$g_view['data_count']);
	if(!$success){
		die("Cannot get deal data");
	}
}
///////////////////////////////////////
if(isset($_POST['action'])&&($_POST['action']=="search_deal")){
	$g_view['data_count'] = 0;
	$g_view['data'] = NULL;
	$success = $g_trans->admin_search_for_deal($_POST,$g_view['start_offset'],$g_view['num_to_fetch'],$g_view['data'],$g_view['data_count']);
	if(!$success){
		die("Cannot get deal data");
	}
}

if(isset($_POST['action'])&&($_POST['action']=="search_deal_by_id")){
	$g_view['data_count'] = 0;
	$g_view['data'] = NULL;
	$success = $g_trans->admin_search_for_deal($_POST,$g_view['start_offset'],$g_view['num_to_fetch'],$g_view['data'],$g_view['data_count']);
	if(!$success){
		die("Cannot get deal data");
	}
}
////////////////////////////////////////////////////////
//we need to get the data from master tables to show in dropdowns
//fetch Category names
$g_view['cat_list'] = array();
$g_view['cat_count'] = 0;
$success = $g_trans->get_all_category_type("type",$g_view['cat_list'],$g_view['cat_count']);
if(!$success){
	die("Cannot get category list");
}
//////////////////////////////////////////////
//fetch subcategories for this category
$g_view['subcat_list'] = array();
$g_view['subcat_count'] = 0;
$success = $g_trans->get_all_category_subtype1_for_category_type($_POST['deal_cat_name'],$g_view['subcat_list'],$g_view['subcat_count']);
if(!$success){
	die("Cannot get sub category list");
}
//////////////////////////////////////////////////////
//fetch sub subcategories for this category
$g_view['sub_subcat_list'] = array();
$g_view['sub_subcat_count'] = 0;
$success = $g_trans->get_all_category_subtype2_for_category_type($_POST['deal_cat_name'],$_POST['deal_subcat1_name'],$g_view['sub_subcat_list'],$g_view['sub_subcat_count']);
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
//////////////////////////////////////////////////////////
//fetch countries
$g_view['country_list'] = array();
$g_view['country_count'] = 0;
$success = $g_country->get_all_country_list($g_view['country_list'],$g_view['country_count']);
if(!$success){
	die("Cannot get country list");
}
////////////////////////////////////////////////////////
/**************************
sng:31/aug/2010
fetch sector
*******/
$g_view['sector_list'] = array();
$g_view['sector_count'] = 0;
$success = $g_company->get_all_sector_list($g_view['sector_list'],$g_view['sector_count']);
if(!$success){
	die("Cannot get sector list");
}
/*************************
sng:31/aug/2010
fetch industry as per sector selected
***********/
$g_view['industry_list'] = array();
$g_view['industry_count'] = 0;
$success = $g_company->get_all_industry_for_sector($_POST['sector'],$g_view['industry_list'],$g_view['industry_count']);
if(!$success){
	die("Cannot get industry list");
}
/****************************************************
sng:28/sep/2012
Now deals can have exact value, as well as fuzzy value (in the form of range id). In fact, even if exact value is specified,
the range is calculated and stored. In front end, we filter by the range. We do the same here and get rid of min value and max value inputs
*********************/
$g_view['deal_size_filter_list'] = array();
$g_view['deal_size_filter_list_count'] = 0;
$success = $deal_support->front_get_deal_value_range_list($g_view['deal_size_filter_list'],$g_view['deal_size_filter_list_count']);
if(!$success){
	die("Cannot get deal size filter list");
}
/******************************************************/
$g_view['heading'] = "Search for Transaction";
$g_view['content_view'] = "admin/deal_search_view.php";
include("admin/content_view.php");
?>