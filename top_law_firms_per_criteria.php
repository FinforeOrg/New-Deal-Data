<?php
include("include/global.php");
require_once("classes/class.statistics.php");
require_once("classes/class.magic_quote.php");
////////////////////////////////////
//get the captions and the banks for each
$g_view['data'] = array();
$g_view['data_count'] = 0;
$success = $g_stat->front_get_top_firms_per_criteria("law firm",$g_view['data'],$g_view['data_count']);
//////////////////////////////////////
require_once("default_metatags.php");
$g_view['page_heading'] = "Best law firms";
$g_view['content_view'] = "top_law_firms_per_criteria_view.php";
require("content_view.php");
?>