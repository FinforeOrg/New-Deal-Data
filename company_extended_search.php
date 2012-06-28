<?php
/****************
This is used to search for company when the visitor use filters in company page
***************/
include("include/global.php");
require_once("classes/class.company.php");
require_once("classes/class.magic_quote.php");
/////////////////////////////////////////////////
if(isset($_POST['myaction'])&&($_POST['myaction']=="extended_search")){
	//search request
	//pagination support
	if(!isset($_POST['start'])||($_POST['start']=="")){
		$g_view['start_offset'] = 0;
	}else{
		$g_view['start_offset'] = $_POST['start'];
	}
	$g_view['num_to_show'] = 10;
	$g_view['data'] = array();
	$g_view['data_count'] = 0;
	$g_view['total_data_count'] = 0;
	$success = $g_company->front_company_extended_search_paged($_POST,$g_view['start_offset'],$g_view['num_to_show']+1,$g_view['data'],$g_view['data_count'],$g_view['total_data_count']);
	
	if(!$success){
		die("Cannot search for company");
	}
	/////////////////////////////////////////
}else{
	$g_view['data_count'] = 0;
}
////////////////////////////////////
require_once("company_extended_search_support.php");
////////////////////////////////
require_once("default_metatags.php");
/////////////////////////////////////////////////////////////////////////////////
$g_view['page_heading'] = "Company extended search result";
if(isset($g_view['total_data_count'])&&($g_view['total_data_count'] > 0)){
	$g_view['page_heading'].=" [".$g_view['total_data_count']." found]";
}
/***************************
sng:21/nov/2011
We have the default search and set the dropdown option to 'company' if top_search_area is not set
***/
if(!isset($_POST['top_search_area'])){
	$_POST['top_search_area'] = "company";
}
/////////////////////////////////////////////////////////////////////////////
$g_view['content_view'] = "company_extended_search_view.php";
require("content_view.php");
?>