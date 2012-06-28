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
require_once("classes/class.country.php");
require_once("classes/class.company.php");
require_once("classes/class.statistics.php");
require_once("classes/class.savedSearches.php");
 
$savedSearches = new SavedSearches();
if (isset($_REQUEST['token'])) {
    $savedSearches->loadIntoPost($_REQUEST['token']);
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
$categories = $g_trans->getCategoryTree();
$g_view['page_heading'] = "League Table";
$g_view['content_view'] = "league_table_detail_view.php";
require("content_view.php");
?>