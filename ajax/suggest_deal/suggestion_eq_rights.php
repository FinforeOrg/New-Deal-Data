<?php
/****************************
sng:28/july/2011

Handle Equity Equity Rights Issue type of deal suggestion

sng:12/mar/2012
Now we create direct deal data from this
We need to create query stmt to update transaction table with some deal specific fields

Use casting and sanitize filters
********************************/
if(isset($_POST['premium_discount_to_terp'])&&($_POST['premium_discount_to_terp']!="")){
	$update_transaction_q.=",discount_to_terp='".(float)$_POST['premium_discount_to_terp']."'";
}
if(isset($_POST['fee_gross'])&&($_POST['fee_gross']!="")){
	$update_transaction_q.=",base_fee='".(float)$_POST['fee_gross']."'";
}
/*********************************************************************************/


if(isset($_POST['announced_date'])&&($_POST['announced_date']!="")){
	$transaction_extra_q.=",date_announced='".fotmat_date_for_suggestion($_POST['announced_date'])."'";
}
if(isset($_POST['exrights_date'])&&($_POST['exrights_date']!="")){
	$transaction_extra_q.=",date_ex_rights='".fotmat_date_for_suggestion($_POST['exrights_date'])."'";
}
if(isset($_POST['closed_date'])&&($_POST['closed_date']!="")){
	$transaction_extra_q.=",date_closed='".fotmat_date_for_suggestion($_POST['closed_date'])."'";
}


if(isset($_POST['nr_shares_sold'])&&($_POST['nr_shares_sold']!="")){
	$transaction_extra_q.=",num_shares_underlying_million='".(float)$_POST['nr_shares_sold']."'";
}
if(isset($_POST['nr_shares_in_issue'])&&($_POST['nr_shares_in_issue']!="")){
	$transaction_extra_q.=",curr_num_shares_outstanding_million='".(float)$_POST['nr_shares_in_issue']."'";
}
if(isset($_POST['subscription_ratio'])&&($_POST['subscription_ratio']!="")){
	$transaction_extra_q.=",subscription_ratio='".mysql_real_escape_string($_POST['subscription_ratio'])."'";
}
if(isset($_POST['offer_price'])&&($_POST['offer_price']!="")){
	$transaction_extra_q.=",offer_price='".(float)$_POST['offer_price']."'";
}


if(isset($_POST['free_float_post_transaction'])&&($_POST['free_float_post_transaction']!="")){
	$transaction_extra_q.=",free_float_percent='".(float)$_POST['free_float_post_transaction']."'";
}
if(isset($_POST['price_prior_to_announcement'])&&($_POST['price_prior_to_announcement']!="")){
	$transaction_extra_q.=",price_per_share_before_deal_announcement='".(float)$_POST['price_prior_to_announcement']."'";
}
if(isset($_POST['date_prior_to_announcement'])&&($_POST['date_prior_to_announcement']!="")){
	$transaction_extra_q.=",date_price_per_share_before_deal_announcement='".fotmat_date_for_suggestion($_POST['date_prior_to_announcement'])."'";
}
if(isset($_POST['terp'])&&($_POST['terp']!="")){
	$transaction_extra_q.=",terp='".(float)$_POST['terp']."'";
}
/************************
premium_discount_to_terp goes to discount_to_terp of transaction
*************************/

if(isset($_POST['subscription_rate'])&&($_POST['subscription_rate']!="")){
	$transaction_extra_q.=",subscription_rate_percent='".(float)$_POST['subscription_rate']."'";
}

if(isset($_POST['rump_placement'])&&($_POST['rump_placement']=="on")){
	$transaction_extra_q.=",rump_placement='y'";
}else{
	$transaction_extra_q.=",rump_placement='n'";
}
if(isset($_POST['nr_shares_sold_in_rump'])&&($_POST['nr_shares_sold_in_rump']!="")){
	$transaction_extra_q.=",num_shares_sold_in_rump_million='".(float)$_POST['nr_shares_sold_in_rump']."'";
}
if(isset($_POST['price_of_rump_placement'])&&($_POST['price_of_rump_placement']!="")){
	$transaction_extra_q.=",price_per_share_in_rump='".(float)$_POST['price_of_rump_placement']."'";
}

/**********************
fee_gross goes to base_fee of transaction
************************/




?>