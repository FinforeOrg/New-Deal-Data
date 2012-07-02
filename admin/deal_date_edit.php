<?php
include("../include/global.php");
require_once ("admin/checklogin.php");
require_once("classes/class.transaction.php");
/////////////
$g_view['id'] = $_REQUEST['id'];
$g_view['msg'] = "";
$g_view['err'] = array();
/////////////
if(isset($_POST['action'])&&($_POST['action']=="edit")){
	$validation_passed = false;
	$success = $g_trans->admin_edit_deal_date($g_view['id'],$_POST,$validation_passed,$g_view['err']);
	if(!$success){
		die("Cannot edit deal date");
	}
	if($validation_passed){
		$g_view['msg'] = "Updated";
	}
}
///////////////////////
//get the preset data
$g_view['data'] = NULL;
$g_view['data_count'] = 0;
$success = $g_trans->admin_get_deal_date($g_view['id'],$g_view['data']);
if(!$success){
	die("Cannot get deal date detail");
}
////////////////
$g_view['heading'] = "Edit ".$g_view['data']['name'];
$g_view['content_view'] = "admin/deal_date_edit_view.php";
include("admin/content_view.php");
?>