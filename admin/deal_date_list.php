<?php
/***
Admin set a range like
2008: 2008-01-01 to 2008-12-31
2Q 2010: 2010-04-01 to 2010-06-30
2008-2009: 2008-01-01 to 2009-12-31
2010YTD: 2010-01-01 to (blank)
*******/
include("../include/global.php");
require_once ("admin/checklogin.php");
require_once("classes/class.transaction.php");
///////////////////////////////////////////////////////
$g_view['msg'] = "";
$g_view['err'] = array();
///////////////////////////////////////
if(isset($_POST['action'])&&($_POST['action']=="add")){
	$validation_passed = false;
	$success = $g_trans->admin_add_deal_date($_POST,$validation_passed,$g_view['err']);
	if(!$success){
		die("Cannot add deal date preset");
	}
}
//////////////////////////////////////////
if(isset($_POST['action'])&&($_POST['action']=="delete")){
	$validation_passed = false;
	$success = $g_trans->admin_delete_deal_date($_POST['id']);
	if(!$success){
		die("Cannot delete deal date");
	}
}
/////////////////////////////////
//get all date ranges
$g_view['data_count'] = 0;
$g_view['data'] = array();
$success = $g_trans->admin_get_all_date_range($g_view['data'],$g_view['data_count']);
if(!$success){
	die("Cannot get deal range data");
}
////////////////////////////////////////////////

$g_view['heading'] = "List of Date Ranges";
$g_view['content_view'] = "admin/deal_date_list_view.php";
include("admin/content_view.php");
?>