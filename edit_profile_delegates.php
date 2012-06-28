<?php
/***
edit the list of members who admire me or recommended me
***/
require_once("include/global.php");
require_once("classes/class.member.php");
require_once("check_mem_login.php");
require_once("classes/class.magic_quote.php");
///////////////////////////////////////////////////
$g_view['member_id'] = $_SESSION['mem_id'];
$g_view['validation_passed'] = false;
$g_view['err'] = array();
$g_view['msg'] = "";
$g_view['input'] = array();
///////////////////////////////////////
if(isset($_POST['action'])&&($_POST['action']=="add_delegate")){
	
	$success = $g_mem->add_delegate_via_edit($g_view['member_id'],$_POST['colleague_id'],$g_view['validation_passed'],$g_view['err']);
	if(!$success){
		die("Cannot add delegate");
	}
	if($g_view['validation_passed']){
		$g_view['msg'] = "Delegate added";
	}else{
		$g_view['input']['colleague_name'] = $g_mc->view_to_view($_POST['colleague_name']);
	}
}
if(isset($_POST['action'])&&($_POST['action']=="delete_delegate")){
	$success = $g_mem->delete_delegate_via_edit($g_view['member_id'],$_POST['colleague_id']);
	if(!$success){
		die("Cannot delete delegate");
	}
}
//////////////////////////////////////////
//get list of delegates
$g_view['delegate_data'] = array();
$g_view['delegate_count'] = 0;
$success = $g_mem->front_get_delegate_list($g_view['member_id'],$g_view['delegate_data'],$g_view['delegate_count']);
if(!$success){
	die("Cannot fetch delegates");
}
/***
sng:4/may/2010
We need to change this a bit. Now a delegate can assume my identity and enter here to change the delegates.
This should not be allowed.
However, a delegate can appoint his/her own delegate.
So how to know that the delegate is on assumed identity or self identity?
If the real mem id is same as mem_id, that means, the delegate has not assumed the identity of another member
*****/
//////////////////////////////////////////////////
$g_view['edit_heading'] = "Edit Delegates";
if($_SESSION['is_delegate']){
	if($_SESSION['real_mem_id']!=$_SESSION['mem_id']){
		$g_view['edit_view'] = "edit_profile_denied_view.php";
	}else{
		$g_view['edit_view'] = "edit_profile_delegates_view.php";
	}
}else{
	$g_view['edit_view'] = "edit_profile_delegates_view.php";
}
///////////////////////////////////////////////////////////
require_once("default_metatags.php");
$g_view['page_heading'] = "Edit Your Profile";
$g_view['content_view'] = "edit_profile_container_view.php";
require("content_view.php");
////////////////////////////////////////////////////////////
?>