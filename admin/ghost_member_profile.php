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
$g_view['data'] = NULL;
$g_view['mem_id'] = $_POST['mem_id'];
///////////////////////////////////////
if(isset($_POST['action'])&&($_POST['action']=="edit")){
     
	$validation_passed = false;
	$success = $g_mem->update_ghost_member($_POST,$validation_passed,$g_view['err']);
	if(!$success){
		die("Cannot update ghost member");
	}
	//////////////////////////////////////////
	if($validation_passed){
		$g_view['msg'] = "Ghost member updated";
	}
}
//////////////////////////////////////////////////////////////
//get the ghost member data
$success = $g_mem->get_ghost_member_profile($g_view['mem_id'],$g_view['data']);
if(!$success){
	die("Cannot get ghost member data");
}
///////////////////////////////////////////////////////////
/****
we check if membership type is selected or not. If selected, we fetch only those designations
**********/
$g_view['designation_list'] = array();
$g_view['designation_count'] = 0;

$success = $g_mem->get_all_designation_list_by_type($g_view['data']['member_type'],$g_view['designation_list'],$g_view['designation_count']);
if(!$success){
	die("Cannot get designation list");
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

$g_view['heading'] = "Edit Ghost Member";
$g_view['content_view'] = "admin/ghost_member_profile_view.php";
include("admin/content_view.php");
?>