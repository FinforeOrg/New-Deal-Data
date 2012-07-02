<?php
/********************
sng:25/apr/2012
In the front end, when we use the simple submission form OR the detailed submission form to submit a deal, it gets stored as a deal record.

This means, admin no longer needs to view the list of suggestions and create the deal record  .
************************/
die("no longer needed");

include("../include/global.php");
require_once ("admin/checklogin.php");
require_once("classes/class.transaction.php");
///////////////////////////////////////////////////////
$g_view['msg'] = "";
//////////////////////////////////////
if(isset($_POST['action'])&&($_POST['action']=="reject")){
	$success = $g_trans->reject_suggested_deal($_POST['id'],$g_view['msg']);
	if(!$success){
		die("Cannot reject suggested deal");
	}
}

/////////////////////////////////////////////
//get the list of suggestions
$g_view['data_count'] = 0;
$g_view['data'] = array();
$g_view['num_to_show'] = 50;
$g_view['start'] = 0;
if(isset($_REQUEST['start'])&&($_REQUEST['start']!="")){
	$g_view['start'] = $_REQUEST['start'];
}
/////////////////
$success = $g_trans->get_deal_suggestion_paged($g_view['start'],$g_view['num_to_show']+1,$g_view['data'],$g_view['data_count']);
if(!$success){
	die("Cannot get deal suggestion list");
}
////////////////////////////////////////////////
$g_view['heading'] = "List of Suggested Transactions";
$g_view['content_view'] = "admin/deal_suggestion_list_view.php";
include("admin/content_view.php");
?>