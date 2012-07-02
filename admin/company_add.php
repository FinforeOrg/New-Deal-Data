<?php
include("../include/global.php");
require_once ("admin/checklogin.php");
require_once("classes/class.company.php");
require_once("classes/class.country.php");
require_once("classes/class.magic_quote.php");
///////////////////////////////////////////////////////
$g_view['err'] = array();
$g_view['msg'] = "";
$g_view['input'] = array();
///////////////////////////////////////
if(isset($_POST['action'])&&($_POST['action']=="add")){
     
	$validation_passed = false;
	$success = $g_company->add_company($_POST,"logo","../uploaded_img/logo",$validation_passed,$g_view['err']);
	if(!$success){
		die("Cannot add company");
	}
	if($validation_passed){
		$g_view['msg'] = "Company added";
	}else{
		//the form is to be shown with data just entered
		$g_view['input']['name'] = $g_mc->view_to_view($_POST['name']);
		$g_view['input']['type'] = $_POST['type'];
		$g_view['input']['hq_country'] = $_POST['hq_country'];
		$g_view['input']['sector'] = $_POST['sector'];
		$g_view['input']['industry'] = $_POST['industry'];
		$g_view['input']['brief_desc'] = $g_mc->view_to_view($_POST['brief_desc']);
	}
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
*****/
//fetch industries for the seleccted sector
$g_view['industry_list'] = array();
$g_view['industry_count'] = 0;
$success = $g_company->get_all_industry_for_sector($g_view['input']['sector'],$g_view['industry_list'],$g_view['industry_count']);
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

$g_view['heading'] = "Add Company";
$g_view['content_view'] = "admin/company_add_view.php";
include("admin/content_view.php");
?>