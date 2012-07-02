<?php
include("../include/global.php");
require_once ("admin/checklogin.php");
require_once("classes/class.preset.php");
require_once("classes/class.company.php");
///////////////////////////////////////////////////////
$g_view['preset_id'] = $_REQUEST['preset_id'];
$g_view['msg'] = "";
$g_view['err'] = array();
///////////////////////////////////////
if(isset($_POST['action'])&&($_POST['action']=="add")){
	$validation_passed = false;
	$success = $g_preset->admin_add_preset_value_for_sector_industry($g_view['preset_id'],$_POST,$validation_passed,$g_view['err']);
	if(!$success){
		die("Cannot add value");
	}
}
//////////////////////////////////////////
if(isset($_POST['action'])&&($_POST['action']=="delete")){
	$success = $g_preset->admin_delete_preset_value_for_sector_industry($_POST['value_id']);
	if(!$success){
		die("Cannot delete value from preset");
	}
}
////////////////////////////////////////////////
//get the preset data
$g_view['preset_data'] = NULL;
$g_view['preset_data_count'] = 0;
$success = $g_preset->admin_get_preset_for_sector_industry($g_view['preset_id'],$g_view['preset_data']);
if(!$success){
	die("Cannot get sector industry preset data detail");
}
/////////////////////////////////////////
//get all entries for this preset
$g_view['data_count'] = 0;
$g_view['data'] = array();
$success = $g_preset->admin_get_preset_values_for_sector_industry($g_view['preset_id'],$g_view['data'],$g_view['data_count']);
if(!$success){
	die("Cannot get sector industry preset values");
}
////////////////////////////////////////////////
//we need to get the data from master tables to show in dropdowns
//fetch sector names
$g_view['sector_list'] = array();
$g_view['sector_count'] = 0;
$success = $g_company->get_all_sector_list($g_view['sector_list'],$g_view['sector_count']);
if(!$success){
	die("Cannot get sector list");
}
//////////////////////////////////////////////
//fetch subcategories for this category
$g_view['industry_list'] = array();
$g_view['industry_count'] = 0;
//$success = $g_trans->get_all_category_list($g_view['cat_list'],$g_view['cat_count']);
$success = $g_company->get_all_industry_for_sector($_POST['sector'],$g_view['industry_list'],$g_view['industry_count']);
if(!$success){
	die("Cannot get industry list");
}
///////////////////////////////////////////////////
$g_view['heading'] = "Entries for ".$g_view['preset_data']['name'];
$g_view['content_view'] = "admin/preset_sector_industry_detail_view.php";
include("admin/content_view.php");
?>