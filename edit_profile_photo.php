<?php
/**************
sng:28/sep/2012
No longer used since we no longer create a separate page to edit photo
****************/
die("do not use");
require_once("include/global.php");
require_once("check_mem_login.php");
require_once("classes/class.member.php");
/////////////////////////////////////////////////////////
$g_view['member_id'] = $_SESSION['mem_id'];
$g_view['err'] = array();
$g_view['msg'] = "";
$g_view['validation_passed'] = false;
///////////////////////////////////////////////////////
if(isset($_POST['action'])&&($_POST['action']=="change_photo")){
	$success = $g_mem->update_profile_photo_via_edit($g_view['member_id'],"profile_img","uploaded_img/profile",$g_view['validation_passed'],$g_view['err']);
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
$success = $g_mem->get_profile_photo_for_edit($g_view['member_id'],$g_view['data']);
if(!$success){
	die("Cannot get data");
}
///////////////////////////////////////////////////////////
$g_view['edit_heading'] = "Edit Profile Photo";
$g_view['edit_view'] = "edit_profile_photo_view.php";
///////////////////////////////////////////////////////////
require_once("default_metatags.php");
$g_view['page_heading'] = "Edit Your Profile";
$g_view['content_view'] = "edit_profile_container_view.php";
require("content_view.php");
////////////////////////////////////////////////////////////
?>