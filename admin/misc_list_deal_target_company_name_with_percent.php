<?php
/******
sng:28/sep/2010
In M&A deals, frequently, company A buy x% stake in company B. Hence the target is written
as company B (x% stake). This has to be found
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
$success = $g_misc->admin_get_target_name_with_percent_paged($g_view['start'],$g_view['num_to_show']+1,$g_view['data'],$g_view['data_count']);
if(!$success){
	die("Cannot get data");
}
////////////////////////////////////////////////
$g_view['heading'] = "List of M&A targets with %";
$g_view['content_view'] = "admin/misc_list_deal_target_company_name_with_percent_view.php";
include("admin/content_view.php");
?>