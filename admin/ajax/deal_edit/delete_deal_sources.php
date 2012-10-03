<?php
/*****************
sng:3/oct/2012
****************/
require_once("../../../include/global.php");
require_once("classes/class.account.php");

$json = array();
$json['msg'] = "";
$json['deleted'] = 'n';

if(!$g_account->is_admin_logged()){
	$json['deleted'] = 'n';
	$json['msg'] = "You need to login first";
	echo json_encode($json);
	exit;
}
require_once("classes/class.transaction_source.php");
$trans_source = new transaction_source();
$ok = $trans_source->admin_delete_deal_source($_POST['id']);
if($ok){
	$json['deleted'] = 'y';
	$json['msg'] = "Deleted";
}else{
	$json['deleted'] = 'n';
	$json['msg'] = "Could not delete";
}
echo json_encode($json);
exit;
?>