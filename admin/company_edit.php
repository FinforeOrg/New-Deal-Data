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
	
	
	$success = $g_company->edit_company($_POST['company_id'],$_POST,"logo","../uploaded_img/logo",$validation_passed,$g_view['err']);
	if(!$success){
		die("Cannot update company");
	}
	if($validation_passed){
		$g_view['msg'] = "Company data updated";
	}
}
////////////////////////////////////////////////////////////
if(isset($_POST['action'])&&($_POST['action']=="add_identifier")){
	$validation_passed = false;
	
	
	$success = $g_company->admin_add_company_identifier($_POST['company_id'],$_POST['identifier_id'],$_POST['value'],$validation_passed,$g_view['msg']);
	if(!$success){
		die("Cannot add company identifier");
	}
	if($validation_passed){
		$g_view['msg'] = "Company identifier added";
	}
}
/////////////////////////////////////////////////////////////////////////

///////////////////////////////////////////////////////////////////////
if(isset($_POST['action'])&&($_POST['action']=="edit_identifier")){
	$success = $g_company->admin_edit_company_identifier($_POST['company_id'],$_POST['identifier_id'],$_POST['value']);
	if(!$success){
		die("Cannot edit company identifier");
	}
}
///////////////////////////////////////////////////////////////////////
//get the company data
$g_view['data'] = NULL;
$success = $g_company->get_company($_POST['company_id'],$g_view['data']);

if(!$success){
	die("Cannot get company data");
}
/////////////////////////////////////////////////////////////
//fetch sector names
$g_view['sector_list'] = array();
$g_view['sector_count'] = 0;
$success = $g_company->get_all_sector_list($g_view['sector_list'],$g_view['sector_count']);
if(!$success){
	die("Cannot get company sector list");
}
///////////////////////////////////////////////////////////
/***
sng:21/may/2010
fetch industries for the sector
*******/
$g_view['industry_list'] = array();
$g_view['industry_count'] = 0;
$success = $g_company->get_all_industry_for_sector($g_view['data']['sector'],$g_view['industry_list'],$g_view['industry_count']);
if(!$success){
	die("Cannot get company industry list");
}
///////////////////////////////////////////////////////////


//fetch headquarter_country names
$g_view['country_list'] = array();
$g_view['country_count'] = 0;
$success = $g_country->get_all_country_list($g_view['country_list'],$g_view['country_count']);
if(!$success){
	die("Cannot get country list");
}
///////////////////////////////////////////////////////////
/*****************
sng:7/sep/2011
get the identifiers for this company
******************/
$g_view['identifiers'] = NULL;
$g_view['identifiers_cnt'] = 0;
$success = $g_company->admin_get_company_identifiers($_POST['company_id'],$g_view['identifiers'],$g_view['identifiers_cnt']);
if(!$success){
	die("Cannot get the identifiers");
}

/******************************
get all the identifier options
***********/
$g_view['identifier_options'] = NULL;
$g_view['identifier_options_cnt'] = 0;
$success = $g_company->admin_get_identifier_options($g_view['identifier_options'],$g_view['identifier_options_cnt']);
if(!$success){
	die("Cannot get the identifier options");
}
//////////////////////////////////////////////////////////
$g_view['heading'] = "Edit Company";
$g_view['content_view'] = "admin/company_edit_view.php";
include("admin/content_view.php");
?>