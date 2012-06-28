<?php
/**********
This is used to create stat chart on the fly, based on conditions

sng: 3/jun/2010
we show a home page chart by default
********/
include("include/global.php");
/**********
sng:23/nov/2010
This now require login
*******/
$_SESSION['after_login'] = "issuance_data.php";
require_once("check_mem_login.php");

require_once("classes/class.transaction.php");
require_once("classes/class.country.php");
require_once("classes/class.company.php");
require_once("classes/class.account.php");
require_once("classes/class.statistics.php");
require_once("classes/class.savedSearches.php");
require_once("classes/class.stat_help.php");
$savedSearches = new SavedSearches();
if (isset($_REQUEST['token'])) {
    $savedSearches->loadIntoPost($_REQUEST['token']);
}
//////////////////
//sng: 21/apr/2010
require("league_table_filter_support.php");
////////////////////////////////////////////
require_once("default_metatags.php");
//////////////////////
/***
sng:1/jun/2010
we put header and links, so we will not use the default
***/
//$g_view['page_heading'] = "League Table";
/////////////////////////////////////////////////////////
$g_view['num_issuance_chart_to_fetch'] = 1;
$g_view['issuance_chart'] = array();
$g_view['num_issuance_chart_found'] = 0;
/********************************
sng:7/jan/2011
If the page comes from issuance_data_detail_view, $_POST['myaction'] is set to gen_chart. In that case, we do not
get the random chart data and the count is 0 and no default chart is displayed
*************/
if(!isset($_POST['myaction'])||($_POST['myaction']!="gen_chart")){
	$success = $g_stat->front_get_random_issuance_charts($g_view['num_issuance_chart_to_fetch'],$g_view['issuance_chart'],$g_view['num_issuance_chart_found']);
	if(!$success){
		die("Cannot get issuance chart data");
	}
}
//////////////////////////////////
/***************************************
sng:5/jan/2011
By default, client wants default groupings to be half year
*********/
if(!isset($_POST['month_division'])||($_POST['month_division']=="")){
	$_POST['month_division'] = "h";
}
/*****************************************/
/*********
sng:27/nov/2010
get the month div list based on the month div selected
*******************/
$g_view['month_div'] = array();
$g_view['month_div']['value_arr'] = NULL;
$g_view['month_div']['label_arr'] = NULL;
$g_view['month_div_cnt'] = 0;
$g_stat_h->volume_get_month_div_entries($_POST['month_division'],$g_view['month_div']['value_arr'],$g_view['month_div']['label_arr']);
$g_view['month_div_cnt'] = count($g_view['month_div']['value_arr']);
/********************************
sng:5/jan/2011
By default, the first value of the list is to be selected
******/
if(!isset($_POST['month_division_list'])||($_POST['month_division_list']=="")){
	$_POST['month_division_list'] = $g_view['month_div']['value_arr'][0];
}
/*********************************************/
$g_view['content_view'] = "issuance_data_view2.php";
$categories = $g_trans->getCategoryTree(); 
require("content_view.php");
?>