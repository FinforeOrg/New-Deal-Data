<?php
require_once("../../include/global.php");
require_once("classes/class.account.php");

if(!$g_account->is_admin_logged()){
	echo "Not logged as admin";
	exit;
}
require_once("classes/class.transaction_doc.php");
$trans_doc = new transaction_doc();
$g_view['msg'] = "";
$success = $trans_doc->ajax_accept_deal_suggestion_file($_POST['id'],$_POST['transaction_id'],$g_view['msg']);
if(!$success){
	echo "Error accepting file";
	exit;
}
echo $g_view['msg'];
?>