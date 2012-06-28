<?php
/***
only logged in members can suggest a deal
****/
include("include/global.php");
/**********
sng:21/mar/2011
now, we can come back here after login
**********/
$_SESSION['after_login'] = "suggest_deal.php";
require_once("check_mem_login.php");
require_once("classes/class.country.php");
require_once("classes/class.company.php");
require_once("classes/class.transaction.php");
require_once("classes/class.magic_quote.php");
//////////////////////////////////////////////
if(isset($_POST['action'])&&($_POST['action']=="suggest")){
	$validation_passed = false;
	$success = $g_trans->member_suggest_deal($_SESSION['mem_id'],$_POST,$validation_passed,$g_view['err']);
	if(!$success){
		die("Cannot store deal suggestion");
	}
	if($validation_passed){
		$g_view['msg'] = "Deal suggestion stored. We shall review and add the data within 24 hours to the database.";
	}else{
		//the form is to be shown with data just entered
		/***
		sng:16/aug/2010
		apart from the specific error messages below the fields, client wants a general error message
		***/	
		$g_view['err_msg'] = "Please check that all the mandatory fields are completed"; 
		$g_view['input']['deal_company_name'] = $g_mc->view_to_view($_POST['deal_company_name']);
		$g_view['input']['value_in_billion'] =$_POST['value_in_billion'];
		
		$g_view['input']['currency'] =$_POST['currency'];
		$g_view['input']['exchange_rate'] =$_POST['exchange_rate'];
		$g_view['input']['value_in_billion_local_currency'] =$_POST['value_in_billion_local_currency'];
		
		$g_view['input']['date_of_deal'] = $_POST['date_of_deal'];
		
		$g_view['input']['deal_cat_name'] = $_POST['deal_cat_name'];
		$g_view['input']['deal_subcat1_name'] = $_POST['deal_subcat1_name'];
		$g_view['input']['deal_subcat2_name'] = $_POST['deal_subcat2_name'];
		$g_view['input']['coupon'] = $_POST['coupon'];
		
		$g_view['input']['maturity_date'] = $_POST['maturity_date'];
		
		
		$g_view['input']['target_company_name'] = $g_mc->view_to_view($_POST['target_company_name']);
		$g_view['input']['target_country'] = $_POST['target_country'];
		$g_view['input']['target_sector'] = $_POST['target_sector'];
		
		$g_view['input']['deal_note'] = $g_mc->view_to_view($_POST['deal_note']);
		$g_view['input']['deal_sources'] = $g_mc->view_to_view($_POST['deal_sources']);
		/***
		sng:6/aug/2010
		extra fields
		***/
		$g_view['input']['base_fee'] =$_POST['base_fee'];
		$g_view['input']['incentive_fee'] =$_POST['incentive_fee'];
		$g_view['input']['current_rating'] =$_POST['current_rating'];
		$g_view['input']['1_day_price_change'] =$_POST['1_day_price_change'];
		$g_view['input']['discount_to_last'] =$_POST['discount_to_last'];
		$g_view['input']['discount_to_terp'] =$_POST['discount_to_terp'];
		
		$g_view['input']['ev_ebitda_ltm'] =$_POST['ev_ebitda_ltm'];
		$g_view['input']['ev_ebitda_1yr'] =$_POST['ev_ebitda_1yr'];
		$g_view['input']['30_days_premia'] =$_POST['30_days_premia'];
		
		$g_view['input']['seller_company_name'] =$g_mc->view_to_view($_POST['seller_company_name']);
		$g_view['input']['seller_country'] =$_POST['seller_country'];
		$g_view['input']['seller_sector'] =$_POST['seller_sector'];
		
		for($i=1;$i<=9;$i++){
			$input_name = "bank".$i;
			$g_view['input'][$input_name] = $_POST[$input_name];
		}
		for($i=1;$i<=9;$i++){
			$input_name = "law_firm".$i;
			$g_view['input'][$input_name] = $_POST[$input_name];
		}
	}
}
////////////////////////////////////////////////////
//fetch headquarter_country names
$g_view['country_list'] = array();
$g_view['country_count'] = 0;
$success = $g_country->get_all_country_list($g_view['country_list'],$g_view['country_count']);
if(!$success){
	die("Cannot get country list");
}
///////////////////////////////////////////
//fetch Category names
$g_view['cat_list'] = array();
$g_view['cat_count'] = 0;
//$success = $g_trans->get_all_category_list($g_view['cat_list'],$g_view['cat_count']);
$success = $g_trans->get_all_category_type("type",$g_view['cat_list'],$g_view['cat_count']);
if(!$success){
	die("Cannot get category list");
}
////////////////////////////////////////
//fetch subcategories for this category
$g_view['subcat1_list'] = array();
$g_view['subcat1_count'] = 0;
$success = $g_trans->get_all_category_subtype1_for_category_type($g_view['input']['deal_cat_name'],$g_view['subcat1_list'],$g_view['subcat1_count']);
if(!$success){
	die("Cannot get sub category list");
}
////////////////////////////////////////////
//fetch sub subcategories for this category
$g_view['subcat2_list'] = array();
$g_view['subcat2_count'] = 0;
$success = $g_trans->get_all_category_subtype2_for_category_type($g_view['input']['deal_cat_name'],$g_view['input']['deal_subcat1_name'],$g_view['subcat2_list'],$g_view['subcat2_count']);
if(!$success){
	die("Cannot get sub sub category list");
}
/////////////////////////////////////////////////
/***
sng:12/may/2010
for m and a deals, there will be target sector
***/
$g_view['sector_list'] = array();
$g_view['sector_count'] = 0;
$success = $g_company->get_all_sector_list($g_view['sector_list'],$g_view['sector_count']);
if(!$success){
	die("Cannot get sector list");
}
////////////////////////////////////////////////////////
require_once("default_metatags.php");
$g_view['page_heading'] = "Suggest a Deal";
$g_view['content_view'] = "suggest_deal_view.php";
require("content_view.php");
?>