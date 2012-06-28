<?php
/***
edit the list of members who admire me or recommended me
***/
require_once("include/global.php");
require_once("classes/class.member.php");
require_once("check_mem_login.php");
require_once("classes/class.magic_quote.php");
require_once("classes/class.account.php");
///////////////////////////////////////////////////
//a delegate can switch identity. In that case, mem_id is different.
//so if you really need to know the actual mem id of this user, use real_mem_id
$g_view['member_id'] = $_SESSION['real_mem_id'];
$g_view['validation_passed'] = false;
$g_view['err'] = array();
$g_view['msg'] = "";
$g_view['input'] = array();
///////////////////////////////////////

if(isset($_POST['action'])&&($_POST['action']=="switch_identity")){
	$switch_accepted = false;
	$success = $g_account->switch_identity_for_delegate($g_view['member_id'],$_POST['switch_to_mem_id'],$switch_accepted);
	if(!$success){
		die("Cannot switch identity");
	}else{
		if($switch_accepted){
			header("Location: index.php");
			exit;
		}else{
			$g_view['msg'] = "You are not authorised to act as delegate";
		}
	}
}
//////////////////////////////////////////
if(isset($_POST['action'])&&($_POST['action']=="switch_to_self")){
	
	$success = $g_account->switch_to_self($_SESSION['real_mem_id']);
	if(!$success){
		die("Cannot switch identity");
	}else{
		header("Location: index.php");
		exit;
	}
}
///////////////////////////////////////
//get list of members for whom I am delegating
$g_view['delegate_for_data'] = array();
$g_view['delegate_for_count'] = 0;
$success = $g_mem->front_get_delegate_for_list($g_view['member_id'],$g_view['delegate_for_data'],$g_view['delegate_for_count']);
if(!$success){
	die("Cannot fetch members");
}
//////////////////////////////////////////////////
$g_view['edit_heading'] = "List of Members Delegating For";
$g_view['edit_view'] = "edit_profile_delegates_for_view.php";
///////////////////////////////////////////////////////////
require_once("default_metatags.php");
$g_view['page_heading'] = "Edit Your Profile";
$g_view['content_view'] = "edit_profile_container_view.php";
require("content_view.php");
////////////////////////////////////////////////////////////
?>