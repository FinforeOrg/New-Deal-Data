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

if($_POST['greenshoe_included']!="") $extra_edit_q.=",greenshoe_included='".mysql_real_escape_string($_POST['greenshoe_included'])."'";

if($_POST['ipo_stock_exchange']!="") $extra_edit_q.=",ipo_stock_exchange='".mysql_real_escape_string($_POST['ipo_stock_exchange'])."'";
if($_POST['price_at_end_of_first_day']!="") $extra_edit_q.=",price_at_end_of_first_day='".mysql_real_escape_string($_POST['price_at_end_of_first_day'])."'";

if($_POST['date_first_trading']!="") $extra_edit_q.=",date_first_trading='".mysql_real_escape_string($_POST['date_first_trading'])."'";
if($_POST['1_day_price_change']!="") $edit_q.=",1_day_price_change='".mysql_real_escape_string($_POST['1_day_price_change'])."'";
if($_POST['base_fee']!="") $edit_q.=",base_fee='".mysql_real_escape_string($_POST['base_fee'])."'";
if($_POST['incentive_fee']!="") $edit_q.=",incentive_fee='".mysql_real_escape_string($_POST['incentive_fee'])."'";
?>