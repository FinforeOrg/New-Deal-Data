<?php
/******
sng:30/sep/2010
Utility to search for companies having all caps in the name
like CITI instead of Citi
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
	$success = $g_misc->admin_get_company_name_with_all_cap_paged($g_view['start'],$g_view['num_to_show']+1,$g_view['data'],$g_view['data_count']);
	if(!$success){
		die("Cannot get data");
	}
}
////////////////////////////////////////////////
$g_view['heading'] = "List of company name with all cap";
$g_view['content_view'] = "admin/misc_list_company_name_with_all_cap_view.php";
include("admin/content_view.php");
?>