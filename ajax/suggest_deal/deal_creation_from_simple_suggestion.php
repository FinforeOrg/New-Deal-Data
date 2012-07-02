<?php
/******************
sng:20/feb/2012
*********************/
require_once("classes/class.transaction.php");
$suggestion_mem_id = $_SESSION['mem_id'];
$deal_created = false;
$msg = "";
/***************
the date is set as 15/Feb/2012 format and the code expect it as yyyy-mm-dd, so we change it
***************/
$_POST['deal_date'] = fotmat_date_for_suggestion($_POST['deal_date']);
$ok = $g_trans->front_create_deal_from_simple_suggestion($suggestion_mem_id,$_POST,$activate_deal,$deal_created,$msg);
if(!$ok){
	$result['status'] = 0;
	$result['msg'] = "Internal error";
	echo json_encode($result);
	exit;
}
if(!$deal_created){
	$result['status'] = 0;
	$result['msg'] = $msg;
	echo json_encode($result);
	exit;
}
$result['status'] = 1;
$result['msg'] = $msg;
echo json_encode($result);
exit;
?>