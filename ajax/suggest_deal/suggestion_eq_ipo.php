<?php
/****************************
sng:26/july/2011

Handle Equity Equity IPO type of deal suggestion

sng:12/mar/2012
Now we create direct deal data from this
We need to create query stmt to update transaction table with some deal specific fields

Use casting and sanitize filters
********************************/
if(isset($_POST['fee_incentive'])&&($_POST['fee_incentive']!="")){
	$update_transaction_q.=",incentive_fee='".(float)$_POST['fee_incentive']."'";
}

if(isset($_POST['fee_base'])&&($_POST['fee_base']!="")){
	$update_transaction_q.=",base_fee='".(float)$_POST['fee_base']."'";
}

if(isset($_POST['performance_on_first_day'])&&($_POST['performance_on_first_day']!="")){
	$update_transaction_q.=",1_day_price_change='".(float)$_POST['performance_on_first_day']."'";
}
/******************************************************************/

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

if(isset($_POST['greenshoe_included'])&&($_POST['greenshoe_included']=="on")){
	$transaction_extra_q.=",greenshoe_included='y'";
}else{
	$transaction_extra_q.=",greenshoe_included='n'";
}



if(isset($_POST['ipo_stock_exchange'])&&($_POST['ipo_stock_exchange']!="")){
	$transaction_extra_q.=",ipo_stock_exchange='".mysql_real_escape_string($_POST['ipo_stock_exchange'])."'";
}

if(isset($_POST['price_at_end_of_1st_day'])&&($_POST['price_at_end_of_1st_day']!="")){
	$transaction_extra_q.=",price_at_end_of_first_day='".(float)$_POST['price_at_end_of_1st_day']."'";
}

if(isset($_POST['first_day_of_trading'])&&($_POST['first_day_of_trading']!="")){
	$transaction_extra_q.=",date_first_trading='".fotmat_date_for_suggestion($_POST['first_day_of_trading'])."'";
}
/*****************
performance_on_first_day goes to 1_day_price_change of transaction
*****************/

/*************
fee_base goes to base_fee of transaction
**************/

/************
fee_incentive goes to incentive_fee of transaction
**************/





?>