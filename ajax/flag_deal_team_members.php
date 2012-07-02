<?php
include("../include/global.php");
require_once("classes/class.account.php");
require_once("classes/class.transaction.php");
///////////////////////////////////
//since this is ajax, we cannot send anywhere
if(!$g_account->is_site_member_logged()){
	echo "you have to login";
	return;
}
////////////////////////////////
//get the data
$deal_id = $_POST['deal_id'];
$partner_id = $_POST['partner_id'];
$flagged_mems_csv = $_POST['flag_members'];
$flagged_by = $_SESSION['mem_id'];
/////////////////////////////
if($deal_id == ""){
	echo "deal unspecified";
	return;
}
if($partner_id == ""){
	echo "bank/law firm for the deal unspecified";
	return;
}
if($flagged_mems_csv == ""){
	echo "members to flag unspecified";
	return;
}
////////////////////////////
//flag
$msg = "";
$success = $g_trans->flag_deal_partner_team_members($deal_id,$partner_id,$flagged_mems_csv,$flagged_by,$msg);
if(!$success){
	$msg = "Could not flag";
}
echo $msg;
return;
?>