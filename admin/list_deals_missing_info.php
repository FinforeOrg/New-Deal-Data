<?php
include("../include/global.php");
require_once ("admin/checklogin.php");
require_once("classes/class.transaction.php");
///////////////////////////////////////////////////////

$g_view['msg'] = "";

///////////////////////////////////////

if(isset($_POST['myaction'])&&($_POST['myaction']=="search_deal")){
	$g_view['data_count'] = 0;
	$g_view['data'] = array();
	$g_view['num_to_show'] = 50;
	$g_view['start'] = 0;
	if(isset($_REQUEST['start'])&&($_REQUEST['start']!="")){
		$g_view['start'] = $_REQUEST['start'];
	}
	$success = $g_trans->get_all_deals_missing_info_paged($_POST['missing_info_name'],$g_view['start'],$g_view['num_to_show']+1,$g_view['data'],$g_view['data_count']);
	if(!$success){
		die("Cannot get deal data");
	}
}
////////////////////////////////////////////////
$g_view['heading'] = "List of Deals Missing Info";
$g_view['content_view'] = "admin/list_deals_missing_info_view.php";
include("admin/content_view.php");
?>