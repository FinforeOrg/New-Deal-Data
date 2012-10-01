<?php
/************
sng:1/oct/2012
For now, we do not allow admin to add deal. In future, even if we allow, it will be the detailed template of front end
*****/
die("DO NOT USE");
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

///////////////////////////////////////
if(isset($_POST['action'])&&($_POST['action']=="add")){
	
	$validation_passed = false;
	/***
	sng:31/aug/2010
	When we add a deal, we also want to add the banks and law firms. We already have the popups. We
	just need the newly added transaction id
	*************/
	$new_transaction_id = 0;
	$success = $g_trans->add_deal_simple($_POST,$validation_passed,$new_transaction_id,$g_view['err']);
	if(!$success){
		die("Cannot add deal");
	}
	if($validation_passed){
		$g_view['msg'] = "Deal added";
		$g_view['heading'] = "Add Transaction";
		$g_view['content_view'] = "admin/deal_add_step_edit_deal_view.php";
		include("admin/content_view.php");
		exit;
		/***************************************************/
	}else{
		//the form is to be shown with data just entered
		$g_view['input']['company_id'] = $_POST['company_id'];
		$g_view['input']['deal_company_name'] = $g_mc->view_to_view($_POST['deal_company_name']);
		$g_view['input']['date_rumour'] = $_POST['date_rumour'];
		$g_view['input']['date_announced'] = $_POST['date_announced'];
		$g_view['input']['date_closed'] = $_POST['date_closed'];
		
		$g_view['input']['deal_cat_name'] = $_POST['deal_cat_name'];
		$g_view['input']['deal_subcat1_name'] = $_POST['deal_subcat1_name'];
		$g_view['input']['deal_subcat2_name'] = $_POST['deal_subcat2_name'];
		
		/****************
		sng:14/sep/2011
		We need to have the checkbox 'Notify participants' so that when the banks / law firms for this deal is
		added, the relevant parties are emailed.
		
		If this deal is being added based on suggestion sent by member and that member wishes to notify others,
		admin will check this checkbox
		***********************/
		$g_view['input']['email_participants'] = $_POST['email_participants'];
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
///////////////////////////////////////////////////////////////////
$g_view['heading'] = "Add Transaction";
$g_view['content_view'] = "admin/simple_deal_add_view.php";
include("admin/content_view.php");
?>