<?php
/****
sng:25/mar/2012
even though this is called in ajax, we need to check if the caller has logged in or not
*******/
require_once("../../include/global.php");
require_once("classes/class.account.php");
require_once("classes/class.transaction_suggestion.php");
$trans_suggestion = new transaction_suggestion();
///////////////
if(!$g_account->is_site_member_logged()){
	echo "You need to login first";
	return;
}
//////////////////////
$this_member = $_SESSION['mem_id'];
$deal_id = $_POST['deal_id'];


$msg = "";
$success = $trans_suggestion->front_submit_deal_data($deal_id,$this_member,$_POST,$msg);
if(!$success){
	
	echo "Cound not send the deal data";
	return;
}
////////////////////////////
//no error
echo $msg;
return;
?>