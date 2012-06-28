<?php
/****
sng:30/oct/2010
This is for testing the make me top, to get the statistics.
we have no cron job to trigger the processing of the pending job. We do that manually
********/
require_once("include/global.php");
/***
so that if not logged in then go to login
and after login can return here
***/
$_SESSION['after_login'] = "monitor_make_me_top.php";
require_once("check_mem_login.php");
require_once("classes/class.preset.php");
require_once("classes/class.probe.php");
//////////////////////////////////////////////////////
//get current submitted jobs
//but check if there is a cutoffdate or not
$cutoff = $_GET['fromdate'];
$g_view['request_data'] = array();
$g_view['request_count'] = 0;
$success = $g_probe->front_get_all_top_search_request($g_view['request_data'],$g_view['request_count'],$cutoff);
if(!$success){
	die("Cannot get the search requests");
}
/////////////////
require_once("default_metatags.php");
$g_view['page_heading'] = "Make Me Top";
$g_view['content_view'] = "monitor_make_me_top_view.php";
require("content_view.php");
////////////////////////////////////////////////////////////
?>