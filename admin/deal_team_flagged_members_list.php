<?php
include("../include/global.php");
require_once ("admin/checklogin.php");
require_once("classes/class.transaction.php");
///////////////////////////////////////////////////////
$g_view['msg'] = "";
//////////////////////////////////////
if(isset($_POST['action'])&&($_POST['action']=="unflag")){
	$success = $g_trans->unflag_deal_partner_team_members($_POST['deal_id'],$_POST['partner_id'],$_POST['member_id']);
	if(!$success){
		die("Cannot unflag the disputed member");
	}
}
///////////////////////////////////////////////////////////
$mem_removed = false;
if(isset($_POST['action'])&&($_POST['action']=="remove")){
	$success = $g_trans->remove_deal_partner_team_member($_POST['deal_id'],$_POST['partner_id'],$_POST['member_id'],$mem_removed,$g_view['msg']);
	if(!$success){
		die("Cannot remove the disputed member from the deal");
	}
}
/////////////////////////////////////////////
//get the list of disputed members
$g_view['data_count'] = 0;
$g_view['data'] = array();
$g_view['num_to_show'] = 10;
$g_view['start'] = 0;
if(isset($_REQUEST['start'])&&($_REQUEST['start']!="")){
	$g_view['start'] = $_REQUEST['start'];
}
/////////////////
$success = $g_trans->get_disputed_deal_team_members_paged($g_view['start'],$g_view['num_to_show']+1,$g_view['data'],$g_view['data_count']);
if(!$success){
	die("Cannot disputed member data");
}
////////////////////////////////////////////////
$g_view['heading'] = "List of Flagged Deal Team Members";
$g_view['content_view'] = "admin/deal_team_flagged_members_list_view.php";
include("admin/content_view.php");
?>