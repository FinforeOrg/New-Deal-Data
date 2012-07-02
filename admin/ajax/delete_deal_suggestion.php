<?php
require_once("../../include/global.php");
require_once("classes/class.account.php");

if(!$g_account->is_admin_logged()){
	echo "Not logged as admin";
	exit;
}
require_once("classes/class.transaction.php");

$success = $g_trans->ajax_delete_suggested_deal($_POST['id'],$_POST['accepted']);
if(!$success){
	echo "Error deletng suggestion";
	exit;
}
echo "Suggestion deleted";
?>