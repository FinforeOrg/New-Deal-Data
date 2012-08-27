<?php

require_once("include/global.php"); 
/************************
sng:12/nov/2011
We make this open

$_SESSION['after_login'] = "suggest_a_deal.php";
require_once("check_mem_login.php");
********************/
require_once("classes/class.account.php");
require_once("classes/class.country.php");
require_once("classes/class.company.php");
require_once("classes/class.transaction.php");
require_once("classes/class.magic_quote.php");
require_once("classes/class.deal_support.php");
$success = $g_company->get_all_sector_list($g_view['sector_list'],$g_view['sector_count']); 
$success = $g_company->get_all_industry_list($g_view['industry_list'],$g_view['industry_count']); 

require_once("classes/class.transaction_support.php");
$trans_support = new transaction_support();
$categories = $trans_support->get_category_tree();

/**************************************
sng:16/mar/2012
We need to get the roles for banks and law firms as per the deal type
By default, we show the M&A deal form, so we fetch data for M&A
***************/
$deal_support = new deal_support();
$g_view['bank_roles'] = NULL;
$g_view['bank_roles_count'] = 0;

$g_view['law_firm_roles'] = NULL;
$g_view['law_firm_roles_count'] = 0;

$success = $deal_support->front_get_deal_partner_roles("bank","M&A",$g_view['bank_roles'],$g_view['bank_roles_count']);
if(!$success){
	die("Cannot get the roles for bank");
}

$success = $deal_support->front_get_deal_partner_roles("law firm","M&A",$g_view['law_firm_roles'],$g_view['law_firm_roles_count']);
if(!$success){
	die("Cannot get the roles for law firm");
}
/***********************************************/

require_once("default_metatags.php");
$g_view['page_heading'] = "Suggest a Deal";
$g_view['content_view'] = "suggest_a_deal_view.php";
require("content_view.php");
?>
