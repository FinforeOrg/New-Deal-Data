<?php
include("../include/global.php");
require_once ("admin/checklogin.php");
require_once("classes/class.preset.php");
/////////////
$g_view['option_id'] = $_REQUEST['option_id'];
$g_view['msg'] = "";
$g_view['err'] = array();
///////////////////////
if(isset($_POST['action'])&&($_POST['action']=="add")){
	$validation_passed = false;
	$success = $g_preset->admin_add_top_search_option_mapping_for_deal_type($g_view['option_id'],$_POST,$validation_passed,$g_view['err']);
	if(!$success){
		die("Cannot add option mapping for deal type");
	}
}
///////////////////////////////
if(isset($_POST['action'])&&($_POST['action']=="delete")){
	$success = $g_preset->admin_delete_top_search_option_mapping_for_deal_type($_POST['mapping_id']);
	if(!$success){
		die("Cannot delete option mapping for deal type");
	}
}
/////////////////////////////////////
//get the list of deal type presets
$g_view['preset_data'] = array();
$g_view['preset_data_count'] = 0;
$success = $g_preset->admin_get_all_preset_for_deal_type($g_view['preset_data'],$g_view['preset_data_count']);
if(!$success){
	die("Cannot get preset deal type");
}
///////////////////////////////////
//get the preset names mapped for this
$g_view['mapping_data'] = array();
$g_view['mapping_data_count'] = 0;
$success = $g_preset->admin_get_top_search_option_mapping_for_deal_type($g_view['option_id'],$g_view['mapping_data'],$g_view['mapping_data_count']);
if(!$success){
	die("Cannot get deal type mapping list");
}
//////////////////////////////
//get the option data
$g_view['option_data'] = NULL;

$success = $g_preset->admin_get_top_search_option_for_deal_type($g_view['option_id'],$g_view['option_data']);
if(!$success){
	die("Cannot get deal type option data detail");
}
////////////////
$g_view['heading'] = "Mapping for ".$g_view['option_data']['name'];
$g_view['content_view'] = "admin/top_search_option_deal_type_preset_mapping_view.php";
include("admin/content_view.php");
?>