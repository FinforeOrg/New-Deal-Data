<?php
include("../include/global.php");
require_once ("admin/checklogin.php");
require_once("classes/class.company.php");
require_once("classes/class.image_util.php");
///////////////////////////////////////////////////////
$g_view['err'] = array();
$g_view['msg'] = "";
///////////////////////////////////////
if(isset($_POST['action'])&&($_POST['action']=="edit")){
     
	$validation_passed = false;
	
	$success = $g_company->edit_extra_company_logo($_POST['deal_id'],$_POST,"logo","../uploaded_img/logo",$validation_passed,$g_view['err']);
	if(!$success){
		die("Cannot update extra company logo");
	}
	if($validation_passed){
		$g_view['msg'] = "extra company logo updated";
	}
}
/////////////////////////////////////////////////////////////////////////
//get the company data
$g_view['data'] = NULL;
$success = $g_company->get_extra_company_logo($_POST['deal_id'],$_POST['company_type'],$g_view['data']);
if(!$success){
	die("Cannot get extra company logo");
}
///////////////////////////////////////////////////////////
$g_view['heading'] = "Update Extra Company Logo";
$g_view['content_view'] = "admin/extra_company_logo_view.php";
include("admin/content_view.php");

?>