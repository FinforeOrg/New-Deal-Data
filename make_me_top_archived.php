<?php
/****
sng:05/oct/2010
These are just list of make me top requests that are marked as archived
********/
require_once("include/global.php");
require_once("check_mem_login.php");
require_once("classes/class.preset.php");
//////////////////////////////////////////////////////
//get current submitted jobs
$g_view['request_data'] = array();
$g_view['request_count'] = 0;
$success = $g_preset->front_get_archived_top_search_request($_SESSION['mem_id'],$g_view['request_data'],$g_view['request_count']);
if(!$success){
	die("Cannot get the search requests");
}
/////////////////
require_once("default_metatags.php");
$g_view['page_heading'] = "Make Me Top [Archived]";
$g_view['content_view'] = "make_me_top_archived_view.php";
require("content_view.php");
////////////////////////////////////////////////////////////
?>