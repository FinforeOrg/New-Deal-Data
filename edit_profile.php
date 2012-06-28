<?php
/******************************************
sng:20/sep/2011
NOT USED
********************************************/
require_once("include/global.php");
require_once("check_mem_login.php");
require_once("classes/class.member.php");
/////////////////////////////////////////////////////////
$g_view['member_id'] = $_SESSION['mem_id'];
$g_view['err'] = array();
$g_view['msg'] = "";
$g_view['validation_passed'] = false;
///////////////////////////////////////////////////////
if(isset($_POST['action'])&&($_POST['action']=="change_profile")){
	$success = $g_mem->update_profile_via_edit($g_view['member_id'],$_POST,$g_view['validation_passed'],$g_view['err']);
	if(!$success){
		die("Cannot update profile");
	}
	if($g_view['validation_passed']){
		$g_view['msg'] = "Profile updated";
	}else{
		//nothing
	}
}
////////////////////////////////////////////////////////////
$g_view['data'] = NULL;
$success = $g_mem->get_profile_for_edit($g_view['member_id'],$g_view['data']);
if(!$success){
	die("Cannot get account data");
}
///////////////////////////////////////////////////////////
$g_view['edit_heading'] = "Edit Account";
$g_view['edit_view'] = "edit_profile_account_view.php";
///////////////////////////////////////////////////////////
require_once("default_metatags.php");
$g_view['page_heading'] = "Edit Your Profile";
$g_view['content_view'] = "edit_profile_container_view.php";
require("content_view.php");
////////////////////////////////////////////////////////////
?>