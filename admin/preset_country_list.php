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
	$success = $g_preset->admin_add_preset_for_country($_POST,$validation_passed,$g_view['err']);
	if(!$success){
		die("Cannot add country preset");
	}
}
//////////////////////////////////////////
//get all presets
$g_view['data_count'] = 0;
$g_view['data'] = array();
$success = $g_preset->admin_get_all_preset_for_country($g_view['data'],$g_view['data_count']);
if(!$success){
	die("Cannot get country preset data");
}
////////////////////////////////////////////////

$g_view['heading'] = "List of Presets For Country";
$g_view['content_view'] = "admin/preset_country_list_view.php";
include("admin/content_view.php");
?>