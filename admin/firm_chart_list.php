<?php
include("../include/global.php");
require_once ("admin/checklogin.php");
require_once("classes/class.statistics.php");
require_once("classes/class.magic_quote.php");
///////////////////////////////////////////////////////
$g_view['msg'] = "";
//////////////////////////////////////////
if(isset($_POST['action'])&&($_POST['action']=="del")){
	$success = $g_stat->delete_firm_chart($_POST['id'],$g_view['msg']);
	if(!$success){
		die("Cannot delete firm chart");
	}
}
///////////////////////////////////////////////
//get the list
$g_view['data_count'] = 0;
$g_view['data'] = array();
$g_view['num_to_show'] = 50;
$g_view['start'] = 0;
if(isset($_REQUEST['start'])&&($_REQUEST['start']!="")){
	$g_view['start'] = $_REQUEST['start'];
}
$success = $g_stat->firm_chart_list_paged($g_view['start'],$g_view['num_to_show']+1,$g_view['data'],$g_view['data_count']);
if(!$success){
	die("Cannot get firm chart list");
}
///////////////////////////////////////////////////////////
$g_view['heading'] = "List Firm Charts";
$g_view['content_view'] = "admin/firm_chart_list_view.php";
include("admin/content_view.php");
?>