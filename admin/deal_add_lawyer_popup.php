<?php
include("../include/global.php");
require_once ("admin/checklogin.php");
require_once("classes/class.transaction.php");
//////////////////////////////////////////////////
$g_view['deal_id'] = $_REQUEST['transaction_id'];
$g_view['msg'] = "";
if(isset($_POST['myaction'])&&($_POST['myaction']=="add")){
	$partner_id = $_POST['partner_id'];
	$member_id = $_POST['member_id'];
	$mem_added = false;
	$success = $g_trans->admin_add_deal_partner_team_member($g_view['deal_id'],$partner_id,$member_id,$mem_added,$g_view['msg']);
	if(!$success){
		die("Cannot add lawyer to deal");
	}
	if($mem_added){
		$g_view['msg'] = "Lawyer added to deal";
	}
}
//////////////////////////////////////
//get the law firms for this deal
$g_view['data_count'] = 0;
$g_view['data'] = NULL;
$success = $g_trans->get_all_partner($_REQUEST['transaction_id'],"law firm",$g_view['data'],$g_view['data_count']);
if(!$success){
	die("Cannot get partner data");
}
include("admin/deal_add_lawyer_popup_view.php");
?>