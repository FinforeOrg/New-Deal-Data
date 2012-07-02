<?php
include("../include/global.php");
require_once ("admin/checklogin.php");
require_once("classes/class.preset.php");
/////////////
$g_view['preset_id'] = $_REQUEST['preset_id'];
$g_view['msg'] = "";
$g_view['err'] = array();
/////////////
if(isset($_POST['action'])&&($_POST['action']=="edit")){
	$validation_passed = false;
	$success = $g_preset->admin_edit_preset_for_deal_date($g_view['preset_id'],$_POST,$validation_passed,$g_view['err']);
	if(!$success){
		die("Cannot edit deal date preset");
	}
	if($validation_passed){
		$g_view['msg'] = "Updated";
	}
}
///////////////////////
//get the preset data
$g_view['preset_data'] = NULL;
$g_view['preset_data_count'] = 0;
$success = $g_preset->admin_get_preset_for_deal_date($g_view['preset_id'],$g_view['preset_data']);
if(!$success){
	die("Cannot get deal date preset data detail");
}
////////////////
$g_view['heading'] = "Edit ".$g_view['preset_data']['name'];
$g_view['content_view'] = "admin/preset_deal_date_edit_view.php";
include("admin/content_view.php");
?>