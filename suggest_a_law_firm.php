<?php
/*************************************
sng:7/dec/2011
This is to suggest a law firm
we make the page open.
**************************************/
require_once("include/global.php");
require_once("classes/class.account.php");

$g_view['firm_type'] = "law firm";
if(!isset($_POST['top_search_area'])){
	$_POST['top_search_area'] = "law_firm";
}

require_once("default_metatags.php");
$g_view['page_heading'] = "Suggest a Law Firm";
$g_view['content_view'] = "suggest_a_firm_view.php";
require("content_view.php");
?>