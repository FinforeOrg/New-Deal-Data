<?php
die("not used");
/***
sng:20/aug/2010
Now we use the search term to search all sections and show results
in small groups. Under each section, there will be 'show all' that actually opens the specific
area search and show all the result.
Since no pagination is here, we do not fetch one extra
The search areas are
company: see company_search.php
deal: see deal_search.php
M&A deals where target/seller match the search term
bank: bank_search.php
law firm: law_firm_search.php
//right now we are not into banker search and lawyer search
*************/
require_once("include/global.php");
require_once("classes/class.magic_quote.php");
require_once("classes/class.company.php");
require_once("classes/class.transaction.php");
require_once("nifty_functions.php");
/////////////////////////////////////////////////
$g_view['search_form_input'] = $g_mc->view_to_view($_POST['top_search_term']);
$g_view['num_to_show'] = 10;
$g_view['start_offset'] = 0;
/////////////////////////////////////////////
//search for companies
$g_view['company_data'] = array();
$g_view['company_data_count'] = 0;
$g_view['company_total_data_count'] = 0;
$success = $g_company->front_company_search_paged($_POST['top_search_term'],$g_view['start_offset'],$g_view['num_to_show'],$g_view['company_data'],$g_view['company_data_count'],$g_view['company_total_data_count']);
	
if(!$success){
	die("Cannot search for company");
}
$g_view['company_search_heading'] = "Company search result";
if(isset($g_view['company_total_data_count'])&&($g_view['company_total_data_count'] > 0)){
	$g_view['company_search_heading'].=" [".$g_view['company_total_data_count']." found]";
}
/////////////////////////////////////////////////
//search for deals
$g_view['deal_data'] = array();
$g_view['deal_data_count'] = 0;
$success = $g_trans->front_deal_search_paged($_POST,$g_view['start_offset'],$g_view['num_to_show'],$g_view['deal_data'],$g_view['deal_data_count']);
if(!$success){
	die("Cannot search for deal");
}
$g_view['deal_search_heading'] = "Deal search result";
////////////////////////////////////////
//M&A deal target/seller search
$g_view['target_data'] = array();
$g_view['target_data_count'] = 0;
//we artificially set this so that only M&A deals are considered
$_POST['deal_cat_name'] = "M&A";
//the name is used to match the company name doing the deal. To alter this behaviour, we introduce another param
//search_target='y'
$_POST['search_target'] = "y";

$success = $g_trans->front_deal_search_paged($_POST,$g_view['start_offset'],$g_view['num_to_show'],$g_view['target_data'],$g_view['target_data_count']);
if(!$success){
	die("Cannot search for M&A deal target / seller");
}
$g_view['target_search_heading'] = "M&amp;A target / seller search results";

///////////////////////////////////////
//search for banks
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
///////////////////////////////
//search for law firms
$g_view['law_firm_data'] = array();
$g_view['law_firm_data_count'] = 0;
$g_view['law_firm_total_data_count'] = 0;
$success = $g_company->front_firm_search_paged($_POST['top_search_term'],"law firm",$g_view['start_offset'],$g_view['num_to_show'],$g_view['law_firm_data'],$g_view['law_firm_data_count'],$g_view['law_firm_total_data_count']);
	
if(!$success){
	die("Cannot search for law firm");
}
$g_view['law_firm_search_heading'] = "Law firm search result";
if(isset($g_view['law_firm_total_data_count'])&&($g_view['law_firm_total_data_count'] > 0)){
	$g_view['law_firm_search_heading'].=" [".$g_view['law_firm_total_data_count']." found]";
}
/////////////////////////////////////
require_once("default_metatags.php");
////////////////////////////////////////////////
$g_view['page_heading'] = "Search Result";
$g_view['top_search_view'] = "all_search_view.php";
$g_view['content_view'] = "search_all_view.php";
require("content_view.php");
?>