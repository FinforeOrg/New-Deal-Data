<?php
/***************************
sng:17/jun/2011
This now handles the edit of deal data. We no longer use the code in transaction::edit_deal

This performs the common validation and create the query string and then, based on deal type,
include the proper file to do additional validation and build query.

Then this file calls methods to perform the edit.

This way we have more flexibility

sng:29/jan/2013
deal subtype Additional is Secondaries
deal subtype IPO is IPOs
deal subtype Equity is Common Equity
**************************************/
$validation_passed = true;
$edit_q = "";
$extra_edit_q = "";

/***********
sng:13/feb/2012
Now we do not use the company_id in the transaction table.
**************/
if($_POST['deal_cat_name'] == ""){
	$g_view['err']['deal_cat_name'] = "Please specify the deal category";
	$validation_passed = false;
}
if($_POST['deal_subcat1_name'] == ""){
	$g_view['err']['deal_subcat1_name'] = "Please specify the deal subcategory1";
	$validation_passed = false;
}
if($_POST['deal_subcat2_name'] == ""){
	$g_view['err']['deal_subcat2_name'] = "Please specify the deal subcategory2";
	$validation_passed = false;
}
if($_POST['value_in_billion'] == ""){
	/***********
	sng:24/jan/2012
	It may happen that admin does not know the exact deal value and has selected range option
	**************/
	if(!isset($_POST['value_range_id'])){
		//since this will be shown in the snippet called by ajax
		create_flash("value_in_billion","Please specify the deal value in USD");
		$validation_passed = false;
	}
}else{
	/******************
	sng:1/feb/2012
	When admin has the exact deal value, admin should also select the proper range id.
	Later we will automate it
	
	sng:2/mar/2012
	Admin has specified the deal value, so we can calculate the range id automatically
	No need to force the admin
	*************/
	//if(!isset($_POST['value_range_id'])){
		//since this will be shown in the snippet called by ajax
		//create_flash("value_in_billion","Please specify the range also");
		//$validation_passed = false;
	//}
}

/********************
before including the specific file, let us create the common part for the query.
We blindly put the values. However it may happen that one ot two mandatory values may be missing.
No problem. before we run the query, we check whether validation passed or not.

sng:9/sep/2011
We now need to keep track of when the deal is added. It will be useful for
writing queries like 'what has changed since ...'

sng:13/feb/2012
We no longer need company_id for transaction. We now have list of participants
************************/

/******************
if deal_value is not given, we check for value range id. If that is not given
treat it as 0, treat value as 0 and if given, store it with deal value of 0

Special case: if deal_value is 0 then we consider value_range_id = 0

if deal_value (in million) is given, we convert it to billion and store it
we also ignore value_range_id and calculate the range id from value
**********************/
if($_POST['value_in_billion']==""){
	$value_in_billion=0.0;
	
	if(isset($_POST['value_range_id'])){
		$value_range_id = $_POST['value_range_id'];
	}else{
		//no deal value, no value range, treat all as 0
		$value_range_id = 0;
	}
}elseif($_POST['value_in_billion']<=0.0){
	$value_in_billion = 0.0;
	//treat the deal as undisclosed
	$value_range_id = 0;
}else{
	//deal value given and it is not 0, it is already in billion
	//ignore what is given as range id and calculate from value
	$value_in_billion = $_POST['value_in_billion'];
	
	require_once("classes/class.deal_support.php");
	$deal_support = new deal_support();
	
	$value_range_id = 0;
	$ok = $deal_support->front_get_value_range_id_from_value($value_in_billion*1000,$value_range_id);
	if(!$ok){
		return false;
	}
}

$edit_q = "update ".TP."transaction set
deal_country='".mysql_real_escape_string($_POST['deal_country'])."'
,deal_sector='".mysql_real_escape_string($_POST['deal_sector'])."'
,deal_industry='".mysql_real_escape_string($_POST['deal_industry'])."'
,deal_cat_name='".mysql_real_escape_string($_POST['deal_cat_name'])."'
,deal_subcat1_name='".mysql_real_escape_string($_POST['deal_subcat1_name'])."'
,deal_subcat2_name='".mysql_real_escape_string($_POST['deal_subcat2_name'])."'
,currency='".mysql_real_escape_string($_POST['currency'])."'
,exchange_rate='".$_POST['exchange_rate']."'
,value_in_billion_local_currency='".$_POST['value_in_billion_local_currency']."'
,value_in_billion='".$value_in_billion."'
,value_range_id='".$value_range_id."'
,last_edited='".date("Y-m-d H:i:s")."'";
//email participants is a checkbox
if(isset($_POST['email_participants'])&&$_POST['email_participants']=='y'){
	$email_participants = 'y';
}else{
	$email_participants = 'n';
}
$edit_q.=",email_participating_syndicates='".$email_participants."'";

/***********************
sng:2/mar/2012
Now deals can be created by members. If it is created by non privileged member
the deal record is inactive till admin marks it as active.

Also, at some point of time, admin needs to check the deal data. If admin has checked everything
then admin mark it as admin verified
****************************/
if(isset($_POST['admin_verified'])&&$_POST['admin_verified']=='y'){
	$admin_verified = 'y';
}else{
	$admin_verified = 'n';
}
$edit_q.=",admin_verified='".$admin_verified."'";

