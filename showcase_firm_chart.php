<?php
/*****
This is used to show case a bank or a law firm.
This shows the charts that makes this firm look good
********/
include("include/global.php");
require_once("classes/class.company.php");
require_once("classes/class.statistics.php");
//////////////////////////////////////
$g_view['firm_id'] = $_REQUEST['id'];
//get the firm data
$g_view['company_data'] = array();
$success = $g_company->get_company($g_view['firm_id'],$g_view['company_data']);
if(!$success){
	die("Cannot get company data");
}
////////////////////////////////////////////
//get the charts associated with this firm
$g_view['data'] = array();
$g_view['data_count'] = 0;
$success = $g_stat->front_get_charts_for_firm($g_view['firm_id'],$g_view['data'],$g_view['data_count']);
if(!$success){
	die("Cannot get charts");
}
////////////////////////////////////////////
require_once("default_metatags.php");
$g_view['content_view'] = "showcase_firm_chart_view.php";
require("content_view.php");
?>