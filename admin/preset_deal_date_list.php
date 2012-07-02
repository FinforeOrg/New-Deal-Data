<?php
/***
for deal date, there is no combo, only singleton. So preset names and conditions
are in one table.
Admin set a range like
2008: 2008-01-01 to 2008-12-31
2Q 2010: 2010-04-01 to 2010-06-30
2008-2009: 2008-01-01 to 2009-12-31
2010YTD: 2010-01-01 to (blank)
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
	$success = $g_preset->admin_add_preset_for_deal_date($_POST,$validation_passed,$g_view['err']);
	if(!$success){
		die("Cannot add deal date preset");
	}
}
//////////////////////////////////////////
if(isset($_POST['action'])&&($_POST['action']=="delete")){
	$validation_passed = false;
	$success = $g_preset->admin_delete_preset_value_for_deal_date($_POST['preset_id']);
	if(!$success){
		die("Cannot delete deal date preset");
	}
}
/////////////////////////////////
//get all presets
$g_view['data_count'] = 0;
$g_view['data'] = array();
$success = $g_preset->admin_get_all_preset_for_deal_date($g_view['data'],$g_view['data_count']);
if(!$success){
	die("Cannot get deal date preset data");
}
////////////////////////////////////////////////

$g_view['heading'] = "List of Presets For Deal Date";
$g_view['content_view'] = "admin/preset_deal_date_list_view.php";
include("admin/content_view.php");
?>