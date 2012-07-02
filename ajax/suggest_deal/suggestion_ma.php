<?php
/*********************
sng:6/jun/2011
handles M&A submission

No need for any validation since data is validated prior to submission

We should have written this in the transaction class but as things are, I am writing it here

For M&A, there the deal sub sub type is n/a
There is no date_ex_rights

sng:12/mar/2012
Now we create direct deal data from this
We need to create query stmt to update transaction table with some deal specific fields

Use casting and sanitize filters
*********************/
if(isset($_POST['rumour_date'])&&($_POST['rumour_date']!="")){
	$transaction_extra_q.=",date_rumour='".fotmat_date_for_suggestion($_POST['rumour_date'])."'";
}
if(isset($_POST['announced_date'])&&($_POST['announced_date']!="")){
	$transaction_extra_q.=",date_announced='".fotmat_date_for_suggestion($_POST['announced_date'])."'";
}
if(isset($_POST['closed_date'])&&($_POST['closed_date']!="")){
	$transaction_extra_q.=",date_closed='".fotmat_date_for_suggestion($_POST['closed_date'])."'";
}

if(isset($_POST['implied_equity_value'])&&($_POST['implied_equity_value']!="")){
	$transaction_extra_q.=",implied_equity_value_in_million_local_currency='".(float)$_POST['implied_equity_value']."'";
}

if(isset($_POST['aquisition_percentage'])&&($_POST['aquisition_percentage']!="")){
	$transaction_extra_q.=",acquisition_percentage='".(float)$_POST['aquisition_percentage']."'";
}

if(isset($_POST['net_debt'])&&($_POST['net_debt']!="")){
	$transaction_extra_q.=",net_debt_in_million_local_currency='".(float)$_POST['net_debt']."'";
}

/****************
sng:3/may/2012
Some new fields
****************/
if(isset($_POST['total_debt_million_local_currency'])&&($_POST['total_debt_million_local_currency']!="")){
	$transaction_extra_q.=",total_debt_million_local_currency='".(float)$_POST['total_debt_million_local_currency']."'";
}
if(isset($_POST['cash_million_local_currency'])&&($_POST['cash_million_local_currency']!="")){
	$transaction_extra_q.=",cash_million_local_currency='".(float)$_POST['cash_million_local_currency']."'";
}
if(isset($_POST['adjustments_million_local_currency'])&&($_POST['adjustments_million_local_currency']!="")){
	$transaction_extra_q.=",adjustments_million_local_currency='".(float)$_POST['adjustments_million_local_currency']."'";
}

if(isset($_POST['divident_payment'])&&($_POST['divident_payment']!="")){
	$transaction_extra_q.=",dividend_on_top_of_equity_million_local_curency='".(float)$_POST['divident_payment']."'";
}

if(isset($_POST['enterprise_value'])&&($_POST['enterprise_value']!="")){
	$transaction_extra_q.=",enterprise_value_million_local_currency='".(float)$_POST['enterprise_value']."'";
}





if(isset($_POST['transaction_type'])&&($_POST['transaction_type']!="")){
	$transaction_extra_q.=",payment_type='".mysql_real_escape_string($_POST['transaction_type'])."'";
}

//takeover type, we expect the id
if(isset($_POST['friendly_or_hostile'])&&($_POST['friendly_or_hostile']!="")){
	$transaction_extra_q.=",takeover_id='".(int)$_POST['friendly_or_hostile']."'";
}

if(isset($_POST['equity_percentage'])&&($_POST['equity_percentage']!="")){
	$transaction_extra_q.=",equity_payment_percent='".(float)$_POST['equity_percentage']."'";
}





if(isset($_POST['implied_enterprise_value'])&&($_POST['implied_enterprise_value']!="")){
	$transaction_extra_q.=",enterprise_value_million='".$_POST['implied_enterprise_value']."'";
}

if(isset($_POST['publicly_listed'])&&($_POST['publicly_listed']=="on")){
	$transaction_extra_q.=",target_listed_in_stock_exchange='y'";
}else{
	$transaction_extra_q.=",target_listed_in_stock_exchange='n'";
}

if(isset($_POST['target_stock_exchange_name'])&&($_POST['target_stock_exchange_name']!="")){
	$transaction_extra_q.=",target_stock_exchange_name='".mysql_real_escape_string($_POST['target_stock_exchange_name'])."'";
}

if(isset($_POST['deal_price_per_share'])&&($_POST['deal_price_per_share']!="")){
	$transaction_extra_q.=",deal_price_per_share='".(float)$_POST['deal_price_per_share']."'";
}

