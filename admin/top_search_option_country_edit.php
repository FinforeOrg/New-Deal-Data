<?php
include("../include/global.php");
require_once ("admin/checklogin.php");
require_once("classes/class.preset.php");
/////////////
$g_view['option_id'] = $_REQUEST['option_id'];
$g_view['msg'] = "";
$g_view['err'] = array();
/////////////
if(isset($_POST['action'])&&($_POST['action']=="edit")){
	$validation_passed = false;
	$success = $g_preset->admin_edit_top_search_option_for_country($g_view['option_id'],$_POST,$validation_passed,$g_view['err']);
	if(!$success){
		die("Cannot edit country top search option");
	}
	if($validation_passed){
		$g_view['msg'] = "Updated";
	}
}
///////////////////////
//get the option data
$g_view['option_data'] = NULL;

$success = $g_preset->admin_get_top_search_option_for_country($g_view['option_id'],$g_view['option_data']);
if(!$success){
	die("Cannot get country option data detail");
}
////////////////
$g_view['heading'] = "Edit ".$g_view['option_data']['name'];
$g_view['content_view'] = "admin/top_search_option_country_edit_view.php";
include("admin/content_view.php");
?>