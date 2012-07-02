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


if($_POST['offer_price']!="") $extra_edit_q.=",offer_price='".mysql_real_escape_string($_POST['offer_price'])."'";
if($_POST['num_shares_underlying_million']!="") $extra_edit_q.=",num_shares_underlying_million='".mysql_real_escape_string($_POST['num_shares_underlying_million'])."'";
if($_POST['num_primary_shares_million']!="") $extra_edit_q.=",num_primary_shares_million='".mysql_real_escape_string($_POST['num_primary_shares_million'])."'";
if($_POST['num_secondary_shares_million']!="") $extra_edit_q.=",num_secondary_shares_million='".mysql_real_escape_string($_POST['num_secondary_shares_million'])."'";
if($_POST['num_shares_outstanding_after_deal_million']!="") $extra_edit_q.=",num_shares_outstanding_after_deal_million='".mysql_real_escape_string($_POST['num_shares_outstanding_after_deal_million'])."'";
if($_POST['free_float_percent']!="") $extra_edit_q.=",avg_daily_trading_vol_million='".mysql_real_escape_string($_POST['free_float_percent'])."'";
if($_POST['avg_daily_trading_vol_million']!="") $extra_edit_q.=",avg_daily_trading_vol_million='".mysql_real_escape_string($_POST['avg_daily_trading_vol_million'])."'";
if($_POST['shares_underlying_vs_adtv_ratio']!="") $extra_edit_q.=",shares_underlying_vs_adtv_ratio='".mysql_real_escape_string($_POST['shares_underlying_vs_adtv_ratio'])."'";


if($_POST['price_per_share_before_deal_announcement']!="") $extra_edit_q.=",price_per_share_before_deal_announcement='".mysql_real_escape_string($_POST['price_per_share_before_deal_announcement'])."'";

if($_POST['date_price_per_share_before_deal_announcement']!="") $extra_edit_q.=",date_price_per_share_before_deal_announcement='".mysql_real_escape_string($_POST['date_price_per_share_before_deal_announcement'])."'";
if($_POST['discount_to_last']!="") $edit_q.=",discount_to_last='".mysql_real_escape_string($_POST['discount_to_last'])."'";
if($_POST['base_fee']!="") $edit_q.=",base_fee='".mysql_real_escape_string($_POST['base_fee'])."'";
?>