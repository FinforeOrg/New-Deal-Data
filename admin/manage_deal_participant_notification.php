<?php
require_once("../include/global.php");
require_once ("admin/checklogin.php");
require_once("classes/class.country.php");
require_once("classes/class.company.php");
require_once("classes/class.deal_support.php");
require_once("classes/class.transaction.php");

$support = new deal_support();

$g_view['msg'] = "";
$g_view['err'] = array();

if(isset($_POST['myaction'])&&($_POST['myaction']=="add")){
	$validation_passed = false;
	$success = $support->admin_add_participant_notification_detail($_POST,$validation_passed,$g_view['err']);
	if(!$success){
		die("Cannot add data");
	}
	if($validation_passed){
		$g_view['msg'] = "Added";
	}
}
/******************************************************************************************/
if(isset($_POST['myaction'])&&($_POST['myaction']=="delete")){
	$success = $support->admin_delete_participant_notification_detail($_POST['notify_id']);
	if(!$success){
		die("Cannot delete data");
	}
	$g_view['msg'] = "Deleted";
	
}
/******************************************************************************************/
//fetch countries
$g_view['country_list'] = array();
$g_view['country_count'] = 0;
$success = $g_country->get_all_country_list($g_view['country_list'],$g_view['country_count']);
if(!$success){
	die("Cannot get country list");
}
//fetch regions
$g_view['region_list'] = array();
$g_view['region_count'] = 0;
$success = $g_country->get_all_region_list($g_view['region_list'],$g_view['region_count']);
if(!$success){
	die("Cannot get region list");
}
/***************************************************************************************/
//fetch Category names
$g_view['cat_list'] = array();
$g_view['cat_count'] = 0;
$success = $g_trans->get_all_category_type("type",$g_view['cat_list'],$g_view['cat_count']);
if(!$success){
	die("Cannot get category list");
}
//////////////////////////////////////////////
//fetch subcategories for this category
$g_view['subcat_list'] = array();
$g_view['subcat_count'] = 0;
$success = $g_trans->get_all_category_subtype1_for_category_type($_POST['deal_cat_name'],$g_view['subcat_list'],$g_view['subcat_count']);
if(!$success){
	die("Cannot get sub category list");
}
//////////////////////////////////////////////////////
//fetch sub subcategories for this category
$g_view['sub_subcat_list'] = array();
$g_view['sub_subcat_count'] = 0;
$success = $g_trans->get_all_category_subtype2_for_category_type($_POST['deal_cat_name'],$_POST['deal_subcat1_name'],$g_view['sub_subcat_list'],$g_view['sub_subcat_count']);
if(!$success){
	die("Cannot get sub sub category list");
}
/****************************************************************************************/
$g_view['sector_list'] = array();
$g_view['sector_count'] = 0;
$success = $g_company->get_all_sector_list($g_view['sector_list'],$g_view['sector_count']);
if(!$success){
	die("Cannot get sector list");
}

$g_view['industry_list'] = array();
$g_view['industry_count'] = 0;
$success = $g_company->get_all_industry_for_sector($_POST['sector'],$g_view['industry_list'],$g_view['industry_count']);
if(!$success){
	die("Cannot get industry list");
}
/***********************************************************************************/
$g_view['data_count'] = 0;
$g_view['data'] = NULL;
$g_view['num_to_show'] = 50;
$g_view['start'] = 0;
if(isset($_REQUEST['start'])&&($_REQUEST['start']!="")){
	$g_view['start'] = $_REQUEST['start'];
}
/**********************************************************************************/
$success = $support->admin_list_all_participant_notification_detail_paged($g_view['start'],$g_view['num_to_show']+1,$g_view['data'],$g_view['data_count']);
if(!$success){
	die("Cannot get data");
}

$g_view['heading'] = "Manage Participant Notification";
$g_view['content_view'] = "admin/manage_deal_participant_notification_view.php";
include("admin/content_view.php");
?>