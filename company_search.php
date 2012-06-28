<?php
/****************
This is used to search for company when the visitor type company name or domain
in the company page
***************/
include("include/global.php");
require_once("classes/class.company.php");
require_once("classes/class.magic_quote.php");
/////////////////////////////////////////////////
if(isset($_POST['myaction'])&&($_POST['myaction']=="search")){
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
	$success = $g_company->front_company_search_paged($_POST['top_search_term'],$g_view['start_offset'],$g_view['num_to_show']+1,$g_view['data'],$g_view['data_count'],$g_view['total_data_count']);
	
	if(!$success){
		die("Cannot search for company");
	}
	$g_view['search_form_input'] = $g_mc->view_to_view($_POST['top_search_term']);
	/////////////////////////////////////////
}else{
	/***
	sng:19/may/2010
	The default search form is to be used and it can be used to search company or deal or banker etc
	so no need for default message
	********/
	$g_view['data_count'] = 0;
}
////////////////////////////////
require_once("default_metatags.php");
/////////////////////////////////////////////////////////////////////////////////
$g_view['page_heading'] = "company search result";
if(isset($g_view['total_data_count'])&&($g_view['total_data_count'] > 0)){
	$g_view['page_heading'].=" [".$g_view['total_data_count']." found]";
}
/***
sng:19/may/2010
We now use the default search view which can search for company
*******/
$g_view['content_view'] = "company_search_view.php";
require("content_view.php");
?>