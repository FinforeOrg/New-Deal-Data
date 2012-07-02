<?php
include("../include/global.php");
require_once ("admin/checklogin.php");
require_once("classes/class.misc.php");
require_once("classes/class.magic_quote.php");
///////////////////////////////////////////////////////
$g_view['msg'] = "";
/////////////////////////////////////////////

///////////////////////////////////////
if(isset($_POST['action'])&&($_POST['action']=="search_deal")){
	$g_view['data_count'] = 0;
	$g_view['data'] = array();
	$g_view['num_to_show'] = 50;
	$g_view['start'] = 0;
	if(isset($_POST['start'])&&($_POST['start']!="")){
		$g_view['start'] = $_POST['start'];
	}
	$success = $g_misc->admin_get_deals_by_sector_industry_paged($_POST,$g_view['start'],$g_view['num_to_show']+1,$g_view['data'],$g_view['data_count']);
	if(!$success){
		die("Cannot get deal data");
	}
}
//////////////////////////////////////////////////////////
$g_view['heading'] = "Search for Transaction";
$g_view['content_view'] = "admin/misc_search_deals_by_sector_industry_view.php";
include("admin/content_view.php");
?>