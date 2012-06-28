<?php
/************************
sng:6/mar/2012

In the deal detail page, a member can click [confirm detail] button to confirm that
all the details are ok. We do not use ajax to handle it since ajax does not handle the login and data sending nicely.

we use page redirect technique.
******************/
require_once("include/global.php");
require_once("classes/class.account.php");

$g_view['deal_id'] = $_GET['deal_id'];
$_SESSION['after_login'] = "confirm_deal_by_member.php?deal_id=".$g_view['deal_id'];
require_once("check_mem_login.php");

require_once("classes/class.transaction_verification.php");
$trans_verify = new transaction_verification();

$g_view['msg'] = "";
$ok = $trans_verify->verification_by_member($g_view['deal_id'],$_SESSION['mem_id'],$g_view['msg']);
if(!$ok){
	$g_view['msg'] = "Error confirming deal";
}
/************
since we will do a redirect, let us put the message in flash
************/
create_flash("confirm_deal_msg",$g_view['msg']);
header("Location: deal_detail.php?deal_id=".$g_view['deal_id']);
exit;
?>