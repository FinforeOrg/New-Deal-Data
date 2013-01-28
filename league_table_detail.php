<?php
/**********
this is used to list the stat details for league table, based on filter condition.
This can be accessed pnly by logged in members
**************/
include("include/global.php");
require_once("check_mem_login.php");
//////////////////////////////////////////
//support for filters
require_once("classes/class.transaction.php");

require_once("classes/class.transaction_support.php");
$trans_support = new transaction_support();

require_once("classes/class.country.php");
require_once("classes/class.company.php");
require_once("classes/class.statistics.php");
require_once("classes/class.savedSearches.php");
 
$savedSearches = new SavedSearches();
if (isset($_REQUEST['token'])) {
    $savedSearches->loadIntoPost($_REQUEST['token']);
}
/************
sng:23/jul/2012
We cannot send conditions like >=23. The sanitizer will erase it. We base64_encode it in the forms and decode it here
*****************/
if(isset($_POST['deal_size'])){
	$_POST['deal_size'] = base64_decode($_POST['deal_size']);
}
///////////////////////////////////////////////
//support for filters
require("league_table_filter_support.php");
////////////////////////////////////////////
//pagination support
if(!isset($_POST['start'])||($_POST['start']=="")){
	$g_view['start_offset'] = 0;
}else{
	$g_view['start_offset'] = $_POST['start'];
}
$g_view['num_to_show'] = 10;
$g_view['data'] = array();
$g_view['data_count'] = 0;
/////////////////////////////////////////////////
//get the data
$success = $g_stat->front_generate_league_table_for_firms_paged($_POST,$g_view['start_offset'],$g_view['num_to_show']+1,$g_view['data'],$g_view['data_count']);
if(!$success){
	die("Cannot generate league table data");
}
/////////////////////////////////////////////
require_once("default_metatags.php");
//////////////////////
/**************
sng:28/jan/2013
We will now use the method from transaction_support

sng:26/jan/2013
HACK
We just want a restricted set, so we use the hack version
**********************/
$categories = $trans_support->hack_get_category_tree();
$g_view['page_heading'] = "League Table";
$g_view['content_view'] = "league_table_detail_view.php";
require("content_view.php");
?>