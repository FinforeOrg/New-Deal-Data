<?php
include("../include/global.php");
require_once ("admin/checklogin.php");
require_once("classes/class.statistics.php");
require_once("classes/class.magic_quote.php");
///////////////////////////////////////////////////////
$g_view['msg'] = "";
//////////////////////////////////////////
if(isset($_POST['action'])&&($_POST['action']=="del")){
	/***
	sng:26/may/2010
	since a chart may be assigned to a firm, we let the function decide what to do
	and send proper message
	****/
	$success = $g_stat->delete_issuance_page_chart($_POST['id'],$g_view['msg']);
	if(!$success){
		die("Cannot delete chart");
	}
}
///////////////////////////////////////////////
//get the list of charts
$g_view['data_count'] = 0;
$g_view['data'] = array();
$g_view['num_to_show'] = 50;
$g_view['start'] = 0;
if(isset($_REQUEST['start'])&&($_REQUEST['start']!="")){
	$g_view['start'] = $_REQUEST['start'];
}
$success = $g_stat->get_issuance_page_chart_list_paged($g_view['start'],$g_view['num_to_show']+1,$g_view['data'],$g_view['data_count']);
if(!$success){
	die("Cannot get chart list");
}
///////////////////////////////////////////////////////////
$g_view['heading'] = "List Issuance Page Charts";
$g_view['content_view'] = "admin/issuance_page_chart_list_view.php";
include("admin/content_view.php");
?>