<?php
/****
even though this is called in ajax, we need to check if the caller has logged in or not
The return data is in json
*******/
require_once("../include/global.php");
require_once("classes/class.account.php");
require_once("classes/db.php");

$json = array();
$json['error'] = "";
$json['posted'] = 'n';

if(!$g_account->is_site_member_logged()){
	$json['posted'] = 'n';
	$json['error'] = "You need to login to post correction";
	echo json_encode($json);
	exit;
}
/*****************
We store this in tombstone_transaction_suggestions and specify the deal id. That separates it from new deal suggestions
***************/
$suggestion_mem_id = $_SESSION['mem_id'];
$suggestion_id = $suggestion_mem_id."-".time();
$suggestion_date = date("Y-m-d H:i:s");
$deal_id = $_POST['deal_id'];

$suggestion_ins_q = "";
//we create the sql statements in the include files

$deal_cat_name = $_POST['deal_cat_name'];
		
if($deal_cat_name == "M&A"){
	include("post_deal_correction_ma.php");
}elseif($deal_cat_name == "Debt"){
	include("post_deal_correction_debt.php");
}elseif((strtolower($_POST['deal_cat_name'])=="equity")&&(strtolower($_POST['deal_subcat1_name'])=="convertible")){
	include("post_deal_correction_eq_convertible.php");
}elseif((strtolower($_POST['deal_cat_name'])=="equity")&&(strtolower($_POST['deal_subcat1_name'])=="preferred")){
	include("post_deal_correction_eq_preferred.php");
}elseif((strtolower($_POST['deal_cat_name'])=="equity")&&(strtolower($_POST['deal_subcat1_name'])=="equity")&&(strtolower($_POST['deal_subcat2_name'])=="additional")){
	include("post_deal_correction_eq_additional.php");
}elseif((strtolower($_POST['deal_cat_name'])=="equity")&&(strtolower($_POST['deal_subcat1_name'])=="equity")&&(strtolower($_POST['deal_subcat2_name'])=="ipo")){
	include("post_deal_correction_eq_ipo.php");
}elseif((strtolower($_POST['deal_cat_name'])=="equity")&&(strtolower($_POST['deal_subcat1_name'])=="equity")&&(strtolower($_POST['deal_subcat2_name'])=="rights issue")){
	include("post_deal_correction_eq_rights.php");
}
//run query
$db = new db();
$success = $db->mod_query($suggestion_ins_q);
if(!$success){
	$json['posted'] = 'n';
	$json['error'] = "Error in database";
	echo json_encode($json);
	exit;
}
/******************************
sng:7/mar/2012
If a member posts correction on a deal, it is interpreted as "you are saying the deal, subject to your edits, is correct"
*****************************/
require_once("classes/class.transaction_verification.php");
$trans_verify = new transaction_verification();
$g_view['msg'] = "";
$ok = $trans_verify->verification_by_member($deal_id,$_SESSION['mem_id'],$g_view['msg']);
/******************************************
If error, well it is not a big deal
It may also happen that the member is sending edit twice. No problem. The code
checks if the member has already confirmed the deal or not. If so, no new record is
inserted and an error message is returned which we ignore
******************/

$json['posted'] = 'y';
$json['error'] = "";
echo json_encode($json);
exit;
?>