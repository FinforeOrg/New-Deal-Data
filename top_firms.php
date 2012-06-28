<?php
include("include/global.php");
/*********************************
sng:23/mar/2011
This now requires login
**************/
$_SESSION['after_login'] = "top_firms.php";
require_once("check_mem_login.php");

require_once("classes/class.company.php");
require_once("classes/class.magic_quote.php");
////////////////////////////////////
/********************************************************
sng:19/jul/2010
admin can create categories for top banks/law firms. We need to show the top categories.
We also need to highlight the entry if set
*********/
$g_view['top_firm_cat_data'] = array();
$g_view['top_firm_cat_data_count'] = 0;
$success = $g_company->get_all_top_firms_categories($g_view['top_firm_cat_data'],$g_view['top_firm_cat_data_count']);
if(!$success){
	die("Cannot get top categories");
}
if(!isset($_POST['top_firm_cat_id'])){
	$g_view['top_firm_cat_id'] = "";
}else{
	$g_view['top_firm_cat_id'] = $_POST['top_firm_cat_id'];
}
/******************************************************/
//get the top banks
$g_view['bank_data'] = array();
$g_view['bank_data_count'] = 0;
$success = $g_company->front_get_top_firms_list_by_type("bank",$g_view['top_firm_cat_id'],$g_view['bank_data'],$g_view['bank_data_count']);
if(!$success){
	die("Cannot get top banks");
}
//////////////////////////////////////
//get the top law firms
$g_view['lawfirm_data'] = array();
$g_view['lawfirm_data_count'] = 0;
$success = $g_company->front_get_top_firms_list_by_type("law firm",$g_view['top_firm_cat_id'],$g_view['lawfirm_data'],$g_view['lawfirm_data_count']);
if(!$success){
	die("Cannot get top law firms");
}
//////////////////////////////////
require_once("default_metatags.php");
$g_view['page_heading'] = "Top Firms";
$g_view['content_view'] = "top_firms_view.php";
require("content_view.php");
?>