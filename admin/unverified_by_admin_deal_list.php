<?php
/********************************************
sng:3/mar/2012
Now users can create deal directly using simple submission form.
For privileged members, the deal is active but it is marked as unverified by admin.

At some point of time, admin has to check the deal to see if everything is ok or not.
This is a minimal requirement.
*************************************************/
require_once("../include/global.php");
require_once ("admin/checklogin.php");
require_once("classes/class.transaction_verification.php");
$g_trans_verify = new transaction_verification();
///////////////////////////////////////////////////////
$g_view['msg'] = "";
//////////////////////////////////////
if(isset($_POST['my_action'])&&($_POST['my_action']=="delete")){
	$success = $g_trans->delete_transaction($_POST['deal_id']);
	if(!$success){
		die("Cannot delete the transaction");
	}
}

/////////////////////////////////////////////
//get the list inactive deals.
$g_view['data_count'] = 0;
$g_view['data'] = array();
$g_view['num_to_show'] = 50;
$g_view['start'] = 0;
if(isset($_REQUEST['start'])&&($_REQUEST['start']!="")){
	$g_view['start'] = $_REQUEST['start'];
}
/////////////////
$success = $g_trans_verify->admin_get_admin_unverified_deals_paged($g_view['start'],$g_view['num_to_show']+1,$g_view['data'],$g_view['data_count']);
if(!$success){
	die("Cannot get unverified deal list");
}
////////////////////////////////////////////////
$g_view['heading'] = "List of Transactions Unverified by Admin";
$g_view['content_view'] = "admin/unverified_by_admin_deal_list_view.php";
include("admin/content_view.php");
?>