<?php
include("../include/global.php");
require_once ("admin/checklogin.php");
require_once("classes/class.company.php");
require_once("classes/class.country.php");
require_once("classes/class.magic_quote.php");
///////////////////////////////////////////////////////
$g_view['err'] = array();
$g_view['msg'] = "";
///////////////////////////////////////
if(isset($_POST['action'])&&($_POST['action']=="edit")){
     
	$validation_passed = false;
	
	
	$success = $g_company->edit_bank_lawfirm($_POST['company_id'],$_POST,"logo",LOGO_PATH,$validation_passed,$g_view['err']);
	if(!$success){
		die("Cannot update company");
	}
	if($validation_passed){
		$g_view['msg'] = "Company data updated";
	}
}
/////////////////////////////////////////////////////////////////////////
//get the company data
$g_view['data'] = NULL;
$success = $g_company->get_company($_POST['company_id'],$g_view['data']);

if(!$success){
	die("Cannot get company data");
}
///////////////////////////////////////////////////////////
$g_view['heading'] = "Edit Bank / Law Firm";
$g_view['content_view'] = "admin/blf_edit_view.php";
include("admin/content_view.php");
?>