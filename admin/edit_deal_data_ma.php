<?php
/*******************************************************************
include deal specific snippet, populate $edit_q and $extra_edit_q
write as var.=",fieldname='postvars'"
$edit_q holds SQL for tombstone_transaction. Use it for data that will go in tombstone_transaction
$extra_edit_q holds SQL for tombstone_transaction_extra_detail. Use it for data that will go in tombstone_transaction_extra_detail

Since the deal_data_ma is called in ajax, setting the $g_view['err'] will not help. Use session based flash
**/
$extra_edit_q.=",date_rumour='".$_POST['date_rumour']."'";
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
	$extra_edit_q.=",date_rumour='".$_POST['date_rumour']."'";
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
/************
sng:13/feb/2012
we no longer require target or seller. Now we have one or more companies with roles
**************/
//
$extra_edit_q.=",implied_equity_value_in_million_local_currency='".$_POST['implied_equity_value_in_million_local_currency']."'";
$extra_edit_q.=",acquisition_percentage='".$_POST['acquisition_percentage']."'";
$extra_edit_q.=",net_debt_in_million_local_currency='".$_POST['net_debt_in_million_local_currency']."'";
$extra_edit_q.=",dividend_on_top_of_equity_million_local_curency='".$_POST['dividend_on_top_of_equity_million_local_curency']."'";
$extra_edit_q.=",enterprise_value_million_local_currency='".$_POST['enterprise_value_million_local_currency']."'";
$extra_edit_q.=",enterprise_value_million='".$_POST['enterprise_value_million']."'";
$extra_edit_q.=",payment_type='".$_POST['payment_type']."'";
$extra_edit_q.=",equity_payment_percent='".$_POST['equity_payment_percent']."'";
$extra_edit_q.=",takeover_id='".$_POST['takeover_id']."'";
//target listed in stock exchange is radio button
$extra_edit_q.=",target_listed_in_stock_exchange='".$_POST['target_listed_in_stock_exchange']."'";

$extra_edit_q.=",target_stock_exchange_name='".mysql_real_escape_string($_POST['target_stock_exchange_name'])."'";
$extra_edit_q.=",currency_price_per_share='".mysql_real_escape_string($_POST['currency_price_per_share'])."'";

$extra_edit_q.=",deal_price_per_share='".$_POST['deal_price_per_share']."'";
$extra_edit_q.=",price_per_share_before_deal_announcement='".$_POST['price_per_share_before_deal_announcement']."'";
$extra_edit_q.=",date_price_per_share_before_deal_announcement='".$_POST['date_price_per_share_before_deal_announcement']."'";
$extra_edit_q.=",implied_premium_percentage='".$_POST['implied_premium_percentage']."'";
$extra_edit_q.=",total_shares_outstanding_million='".$_POST['total_shares_outstanding_million']."'";


$extra_edit_q.=",termination_fee_million='".$_POST['termination_fee_million']."'";
$extra_edit_q.=",end_date_termination_fee='".$_POST['end_date_termination_fee']."'";

$extra_edit_q.=",fee_percent_to_sellside_advisor='".$_POST['fee_percent_to_sellside_advisor']."'";
$extra_edit_q.=",fee_percent_to_buyside_advisor='".$_POST['fee_percent_to_buyside_advisor']."'";

$extra_edit_q.=",revenue_ltm_million='".$_POST['revenue_ltm_million']."'";
$extra_edit_q.=",revenue_mry_million='".$_POST['revenue_mry_million']."'";
$extra_edit_q.=",revenue_ny_million='".$_POST['revenue_ny_million']."'";

$extra_edit_q.=",ebitda_ltm_million='".$_POST['ebitda_ltm_million']."'";
$extra_edit_q.=",ebitda_mry_million='".$_POST['ebitda_mry_million']."'";
$extra_edit_q.=",ebitda_ny_million='".$_POST['ebitda_ny_million']."'";

$extra_edit_q.=",net_income_ltm_million='".$_POST['net_income_ltm_million']."'";
$extra_edit_q.=",net_income_mry_million='".$_POST['net_income_mry_million']."'";
$extra_edit_q.=",net_income_ny_million='".$_POST['net_income_ny_million']."'";

$extra_edit_q.=",date_year_end_of_recent_financial_year='".$_POST['date_year_end_of_recent_financial_year']."'";
?>