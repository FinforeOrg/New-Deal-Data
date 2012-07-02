<?php
require_once("../include/global.php");
require_once ("admin/checklogin.php");
require_once("classes/class.ma_metrics.php");

$g_view['series_id'] = $_POST['series_id'];

/*************************************************
add data
****/
if(isset($_POST['action'])&&($_POST['action']=="add")){
	$validation_passed = false;
	$success = $g_ma_metrics->admin_add_data_point($g_view['series_id'],$_POST,$validation_passed,$g_view['err']);
	if(!$success){
		die("Cannot add data point");
	}
	if($validation_passed){
		$g_view['msg'] = "Data point added";
	}
}

/**********************************************************************
get the detail for the series
******/
$g_view['series'] = NULL;

$success = $g_ma_metrics->admin_get_series_detail($g_view['series_id'],$g_view['series']);
if(!$success){
	die("Cannot get series datail");
}
/*********************************************************************
get the data points for this series
***/
$g_view['series_points'] = NULL;
$g_view['series_points_count'] = 0;
$success = $g_ma_metrics->admin_get_series_data_points($g_view['series_id'],$g_view['series_points'],$g_view['series_points_count']);
if(!$success){
	die("Cannot get series data");
}
$g_view['heading'] = "M&A Metrics Series Data Points";
$g_view['content_view'] = "admin/ma_series_data_points_view.php";
include("admin/content_view.php");
?>