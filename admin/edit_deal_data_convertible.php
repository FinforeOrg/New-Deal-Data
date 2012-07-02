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
/**********
sng:14/feb/2012
Now that we have one or more participants, we no longer need to track the security
*************/

if($_POST['years_to_maturity']!="") $extra_edit_q.=",years_to_maturity='".mysql_real_escape_string($_POST['years_to_maturity'])."'";
if($_POST['maturity_date']!="") $edit_q.=",maturity_date='".mysql_real_escape_string($_POST['maturity_date'])."'";
if($_POST['coupon']!="") $edit_q.=",coupon='".mysql_real_escape_string($_POST['coupon'])."'";
if($_POST['current_rating']!="") $edit_q.=",current_rating='".mysql_real_escape_string($_POST['current_rating'])."'";
if($_POST['format']!="") $extra_edit_q.=",format='".mysql_real_escape_string($_POST['format'])."'";
if($_POST['guarantor']!="") $extra_edit_q.=",guarantor='".mysql_real_escape_string($_POST['guarantor'])."'";

if($_POST['collateral']!="") $extra_edit_q.=",collateral='".mysql_real_escape_string($_POST['collateral'])."'";
if($_POST['seniority']!="") $extra_edit_q.=",seniority='".mysql_real_escape_string($_POST['seniority'])."'";

if($_POST['base_fee']!="") $edit_q.=",base_fee='".mysql_real_escape_string($_POST['base_fee'])."'";

if($_POST['year_to_call']!="") $extra_edit_q.=",year_to_call='".mysql_real_escape_string($_POST['year_to_call'])."'";
if($_POST['call_date']!="") $extra_edit_q.=",call_date='".mysql_real_escape_string($_POST['call_date'])."'";
if($_POST['redemption_price']!="") $extra_edit_q.=",redemption_price='".mysql_real_escape_string($_POST['redemption_price'])."'";

if($_POST['reference_price']!="") $extra_edit_q.=",reference_price='".mysql_real_escape_string($_POST['reference_price'])."'";
if($_POST['currency_reference_price']!="") $extra_edit_q.=",currency_reference_price='".mysql_real_escape_string($_POST['currency_reference_price'])."'";
if($_POST['conversion_price']!="") $extra_edit_q.=",conversion_price='".mysql_real_escape_string($_POST['conversion_price'])."'";
if($_POST['conversion_premia_percent']!="") $extra_edit_q.=",conversion_premia_percent='".mysql_real_escape_string($_POST['conversion_premia_percent'])."'";
if($_POST['num_shares_underlying_million']!="") $extra_edit_q.=",num_shares_underlying_million='".mysql_real_escape_string($_POST['num_shares_underlying_million'])."'";
if($_POST['curr_num_shares_outstanding_million']!="") $extra_edit_q.=",curr_num_shares_outstanding_million='".mysql_real_escape_string($_POST['curr_num_shares_outstanding_million'])."'";
if($_POST['avg_daily_trading_vol_million']!="") $extra_edit_q.=",avg_daily_trading_vol_million='".mysql_real_escape_string($_POST['avg_daily_trading_vol_million'])."'";
if($_POST['shares_underlying_vs_adtv_ratio']!="") $extra_edit_q.=",shares_underlying_vs_adtv_ratio='".mysql_real_escape_string($_POST['shares_underlying_vs_adtv_ratio'])."'";

if(isset($_POST['dividend_protection'])&&($_POST['dividend_protection']!="")) $extra_edit_q.=",dividend_protection='".$_POST['dividend_protection']."'";
?>