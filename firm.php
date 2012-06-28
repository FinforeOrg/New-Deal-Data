<?php
/***************************
sng:8/dec/2011
**********/
require_once("include/global.php");
require_once("classes/class.company.php");
require_once("classes/class.account.php");

$g_view['company_id'] = $_GET['id'];
/*******************
get the firm detail
*********/
$g_view['data'] = NULL;
$success = $g_company->get_company($g_view['company_id'],$g_view['data']);
if(!$success){
	die("Cannot get firm data");
}

if($g_view['data']['type']=="bank"){
	$g_view['page_heading'] = "Bank Detail";
}elseif($g_view['data']['type']=="law firm"){
	$g_view['page_heading'] = "Law Firm Detail";
}else{
	die("Not a bank/law firm");
}

if(!isset($_POST['top_search_area'])){
	if($g_view['data']['type']=="bank"){
		$_POST['top_search_area'] = "bank";
	}else{
		$_POST['top_search_area'] = "law_firm";
	}
}

require_once("default_metatags.php");
$g_view['content_view'] = "firm_view.php";
require("content_view.php");
?>