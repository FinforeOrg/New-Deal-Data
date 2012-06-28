<?php
require_once("include/global.php");
$_SESSION['after_login'] = "ma_metrics.php";
require_once("check_mem_login.php");

require_once("classes/class.ma_metrics.php");
/****************************************************
get the regions that are there for the metrics
***/
$g_view['region_list'] = array();
$g_view['region_count'] = 0;
$success = $g_ma_metrics->get_all_region_list($g_view['region_list'],$g_view['region_count']);
if(!$success){
	die("Cannot get region list");
}
/****************************************************
get the countries that are there for the metrics
***/
$g_view['country_list'] = array();
$g_view['country_count'] = 0;
$success = $g_ma_metrics->get_all_country_list($g_view['country_list'],$g_view['country_count']);
if(!$success){
	die("Cannot get country list");
}
/****************************************************
get the sectors that are there for the metrics
***/
$g_view['sector_list'] = array();
$g_view['sector_count'] = 0;
$success = $g_ma_metrics->get_all_sector_list($g_view['sector_list'],$g_view['sector_count']);
if(!$success){
	die("Cannot get sector list");
}
/****************************************************
get the industries that are there for the metrics
***/
$g_view['industry_list'] = array();
$g_view['industry_count'] = 0;
$success = $g_ma_metrics->get_all_industry_list($g_view['industry_list'],$g_view['industry_count']);
if(!$success){
	die("Cannot get industry list");
}
/**********************************************************
get the types for metrics
*******/
$g_view['type_list'] = array();
$g_view['type_count'] = 0;
$success = $g_ma_metrics->get_all_types($g_view['type_list'],$g_view['type_count']);
if(!$success){
	die("Cannot get type list");
}
/**********************************************************
get initial region/country id and sector/industry id for which there is data points for both metrics
and use that to fetch data for the initial charts on page load
***/
$g_view['featured_metrics_region_country_id'] = 0;
$g_view['featured_metrics_sector_industry_id'] = 0;
$g_view['has_featured'] = false;
$success = $g_ma_metrics->get_featured_series($g_view['has_featured'],$g_view['featured_metrics_region_country_id'],$g_view['featured_metrics_sector_industry_id']);
if(!$success){
	die("Cannot get featured charts");
}
/*****************************************************************/
require_once("default_metatags.php");
$g_view['page_heading'] = "M&A Metrics";

$g_view['content_view'] = "ma_metrics_view.php";
require("content_view.php");
?>