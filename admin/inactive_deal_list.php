<?php
/********************************************
sng:2/mar/2012
Now users can create deal directly using simple submission form.
For privileged members, the deal is active but for non privileged members
the deal is not active till admin set it.

Here we allow admin to see the list of inactive deals. If admin wants, admin can edit the record
and there, mark it as active
*************************************************/
require_once("../include/global.php");
require_once ("admin/checklogin.php");
require_once("classes/class.transaction.php");
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
$success = $g_trans->admin_get_inactive_deals_paged($g_view['start'],$g_view['num_to_show']+1,$g_view['data'],$g_view['data_count']);
if(!$success){
	die("Cannot get inactive deal list");
}
////////////////////////////////////////////////
$g_view['heading'] = "List of Inactive Transactions";
$g_view['content_view'] = "admin/inactive_deal_list_view.php";
include("admin/content_view.php");
?>