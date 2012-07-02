<?php
/************************
13/jan/2011
Now a member can search for deals done by his/her firm. In that case
the member can add self to the deal
************/
include("../include/global.php");
require_once("classes/class.account.php");
require_once("classes/class.transaction.php");

if(!$g_account->is_site_member_logged()){
	echo "You need to login first";
	return;
}
/***********************************************/
$this_member = $_SESSION['mem_id'];
$deal_id = $_POST['deal_id'];
$partner_id = $_POST['partner_id'];
$mem_added = false;
$msg = "";
$success = $g_trans->add_deal_partner_team_member($deal_id,$partner_id,$this_member,$mem_added,$msg);
if(!$success){
	die("Cannot add to deal team");
}
if($mem_added){
	echo "You have been added to the deal";
}else{
	echo $msg;
}
exit;
?>