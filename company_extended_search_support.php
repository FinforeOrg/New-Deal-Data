<?php
/****
embedded inside company.php that now supports search for company by filter
*******/
require_once("classes/class.company.php");
require_once("classes/class.country.php");
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