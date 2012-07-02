<?php
include("../include/global.php");
require_once ("admin/checklogin.php");
require_once("classes/class.transaction.php");
///////////////////////////////////////////////////////
$g_view['member_id'] = $_GET['mem_id'];
$g_view['msg'] = "";
//////////////////////////////////////
$mem_removed = false;
if(isset($_POST['action'])&&($_POST['action']=="remove_from_deal")){
	$success = $g_trans->remove_deal_partner_team_member($_POST['deal_id'],$_POST['partner_id'],$g_view['member_id'],$mem_removed,$g_view['msg']);
	if(!$success){
		die("Cannot remove the member from the deal");
	}
	if($mem_removed){
		$g_view['msg'] = "The member has been removed from the deal";
	}else{
		$g_view['msg'] = "Could not remove the member from the deal";
	}
}

/////////////////////////////////////////////
//get the deals of this member
$g_view['deal_data'] = array();
$g_view['deal_count'] = 0;
if(!isset($_GET['start'])||($_GET['start']=="")){
	$g_view['start_offset'] = 0;
}else{
	$g_view['start_offset'] = $_GET['start'];
}
$g_view['num_to_show'] = 20;

$success = $g_trans->admin_get_deals_of_member_paged($g_view['member_id'],$g_view['num_to_show']+1,$g_view['start_offset'],$g_view['deal_data'],$g_view['deal_count']);
if(!$success){
	die("Cannot fetch deal data");
}
////////////////////////////////////////////////
$g_view['heading'] = "Deals of this member";
$g_view['content_view'] = "admin/ghost_member_deals_view.php";
include("admin/content_view.php");
?>