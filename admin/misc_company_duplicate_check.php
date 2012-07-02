<?php
include("../include/global.php");
require_once ("admin/checklogin.php");
require_once("classes/class.company.php");
require_once("classes/class.misc.php");
///////////////////////////////////////////////////////

$g_view['msg'] = "";

///////////////////////////////////////
//handle action
if(isset($_POST['action'])&&($_POST['action']=="del_company")){
	$company_id = $_POST['company_id'];
	$success = $g_company->delete_company($company_id,"../uploaded_img",$g_view['msg']);
	if(!$success){
		die("Cannot delete company");
	}
}
///////////////////////////////////////////////////////////
//get the list of duplicate firms
$g_view['data_count'] = 0;
$g_view['data'] = array();
$success = $g_misc->get_all_duplicate_firms($g_view['data'],$g_view['data_count']);
if(!$success){
	die("Cannot get data");
}
////////////////////////////////////////////////
$g_view['heading'] = "List of Duplicate Firms";
$g_view['content_view'] = "admin/misc_company_duplicate_check_view.php";
include("admin/content_view.php");
?>