<?php
include("../include/global.php");
require_once ("admin/checklogin.php");
require_once("classes/class.company.php");
require_once("classes/class.country.php");
require_once("classes/class.magic_quote.php");
///////////////////////////////////////////////////////
$g_view['msg'] = "";
///////////////////////////////////////
if(isset($_POST['action'])&&($_POST['action']=="search_company")){
	$g_view['data_count'] = 0;
	$g_view['data'] = array();
	$success = $g_company->admin_extended_search_for_company($_POST,$g_view['data'],$g_view['data_count']);
	if(!$success){
		die("Cannot get company data");
	}
}
/***********
sng: 2/sep/2010
Need to allow search by region, country, sector, industry
***********/
////////////////////////////////////////////////////////
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
//////////////////////////////////////////////////
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
$success = $g_company->get_all_industry_for_sector($_POST['sector'],$g_view['industry_list'],$g_view['industry_count']);
if(!$success){
	die("Cannot get industry list");
}
//////////////////////////////////////////////
$g_view['heading'] = "Search for Company";
$g_view['content_view'] = "admin/company_search_view.php";
include("admin/content_view.php");
?>