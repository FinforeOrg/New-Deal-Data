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
	$success = $g_preset->admin_add_top_search_option_for_sector_industry($_POST,$validation_passed,$g_view['err']);
	if(!$success){
		die("Cannot add sector industry top search option");
	}
}
//////////////////////////////////////////
//get all presets
$g_view['data_count'] = 0;
$g_view['data'] = array();
$success = $g_preset->admin_get_all_top_search_option_for_sector_industry($g_view['data'],$g_view['data_count']);
if(!$success){
	die("Cannot get sector industry top search option data");
}
////////////////////////////////////////////////

$g_view['heading'] = "List of Top Search Option For Sector Industry";
$g_view['content_view'] = "admin/top_search_option_sector_industry_list_view.php";
include("admin/content_view.php");
?>