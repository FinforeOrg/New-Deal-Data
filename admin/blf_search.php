<?php
include("../include/global.php");
require_once ("admin/checklogin.php");
require_once("classes/class.company.php");
require_once("classes/class.magic_quote.php");
///////////////////////////////////////////////////////
$g_view['msg'] = "";
////////////////////////////////////////////
/****
sng:15/may/2010
We may have to delete a bank/law firm
But since this is a search, the search params are also sent. so after the delete
we do the search and show result
Note: the function, delete_company checks whether there is member associated with the company etc
********/
if(isset($_POST['action'])&&($_POST['action']=="blf_delete")){
	/////////////////////////////////////////////////////////////////
	/********************
	sng:2/dec/2011
	we now use a constant to specify the logo path instead of image root
	*********************/
	$success = $g_company->delete_company($_POST['company_id'],LOGO_PATH,$g_view['msg']);
	if(!$success){
		die("Cannot delete bank / law firm");
	}
}
///////////////////////////////////////
if(isset($_POST['action'])&&(($_POST['action']=="search_company")||($_POST['action']=="blf_delete"))){
	$g_view['data_count'] = 0;
	$g_view['data'] = array();
	$success = $g_company->admin_search_for_company($_POST,$g_view['data'],$g_view['data_count']);
	if(!$success){
		die("Cannot get bank / law firm data");
	}
}
////////////////////////////////////////////////////////
$g_view['heading'] = "Search for Bank / Law Firm";
$g_view['content_view'] = "admin/blf_search_view.php";
include("admin/content_view.php");
?>