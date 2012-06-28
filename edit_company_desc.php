<?php
require_once("include/global.php");
require_once("check_mem_login.php");
require_once("classes/class.member.php");
////////////////////////////////////////////////////////////
$g_view['member_id'] = $_SESSION['mem_id'];
$g_view['company_id'] = $_SESSION['company_id'];
//can change desc of his/her own company only
$g_view['err'] = array();
$g_view['msg'] = "";
$g_view['validation_passed'] = false;
////////////////////////////////////////////////////////////
/***************************************************************************
sng:20/sep/2011
From now on, only admin can change company description. It cannot be changed from front end

if(isset($_POST['action'])&&($_POST['action']=="update_desc")){

	$success = $g_mem->update_company_description_via_edit($g_view['company_id'],$_POST,$g_view['member_id'],$g_view['validation_passed'],$g_view['err']);
	if(!$success){
		die("Cannot update description");
	}
	if($g_view['validation_passed']){
		$g_view['msg'] = "Updated";
	}else{
		//nothing
	}
}
*********************************************************************************/
/////////////////////////////////////////////////
//get company desc
$g_view['data'] = array();
$success = $g_mem->get_company_description_for_edit($g_view['company_id'],$g_view['data']);
if(!$success){
	die("Cannot get description");
}
/////////////////////////////////////////////////////////////
$g_view['edit_heading'] = "Change Company Description";
/*****************
sng:20/sep/2011
From now on, only admin can change company description. It cannot be changed from front end
$g_view['edit_view'] = "edit_company_desc_view.php";
**************************/
///////////////////////////////////////////////////////////
require_once("default_metatags.php");
$g_view['page_heading'] = "Edit Your Profile";
$g_view['content_view'] = "edit_profile_container_view.php";
require("content_view.php");
////////////////////////////////////////////////////////////
?>