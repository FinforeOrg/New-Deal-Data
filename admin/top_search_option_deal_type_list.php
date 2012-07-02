<?php
include("../include/global.php");
require_once ("admin/checklogin.php");
require_once("classes/class.preset.php");
///////////////////////////////////////////////////////
$g_view['msg'] = "";
$g_view['err'] = array();
///////////////////////////////////////
if(isset($_POST['action'])&&($_POST['action']=="add")){
	$validation_passed = false;
	$success = $g_preset->admin_add_top_search_option_for_deal_type($_POST,$validation_passed,$g_view['err']);
	if(!$success){
		die("Cannot add deal type top search option");
	}
}
//////////////////////////////////////////
//get all presets
$g_view['data_count'] = 0;
$g_view['data'] = array();
$success = $g_preset->admin_get_all_top_search_option_for_deal_type($g_view['data'],$g_view['data_count']);
if(!$success){
	die("Cannot get deal type top search option data");
}
////////////////////////////////////////////////

$g_view['heading'] = "List of Top Search Option For Deal Type";
$g_view['content_view'] = "admin/top_search_option_deal_type_list_view.php";
include("admin/content_view.php");
?>