if(isset($_POST['local_currency_of_share_price'])&&($_POST['local_currency_of_share_price']!="")){
	$transaction_extra_q.=",currency_price_per_share='".mysql_real_escape_string($_POST['local_currency_of_share_price'])."'";
}

if(isset($_POST['share_price_prior_to_announcement'])&&($_POST['share_price_prior_to_announcement']!="")){
	$transaction_extra_q.=",price_per_share_before_deal_announcement='".(float)$_POST['share_price_prior_to_announcement']."'";
}

if(isset($_POST['date_of_share_price_prior_to_announce'])&&($_POST['date_of_share_price_prior_to_announce']!="")){
	$transaction_extra_q.=",date_price_per_share_before_deal_announcement='".fotmat_date_for_suggestion($_POST['date_of_share_price_prior_to_announce'])."'";
}

if(isset($_POST['implied_premium'])&&($_POST['implied_premium']!="")){
	$transaction_extra_q.=",implied_premium_percentage='".(float)$_POST['implied_premium']."'";
}

if(isset($_POST['total_shares_outstanding'])&&($_POST['total_shares_outstanding']!="")){
	$transaction_extra_q.=",total_shares_outstanding_million='".(float)$_POST['total_shares_outstanding']."'";
}



if(isset($_POST['termination_fee'])&&($_POST['termination_fee']!="")){
	$transaction_extra_q.=",termination_fee_million='".(float)$_POST['termination_fee']."'";
}

if(isset($_POST['end_date_for_termination_fee'])&&($_POST['end_date_for_termination_fee']!="")){
	$transaction_extra_q.=",end_date_termination_fee='".fotmat_date_for_suggestion($_POST['end_date_for_termination_fee'])."'";
}





if(isset($_POST['fee_to_sellside'])&&($_POST['fee_to_sellside']!="")){
	$transaction_extra_q.=",fee_percent_to_sellside_advisor='".(float)$_POST['fee_to_sellside']."'";
}

if(isset($_POST['fee_to_buyside'])&&($_POST['fee_to_buyside']!="")){
	$transaction_extra_q.=",fee_percent_to_buyside_advisor='".(float)$_POST['fee_to_buyside']."'";
}

if(isset($_POST['revenues_last_12_months'])&&($_POST['revenues_last_12_months']!="")){
	$transaction_extra_q.=",revenue_ltm_million='".(float)$_POST['revenues_last_12_months']."'";
}

if(isset($_POST['revenues_most_recent_year'])&&($_POST['revenues_most_recent_year']!="")){
	$transaction_extra_q.=",revenue_mry_million='".(float)$_POST['revenues_most_recent_year']."'";
}

if(isset($_POST['revenues_next_year'])&&($_POST['revenues_next_year']!="")){
	$transaction_extra_q.=",revenue_ny_million='".(float)$_POST['revenues_next_year']."'";
}

if(isset($_POST['ebitda_last_12_months'])&&($_POST['ebitda_last_12_months']!="")){
	$transaction_extra_q.=",ebitda_ltm_million='".(float)$_POST['ebitda_last_12_months']."'";
}

if(isset($_POST['ebitda_most_recent_year'])&&($_POST['ebitda_most_recent_year']!="")){
	$transaction_extra_q.=",ebitda_mry_million='".(float)$_POST['ebitda_most_recent_year']."'";
}

if(isset($_POST['ebitda_next_year'])&&($_POST['ebitda_next_year']!="")){
	$transaction_extra_q.=",ebitda_ny_million='".(float)$_POST['ebitda_next_year']."'";
}

if(isset($_POST['net_income_last_12_months'])&&($_POST['net_income_last_12_months']!="")){
	$transaction_extra_q.=",net_income_ltm_million='".(float)$_POST['net_income_last_12_months']."'";
}

if(isset($_POST['net_income_most_recent_year'])&&($_POST['net_income_most_recent_year']!="")){
	$transaction_extra_q.=",net_income_mry_million='".(float)$_POST['net_income_most_recent_year']."'";
}

if(isset($_POST['net_income_net_year'])&&($_POST['net_income_net_year']!="")){
	$transaction_extra_q.=",net_income_ny_million='".(float)$_POST['net_income_net_year']."'";
}

if(isset($_POST['year_end_of_most_recent_financial_year'])&&($_POST['year_end_of_most_recent_financial_year']!="")){
	$transaction_extra_q.=",date_year_end_of_recent_financial_year='".fotmat_date_for_suggestion($_POST['year_end_of_most_recent_financial_year'])."'";
}


//store the data
?>