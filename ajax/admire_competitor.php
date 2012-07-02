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
$competitor_id = $_POST['competitor_id'];
$msg = "";
$success = $g_mem->front_admire_competitor($this_member,$competitor_id,$msg);
if(!$success){
	echo "Cound not add competitor";
	return;
}
//////////
//no error
echo $msg;
return;
?>