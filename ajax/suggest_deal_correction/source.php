<?php
/****
sng:21/mar/2012
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
$validation_passed = false;
$success = $trans_suggestion->front_submit_sources($deal_id,$this_member,$_POST['regulatory_links'],$validation_passed,$msg);
if(!$success){
	echo "Cound not send the sources";
	return;
}
////////////////////////////
//no error
echo $msg;
return;
?>