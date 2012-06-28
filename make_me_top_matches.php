<?php
require_once("include/global.php");
require_once("check_mem_login.php");
require_once("classes/class.preset.php");
require_once("classes/class.make_me_top.php");
///////////////////////////////////////////////
$g_view['job_id'] = $_GET['job_id'];
$g_view['request_data'] = NULL;
$g_view['request_found'] = false;
$success = $g_preset->front_get_top_search_request($g_view['job_id'],$g_view['request_data'],$g_view['request_found']);
if(!$success){
	die("Cannot get the search request");
}
/////////////////////////////////////////////
$g_view['start'] = 0;
$g_view['num_to_show'] = 50;
if(isset($_REQUEST['start'])&&($_REQUEST['start']!="")){
	$g_view['start'] = $_REQUEST['start'];
}
$g_view['result'] = array();
$g_view['result_count'] = 0;
$g_view['total_count'] = 0;
$success = $g_maketop->search_result_paged($g_view['job_id'],$_POST,$g_view['start'],$g_view['num_to_show']+1,$g_view['result'],$g_view['result_count'],$g_view['total_count']);
if(!$success){
	die("Cannot fetch search results");
}
////////////////////////////////////////////
/*********************
sng:2/sep/2010
Given the hits for a job, we want the member to allow filter the result by
ranking criteria
*******/
//////////////////////////////////////////////////////
//get the ranks obtained for this job
$g_view['rank_data'] = array();
$g_view['rank_count'] = 0;
$success = $g_maketop->get_presets_for_result($g_view['job_id'],"rank_of_firm",$g_view['rank_data'],$g_view['rank_count']);
if(!$success){
	die("Cannot obtain the ranks");
}
//////////////////////////////////////////////////////
//get the country presets for this job
$g_view['country_data'] = array();
$g_view['country_count'] = 0;
$success = $g_maketop->get_presets_for_result($g_view['job_id'],"country_preset_id",$g_view['country_data'],$g_view['country_count']);
if(!$success){
	die("Cannot obtain the countries");
}
/////////////////////////////////////////////////////
//get the sector/industry presets for this job
$g_view['sector_data'] = array();
$g_view['sector_count'] = 0;
$success = $g_maketop->get_presets_for_result($g_view['job_id'],"sector_industry_preset_id",$g_view['sector_data'],$g_view['sector_count']);
if(!$success){
	die("Cannot obtain the sector/industries");
}
/////////////////////////////////////////////////////
//get the deal type presets for this job
$g_view['deal_type_data'] = array();
$g_view['deal_type_count'] = 0;
$success = $g_maketop->get_presets_for_result($g_view['job_id'],"deal_type_preset_id",$g_view['deal_type_data'],$g_view['deal_type_count']);
if(!$success){
	die("Cannot obtain the deal types");
}
/////////////////////////////////////////////////////
//get the deal size presets for this job
$g_view['deal_size_data'] = array();
$g_view['deal_size_count'] = 0;
$success = $g_maketop->get_presets_for_result($g_view['job_id'],"deal_size_preset_id",$g_view['deal_size_data'],$g_view['deal_size_count']);
if(!$success){
	die("Cannot obtain the deal sizes");
}
/////////////////////////////////////////////////////
//get the deal date presets for this job
$g_view['deal_date_data'] = array();
$g_view['deal_date_count'] = 0;
$success = $g_maketop->get_presets_for_result($g_view['job_id'],"deal_date_preset_id",$g_view['deal_date_data'],$g_view['deal_date_count']);
if(!$success){
	die("Cannot obtain the deal dates");
}
/////////////////////////////////////////////////////
//the ranking criterias will be hard coded
//////////////////////////////////////////////
require_once("default_metatags.php");
//$g_view['page_heading'] = "Make Me Top Results";
$g_view['content_view'] = "make_me_top_matches_view.php";
require("content_view.php");
////////////////////////////////////////////////////////////
?>