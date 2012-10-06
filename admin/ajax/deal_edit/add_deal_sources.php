<?php
/*****************
sng:6/oct/2012
********/
require_once("../../../include/global.php");
require_once("classes/class.account.php");

$json = array();
$json['msg'] = "";
$json['added'] = 'n';

if(!$g_account->is_admin_logged()){
	$json['added'] = 'n';
	$json['msg'] = "You need to login first";
	echo json_encode($json);
	exit;
}
require_once("classes/class.transaction_source.php");
$trans_source = new transaction_source();

$msg = "";
$validation_passed = false;

$ok = $trans_source->admin_add_sources_for_deal($_POST['deal_id'],$_POST['regulatory_links'],$validation_passed,$msg);
if(!$ok){
	$json['added'] = 'n';
	$json['msg'] = "Could not send the sources";
	echo json_encode($json);
	exit;
}
if(!$validation_passed){
	$json['added'] = 'n';
	$json['msg'] = $msg;
	echo json_encode($json);
	exit;
}
$json['added'] = 'y';
$json['msg'] = "Added the sources";
echo json_encode($json);
exit;
?>