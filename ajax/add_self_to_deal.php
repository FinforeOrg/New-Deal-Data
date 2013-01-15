<?php
/************************
13/jan/2011
Now a member can search for deals done by his/her firm. In that case
the member can add self to the deal

sng:19/sep/2012
We have moved the method to transaction_member so we update the code here

sng:25/sep/2012
We return result in json
************/
include("../include/global.php");
require_once("classes/class.account.php");
require_once("classes/class.transaction_member.php");

/************************
sng:15/jan/2013
Since we are expecting json, we cannot use simple echo
*******************/
$result = array();
$result['mem_added'] = 0;
$result['msg'] = 0;

if(!$g_account->is_site_member_logged()){
	$result['mem_added'] = 0;
	$result['msg'] = "You need to login first";
	echo json_encode($result);
	exit;
}
$trans_mem = new transaction_member();
/***********************************************/
$this_member = $_SESSION['mem_id'];
$deal_id = $_POST['deal_id'];
$partner_id = $_POST['partner_id'];
$mem_added = false;
$msg = "";



$success = $trans_mem->add_deal_partner_team_member($deal_id,$partner_id,$this_member,$mem_added,$msg);
if(!$success){
	$result['mem_added'] = 0;
	$result['msg'] = "Cannot add to deal team";
	echo json_encode($result);
	exit;
}
if($mem_added){
	$result['mem_added'] = 1;
	$result['msg'] = "You have been added to the deal";
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