<?php
/************************
sng:5/apr/2013
common code for banker and lawyer league table.

Earlier, a visitor had to login to view league table. Now anybody can view it.

We are copying codes from league table feature
***********************/
require_once("classes/class.country.php");
require_once("classes/class.company.php");
require_once("classes/class.transaction.php");
require_once("classes/class.transaction_support.php");
require_once("classes/class.account.php");
require_once("classes/class.savedSearches.php");

$g_transaction_support = new transaction_support();
$savedSearches = new SavedSearches();
/**********************************
Now some initialization
sng:18/july/2012 It is assumed that $_POST has submitted data.
Otherwise we set to blank
*****/
if(!isset($_POST['deal_cat_name'])){
	$_POST['deal_cat_name'] = "";
}
if(!isset($_POST['deal_subcat1_name'])){
	$_POST['deal_subcat1_name'] = "";
}
if(!isset($_POST['sector'])){
	$_POST['sector'] = "";
}
/***************************
666is saved search needed?
this comes before the $_POST['deal_size'] hack
***/
if (isset($_REQUEST['token'])) {
    $savedSearches->loadIntoPost($_REQUEST['token']);
}

/************
sng:23/jul/2012
We cannot send conditions like >=23. The sanitizer will erase it. We base64_encode it in the forms and decode it here

sng:5/sep/2012
For more explanation on why we put this in if() see issuance_data.php
*****************/
if(isset($_POST['deal_size'])){
	$_POST['deal_size'] = base64_decode($_POST['deal_size']);
}
/***********************************
get regions
****/
$g_view['region_list'] = array();
$g_view['region_count'] = 0;
$success = $g_country->get_all_region_list($g_view['region_list'],$g_view['region_count']);
if(!$success){
	die("Cannot get region list");
}
/****************************************
get countries
****/
$g_view['country_list'] = array();
$g_view['country_count'] = 0;
$success = $g_country->get_all_country_list($g_view['country_list'],$g_view['country_count']);
if(!$success){
	die("Cannot get country list");
}
/***********************************
sng:19/may/2010
We show sector and fetch industry as per the sector selected
****/
$g_view['sector_list'] = array();
$g_view['sector_count'] = 0;
$success = $g_company->get_all_sector_list($g_view['sector_list'],$g_view['sector_count']);
if(!$success){
	die("Cannot get sector list");
}
/********************************************
the industries
***/
$g_view['industry_list'] = array();
$g_view['industry_count'] = 0;
$success = $g_company->get_all_industry_for_sector($_POST['sector'],$g_view['industry_list'],$g_view['industry_count']);
if(!$success){
	die("Cannot get industry list");
}
/*****************************************
sng:23/july/2010
There is another filter on deal size
*****/
$g_view['deal_size_filter_list'] = array();
$g_view['deal_size_filter_list_count'] = 0;
$success = $g_trans->front_get_deal_size_filter_list($g_view['deal_size_filter_list'],$g_view['deal_size_filter_list_count']);
if(!$success){
	die("Cannot get deal size filter list");
}
/**********************************
sng:26/jan/2013
HACK
We need only the Equities, so we get a restricted set for now
***************/
$categories = $g_transaction_support->hack_get_category_tree();
/*********************************************************************/



require_once("default_metatags.php");

if("banker"==$g_view['for']){
	$g_view['page_heading'] = "League Table for Bankers";
}elseif("lawyer"==$g_view['for']){
	$g_view['page_heading'] = "League Table for Lawyers";
}
$g_view['content_view'] = "member_league_table_view.php";
require("content_view.php");
?>