<?php
/**********
this is used to list the stat details for issuance data, based on filter condition.
This can be accessed only by logged in members
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
require_once("classes/class.stat_help.php");
$savedSearches = new SavedSearches();
if (isset($_REQUEST['token'])) {
    $savedSearches->loadIntoPost($_REQUEST['token']);
}
///////////////////////////////////////////////
//support for filters
require("league_table_filter_support.php");
/////////////////////////////////////////////////
//get the data
$g_view['data'] = array();
$g_view['data_count'] = 0;
$g_view['max_value'] = 0;
//print_r($_POST);die();  
$success = $g_stat->generate_issuance_data($_POST,$g_view['data'],$g_view['max_value'],$g_view['data_count']);
if(!$success){
	//echo mysql_error();
	die("Cannot generate issuance data");
}
/////////////////////////////////////////////
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
/////////////////////////////////////
require_once("default_metatags.php");
//////////////////////
$g_view['page_heading'] = "Issuance Data";
$g_view['content_view'] = "issuance_data_detail_view.php";
$categories = $g_trans->getCategoryTree();
require("content_view.php");
?>