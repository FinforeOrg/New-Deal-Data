<?php
/***********************************************
sng:21/nov/2011
This is to suggest a brand new company, not bank or law firm.
we make the page open.
*********************************/
require_once("include/global.php"); 
require_once("classes/class.country.php");
require_once("classes/class.company.php");
require_once('classes/class.account.php');
/**********************************
sng:21/nov/2011
We have the default search and set the dropdown option to 'company' if top_search_area is not set
***/
if(!isset($_POST['top_search_area'])){
	$_POST['top_search_area'] = "company";
}
/*************************************************************
fetch countries
***/
$g_view['country_list'] = array();
$g_view['country_count'] = 0;
$success = $g_country->get_all_country_list($g_view['country_list'],$g_view['country_count']);
if(!$success){
	die("Cannot get country list");
}
/****************************************************************
fetch sector names
**************/
$g_view['sector_list'] = array();
$g_view['sector_count'] = 0;
//$success = $g_trans->get_all_category_list($g_view['cat_list'],$g_view['cat_count']);
$success = $g_company->get_all_sector_list($g_view['sector_list'],$g_view['sector_count']);
if(!$success){
	die("Cannot get sector list");
}
/*****************************************************************
get all the identifiers from master list
***/
$g_view['identifiers'] = NULL;
$g_view['identifiers_cnt'] = 0;
$success = $g_company->admin_get_identifier_options($g_view['identifiers'],$g_view['identifiers_cnt']);
if(!$success){
	die("Cannot get the identifiers");
}
/*******************************************************************/
require_once("default_metatags.php");
$g_view['page_heading'] = "Suggest a Company";
$g_view['content_view'] = "suggest_a_company_view.php";
require("content_view.php");
?>
