<?php
require_once("include/global.php");
require_once("classes/class.transaction.php");
require_once("classes/class.company.php");
require_once("classes/class.country.php");
/*********************************************************
get deal categories
********/
$categories = $g_trans->getCategoryTree();
/********************************************************
fetch regions
***/
$g_view['region_list'] = array();
$g_view['region_count'] = 0;
$success = $g_country->get_all_region_list($g_view['region_list'],$g_view['region_count']);
if(!$success){
	die("Cannot get region list");
}
/**********************************************************
fetch countries
**********/
$g_view['country_list'] = array();
$g_view['country_count'] = 0;
$success = $g_country->get_all_country_list($g_view['country_list'],$g_view['country_count']);
if(!$success){
	die("Cannot get country list");
}
/********************************************************
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
/******************************************************
sng:27/oct/2011
We need the deal size options like in league table
***/
$g_view['deal_size_filter_list'] = array();
$g_view['deal_size_filter_list_count'] = 0;
$success = $g_trans->front_get_deal_size_filter_list($g_view['deal_size_filter_list'],$g_view['deal_size_filter_list_count']);
if(!$success){
	die("Cannot get deal size filter list");
}
/*******************************************************
sng: 2/nov/2011
client want to preselect the 2010-2011 by default
The dropdown is in deal_search_filter_form_view.php, which is included in deal_view.php
In deal_search_filter_form_view.php, the code checks for $_POST['year'].
Problem is, we cannot hardcode 2010-2011. The figures are created on the fly based on current year.
It is (curr_year-1)-(curr_year)
***************/
$y = date("Y");
$_POST['year'] = sprintf('%s-%s',$y-1,$y);
/****************************************************
We get 5 most recent M&A deals, 5 most recent Equity deals, 5 most recent Debt deals
*******************/
$g_view['start_offset'] = 0;
$g_view['num_to_show'] = 5;

$params = array();
$params['deal_cat_name'] = "m&a";
$params['number_of_deals'] = "recent:5";

$g_view['ma_data'] = array();
$g_view['ma_data_count'] = 0;

$success = $g_trans->front_deal_search_paged($params,$g_view['start_offset'],$g_view['num_to_show'],$g_view['ma_data'],$g_view['ma_data_count']);
if(!$success){
	die("Cannot search for deal");
}
/****************************************************/
$params['deal_cat_name'] = "equity";
$params['number_of_deals'] = "recent:5";

$g_view['eq_data'] = array();
$g_view['eq_data_count'] = 0;

$success = $g_trans->front_deal_search_paged($params,$g_view['start_offset'],$g_view['num_to_show'],$g_view['eq_data'],$g_view['eq_data_count']);
if(!$success){
	die("Cannot search for deal");
}
/*****************************************************/
$params['deal_cat_name'] = "debt";
$params['number_of_deals'] = "recent:5";

$g_view['dbt_data'] = array();
$g_view['dbt_data_count'] = 0;

$success = $g_trans->front_deal_search_paged($params,$g_view['start_offset'],$g_view['num_to_show'],$g_view['dbt_data'],$g_view['dbt_data_count']);
if(!$success){
	die("Cannot search for deal");
}
/*****************************************************/

require_once("default_metatags.php");
$g_view['page_heading'] = "Welcome to Data Central Exchange";
$g_view['show_help'] = true;
$g_view['content_view'] = "index_prev_view.php";
require("content_view.php");
?>