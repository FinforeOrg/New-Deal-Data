<?php
/**************
sng:15/july/2011
Handles the Equity Preferred suggestions

sng:12/mar/2012
Now we create direct deal data from this
We need to create query stmt to update transaction table with some deal specific fields

Use casting and sanitize filters
****************/
if(isset($_POST['end_date'])&&($_POST['end_date']!="")){
	$update_transaction_q.=",maturity_date='".fotmat_date_for_suggestion($_POST['end_date'])."'";
}

if(isset($_POST['coupon'])&&($_POST['coupon']!="")){
	$update_transaction_q.=",coupon='".mysql_real_escape_string($_POST['coupon'])."'";
}

if(isset($_POST['current_rating'])&&($_POST['current_rating']!="")){
	$update_transaction_q.=",current_rating='".mysql_real_escape_string($_POST['current_rating'])."'";
}
//gross fee
if(isset($_POST['fee_gross'])&&($_POST['fee_gross']!="")){
	$update_transaction_q.=",base_fee='".(float)$_POST['fee_gross']."'";
}
/***********************************************************************************/
if(isset($_POST['announced_date'])&&($_POST['announced_date']!="")){
	$transaction_extra_q.=",date_announced='".fotmat_date_for_suggestion($_POST['announced_date'])."'";
}
if(isset($_POST['closed_date'])&&($_POST['closed_date']!="")){
	$transaction_extra_q.=",date_closed='".fotmat_date_for_suggestion($_POST['closed_date'])."'";
}

if(isset($_POST['year_to_maturity'])&&($_POST['year_to_maturity']!="")){
	$transaction_extra_q.=",years_to_maturity='".mysql_real_escape_string($_POST['year_to_maturity'])."'";
}
/************
end_date goes to maturity_date of transaction

coupon goes to coupon of transaction

current rating goes to current_rating of transaction
*************/



if(isset($_POST['bond_format'])&&($_POST['bond_format']!="")){
	$transaction_extra_q.=",format='".mysql_real_escape_string($_POST['bond_format'])."'";
}
if(isset($_POST['guarantor'])&&($_POST['guarantor']!="")){
	$transaction_extra_q.=",guarantor='".mysql_real_escape_string($_POST['guarantor'])."'";
}
if(isset($_POST['collateral'])&&($_POST['collateral']!="")){
	$transaction_extra_q.=",collateral='".mysql_real_escape_string($_POST['collateral'])."'";
}
if(isset($_POST['seniority'])&&($_POST['seniority']!="")){
	$transaction_extra_q.=",seniority='".mysql_real_escape_string($_POST['seniority'])."'";
}
if(isset($_POST['year_to_call'])&&($_POST['year_to_call']!="")){
	$transaction_extra_q.=",year_to_call='".mysql_real_escape_string($_POST['year_to_call'])."'";
}
if(isset($_POST['call_date'])&&($_POST['call_date']!="")){
	$transaction_extra_q.=",call_date='".fotmat_date_for_suggestion($_POST['call_date'])."'";
}

if(isset($_POST['redemption_price'])&&($_POST['redemption_price']!="")){
	$transaction_extra_q.=",redemption_price='".mysql_real_escape_string($_POST['redemption_price'])."'";
}

if(isset($_POST['reference_price'])&&($_POST['reference_price']!="")){
	$transaction_extra_q.=",reference_price='".(float)$_POST['reference_price']."'";
}
if(isset($_POST['conversion_price'])&&($_POST['conversion_price']!="")){
	$transaction_extra_q.=",conversion_price='".(float)$_POST['conversion_price']."'";
}
if(isset($_POST['currency_reference_price'])&&($_POST['currency_reference_price']!="")){
	$transaction_extra_q.=",currency_reference_price='".mysql_real_escape_string($_POST['currency_reference_price'])."'";
}
if(isset($_POST['conversion_premium'])&&($_POST['conversion_premium']!="")){
	$transaction_extra_q.=",conversion_premia_percent='".(float)$_POST['conversion_premium']."'";
}

if(isset($_POST['number_shares'])&&($_POST['number_shares']!="")){
	$transaction_extra_q.=",num_shares_underlying_million='".(float)$_POST['number_shares']."'";
}
if(isset($_POST['current_number_of_shares_in_issue'])&&($_POST['current_number_of_shares_in_issue']!="")){
	$transaction_extra_q.=",curr_num_shares_outstanding_million='".(float)$_POST['current_number_of_shares_in_issue']."'";
}
if(isset($_POST['avg_daily_trading_volume'])&&($_POST['avg_daily_trading_volume']!="")){
	$transaction_extra_q.=",avg_daily_trading_vol_million='".(float)$_POST['avg_daily_trading_volume']."'";
}
if(isset($_POST['shares_vs_adtv'])&&($_POST['shares_vs_adtv']!="")){
	$transaction_extra_q.=",shares_underlying_vs_adtv_ratio='".(float)$_POST['shares_vs_adtv']."'";
}
//gross fee
/************
fee_gross goes in base_fee of transaction
***************/


if(isset($_POST['divident_protection_mechanism'])&&($_POST['divident_protection_mechanism']=="on")){
	$transaction_extra_q.=",dividend_protection='y'";
}else{
	$transaction_extra_q.=",dividend_protection='n'";
}
?>