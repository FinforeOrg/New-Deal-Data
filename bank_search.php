<?php
/****************
This is used to search for bank when the visitor type bank name and select Bank
in the top search form

sng:21/oct/2011
if we come to this from credential page, show only the credential button
see competitor_credentials_view.php
***************/
include("include/global.php");
require_once("classes/class.company.php");
require_once("classes/class.magic_quote.php");
/////////////////////////////////////////////////
if(isset($_POST['from_credential'])&&($_POST['from_credential']==1)){
	$g_view['only_cred'] = true;
}else{
	$g_view['only_cred'] = false;
}
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
	$success = $g_company->front_firm_search_paged($_POST['top_search_term'],"bank",$g_view['start_offset'],$g_view['num_to_show']+1,$g_view['data'],$g_view['data_count'],$g_view['total_data_count']);
	
	if(!$success){
		die("Cannot search for bank");
	}
	$g_view['search_form_input'] = $g_mc->view_to_view($_POST['top_search_term']);
	/////////////////////////////////////////
}else{
	$g_view['data_count'] = 0;
}
////////////////////////////////
require_once("default_metatags.php");
/////////////////////////////////////////////////////////////////////////////////
$g_view['page_heading'] = "Bank search result";
if(isset($g_view['total_data_count'])&&($g_view['total_data_count'] > 0)){
	$g_view['page_heading'].=" [".$g_view['total_data_count']." found]";
}
/***
sng:19/may/2010
We now use the default search view which can search for bank
*******/
$g_view['content_view'] = "bank_search_view.php";
require("content_view.php");
?>