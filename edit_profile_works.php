<?php
/***
edit the list of member whome I admire or recommend
***/
require_once("include/global.php");
require_once("check_mem_login.php");

require_once("classes/class.member.php");
////////////////////////////////////////
$g_view['member_id'] = $_SESSION['mem_id'];
/////////////////////////////////////////////////////////
if(isset($_POST['action'])&&($_POST['action']=="add")){
	$validation_passed = false;
	$g_view['err'] = array();
	$success = $g_mem->add_prev_work_via_edit($g_view['member_id'],$_POST,$validation_passed,$g_view['err']);
	if(!$success){
		die("Cannot add prev work");
	}
}
/////////////////////////////////////////////////////////
/***
sng:3/jun/2010
Add support for deletion of prev work record
***/
if(isset($_POST['action'])&&($_POST['action']=="delete_work")){
	$success = $g_mem->delete_prev_work_via_edit($g_view['member_id'],$_POST['work_id']);
	if(!$success){
		die("Cannot delete prev work");
	}
}
//////////////////////////////////////////////////////////
//get the list of designations for this type of user
$g_view['designation_list'] = array();
$g_view['designation_count'] = 0;
$success = $g_mem->get_all_designation_list_by_type($_SESSION['member_type'],$g_view['designation_list'],$g_view['designation_count']);
if(!$success){
	die("Cannot fetch designation list");
}
//////////////////////////////////////////////////////////
//get list of prev works
$g_view['work_data'] = array();
$g_view['work_count'] = 0;

$success = $g_mem->front_prev_work_list($g_view['member_id'],$g_view['work_data'],$g_view['work_count']);
if(!$success){
	die("Cannot fetch prev work records");
}
///////////////////////////////////////////////////////////
$g_view['edit_heading'] = "Edit Previous Works";
$g_view['edit_view'] = "edit_profile_works_view.php";
///////////////////////////////////////////////////////////
require_once("default_metatags.php");
$g_view['page_heading'] = "Edit Your Profile";
$g_view['content_view'] = "edit_profile_container_view.php";
require("content_view.php");
////////////////////////////////////////////////////////////
?>
