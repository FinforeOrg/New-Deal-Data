<?php
include("../include/global.php");
require_once ("admin/checklogin.php");
require_once("classes/class.statistics.php");
require_once("classes/class.magic_quote.php");
///////////////////////////////////////////////////////
$g_view['msg'] = "";
//////////////////////////////////////////
if(isset($_POST['action'])&&($_POST['action']=="del")){
	
	$success = $g_stat->delete_top_firms($_POST['id'],$g_view['msg']);
	if(!$success){
		die("Cannot delete top firms");
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
$success = $g_stat->get_top_firms_list_paged($g_view['start'],$g_view['num_to_show']+1,$g_view['data'],$g_view['data_count']);
if(!$success){
	die("Cannot get chart list");
}
///////////////////////////////////////////////////////////
$g_view['heading'] = "List Top Firms";
$g_view['content_view'] = "admin/top_firms_list_view.php";
include("admin/content_view.php");
?>