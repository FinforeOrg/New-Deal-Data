<?php
include("../include/global.php");
require_once ("admin/checklogin.php");
require_once("classes/class.company.php");
require_once("classes/class.magic_quote.php");
///////////////////////////////////////////////////////
$g_view['msg'] = "";
$g_view['err'] = array();
$g_view['input'] = array();
///////////////////////////////////////
if(isset($_POST['action'])&&($_POST['action']=="add")){
	$validation_passed = false;
	$success = $g_company->add_top_firms_category($_POST,$validation_passed,$g_view['err']);
	if(!$success){
		die("Cannot add top firm category");
	}
	if(!$validation_passed){
		//need to show the inputs again
		$g_view['input']['name'] = $g_mc->view_to_view($_POST['name']);
	}else{
		$g_view['msg'] = "Top firms category added";
	}
}
//////////////////////////////////////////
if(isset($_POST['action'])&&($_POST['action']=="make_default")){
	$success = $g_company->mark_top_firms_category_as_default($_POST['id']);
	if(!$success){
		die("Cannot mark top firm category as default");
	}
	$g_view['msg'] = "Top firms category marked as default";
}
/////////////////////////////////////////////////
//get all top firm category
$g_view['data_count'] = 0;
$g_view['data'] = array();
$success = $g_company->get_all_top_firms_categories($g_view['data'],$g_view['data_count']);
if(!$success){
	die("Cannot get top firms categories");
}
////////////////////////////////////////////////

$g_view['heading'] = "List of Top Firms categories";
$g_view['content_view'] = "admin/blf_top_firms_categories_view.php";
include("admin/content_view.php");
?>