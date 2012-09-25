<?php
/************************
25/sep/2012
This allow a member to add other members from his/her bank to this deal
************/
include("../include/global.php");
require_once("classes/class.account.php");
require_once("classes/class.transaction_member.php");

if(!$g_account->is_site_member_logged()){
	echo "You need to login first";
	return;
}
$trans_mem = new transaction_member();
/***********************************************/
$this_member = $_POST['member_id'];
$deal_id = $_POST['deal_id'];
$partner_id = $_POST['partner_id'];

$mem_added = false;
$msg = "";

$result = array();
$result['mem_added'] = 0;
$result['msg'] = 0;

$success = $trans_mem->add_deal_partner_team_member($deal_id,$partner_id,$this_member,$mem_added,$msg);
if(!$success){
	$result['mem_added'] = 0;
	$result['msg'] = "Cannot add to deal team";
	echo json_encode($result);
	exit;
}
if($mem_added){
	$result['mem_added'] = 1;
	$result['msg'] = "Added to the deal team";
	echo json_encode($result);
	exit;
}else{
	$result['mem_added'] = 0;
	$result['msg'] = $msg;
	echo json_encode($result);
	exit;
}
exit;
?>