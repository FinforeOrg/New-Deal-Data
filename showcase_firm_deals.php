<?php
/*****
This is used to show case a bank or a law firm.
This shows 24 latest deals in which this bank/lawfirm was involved
********/
include("include/global.php");
require_once("classes/class.company.php");
require_once("classes/class.transaction.php");
require_once("nifty_functions.php");
//////////////////////////////////////
$g_view['firm_id'] = $_REQUEST['id'];
//get the firm data
$g_view['company_data'] = array();
$success = $g_company->get_company($g_view['firm_id'],$g_view['company_data']);
if(!$success){
	die("Cannot get company data");
}
////////////////////////////////////////////
//get the latest deals
$g_view['deal_data'] = array();
$g_view['deal_count'] = 0;
$success = $g_trans->front_get_recent_deals_of_firm($g_view['firm_id'],24,$g_view['deal_data'],$g_view['deal_count']);
if(!$success){
	die("Cannot get deals");
}
///////////////////////////////////////
require_once("default_metatags.php");
$g_view['content_view'] = "showcase_firm_deals_view.php";
require("content_view.php");
?>