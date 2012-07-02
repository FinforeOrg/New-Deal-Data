<?php
include("../include/global.php");
require_once ("admin/checklogin.php");
require_once("classes/class.preset.php");
require_once("classes/class.transaction.php");
///////////////////////////////////////////////////////
$g_view['preset_id'] = $_REQUEST['preset_id'];
$g_view['msg'] = "";
$g_view['err'] = array();
///////////////////////////////////////
if(isset($_POST['action'])&&($_POST['action']=="add")){
	$validation_passed = false;
	$success = $g_preset->admin_add_preset_value_for_deal_type($g_view['preset_id'],$_POST,$validation_passed,$g_view['err']);
	if(!$success){
		die("Cannot add value");
	}
}
//////////////////////////////////////////
if(isset($_POST['action'])&&($_POST['action']=="delete")){
	$success = $g_preset->admin_delete_preset_value_for_deal_type($_POST['value_id']);
	if(!$success){
		die("Cannot delete value from preset");
	}
}
////////////////////////////////////////////////
//get the preset data
$g_view['preset_data'] = NULL;
$g_view['preset_data_count'] = 0;
$success = $g_preset->admin_get_preset_for_deal_type($g_view['preset_id'],$g_view['preset_data']);
if(!$success){
	die("Cannot get deal type preset data detail");
}
/////////////////////////////////////////
//get all entries for this preset
$g_view['data_count'] = 0;
$g_view['data'] = array();
$success = $g_preset->admin_get_preset_values_for_deal_type($g_view['preset_id'],$g_view['data'],$g_view['data_count']);
if(!$success){
	die("Cannot get deal type preset values");
}
////////////////////////////////////////////////
//we need to get the data from master tables to show in dropdowns
//fetch Category names
$g_view['cat_list'] = array();
$g_view['cat_count'] = 0;
$success = $g_trans->get_all_category_type("type",$g_view['cat_list'],$g_view['cat_count']);
if(!$success){
	die("Cannot get category list");
}
//////////////////////////////////////////////
//fetch subcategories for this category
$g_view['subcat_list'] = array();
$g_view['subcat_count'] = 0;
$success = $g_trans->get_all_category_subtype1_for_category_type($_POST['deal_cat_name'],$g_view['subcat_list'],$g_view['subcat_count']);
if(!$success){
	die("Cannot get sub category list");
}
//////////////////////////////////////////////////////
//fetch sub subcategories for this category
$g_view['sub_subcat_list'] = array();
$g_view['sub_subcat_count'] = 0;
$success = $g_trans->get_all_category_subtype2_for_category_type($_POST['deal_cat_name'],$_POST['deal_subcat1_name'],$g_view['sub_subcat_list'],$g_view['sub_subcat_count']);
if(!$success){
	die("Cannot get sub sub category list");
}
///////////////////////////////////////////////////
$g_view['heading'] = "Entries for ".$g_view['preset_data']['name'];
$g_view['content_view'] = "admin/preset_deal_type_detail_view.php";
include("admin/content_view.php");
?>