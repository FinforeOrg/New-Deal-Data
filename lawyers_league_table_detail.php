<?php
/**********
this is used to list the stat details for league table for individuals, based on filter condition.
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
$success = $g_stat->generate_top_individuals_paged($_POST,$g_view['start_offset'],$g_view['num_to_show']+1,$g_view['data'],$g_view['data_count']);
if(!$success){
	die("Cannot generate league table data for lawyer");
}
/////////////////////////////////////////////
require_once("default_metatags.php");
//////////////////////
$g_view['page_heading'] = "League Table for lawyers";
/***
sng:19/may/2010
the default search takes care of lawyer search
***/
$g_view['content_view'] = "lawyers_league_table_detail_view.php";
require("content_view.php");
?>