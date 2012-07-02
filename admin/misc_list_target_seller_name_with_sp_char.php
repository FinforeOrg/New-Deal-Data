<?php
/******
sng:28/sep/2010
Utility to search for the given special char in deal target company name or deal seller company name
*********/
include("../include/global.php");
require_once ("admin/checklogin.php");
require_once("classes/class.misc.php");
require_once("classes/class.magic_quote.php");
///////////////////////////////////////////////////////

$g_view['msg'] = "";

///////////////////////////////////////
if(isset($_POST['myaction'])&&($_POST['myaction']=="search")){
	$g_view['data_count'] = 0;
	$g_view['data'] = array();
	$g_view['num_to_show'] = 50;
	$g_view['start'] = 0;
	if(isset($_POST['start'])&&($_POST['start']!="")){
		$g_view['start'] = $_POST['start'];
	}
	$g_view['special_char'] = $g_mc->view_to_view($_POST['special_char']);
	$success = $g_misc->admin_get_seller_target_name_with_sp_char_paged($_POST['special_char'],$g_view['start'],$g_view['num_to_show']+1,$g_view['data'],$g_view['data_count']);
	if(!$success){
		die("Cannot get data");
	}
}
////////////////////////////////////////////////
$g_view['heading'] = "List of target or seller name with special char";
$g_view['content_view'] = "admin/misc_list_target_seller_name_with_sp_char_view.php";
include("admin/content_view.php");
?>