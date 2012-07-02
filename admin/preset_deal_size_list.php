<?php
/***
for deal value, there is no combo, only singleton. So preset names and conditions
are in one table
*******/
include("../include/global.php");
require_once ("admin/checklogin.php");
require_once("classes/class.preset.php");
///////////////////////////////////////////////////////
$g_view['msg'] = "";
$g_view['err'] = array();
///////////////////////////////////////
if(isset($_POST['action'])&&($_POST['action']=="add")){
	$validation_passed = false;
	$success = $g_preset->admin_add_preset_for_deal_value($_POST,$validation_passed,$g_view['err']);
	if(!$success){
		die("Cannot add deal value preset");
	}
}
//////////////////////////////////////////
if(isset($_POST['action'])&&($_POST['action']=="delete")){
	$validation_passed = false;
	$success = $g_preset->admin_delete_preset_value_for_deal_value($_POST['preset_id']);
	if(!$success){
		die("Cannot delete deal value preset");
	}
}
/////////////////////////////////
//get all presets
$g_view['data_count'] = 0;
$g_view['data'] = array();
$success = $g_preset->admin_get_all_preset_for_deal_value($g_view['data'],$g_view['data_count']);
if(!$success){
	die("Cannot get deal value preset data");
}
////////////////////////////////////////////////

$g_view['heading'] = "List of Presets For Deal Size";
$g_view['content_view'] = "admin/preset_deal_size_list_view.php";
include("admin/content_view.php");
?>