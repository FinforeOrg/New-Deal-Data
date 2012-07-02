<?php
/*******************************************************************
include deal specific snippet, populate $edit_q and $extra_edit_q
write as var.=",fieldname='postvars'"
$edit_q holds SQL for tombstone_transaction. Use it for data that will go in tombstone_transaction
$extra_edit_q holds SQL for tombstone_transaction_extra_detail. Use it for data that will go in tombstone_transaction_extra_detail

Since this is called in ajax, setting the $g_view['err'] will not help. Use session based flash
**/
$extra_edit_q.=",date_announced='".$_POST['date_announced']."'";
$extra_edit_q.=",date_closed='".$_POST['date_closed']."'";
/******************************************************************************************************
date of deal is date closed if we have that, else date announced, never, date rumour.
a bit of validation needed
**********/
if($_POST['date_announced']==""&&$_POST['date_closed']==""){
	create_flash("date_of_deal","Please specify the date of deal");
	$validation_passed = false;
}else{
	
	$extra_edit_q.=",date_announced='".$_POST['date_announced']."'";
	$extra_edit_q.=",date_closed='".$_POST['date_closed']."'";
	
	//set the date of deal
	if($_POST['date_closed']!=""){
		$date_of_deal = $_POST['date_closed'];
	}else{
		$date_of_deal = $_POST['date_announced'];
		//we checked that both is not blank
	}
	$edit_q.=",date_of_deal='".$date_of_deal."'";
}
/************************************************************************************************************/
if($_POST['years_to_maturity']!="") $extra_edit_q.=",years_to_maturity='".mysql_real_escape_string($_POST['years_to_maturity'])."'";
if($_POST['maturity_date']!="") $edit_q.=",maturity_date='".mysql_real_escape_string($_POST['maturity_date'])."'";
if($_POST['coupon']!="") $edit_q.=",coupon='".mysql_real_escape_string($_POST['coupon'])."'";
if($_POST['margin_including_ratchet']!="") $extra_edit_q.=",margin_including_ratchet='".mysql_real_escape_string($_POST['margin_including_ratchet'])."'";

if($_POST['collateral']!="") $extra_edit_q.=",collateral='".mysql_real_escape_string($_POST['collateral'])."'";
$extra_edit_q.=",seniority='".mysql_real_escape_string($_POST['seniority'])."'";

if($_POST['fee_upfront']!="") $extra_edit_q.=",fee_upfront='".mysql_real_escape_string($_POST['fee_upfront'])."'";
if($_POST['fee_commitment']!="") $extra_edit_q.=",fee_commitment='".mysql_real_escape_string($_POST['fee_commitment'])."'";
if($_POST['fee_utilisation']!="") $extra_edit_q.=",fee_utilisation='".mysql_real_escape_string($_POST['fee_utilisation'])."'";
if($_POST['fee_arrangement']!="") $extra_edit_q.=",fee_arrangement='".mysql_real_escape_string($_POST['fee_arrangement'])."'";
?>