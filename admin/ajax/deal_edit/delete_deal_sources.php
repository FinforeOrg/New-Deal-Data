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

$validation_passed = false;
$msg = "";
$ok = $trans_source->admin_delete_sources_for_deal($_POST['deal_id'],$_POST['source_id'],$validation_passed,$msg);
if(!$ok){
	$json['deleted'] = 'n';
	$json['msg'] = "Could not delete";
	echo json_encode($json);
	exit;
}
if(!$validation_passed){
	$json['deleted'] = 'n';
	$json['msg'] = $msg;
	echo json_encode($json);
	exit;
}
$json['deleted'] = 'y';
$json['msg'] = "Deleted";
echo json_encode($json);
exit;
?>