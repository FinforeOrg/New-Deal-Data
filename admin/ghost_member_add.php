<?php
include("../include/global.php");
require_once ("admin/checklogin.php");
require_once("classes/class.member.php");
require_once("classes/class.country.php");
require_once("classes/class.company.php");
require_once("classes/class.magic_quote.php");
///////////////////////////////////////////////////////
$g_view['err'] = array();
$g_view['msg'] = "";
$g_view['input'] = array();
///////////////////////////////////////
if(isset($_POST['action'])&&($_POST['action']=="add")){
     
	$validation_passed = false;
	$success = $g_mem->create_ghost_member($_POST,$validation_passed,$g_view['err']);
	if(!$success){
		die("Cannot create ghost member");
	}
	//the form is to be shown with data just entered
	$g_view['input']['first_name'] = $g_mc->view_to_view($_POST['first_name']);
	$g_view['input']['last_name'] = $g_mc->view_to_view($_POST['last_name']);
	$g_view['input']['type'] = $_POST['type'];
	$g_view['input']['firm_name'] = $g_mc->view_to_view($_POST['firm_name']);
	$g_view['input']['designation'] = $_POST['designation'];
	$g_view['input']['location'] = $_POST['location'];
	//////////////////////////////////////////
	if($validation_passed){
		$g_view['msg'] = "Ghost member created";
	}
}

///////////////////////////////////////////////////////////
/****
we check if membership type is selected or not. If selected, we fetch only those designations
**********/
$g_view['designation_list'] = array();
$g_view['designation_count'] = 0;
if(isset($_POST['type'])&&($_POST['type']!="")){
	$success = $g_mem->get_all_designation_list_by_type($_POST['type'],$g_view['designation_list'],$g_view['designation_count']);
	if(!$success){
		die("Cannot get designation list");
	}
}
///////////////////////////////////////////////////////////
//fetch country names
$g_view['country_list'] = array();
$g_view['country_count'] = 0;
$success = $g_country->get_all_country_list($g_view['country_list'],$g_view['country_count']);
if(!$success){
	die("Cannot get country list");
}
///////////////////////////////////////////////////////////

$g_view['heading'] = "Create Ghost Member";
$g_view['content_view'] = "admin/ghost_member_add_view.php";
include("admin/content_view.php");
?>