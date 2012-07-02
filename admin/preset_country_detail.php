<?php
include("../include/global.php");
require_once ("admin/checklogin.php");
require_once("classes/class.preset.php");
require_once("classes/class.country.php");
///////////////////////////////////////////////////////
$g_view['preset_id'] = $_REQUEST['preset_id'];
$g_view['msg'] = "";
$g_view['err'] = array();
///////////////////////////////////////
if(isset($_POST['action'])&&($_POST['action']=="add")){
	$validation_passed = false;
	$success = $g_preset->admin_add_preset_value_for_country($g_view['preset_id'],$_POST,$validation_passed,$g_view['err']);
	if(!$success){
		die("Cannot add value");
	}
}
//////////////////////////////////////////
if(isset($_POST['action'])&&($_POST['action']=="delete")){
	$success = $g_preset->admin_delete_preset_value_for_country($_POST['value_id']);
	if(!$success){
		die("Cannot delete value from preset");
	}
}
////////////////////////////////////////////////
//get the preset data
$g_view['preset_data'] = NULL;
$g_view['preset_data_count'] = 0;
$success = $g_preset->admin_get_preset_for_country($g_view['preset_id'],$g_view['preset_data']);
if(!$success){
	die("Cannot get country preset data detail");
}
/////////////////////////////////////////
//get all entries for this preset
$g_view['data_count'] = 0;
$g_view['data'] = array();
$success = $g_preset->admin_get_preset_values_for_country($g_view['preset_id'],$g_view['data'],$g_view['data_count']);
if(!$success){
	die("Cannot get country preset values");
}
////////////////////////////////////////////////
//we need to get the data from master tables to show in dropdowns
//fetch countries
$g_view['country_list'] = array();
$g_view['country_count'] = 0;
$success = $g_country->get_all_country_list($g_view['country_list'],$g_view['country_count']);
if(!$success){
	die("Cannot get country list");
}
///////////////////////////////////////////////////
$g_view['heading'] = "Entries for ".$g_view['preset_data']['name'];
$g_view['content_view'] = "admin/preset_country_detail_view.php";
include("admin/content_view.php");
?>