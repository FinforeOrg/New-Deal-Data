<?php
/****************
sng:21/nov/2011
This is used to search for bank or law firm when the user type a firm name
and select bank / law firm in top search bar

This is basically the bank and law firm search part from serch_all.php
***************/
require_once("include/global.php");
require_once("classes/class.magic_quote.php");
require_once("classes/class.company.php");
require_once("classes/class.transaction.php");
require_once("nifty_functions.php");
/***********************************************************************************/
$g_view['search_form_input'] = $g_mc->view_to_view($_POST['top_search_term']);
$g_view['num_to_show'] = 10;
$g_view['start_offset'] = 0;
/***********************************************************************************
search for banks
***/
$g_view['bank_data'] = array();
$g_view['bank_data_count'] = 0;
$g_view['bank_total_data_count'] = 0;
$success = $g_company->front_firm_search_paged($_POST['top_search_term'],"bank",$g_view['start_offset'],$g_view['num_to_show'],$g_view['bank_data'],$g_view['bank_data_count'],$g_view['bank_total_data_count']);
	
if(!$success){
	die("Cannot search for bank");
}
$g_view['bank_search_heading'] = "Bank search result";
if(isset($g_view['bank_total_data_count'])&&($g_view['bank_total_data_count'] > 0)){
	$g_view['bank_search_heading'].=" [".$g_view['bank_total_data_count']." found]";
}
/**************************************************************************************/
require_once("default_metatags.php");
$g_view['page_heading'] = "Search Result";
$g_view['content_view'] = "default_search_bank_view.php";
require("content_view.php");
?>