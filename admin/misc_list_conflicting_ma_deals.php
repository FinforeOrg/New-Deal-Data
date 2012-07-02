<?php
/******
sng:12/nov/2010
Find the pending M&A deals, with older deals first
*********/
include("../include/global.php");
require_once ("admin/checklogin.php");
require_once("classes/class.misc.php");
///////////////////////////////////////////////////////

$g_view['msg'] = "";

///////////////////////////////////////

///////////////////////////////////////////////////////////

$g_view['data_count'] = 0;
$g_view['data'] = array();
$g_view['num_to_show'] = 50;
$g_view['start'] = 0;
if(isset($_REQUEST['start'])&&($_REQUEST['start']!="")){
	$g_view['start'] = $_REQUEST['start'];
}
///////////////

/////////////////
$success = $g_misc->admin_get_conflicting_ma_deals_paged($g_view['start'],$g_view['num_to_show']+1,$g_view['data'],$g_view['data_count']);
if(!$success){
	die("Cannot get data");
}
////////////////////////////////////////////////
$g_view['heading'] = "List of conflicting M&amp;A deals";
$g_view['content_view'] = "admin/misc_list_conflicting_ma_deals_view.php";
include("admin/content_view.php");
?>