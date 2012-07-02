<?php
/****
even though this is called in ajax, we need to check if the caller has logged in or not
*******/
include("../include/global.php");
require_once("classes/class.account.php");
require_once("classes/class.transaction.php");
require_once("include/strip_html_tags.php");
///////////////
if(!$g_account->is_site_member_logged()){
	echo "You need to login first";
	return;
}
//////////////////////
$this_member = $_SESSION['mem_id'];
$deal_id = $_POST['deal_id'];
$report_date = date("Y-m-d");
$msg = "";
$success = $g_trans->ajax_mark_deal_as_error($deal_id,$this_member,$report_date,strip_tags(strip_html_tags($_POST['error_report'])),$msg);
if(!$success){
	echo "Cound not send the report";
	return;
}
////////////////////////////
//no error
echo $msg;
return;
?>