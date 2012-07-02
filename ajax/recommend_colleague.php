<?php
/****
even though this is called in ajax, we need to check if the caller has logged in or not
*******/
include("../include/global.php");
require_once("classes/class.account.php");
require_once("classes/class.member.php");
///////////////
if(!$g_account->is_site_member_logged()){
	echo "You need to login first";
	return;
}
//////////////////////
$this_member = $_SESSION['mem_id'];
$colleague_id = $_POST['colleague_id'];
$msg = "";
$success = $g_mem->front_recommend_colleague($this_member,$colleague_id,$msg);
if(!$success){
	echo "Cound not add colleague";
	return;
}
//////////
//no error
echo $msg;
return;
?>