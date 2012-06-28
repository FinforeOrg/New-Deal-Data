<?php
/***
edit the list of member whome I admire or recommend
***/
require_once("include/global.php");
require_once("classes/class.member.php");
require_once("check_mem_login.php");
////////////////////////////////////////
$g_view['member_id'] = $_SESSION['mem_id'];
/////////////////////////////////////////////////////////
if(isset($_POST['action'])&&($_POST['action']=="delete_my_recommend")){
	$success = $g_mem->delete_recommended($g_view['member_id'],$_POST['recommended_mem_id']);
	if(!$success){
		die("Cannot remove from recommend list");
	}
}
if(isset($_POST['action'])&&($_POST['action']=="delete_my_admire")){
	$success = $g_mem->delete_admired($g_view['member_id'],$_POST['admired_mem_id']);
	if(!$success){
		die("Cannot remove from admire list");
	}
}
//////////////////////////////////////////////////////////
//get list of members recommended by this member
$g_view['recommended_data'] = array();
$g_view['recommended_count'] = 0;
$success = $g_mem->front_recommended_colleague_list($g_view['member_id'],$g_view['recommended_data'],$g_view['recommended_count']);
if(!$success){
	die("Cannot fetch members recommended");
}
///////////////////////////////////////////////////////
//get list of members admired by this member
$g_view['admired_data'] = array();
$g_view['admired_count'] = 0;
$success = $g_mem->front_admired_competitor_list($g_view['member_id'],$g_view['admired_data'],$g_view['admired_count']);
if(!$success){
	die("Cannot fetch members admired");
}
///////////////////////////////////////////////////////////
$g_view['edit_heading'] = "Edit Members Recommended / Admired by Me";
$g_view['edit_view'] = "edit_profile_recommended_admired_by_me_view.php";
///////////////////////////////////////////////////////////
require_once("default_metatags.php");
$g_view['page_heading'] = "Edit Your Profile";
$g_view['content_view'] = "edit_profile_container_view.php";
require("content_view.php");
////////////////////////////////////////////////////////////
?>
