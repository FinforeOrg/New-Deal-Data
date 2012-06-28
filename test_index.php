<?php   
/**********
This is used to create stat chart on the fly, based on conditions

sng: 3/jun/2010
we show a home page chart by default
********/
include("include/global.php");
require_once("classes/class.transaction.php");
require_once("classes/class.country.php");
require_once("classes/class.company.php");
require_once("classes/class.account.php");
require_once("classes/class.statistics.php");
require_once("classes/class.savedSearches.php");
require_once("classes/class.oneStop.php");  
$savedSearches = new SavedSearches();

//ini_set('display_errors',1);
//error_reporting(E_ALL);
if (isset($_REQUEST['token'])) {
    $savedSearches->loadIntoPost($_REQUEST['token']);
}

if (isset($_REQUEST['from']) && $_REQUEST['from'] == 'oneStop') {
    oneStop::loadIntoPost($_POST['data']);
}
//////////////////
//sng: 21/apr/2010
require("league_table_filter_support.php");
$categories = $g_trans->getCategoryTree();
//echo "<pre>" . print_r($categories,1) . "</pre>";
////////////////////////////////////////////
require_once("default_metatags.php");
//////////////////////
/***
sng:1/jun/2010
we put header and links, so we will not use the default
***/
//$g_view['page_heading'] = "League Table";
/////////////////////////////////////////////////////////
$g_view['home_chart'] = array();
$success = $g_stat->front_get_home_page_charts($g_view['home_chart']);
if(!$success){
    die("Cannot get the featured league tables for home page");
}
//////////////////////////////////
$g_view['content_view'] = "test_index_view.php";
require("content_view.php");
?>