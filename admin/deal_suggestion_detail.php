<?php
/************
sng:1/july/2011
not used any more
*************/
include("../include/global.php");
require_once ("admin/checklogin.php");
require_once("classes/class.transaction.php");
///////////////////////////////////////////////////////
$g_view['msg'] = "";
//we also need to keep track of the page from where we came
$g_view['from'] = 0;
if(isset($_REQUEST['from'])&&($_REQUEST['from']!="")){
	$g_view['from'] = $_REQUEST['from'];
}
$g_view['id'] = $_REQUEST['id'];
//////////////////////////////////////
if(isset($_POST['action'])&&($_POST['action']=="accept")){
	$g_view['deal_accepted'] = false;
	
	$success = $g_trans->accept_deal_suggestion($_POST['id'],$_POST,$g_view['deal_accepted']);
	if(!$success){
		die("Cannot accept deal suggestion");
	}
	if(!$g_view['deal_accepted']){
		$g_view['msg'] = "A deal could not be created from the suggestion";
	}else{
		//if the deal is accepted, the suggestion record is deleted, so we go to listing page
		header("Location: deal_suggestion_list.php?start=".$g_view['from']);
		exit;
	}
}
//get the suggestion data.
$g_view['data'] = NULL;
/////////////////
$success = $g_trans->get_deal_suggestion_detail($g_view['id'],$g_view['data']);
if(!$success){
	die("Cannot get deal suggestion detail");
}
////////////////////////////////////////////////
$g_view['heading'] = "Suggested Transaction Detail";
$g_view['content_view'] = "admin/deal_suggestion_detail_view.php";
include("admin/content_view.php");
?>