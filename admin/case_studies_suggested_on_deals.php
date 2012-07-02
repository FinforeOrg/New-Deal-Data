<?php
include("../include/global.php");
require_once ("admin/checklogin.php");
require_once("classes/class.transaction.php");
///////////////////////////////////////////////////////
$g_view['msg'] = "";
//////////////////////////////////////

if(isset($_POST['action'])&&($_POST['action']=="approve")){
	$success = $g_trans->approve_case_study($_POST['case_study_id']);
	if(!$success){
		die("Cannot approve the case study");
	}
	$g_view['msg'] = "Case study approved";
}

/////////////////////////////////////////////
//get the list of error deals
$g_view['data_count'] = 0;
$g_view['data'] = array();
$g_view['num_to_show'] = 10;
$g_view['start'] = 0;
if(isset($_REQUEST['start'])&&($_REQUEST['start']!="")){
	$g_view['start'] = $_REQUEST['start'];
}
/////////////////
$success = $g_trans->get_suggested_case_studies_on_deals_paged($g_view['start'],$g_view['num_to_show']+1,$g_view['data'],$g_view['data_count']);
if(!$success){
	die("Cannot get suggested case studies on deals");
}
////////////////////////////////////////////////
$g_view['heading'] = "Case Studies Suggested on Deals";
$g_view['content_view'] = "admin/case_studies_suggested_on_deals_view.php";
include("admin/content_view.php");
?>