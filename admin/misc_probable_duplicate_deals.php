<?php
include("../include/global.php");
require_once ("admin/checklogin.php");
require_once("classes/class.misc.php");
require_once("classes/class.transaction.php");
///////////////////////////////////////////////////////

$g_view['msg'] = "";

if(isset($_POST['myaction'])&&($_POST['myaction']=="search_deal")){
	//get the list of duplicate deals
	$g_view['num_to_show'] = 50;
	$g_view['start'] = 0;
	if(isset($_REQUEST['start'])&&($_REQUEST['start']!="")){
		$g_view['start'] = $_REQUEST['start'];
	}

	$g_view['data_count'] = 0;
	$g_view['data'] = array();
	$success = $g_misc->admin_get_probable_duplicate_deals_paged($_POST,$g_view['start'],$g_view['num_to_show']+1,$g_view['data'],$g_view['data_count']);
	if(!$success){
		die("Cannot get data");
	}
}
///////////////////////////////////////
//we need to get the data from master tables to show in dropdowns
//fetch Category names
$g_view['cat_list'] = array();
$g_view['cat_count'] = 0;
$success = $g_trans->get_all_category_type("type",$g_view['cat_list'],$g_view['cat_count']);
if(!$success){
	die("Cannot get category list");
}
////////////////////////////////////////////////
$g_view['heading'] = "List of Probable Duplicate Deals";
$g_view['content_view'] = "admin/misc_probable_duplicate_deals_view.php";
include("admin/content_view.php");
?>