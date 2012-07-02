<?php
/****************************
sng:20/july/2011

Handle Equity Equity Additional type of deal suggestion

sng:12/mar/2012
Now we create direct deal data from this
We need to create query stmt to update transaction table with some deal specific fields

Use casting and sanitize filters
********************************/
if(isset($_POST['fee_gross'])&&($_POST['fee_gross']!="")){
	$update_transaction_q.=",base_fee='".(float)$_POST['fee_gross']."'";
}
if(isset($_POST['premium_discount'])&&($_POST['premium_discount']!="")){
	$update_transaction_q.=",discount_to_last='".(float)$_POST['premium_discount']."'";
}
/****************************************************************************************/

if(isset($_POST['announced_date'])&&($_POST['announced_date']!="")){
	$transaction_extra_q.=",date_announced='".fotmat_date_for_suggestion($_POST['announced_date'])."'";
}
if(isset($_POST['closed_date'])&&($_POST['closed_date']!="")){
	$transaction_extra_q.=",date_closed='".fotmat_date_for_suggestion($_POST['closed_date'])."'";
}


if(isset($_POST['transaction_price'])&&($_POST['transaction_price']!="")){
	$transaction_extra_q.=",offer_price='".(float)$_POST['transaction_price']."'";
}

if(isset($_POST['number_shares_sold'])&&($_POST['number_shares_sold']!="")){
	$transaction_extra_q.=",num_shares_underlying_million='".(float)$_POST['number_shares_sold']."'";
}

if(isset($_POST['primary_shares_sold'])&&($_POST['primary_shares_sold']!="")){
	$transaction_extra_q.=",num_primary_shares_million='".(float)$_POST['primary_shares_sold']."'";
}

if(isset($_POST['secondary_shares_sold'])&&($_POST['secondary_shares_sold']!="")){
	$transaction_extra_q.=",num_secondary_shares_million='".(float)$_POST['secondary_shares_sold']."'";
}

if(isset($_POST['nr_shares_post_transaction'])&&($_POST['nr_shares_post_transaction']!="")){
	$transaction_extra_q.=",num_shares_outstanding_after_deal_million='".(float)$_POST['nr_shares_post_transaction']."'";
}

if(isset($_POST['free_float_post_transaction'])&&($_POST['free_float_post_transaction']!="")){
	$transaction_extra_q.=",free_float_percent='".(float)$_POST['free_float_post_transaction']."'";
}



if(isset($_POST['avg_daily_trading_value'])&&($_POST['avg_daily_trading_value']!="")){
	$transaction_extra_q.=",avg_daily_trading_vol_million='".(float)$_POST['avg_daily_trading_value']."'";
}

if(isset($_POST['shares_sold_vs_adtv'])&&($_POST['shares_sold_vs_adtv']!="")){
	$transaction_extra_q.=",shares_underlying_vs_adtv_ratio='".(float)$_POST['shares_sold_vs_adtv']."'";
}

if(isset($_POST['price_prior_to_announcement'])&&($_POST['price_prior_to_announcement']!="")){
	$transaction_extra_q.=",price_per_share_before_deal_announcement='".(float)$_POST['price_prior_to_announcement']."'";
}

if(isset($_POST['date_prior_to_announcement'])&&($_POST['date_prior_to_announcement']!="")){
	$transaction_extra_q.=",date_price_per_share_before_deal_announcement='".fotmat_date_for_suggestion($_POST['date_prior_to_announcement'])."'";
}
/************
premium_discount goes to discount_to_last in transaction
************/

/**************
fee_gross goes to base_fee of transaction
****************/





?>