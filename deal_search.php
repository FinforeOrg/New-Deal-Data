<?php
/****************
This is used to search for company when the visitor type company name or domain
in the company page

sng:29/apr/2010
We now use another field to check if this is a search from company name
***************/
require_once("include/global.php");
require_once("classes/class.account.php");
require_once("classes/class.transaction.php");
require_once("classes/class.company.php");
require_once("classes/class.country.php");
require_once("classes/class.magic_quote.php");
require_once("classes/class.savedSearches.php");
require_once("classes/class.deal_support.php");
$deal_support = new deal_support();

$savedSearches = new SavedSearches();

if (isset($_GET['token'])) {
    $savedSearches->loadIntoPost($_GET['token']);
    if (isset($_REQUEST['alert'])) {
        $lastAlertId = base64_decode($_REQUEST['lid']);
    }
}
/**********************
sng:27/oct/2011
We cannot send data like >= in POST. The sanitiser will erase it.
So we base64 encoded in deal_search_filter_form_view.php
and we decode it here again
************************/
$_POST['deal_size'] = base64_decode($_POST['deal_size']);
/////////////////////////////////////////////////
if(isset($_POST['myaction'])&&($_POST['myaction']=="search")){
	//search request, this can come from the top search form or the filter form
	//pagination support
	if(!isset($_POST['start'])||($_POST['start']=="")){
		$g_view['start_offset'] = 0;
	}else{
		$g_view['start_offset'] = $_POST['start'];
	}
	$g_view['num_to_show'] = 25;
	/************************************************************************
	sng:22/jul/2010
	if top 10 or recent 25 is selected we show that many in one page
	
	sng:31/oct/2011
	We have added a dummy in number_of_deals called 'size'. We need to check for that also
	*********/
	if($_POST['number_of_deals']!=""){
		if($_POST['number_of_deals']!="size"){
			$to_show_tokens = explode(":",$_POST['number_of_deals']);
			$g_view['num_to_show'] = $to_show_tokens[1];
		}
	}
	/***************************************************************************/
	$g_view['data'] = array();
	$g_view['data_count'] = 0;
	/***
	sng:19/may/2010
	the top form performs its own check so we do not check here
	*******/
	$success = $g_trans->front_deal_search_paged($_POST,$g_view['start_offset'],$g_view['num_to_show']+1,$g_view['data'],$g_view['data_count']);
	if(!$success){
		die("Cannot search for deal");
	}
	$g_view['deal_company_form_input'] = $g_mc->view_to_view($_POST['top_search_term']);
}
/////////////////////////////////////////////////////////////////////////
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
/***
sng:19/may/2010
We show sector and fetch industry as per the sector selected

sng:10/Jul/2010
Now logged in user can filter via industry also. For that, we fetch industries based
on sector selected. We fetch the industries anyway and decide in the view whehter
to show or not.
*******/
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
sng: 10/nov/2011
client want to preselect the 2010-2011 by default
The dropdown is in deal_search_filter_form_view.php, which is included in deal_search_view.php
In deal_search_filter_form_view.php, the code checks for $_POST['year'].
Problem is, we cannot hardcode 2010-2011. The figures are created on the fly based on current year.
It is (curr_year-1)-(curr_year)

Note: do check whether $_POST['year'] is set or not
***************/
if(!isset($_POST['year'])){
	$y = date("Y");
	$_POST['year'] = sprintf('%s-%s',$y-1,$y);
}
/*******************************************************************/
//////////////////////////////////////////////
require_once("default_metatags.php");
/////////////////////////////////////////////////////////////////////////////////
$g_view['page_heading'] = "Deal search result";
$g_view['content_view'] = "deal_search_view.php";
$categories = $g_trans->getCategoryTree();
require("content_view.php");
?>