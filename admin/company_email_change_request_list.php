<?php
include("../include/global.php");
require_once ("admin/checklogin.php");
require_once("classes/class.member.php");
require_once("classes/class.magic_quote.php");
///////////////////////////////////////////////////////
$g_view['msg'] = "";
///////////////////////////////////////////////////////////
if(isset($_POST['myaction'])&&($_POST['myaction']=="reject")){
	$success = $g_mem->admin_reject_company_email_change_request($_POST['mem_id']);
	if(!$success){
		die("Cannot reject company/work email change request");
	}
	$g_view['msg'] = "The request has been deleted";
}
if(isset($_POST['myaction'])&&($_POST['myaction']=="accept")){
	$success = $g_mem->admin_accept_company_email_change_request($_POST['mem_id']);
	if(!$success){
		die("Cannot accept company/work email change request");
	}
	$g_view['msg'] = "The request has been accepted";
}
if(isset($_POST['myaction'])&&($_POST['myaction']=="accept_and_update")){
	$is_updated = false;
	$success = $g_mem->admin_accept_and_update_company_email_change_request($_POST['mem_id'],$is_updated);
	if(!$success){
		die("Cannot accept and update company/work email change request");
	}
	if($is_updated){
		$g_view['msg'] = "The request has been accepted and member profile updated";
	}else{
		$g_view['msg'] = "The request cound not be updated";
	}
}
////////////////////////////////////////////////////////////
//get all members
$g_view['data_count'] = 0;
$g_view['data'] = array();
$g_view['num_to_show'] = 20;
$g_view['start'] = 0;
if(isset($_REQUEST['start'])&&($_REQUEST['start']!="")){
	$g_view['start'] = $_REQUEST['start'];
}
$success = $g_mem->admin_member_company_email_change_list_paged($g_view['start'],$g_view['num_to_show']+1,$g_view['data'],$g_view['data_count']);
if(!$success){
	die("Cannot get company/email change request list");
}
////////////////////////////////////////////////

////////////////////////////////////////////////////////
$g_view['heading'] = "Company / Work email Change Requests";
$g_view['content_view'] = "admin/company_email_change_request_list_view.php";
include("admin/content_view.php");
?>