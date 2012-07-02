<?php
/****
even though this is called in ajax, we need to check if the caller has logged in or not
return data html, but right now nothing
*******/
include("../include/global.php");
require_once("classes/class.transaction_discussion.php");

$g_view['deal_id'] = $_POST['transaction_id'];
$g_view['flag_discussion'] = false;

$success = $g_deal_disc->can_see($g_view['deal_id'],$g_view['flag_discussion']);
if(!$success){
	echo "Error";
	exit;
}
if(!$g_view['flag_discussion']){
	echo "You do not have priviledge to flag the discussion for this deal";
	exit;
}
$success = $g_deal_disc->flag_comment($_POST['posting_id']);
if(!$success){
	echo "Error";
	exit;
}
echo "flagged";
?>