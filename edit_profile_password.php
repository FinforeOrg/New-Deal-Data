<?php
require_once("include/global.php");
require_once("check_mem_login.php");
require_once("classes/class.account.php");
////////////////////////////////////////////////////////////
$g_view['member_id'] = $_SESSION['mem_id'];
$g_view['err'] = array();
$g_view['msg'] = "";
$g_view['validation_passed'] = false;
////////////////////////////////////////////////////////////
if(isset($_POST['action'])&&($_POST['action']=="change_password")){
	$success = $g_account->change_site_member_password($g_view['member_id'],$_POST['curr_password'],$_POST['new_password'],$_POST['re_password'],$g_view['validation_passed'],$g_view['err']);
	if(!$success){
		die("Cannot change password");
	}
	if($g_view['validation_passed']){
		$g_view['msg'] = "Password changed";
	}else{
		//nothing
	}
}
/***
sng:4/may/2010
We need to change this a bit. Now a delegate can assume my identity and enter here to change password.
This should not be allowed. However, a delegate can change his/her password.
So how to know that the delegate is on assumed identity or self identity?
If the real mem id is same as mem_id, that means, the delegate has not assumed the identity of another member
*****/
/////////////////////////////////////////////////////////////
$g_view['edit_heading'] = "Change Password";
if($_SESSION['is_delegate']){
	if($_SESSION['real_mem_id']!=$_SESSION['mem_id']){
		$g_view['edit_view'] = "edit_profile_denied_view.php";
	}else{
		$g_view['edit_view'] = "edit_profile_password_view.php";
	}
}else{
	$g_view['edit_view'] = "edit_profile_password_view.php";
}
///////////////////////////////////////////////////////////
require_once("default_metatags.php");
$g_view['page_heading'] = "Edit Your Profile";
$g_view['content_view'] = "edit_profile_container_view.php";
require("content_view.php");
////////////////////////////////////////////////////////////
?>