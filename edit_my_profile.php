<?php
/**************************
sng:19/feb/2011
*************************/
require_once("include/global.php");
require_once("check_mem_login.php");
require_once("classes/class.member.php");
require_once("classes/class.account.php");
require_once("classes/class.country.php");
/////////////////////////////////////////////////////////
$g_view['member_id'] = $_SESSION['mem_id'];
$g_view['err'] = array();
$g_view['msg'] = "";
$g_view['validation_passed'] = false;
///////////////////////////////////////////////////////
if(isset($_POST['action'])&&($_POST['action']=="change_profile")){
	$success = $g_mem->update_my_profile_via_edit($g_view['member_id'],$_POST,$g_view['validation_passed'],$g_view['err']);
	if(!$success){
		die("Cannot update profile");
	}
	if($g_view['validation_passed']){
		$g_view['msg'] = "Profile updated";
	}else{
		//nothing
	}
}
/////////////////////////////////////////////////////////////
if(isset($_POST['action'])&&($_POST['action']=="change_profile_2")){
	$success = $g_mem->update_my_profile_2_via_edit($g_view['member_id'],$_POST,"profile_img","uploaded_img/profile",$g_view['validation_passed'],$g_view['err']);
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
/////////////////////////////////////////////////////////////
if(isset($_POST['action'])&&($_POST['action']=="change_company")){
	$success = $g_mem->update_company_via_edit($g_view['member_id'],$_POST,$g_view['validation_passed'],$g_view['is_pending'],$g_view['is_favoured'],$g_view['err']);
	if(!$success){
		die("Cannot update company");
	}
	if($g_view['validation_passed']){
		if($g_view['is_pending']){
			/************
			sng:28/jan/2011
			if this is favoured, an email has already been sent
			*********/
			if($g_view['is_favoured']){
				$g_view['msg'] = "An email containing the verification code has been emailed to your new email address. Please click the verification link to update your profile.";
			}else{
				$g_view['msg'] = "The change requires verification. The current data is not updated till the request is verified.";
			}
		}else{
			//no data validation error, change request not pending, so
			$g_view['msg'] = "Company updated";
		}
	}else{
		//nothing
	}
}
////////////////////////////////////////////////////////////////////////
$g_view['data'] = NULL;
$success = $g_mem->get_my_profile_for_edit($g_view['member_id'],$g_view['data']);
if(!$success){
	die("Cannot get account data");
}
///////////////////////////////////////////////////////////
$g_view['company_data'] = NULL;
$success = $g_mem->get_company_for_edit($g_view['member_id'],$g_view['company_data']);
if(!$success){
	die("Cannot get company data");
}

//now get designation
$g_view['designation_list'] = array();
$g_view['designation_count'] = 0;

$success = $g_mem->get_all_designation_list_by_type($g_view['company_data']['member_type'],$g_view['designation_list'],$g_view['designation_count']);
if(!$success){
	die("Cannot get designation list");
}

//fetch headquarter_country names
$g_view['country_list'] = array();
$g_view['country_count'] = 0;
$success = $g_country->get_all_country_list($g_view['country_list'],$g_view['country_count']);
if(!$success){
	die("Cannot get country list");
}
////////////////////////////////////////////////////////////////
$g_view['edit_heading'] = "Edit Account";
$g_view['edit_view'] = "edit_my_profile_account_view.php";
///////////////////////////////////////////////////////////
require_once("default_metatags.php");
$g_view['page_heading'] = "Edit Your Profile";
$g_view['content_view'] = "edit_profile_container_view.php";
require("content_view.php");
////////////////////////////////////////////////////////////
?>