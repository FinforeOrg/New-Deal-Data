<?php
require_once("include/global.php");
require_once("classes/class.magic_quote.php");
require_once("nifty_functions.php");
require_once("classes/class.transaction.php");
/////////////////////////////////////////
//by default, this is included inside register.php, so get the post and $g_view data
//get the firm id from name
$company_id = 0;
$found = false;
$deal_count = 0;
$deal_data = array();
////////////////////////
if($_POST['type']=="banker") $company_type = "bank";
if($_POST['type']=="lawyer") $company_type = "law firm";
if($_POST['type']=="company rep") $company_type = "company";
/***************************************
sng:5/apr/2011
we add a 4rth role, but data partners are associated with company
********************/
if($_POST['type']=="data partner") $company_type = "company";
$success = company_id_from_name($g_mc->view_to_db($_POST['firm_name']),$company_type,$company_id,$found);
if(!$success){
	die("Cannot get company id from name");
}
if(!$found){
	$msg = "Company not found";
}else{
	//get the deals of this company
	$success = $g_trans->suggest_deal_during_registration($company_id,$_POST['deal_cat_name'],$deal_data,$deal_count);
	if(!$success){
		die("Cannot get deal suggestions");
	}
}
//////////////////////////////////////
require_once("default_metatags.php");
/////////////////////////////////
$g_view['page_heading'] = "Member Registration Add Deals";
////////////////////////////////////////////////////
$g_view['content_view'] = "register_show_deal_view.php";
require("content_view.php");
?>