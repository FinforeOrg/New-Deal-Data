<?php
/**********
sng:27/jun/2011
Use simple_deal_add.php where we just add the minimum data and then go to edit.
We put all the complexities of deal type specific code in edit
************/
die("do not use");
include("../include/global.php");
require_once ("admin/checklogin.php");
require_once("classes/class.company.php");
require_once("classes/class.country.php");
require_once("classes/class.transaction.php");
require_once("classes/class.magic_quote.php");


///////////////////////////////////////////////////////
$g_view['err'] = array();
$g_view['msg'] = "";
$g_view['input'] = array();
$_SESSION['logosCurrentIndex'] = (int) 0;
if (is_array($_SESSION['logos']) && $_SERVER['HTTP_REFERER'] != "http://data-cx.com/admin/deal_add.php")
    $_SESSION['logos'] = array();
///////////////////////////////////////
if(isset($_POST['action'])&&($_POST['action']=="add")){
	
	$validation_passed = false;
	/***
	sng:31/aug/2010
	When we add a deal, we also want to add the banks and law firms. We already have the popups. We
	just need the newly added transaction id
	*************/
	$new_transaction_id = 0;
	$success = $g_trans->add_deal($_POST,$validation_passed,$new_transaction_id,$g_view['err']);
	if(!$success){
		die("Cannot add deal");
	}
	if($validation_passed){
		$g_view['msg'] = "Deal added";
        $_SESSION['logos'] = array();
        $_SESSION['logosCurrentIndex'] = (int) 0;
		/***********************************************
		sng:31/aug/2010
		dea added, now we check if we have to go to add partner step or not
		*********/
		$g_view['heading'] = "Add Transaction";
		$g_view['content_view'] = "admin/deal_add_step_add_partner_view.php";
		include("admin/content_view.php");
		exit;
		/***************************************************/
	}else{
		//the form is to be shown with data just entered
		$g_view['input']['company_id'] = $_POST['company_id'];
		$g_view['input']['deal_company_name'] = $g_mc->view_to_view($_POST['deal_company_name']);
		$g_view['input']['value_in_billion'] =$_POST['value_in_billion'];
		
		$g_view['input']['deal_country'] =$_POST['deal_country'];
		$g_view['input']['deal_sector'] =$_POST['deal_sector'];
		$g_view['input']['deal_industry'] =$_POST['deal_industry'];
		
		$g_view['input']['currency'] =$_POST['currency'];
		$g_view['input']['exchange_rate'] =$_POST['exchange_rate'];
		$g_view['input']['value_in_billion_local_currency'] =$_POST['value_in_billion_local_currency'];
		
		$g_view['input']['date_of_deal'] = $_POST['date_of_deal'];
		$g_view['input']['deal_cat_name'] = $_POST['deal_cat_name'];
		$g_view['input']['deal_subcat1_name'] = $_POST['deal_subcat1_name'];
		$g_view['input']['deal_subcat2_name'] = $_POST['deal_subcat2_name'];
		$g_view['input']['coupon'] = $_POST['coupon'];
		$g_view['input']['deal_subcat1_name'] = $_POST['deal_subcat1_name'];
		$g_view['input']['maturity_date'] = $_POST['maturity_date'];
		
		$g_view['input']['target_company_id'] = $_POST['target_company_id'];
		$g_view['input']['target_company_name'] = $g_mc->view_to_view($_POST['target_company_name']);
		$g_view['input']['target_country'] = $_POST['target_country'];
		$g_view['input']['target_sector'] = $_POST['target_sector'];
		/***
		sng:21/may/2010
		*******/
		$g_view['input']['note'] = $g_mc->view_to_view($_POST['note']);
		/****
		sng:8/jul/2010
		********/
		$g_view['input']['sources'] = $g_mc->view_to_view($_POST['sources']);
		/***
		sng:9/aug/2010
		***/
		$g_view['input']['base_fee'] =$_POST['base_fee'];
		$g_view['input']['incentive_fee'] =$_POST['incentive_fee'];
		$g_view['input']['current_rating'] =$_POST['current_rating'];
		
		$g_view['input']['ev_ebitda_ltm'] =$_POST['ev_ebitda_ltm'];
		$g_view['input']['ev_ebitda_1yr'] =$_POST['ev_ebitda_1yr'];
		$g_view['input']['30_days_premia'] =$_POST['30_days_premia'];
		
		$g_view['input']['1_day_price_change'] =$_POST['1_day_price_change'];
		$g_view['input']['discount_to_last'] =$_POST['discount_to_last'];
		$g_view['input']['discount_to_terp'] =$_POST['discount_to_terp'];
		
		$g_view['input']['seller_company_name'] = $g_mc->view_to_view($_POST['seller_company_name']);
		$g_view['input']['seller_country'] = $_POST['seller_country'];
		$g_view['input']['seller_sector'] = $_POST['seller_sector'];
		
	}
}
/////////////////////////////////////////////////////////////
//fetch company names (of type company only)
/***
sng:8/may/2010
We now allow admin to type company name and hint appears. Selecting a name put the id
so we no longer need to fetch the long list
******/
///////////////////////////////////////////////////////////

//fetch headquarter_country names
$g_view['country_list'] = array();
$g_view['country_count'] = 0;
$success = $g_country->get_all_country_list($g_view['country_list'],$g_view['country_count']);
if(!$success){
	die("Cannot get country list");
}
///////////////////////////////////////////////////////////
//fetch Category names
$g_view['cat_list'] = array();
$g_view['cat_count'] = 0;
//$success = $g_trans->get_all_category_list($g_view['cat_list'],$g_view['cat_count']);
$success = $g_trans->get_all_category_type("type",$g_view['cat_list'],$g_view['cat_count']);
if(!$success){
	die("Cannot get category list");
}
///////////////////////////////////////////////////////////
//fetch Sub_Category1 names
$g_view['subcat1_list'] = array();
$g_view['subcat1_count'] = 0;
//$success = $g_trans->get_all_subcategory1($g_view['subcat1_list'],$g_view['subcat1_count']);
$success = $g_trans->get_all_category_type("subtype1",$g_view['subcat1_list'],$g_view['subcat1_count']);
if(!$success){
	die("Cannot get subcategory1 list");
}

///////////////////////////////////////////////////////////
//fetch Sub_Category2 names
$g_view['subcat2_list'] = array();
$g_view['subcat2_count'] = 0;
//$success = $g_trans->get_all_subcategory2($g_view['subcat2_list'],$g_view['subcat2_count']);
$success = $g_trans->get_all_category_type("subtype2",$g_view['subcat2_list'],$g_view['subcat2_count']);
if(!$success){
	die("Cannot get subcategory2 list");
}
//////////////////////////////////////////
//fetch industry list
$g_view['industry_list'] = array();
$g_view['industry_count'] = 0;
$success = $g_company->get_all_industry_list($g_view['industry_list'],$g_view['industry_count']);
if(!$success){
	die("Cannot get industry list");
}
////////////////////////////////////////////////////////
/***
sng:12/may/2010
for m and a deals, there will be target sector
sng:22/May
We now take target sector from target_sector field
***/
//fetch sector list
$g_view['sector_list'] = array();
$g_view['sector_count'] = 0;
$success = $g_company->get_all_sector_list($g_view['sector_list'],$g_view['sector_count']);
if(!$success){
	die("Cannot get sector list");
}
///////////////////////////////////////////////////////////////////
$g_view['heading'] = "Add Transaction";
$g_view['content_view'] = "admin/deal_add_view.php";
include("admin/content_view.php");
?>