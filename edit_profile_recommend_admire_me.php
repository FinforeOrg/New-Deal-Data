<?php
/***
edit the list of members who admire me or recommended me
***/
require_once("include/global.php");
require_once("classes/class.member.php");
require_once("check_mem_login.php");
///////////////////////////////////////////////////
$g_view['member_id'] = $_SESSION['mem_id'];
///////////////////////////////////////
if(isset($_POST['action'])&&($_POST['action']=="delete_recommend_me")){
	$success = $g_mem->delete_recommended($_POST['mem_id'],$g_view['member_id']);
	if(!$success){
		die("Cannot remove from recommend list");
	}
}
if(isset($_POST['action'])&&($_POST['action']=="delete_admire_me")){
	$success = $g_mem->delete_admired($_POST['mem_id'],$g_view['member_id']);
	if(!$success){
		die("Cannot remove from admire list");
	}
}
//////////////////////////////////////////
//get list of members who recommended this member
$g_view['recommended_by_data'] = array();
$g_view['recommended_by_count'] = 0;
$success = $g_mem->front_recommended_by_list($g_view['member_id'],$g_view['recommended_by_data'],$g_view['recommended_by_count']);
if(!$success){
	die("Cannot fetch members who recommended this member");
}
///////////////////////////
//get list of members who admire this member
$g_view['admired_by_data'] = array();
$g_view['admired_by_count'] = 0;
$success = $g_mem->front_admired_by_list($g_view['member_id'],$g_view['admired_by_data'],$g_view['admired_by_count']);
if(!$success){
	die("Cannot fetch members who admired this member");
}
/////////////////////////////////////////////
$g_view['edit_heading'] = "Edit Members who Recommends / Admires Me";
$g_view['edit_view'] = "edit_profile_recommend_admire_me_view.php";
///////////////////////////////////////////////////////////
require_once("default_metatags.php");
$g_view['page_heading'] = "Edit Your Profile";
$g_view['content_view'] = "edit_profile_container_view.php";
require("content_view.php");
////////////////////////////////////////////////////////////
?>