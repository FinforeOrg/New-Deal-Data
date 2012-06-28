<?php
require_once("include/global.php");
require_once("check_mem_login.php");
require_once("classes/class.member.php");
require_once("classes/class.country.php");
/////////////////////////////////////////////////////////
$g_view['member_id'] = $_SESSION['mem_id'];
$g_view['err'] = array();
$g_view['msg'] = "";
$g_view['validation_passed'] = false;
$g_view['is_pending'] = false;
$g_view['is_favoured'] = false;
///////////////////////////////////////////////////////
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
//show data from db
//in fact, it has to be this way or else, the code will break
////////////////////////////////////////////////////////////
$g_view['data'] = NULL;
$success = $g_mem->get_company_for_edit($g_view['member_id'],$g_view['data']);
if(!$success){
	die("Cannot get company data");
}
/////////////////////////////////////////////////////
//now get designation
$g_view['designation_list'] = array();
$g_view['designation_count'] = 0;

$success = $g_mem->get_all_designation_list_by_type($g_view['data']['member_type'],$g_view['designation_list'],$g_view['designation_count']);
if(!$success){
	die("Cannot get designation list");
}

/////////////////////////////////////////////////////////////////
//fetch headquarter_country names
$g_view['country_list'] = array();
$g_view['country_count'] = 0;
$success = $g_country->get_all_country_list($g_view['country_list'],$g_view['country_count']);
if(!$success){
	die("Cannot get country list");
}
///////////////////////////////////////////////////////////
$g_view['edit_heading'] = "Edit Company";
$g_view['edit_view'] = "edit_profile_company_view.php";
///////////////////////////////////////////////////////////
require_once("default_metatags.php");
$g_view['page_heading'] = "Edit Your Profile";
$g_view['content_view'] = "edit_profile_container_view.php";
require("content_view.php");
////////////////////////////////////////////////////////////
?>