<?php
require_once("../include/global.php");
require_once ("admin/checklogin.php");

require_once("classes/class.transaction.php");
require_once("classes/class.deal_support.php");

$deal_support = new deal_support();

$g_view['deal_id'] = $_POST['deal_id'];
/***********************************************
get the min data for heading
************************/
$g_view['deal_found'] = false;
$g_view['min_deal_data'] = NULL;
$success = $g_trans->admin_get_min_deal_detail_for_edit_heading($g_view['deal_id'],$g_view['min_deal_data'],$g_view['deal_found']);
if(!$success){
	die("Cannot get the deal data");
}
/******************************/
$g_view['heading'] = "Edit Transaction";
$g_view['content_view'] = "admin/deal_edit_detailed_view.php";
include("admin/content_view.php");
?>