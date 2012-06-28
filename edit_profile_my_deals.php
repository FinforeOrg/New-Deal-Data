<?php
require_once("include/global.php");
require_once("check_mem_login.php");
require_once("classes/class.transaction.php");
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
$g_view['deal_data'] = array();
$g_view['deal_count'] = 0;
if(!isset($_REQUEST['start'])||($_REQUEST['start']=="")){
	$g_view['start_offset'] = 0;
}else{
	$g_view['start_offset'] = $_REQUEST['start'];
}
$g_view['num_to_show'] = 10;

$success = $g_trans->front_get_deals_of_member_paged($g_view['member_id'],$g_view['num_to_show']+1,$g_view['start_offset'],$g_view['deal_data'],$g_view['deal_count']);
if(!$success){
	die("Cannot fetch deal data");
}
///////////////////////////////////////////////////////////
$g_view['edit_heading'] = "My Deals";
$g_view['edit_view'] = "edit_profile_my_deals_view.php";
///////////////////////////////////////////////////////////
require_once("default_metatags.php");
$g_view['page_heading'] = "Edit Your Profile";
$g_view['content_view'] = "edit_profile_container_view.php";
require("content_view.php");
////////////////////////////////////////////////////////////
?>