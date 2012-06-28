<?php
include("include/global.php");
require_once("check_mem_login.php");
////////////////////////////////////////////////////////////
require_once("default_metatags.php");
require_once("classes/class.company.php");
require_once("classes/class.transaction.php");
//////////////////////////////////////////////////////////////////
$g_view['page_heading'] = "Featured Transaction";
////////////////////////////////////////////////////
$g_view['competitor_found'] = false;
$g_view['competitor_id'] = 0;
$success = $g_company->front_get_random_competing_company($_SESSION['company_id'],$g_view['competitor_id'],$g_view['competitor_found']);
if(!$success){
	die("Cannot get the competitor company");
}
//now get the deal
if($g_view['competitor_found']){
	$g_view['deal_found'] = false;
	$g_view['deal_data'] = array();
	$success = $g_trans->front_home_get_last_deal_data_of_company($g_view['competitor_id'],$g_view['deal_data'],$g_view['deal_found']);
	if(!$success){
		die("Cannot get the deal");
	}
	///////////////////////////////
}
////////////////////////////////////////////////////
$g_view['content_view'] = "company_rep_home_view.php";
require("content_view.php");
?>