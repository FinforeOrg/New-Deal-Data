<?php
/*****
This is used to show all the deals done by  bank or law firm

sng:23/july/2010
we need account because we want to use a if clause in storing pagination helper parameter (industry
since logged in user can filter by industry and then we have to show the deals filtered by industry
********/
include("include/global.php");
require_once("classes/class.company.php");
require_once("classes/class.transaction.php");
require_once("nifty_functions.php");
require_once("classes/class.account.php");
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
//pagination support
if(!isset($_REQUEST['start'])||($_REQUEST['start']=="")){
	$g_view['start_offset'] = 0;
}else{
	$g_view['start_offset'] = $_REQUEST['start'];
}
$g_view['num_to_show'] = 10;
$g_view['deal_data'] = array();
$g_view['deal_count'] = 0;
$success = $g_trans->front_get_all_deals_of_firm_paged($g_view['firm_id'],$_POST,$g_view['start_offset'],$g_view['num_to_show']+1,$g_view['deal_data'],$g_view['deal_count']);
if(!$success){
	die("Cannot get deals");
}
///////////////////////////////////////
require_once("default_metatags.php");
$g_view['content_view'] = "firm_deals_view.php";
require("content_view.php");
?>