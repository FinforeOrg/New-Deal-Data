<?php
include("../include/global.php");
require_once ("admin/checklogin.php");
require_once("classes/class.company.php");
///////////////////////////////////////////////////////

$g_view['msg'] = "";

///////////////////////////////////////
if(isset($_POST['action'])&&($_POST['action']=="delete")){
	$success = $g_company->delete_company($_POST['company_id'],"../uploaded_img",$g_view['msg']);
	if(!$success){
		die("Cannot delete company");
	}
}
///////////////////////////////////////////////////////////

$g_view['data_count'] = 0;
$g_view['data'] = array();
$g_view['num_to_show'] = 50;
$g_view['start'] = 0;
if(isset($_REQUEST['start'])&&($_REQUEST['start']!="")){
	$g_view['start'] = $_REQUEST['start'];
}
///////////////

/////////////////
$success = $g_company->get_all_firm_without_deal_list_paged("bank",$g_view['start'],$g_view['num_to_show']+1,$g_view['data'],$g_view['data_count']);
if(!$success){
	die("Cannot get data");
}
////////////////////////////////////////////////
$g_view['heading'] = "List of Banks without deals";
$g_view['content_view'] = "admin/list_banks_without_deals_view.php";
include("admin/content_view.php");
?>