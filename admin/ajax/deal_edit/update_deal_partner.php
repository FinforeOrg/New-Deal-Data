<?php
/******************
sng:11/oct/2012
****************/
require_once("../../../include/global.php");
require_once("classes/class.account.php");

$json = array();
$json['msg'] = "";
$json['updated'] = 'n';

if(!$g_account->is_admin_logged()){
	$json['updated'] = 'n';
	$json['msg'] = "You need to login first";
	echo json_encode($json);
	exit;
}
require_once("classes/class.transaction_partner.php");
$trans_partner = new transaction_partner();

$updated = false;
$ok = $trans_partner->admin_update_deal_partner_role($_POST['record_id'],$_POST['role_id'],$updated,$json['msg']);
if(!$ok){
	$json['updated'] = 'n';
	$json['msg'] = "Could not update";
	echo json_encode($json);
	exit;
}
if(!$updated){
	//the function has already put the note regarding what was the problem (usually validation failed)
	$json['updated'] = 'n';
	echo json_encode($json);
	exit;
}
//updated
$json['updated'] = 'y';
$json['msg'] = "Updated";
echo json_encode($json);
exit;
?>