if(isset($_POST['is_active'])&&$_POST['is_active']=='y'){
	$is_active = 'y';
}else{
	$is_active = 'n';
}
$edit_q.=",is_active='".$is_active."'";



/*******************************************************************
include deal specific snippet, populate $edit_q and $extra_edit_q
write as var.=",fieldname='postvars'"
**/
if(strtolower($_POST['deal_cat_name'])=="m&a") require("edit_deal_data_ma.php");
elseif((strtolower($_POST['deal_cat_name'])=="debt")&&(strtolower($_POST['deal_subcat1_name'])=="loan")) require("edit_deal_data_loan.php");
elseif((strtolower($_POST['deal_cat_name'])=="debt")&&(strtolower($_POST['deal_subcat1_name'])=="bond")) require("edit_deal_data_bond.php");
elseif((strtolower($_POST['deal_cat_name'])=="equity")&&(strtolower($_POST['deal_subcat1_name'])=="convertible")) require("edit_deal_data_convertible.php");
elseif((strtolower($_POST['deal_cat_name'])=="equity")&&(strtolower($_POST['deal_subcat1_name'])=="preferred")) require("edit_deal_data_preferred.php");
elseif((strtolower($_POST['deal_cat_name'])=="equity")&&(strtolower($_POST['deal_subcat1_name'])=="common equity")&&(strtolower($_POST['deal_subcat2_name'])=="secondaries")) require("edit_deal_data_eq_additional.php");
elseif((strtolower($_POST['deal_cat_name'])=="equity")&&(strtolower($_POST['deal_subcat1_name'])=="common equity")&&(strtolower($_POST['deal_subcat2_name'])=="ipos")) require("edit_deal_data_eq_ipo.php");
elseif((strtolower($_POST['deal_cat_name'])=="equity")&&(strtolower($_POST['deal_subcat1_name'])=="common equity")&&(strtolower($_POST['deal_subcat2_name'])=="rights issue")) require("edit_deal_data_eq_rights.php");
/*******************************************************************/
$newLogos = array();
if (is_array($_SESSION['logos'])) {
	$defaultSet = false;
	foreach ($_SESSION['logos'] as $key=>$logo) {
		if ($logo['default']) {
			$defaultSet = true; 
		}
	}
	if (!$defaultSet) {
		foreach ($_SESSION['logos'] as $key=>$logo) {
			$_SESSION['logos'][$key]['default'] = true;
			break; 
		}
	} 

	foreach ($_SESSION['logos'] as $key=>$logo) {
		$newLogos[] = array('fileName'=>$logo['fileName'], 'default'=>$logo['default']);
	}     
}
$edit_q.=",logos='".serialize($newLogos)."'";
$edit_q.=" where id='".$_POST['deal_id']."'";


if(!$validation_passed){
	$g_view['msg'] = "Error in submitted deal data";
	//no need to proceed
	return;
}
//echo $extra_edit_q;
//all ok, run query
$success = $g_db->mod_query($edit_q);
if(!$success){
	//echo $g_db->error();
	die("Cannot edit deal data");
}
/*********************************************
data inserted, now try to insert/update the record in extra
*********/
$exist_q = "select transaction_id from ".TP."transaction_extra_detail where transaction_id='".$_POST['deal_id']."'";
$success = $g_db->select_query($exist_q);
if(!$success){
	die("Cannot edit deal data");
}
/************
sng:13/feb/2012
We have removed the $extra_edit_q from this page (as we no longer require the subsidiary company.
We need to check for the leading , and remove it if needed
******************/
$leading_char = substr($extra_edit_q,0,1);
if($leading_char == ','){
	$extra_edit_q = substr($extra_edit_q,1);
}
/*************************************/
if($g_db->has_row()){
	//edit
	$extra_edit_q = "update ".TP."transaction_extra_detail set ".$extra_edit_q." where transaction_id='".$_POST['deal_id']."'";
}else{
	//insert
	$extra_edit_q = "insert into ".TP."transaction_extra_detail set transaction_id='".$_POST['deal_id']."',".$extra_edit_q;
}
$success = $g_db->mod_query($extra_edit_q);
if(!$success){
	die("Cannot edit deal data");
}

/**************************************************
sng:15/5/2010
If the deal value is changed, update the adjusted values
**********/
if($_POST['value_in_billion']!=$_POST['current_value_in_billion']){
	$success = $g_trans->update_adjusted_values_for_deal($_POST['deal_id']);
	if(!$success){
		die("Cannot update adjusted deal value");
	}
}
/********************************************
sng:21/may/2010
try to update the note
Signature has changed, beside, we may not allow admin to change the note
Also, this has moved to transaction_note
**********************************/
$g_trans->set_note($_POST['deal_id'],$_POST['note']);
//never mind if there is error, this is not that important
/*******************
sng:4/feb/2011
try to update the private note

sng:4/oct/2012
Moved the method to another class and changed the method name
********/
require_once("classes/class.transaction_note.php");
$trans_note = new transaction_note();
$trans_note->set_private_note($_POST['deal_id'],$_POST['deal_private_note']);
//never mind if error
/********************************************
sng:8/jul/2010
try to update the sources
****************/
$g_trans->update_sources($_POST['deal_id'],$_POST['sources']);
//never mind if there is error, this is not that important

/****************************************************************/
//echo $edit_q;
if($validation_passed){
	$g_view['msg'] = "Deal data updated";
}
?>