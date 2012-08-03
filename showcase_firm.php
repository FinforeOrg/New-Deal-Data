<?php
/*****
This is used to show case a bank or a law firm.
This shows 25 latest tombstones and a link to charts that make this firm look good
********/
include("include/global.php");
/**************
sng:7/apr/2011
this requires login

sng:9/nov/2011
Here we keep it open
**********/
//$_SESSION['after_login'] = "showcase_firm.php?id=".$_REQUEST['id'];
/***********************
sng:24/oct/2011
*************************/
//if (isset($_GET['from']) && $_GET['from'] = 'savedSearches') {
//	$_SESSION['after_login'] = "showcase_firm.php?id=".$_REQUEST['id']."&from=savedSearches";
//}
//require_once("check_mem_login.php");

/************
sng:9/nov/2011
We trick the system into using Mihai's code
************/
$_GET['from'] = 'savedSearches';

require_once("classes/class.company.php");
require_once("classes/class.transaction.php");
require_once("classes/class.account.php");
require_once("classes/class.country.php");
require_once("classes/class.account.php");
require_once("classes/class.savedSearches.php");
require_once("classes/class.oneStop.php"); 
require_once("classes/class.deal_support.php");
$deal_support = new deal_support();
$savedSearches = new SavedSearches();
//////////////////////////////////////
if (isset($_GET['from']) && $_GET['from'] == 'savedSearches') {
	/************************************
	sng:7/apr/2011
	part of credential. Allow only banker, lawyer
	
	sng:9/nov/2011
	Keep it open
	*********/
	//if(($_SESSION['member_type']!="banker")&&($_SESSION['member_type']!="lawyer")){
	//	$g_view['page_content'] = "This section is only for bankers and lawyers";
    //    require("not_authorised.php");
    //    exit; 
	//}
	/*********************************************/
}else{
	//normal, all
}

$g_view['firm_id'] = $_REQUEST['id'];
//get the firm data
$g_view['company_data'] = array();
$success = $g_company->get_company($g_view['firm_id'],$g_view['company_data']);
if(!$success){
	die("Cannot get company data");
}
////////////////////////////////////////////
//get the latest tombstones
$g_view['data'] = array();
$g_view['data_count'] = 0;
$success = $g_trans->get_showcase_deal_ids_of_firm($g_view['firm_id'],24,$g_view['data'],$g_view['data_count']);
if(!$success){
	die("Cannot get tombstones");
}
///////////////////////////////////////
require_once("default_metatags.php");

//$g_view['content_view'] = "showcase_firm_view.php";

if (isset($_GET['from']) && $_GET['from'] == 'savedSearches') {
	
    if (isset($_GET['token'])) {
        $savedSearches->loadIntoPost($_GET['token']);
    }
	/************
	sng:3/aug/2012
	The base64_decode of deal_size should be last
	***************/
	if (isset($_POST['data'])) {
       oneStop::loadIntoPost($_POST['data']);
    }
	/**********************
	sng:12/nov/2011
	We cannot send data like >= in POST. The sanitiser will erase it.
	So we base64 encoded in deal_search_filter_form_view.php
	and we decode it here again
	************************/
	$_POST['deal_size'] = base64_decode($_POST['deal_size']);
	
    
    $g_view['data'] = array();
    $g_view['data_count'] = 0;
    
    $g_view['data'] = $g_trans->getTombstonesForFirm($g_view['firm_id']);
    
    
    $g_view['data_count']  = sizeof($g_view['data']);
    
    if(!$success){
        die("Cannot get tombstones");
    }
	/******************
	sng:12/nov/2011
	If I am logged in and viewing my firm then the heading will be
	My Firm's Credentials
	********************/
	if($g_account->is_site_member_logged()&&($_SESSION['company_id']==$g_view['firm_id'])){
		$g_view['page_heading'] = "My Firm's Credentials";
		$g_view['my_firm'] = true;
	}else{
		$g_view['page_heading'] = "List of Credentials";
		$g_view['my_firm'] = false;
	}
    $g_view['content_view'] = "showcase_firm_view_savedSearches.php";
	$g_view['show_help'] = true;
    $g_view['region_list'] = array();
    $g_view['region_count'] = 0;
    $success = $g_country->get_all_region_list($g_view['region_list'],$g_view['region_count']);
    if(!$success){
        die("Cannot get region list");
    }  
    $g_view['country_list'] = array();
    $g_view['country_count'] = 0;
    $success = $g_country->get_all_country_list($g_view['country_list'],$g_view['country_count']);
    if(!$success){
        die("Cannot get country list");
    } 
    
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

    //fetch sector types
    $g_view['sector_list'] = array();
    $g_view['sector_count'] = 0;
    $success = $g_company->get_all_sector_list($g_view['sector_list'],$g_view['sector_count']);
    if(!$success){
        die("Cannot get sector list");
    }
    ////////////////////////////////////
    //fetch industry types
    $g_view['industry_list'] = array();
    $g_view['industry_count'] = 0;
    $success = $g_company->get_all_industry_for_sector($_POST['sector'],$g_view['industry_list'],$g_view['industry_count']);
    if(!$success){
        die("Cannot get industry list");
    }
    $categories = $g_trans->getCategoryTree();
	/***********************************************************
	sng:12/nov/2011
	We need the deal size options like in league table
	***/
	$g_view['deal_size_filter_list'] = array();
	$g_view['deal_size_filter_list_count'] = 0;
	$success = $deal_support->front_get_deal_value_range_list($g_view['deal_size_filter_list'],$g_view['deal_size_filter_list_count']);
	if(!$success){
		die("Cannot get deal size filter list");
	}
	/****************************************************************
	sng: 12/nov/2011
	client want to preselect the 2010-2011 by default
	The dropdown is in deal_search_filter_form_view.php, which is included in deal_view.php
	In deal_search_filter_form_view.php, the code checks for $_POST['year'].
	Problem is, we cannot hardcode 2010-2011. The figures are created on the fly based on current year.
	It is (curr_year-1)-(curr_year)
	***************/
	if(!isset($_POST['year'])){
		$y = date("Y");
		$_POST['year'] = sprintf('%s-%s',$y-1,$y);
	}
	/*******************************************************************/
	/********************************************
	sng:21/nov/2011
	We have the default search and set the dropdown option to 'bank' even if the member is a lawyer if top_search_area is not set
	***/
	if(!isset($_POST['top_search_area'])){
		$_POST['top_search_area'] = "bank";
	}
	/*********************************************/
    
}
$g_view['show_help'] = true;
require("content_view.php");
?>