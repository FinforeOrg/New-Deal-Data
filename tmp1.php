<?php
include("include/global.php");
require_once("default_metatags.php");
$g_view['page_heading'] = "Search Demo";
$g_view['top_search_view'] = "all_search_view.php";
$g_view['content_view'] = "tmp1_view.php";
require("content_view.php");
?>