<?php
include("../include/global.php");
require_once ("admin/checklogin.php");
require_once("classes/class.company.php");
///////////////////////////////////////////////////////
$g_view['msg'] = "";
/****************************************************************/
if(isset($_POST['action'])&&($_POST['action']=="reject")){
	$success = $g_company->admin_reject_suggested_blf($_POST['company_suggestion_id'],$g_view['msg']);
	if(!$success){
		die("Cannot reject suggested bank / law firm");
	}
}
/****************************************************************/
if(isset($_POST['action'])&&($_POST['action']=="accept")){
	$success = $g_company->admin_accept_suggested_blf($_POST['company_suggestion_id'],$g_view['msg']);
	if(!$success){
		die("Cannot accept suggested bank / law firm");
	}
}
/****************************************************************/
//get the list of suggestions
$g_view['data_count'] = 0;
$g_view['data'] = NULL;
$g_view['num_to_show'] = 50;
$g_view['start'] = 0;
if(isset($_REQUEST['start'])&&($_REQUEST['start']!="")){
	$g_view['start'] = $_REQUEST['start'];
}
/////////////////
$success = $g_company->admin_get_suggested_blf_paged($g_view['start'],$g_view['num_to_show']+1,$g_view['data'],$g_view['data_count']);
if(!$success){
	die("Cannot get bank / law firm suggestion list");
}
////////////////////////////////////////////////
$g_view['heading'] = "List of Suggested Banks / Law Firms";
$g_view['content_view'] = "admin/blf_suggestion_list_view.php";
include("admin/content_view.php");
?>