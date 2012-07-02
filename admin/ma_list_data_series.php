<?php
require_once("../include/global.php");
require_once ("admin/checklogin.php");
require_once("classes/class.ma_metrics.php");

/*************************************************
add data
****/
if(isset($_POST['action'])&&($_POST['action']=="add")){
	$validation_passed = false;
	$success = $g_ma_metrics->admin_add_series($_POST,$validation_passed,$g_view['err']);
	if(!$success){
		die("Cannot add series");
	}
	if($validation_passed){
		$g_view['msg'] = "Series added";
	}
}
/*************************************************
delete data
****/
if(isset($_POST['action'])&&($_POST['action']=="delete")){
	$success = $g_ma_metrics->admin_delete_series($_POST['series_id']);
	if(!$success){
		die("Cannot delete series");
	}
	$g_view['msg'] = "Series deleted";
}
/**********************************************************************
get the list of all regions/country
******/
$g_view['region_country'] = NULL;
$g_view['region_country_count'] = 0;
$success = $g_ma_metrics->admin_get_all_region_country_list($g_view['region_country'],$g_view['region_country_count']);
if(!$success){
	die("Cannot get region / countries");
}
/**********************************************************************
get the list of all sectors/industries
******/
$g_view['sector_industry'] = NULL;
$g_view['sector_industry_count'] = 0;
$success = $g_ma_metrics->admin_get_all_sector_industry_list($g_view['sector_industry'],$g_view['sector_industry_count']);
if(!$success){
	die("Cannot get sectors/industries");
}
/**********************************************************************
get the list of all metrics types
******/
$g_view['type'] = NULL;
$g_view['type_count'] = 0;
$success = $g_ma_metrics->get_all_types($g_view['type'],$g_view['type_count']);
if(!$success){
	die("Cannot get metrics types");
}
/*********************************************************************
get the list of data
***/
$g_view['series'] = NULL;
$g_view['series_count'] = 0;
$success = $g_ma_metrics->admin_get_all_series($g_view['series'],$g_view['series_count']);
if(!$success){
	die("Cannot get series data");
}

$g_view['heading'] = "M&A Metrics Data Series";
$g_view['content_view'] = "admin/ma_list_data_series_view.php";
include("admin/content_view.php");
?>