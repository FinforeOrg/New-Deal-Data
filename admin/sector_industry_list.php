<?php
include("../include/global.php");
require_once ("admin/checklogin.php");
require_once("classes/class.company.php");
///////////////////////////////////////////////////////
$g_view['msg'] = "";
$g_view['err'] = array();
///////////////////////////////////////
if(isset($_POST['action'])&&($_POST['action']=="add")){
	$validation_passed = false;
	$success = $g_company->add_sector_industry($_POST['sector'],$_POST['industry'],$validation_passed,$g_view['err']);
	if(!$success){
		die("Cannot add sector industry");
	}
	if($validation_passed){
		$g_view['msg'] = "Sector industry added";
	}
}
if(isset($_POST['myaction'])&&($_POST['myaction']=="del")){
	$success = $g_company->delete_sector_industry($_POST['id'],$g_view['msg']);
	if(!$success){
		die("Cannot delete sector industry");
	}
}
//////////////////////////////////////////
//get all sector industry
$g_view['data_count'] = 0;
$g_view['data'] = array();
$success = $g_company->get_all_sector_industry_list($g_view['data'],$g_view['data_count']);
if(!$success){
	die("Cannot get sector industry");
}
////////////////////////////////////////////////

$g_view['heading'] = "List of Sectors and Industries";
$g_view['content_view'] = "admin/sector_industry_list_view.php";
include("admin/content_view.php");
?>