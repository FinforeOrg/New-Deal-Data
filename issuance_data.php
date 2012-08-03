<?php
/**********
This is used to create stat chart on the fly, based on conditions

sng: 3/jun/2010
we show a home page chart by default
********/
include("include/global.php");
/**************************************************************
sng:23/nov/2010
This now require login

sng:19/jul/2012
This is now open to all
*******
$_SESSION['after_login'] = "issuance_data.php";
require_once("check_mem_login.php");
***********************************************************/
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
/**********************
sng:3/aug/2012
We cannot send data like >= in POST. The sanitiser will erase it.
So we base64 encoded the view file
and we decode it here again
************************/
$_POST['deal_size'] = base64_decode($_POST['deal_size']);
//////////////////
//sng: 21/apr/2010
require("league_table_filter_support.php");
////////////////////////////////////////////
require_once("default_metatags.php");

/********************************
sng:7/jan/2011
If the page comes from issuance_data_detail_view, $_POST['myaction'] is set to gen_chart. In that case, we do not
get the random chart data and the count is 0 and no default chart is displayed

sng:19/july/2012
We no longer show any pre generated chart
*************/


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
$g_view['page_heading'] = "Issuance Data";
$g_view['content_view'] = "issuance_data_view.php";
$categories = $g_trans->getCategoryTree(); 
$g_view['show_help'] = true;
require("content_view.php");
?>