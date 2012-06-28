<?php
/**********
This is used to create top 10 bankers based oon stat

sng:1/jun/2010
It has been decided that only logged in users can see league table of bankers and lawyers
We keep the links on top menu but, we show a message page if the user has not logged in
********/
include("include/global.php");
require_once("classes/class.transaction.php");
require_once("classes/class.country.php");
require_once("classes/class.company.php");
require_once("classes/class.account.php");
///////////////////////
if(!$g_account->is_site_member_logged()){
	$g_view['page_heading'] = "League Table for Lawyers";
	$g_view['content_view'] = "restricted.php";
	require("content_view.php");
	exit;
}
//////////////////
//sng: 21/apr/2010
require("league_table_filter_support.php");
////////////////////////////////////////////
require_once("default_metatags.php");
//////////////////////
//$g_view['page_heading'] = "League Table for Lawyers";
/***
sng:19/may/2010
the default search takes care of lawyer search
***/
$g_view['content_view'] = "lawyers_league_table_view.php";
require("content_view.php");
?>