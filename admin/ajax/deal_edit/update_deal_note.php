<?php
/*****************
sng:5/oct/2012
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
require_once("classes/class.transaction_note.php");
$trans_note = new transaction_note();
$append_to_note = false;
if($_POST['append']=='y'){
	$append_to_note = true;
}
$ok = $trans_note->admin_update_note($_POST['deal_id'],$_POST['note'],$append_to_note);
if($ok){
	$json['updated'] = 'y';
	$json['msg'] = "Updated";
}else{
	$json['updated'] = 'n';
	$json['msg'] = "Could not update";
}
echo json_encode($json);
exit;
?>