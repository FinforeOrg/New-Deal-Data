<?php
/*********
sng:24/sep/2010
code to get 2 random charts for home page

The data is expected in json
left_chart_heading
left_chart_img
right_chart_heading
right_chart_img
*************/
include("../include/global.php");
require_once("classes/class.statistics.php");
///////////////////////////////////////////////
$data = array();
$data['left_chart_heading'] = "";
$data['left_chart_img'] = "";
$data['right_chart_heading'] = "";
$data['right_chart_img'] = "";

//get 2 league table images for home page
$g_view['home_chart'] = array();
$success = $g_stat->front_get_home_page_charts($g_view['home_chart']);
if(!$success){
	echo json_encode($data);
}
////////////////////////////////////
if(isset($g_view['home_chart'][0])){
	$data['left_chart_heading'] = $g_view['home_chart'][0]['name'];
	$data['left_chart_img'] = "admin/charts/".$g_view['home_chart'][0]['img'];
}
if(isset($g_view['home_chart'][1])){
	$data['right_chart_heading'] = $g_view['home_chart'][1]['name'];
	$data['right_chart_img'] = "admin/charts/".$g_view['home_chart'][1]['img'];
}
echo json_encode($data);
?>