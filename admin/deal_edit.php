<?php
require_once("../include/global.php");
require_once ("admin/checklogin.php");

require_once("classes/class.company.php");
require_once("classes/class.country.php");

require_once("classes/class.transaction.php");
require_once("classes/class.magic_quote.php");

require_once("classes/db.php");
$g_db = new db();
/***************************************************************/
$g_view['err'] = array();
$g_view['msg'] = "";
/**********************************************************
handle submit
********************/
if(isset($_POST['action'])&&($_POST['action']=="edit")){
	
	//echo $_POST['company_id'];
	//$success = $g_trans->edit_deal($_POST['deal_id'],$_POST,$validation_passed,$g_view['err']);
	//if(!$success){
	//	die("Cannot update deal");
	//}
	require("admin/edit_deal_data.php");
}
/**************************************************************
get the deal data
***/
$g_view['data_count'] = 0;
$g_view['data'] = NULL;
$success = $g_trans->get_deal_edit($_POST['deal_id'],$g_view['data'],$g_view['data_count']);
if(!$success){
	die("Cannot get deal data");
}
/***************************************************************
fetch company names (of type company only)

sng:18/may/2010
No need to get the looooong company list, use hint
*******/
/**************************************************************
fetch Category names
**/
$g_view['cat_list'] = array();
$g_view['cat_count'] = 0;
//$success = $g_trans->get_all_category_list($g_view['cat_list'],$g_view['cat_count']);
$success = $g_trans->get_all_category_type("type",$g_view['cat_list'],$g_view['cat_count']);
if(!$success){
	die("Cannot get category list");
}
/****************************************************************
fetch subcategories for this category
***/
$g_view['subcat1_list'] = array();
$g_view['subcat1_count'] = 0;
//$success = $g_trans->get_all_category_type("subtype1",$g_view['subcat1_list'],$g_view['subcat1_count']);
$success = $g_trans->get_all_category_subtype1_for_category_type($g_view['data']['deal_cat_name'],$g_view['subcat1_list'],$g_view['subcat1_count']);
if(!$success){
	die("Cannot get subcategory1 list");
}
/*******************************************************************
fetch sub subcategories for this subcategory
**/
$g_view['subcat2_list'] = array();
$g_view['subcat2_count'] = 0;
//$success = $g_trans->get_all_category_type("subtype2",$g_view['subcat2_list'],$g_view['subcat2_count']);
$success = $g_trans->get_all_category_subtype2_for_category_type($g_view['data']['deal_cat_name'],$g_view['data']['deal_subcat1_name'],$g_view['subcat2_list'],$g_view['subcat2_count']);
if(!$success){
	die("Cannot get subcategory2 list");
}
/**********************************************************************
fetch headquarter_country names
***/
$g_view['country_list'] = array();
$g_view['country_count'] = 0;
$success = $g_country->get_all_country_list($g_view['country_list'],$g_view['country_count']);
if(!$success){
	die("Cannot get country list");
}
/*************************************************************************
fetch sector list
**************/
$g_view['sector_list'] = array();
$g_view['sector_count'] = 0;
$success = $g_company->get_all_sector_list($g_view['sector_list'],$g_view['sector_count']);
if(!$success){
	die("Cannot get sector list");
}
/********************************************************************
sng:16/jun/2011
We need industries for subsidiary
****************/
$g_view['subsidiary_industry_list'] = array();
$g_view['subsidiary_industry_list_count'] = 0;
$success = $g_company->get_all_industry_for_sector($g_view['data']['buyer_subsidiary_sector'],$g_view['subsidiary_industry_list'],$g_view['subsidiary_industry_list_count']);
if(!$success){
	die("Cannot get industry list");
}
/*********************************************************************/
$g_view['heading'] = "Edit Transaction";
$g_view['content_view'] = "admin/deal_edit_view.php";
include("admin/content_view.php");
?>