<?php
/**************
sng:30/jun/2011
Handles the Debt type of deal suggestion. Handles both Bond and Loan

sng:12/mar/2012
Now we create direct deal data from this
We need to create query stmt to update transaction table with some deal specific fields

Use casting and sanitize filters
****************/
/************
gross fee for debt bond
***************/
if(isset($_POST['fee_gross'])&&($_POST['fee_gross']!="")){
	$update_transaction_q.=",base_fee='".(float)$_POST['fee_gross']."'";
}

/**********
for bond
************/
if(isset($_POST['current_rating'])&&($_POST['current_rating']!="")){
	$update_transaction_q.=",current_rating='".mysql_real_escape_string($_POST['current_rating'])."'";
}

/*************
in loan
****************/
if(isset($_POST['margin'])&&($_POST['margin']!="")){
	$update_transaction_q.=",coupon='".mysql_real_escape_string($_POST['margin'])."'";
}

/**********
in bond
******************/
if(isset($_POST['coupon'])&&($_POST['coupon']!="")){
	$update_transaction_q.=",coupon='".mysql_real_escape_string($_POST['coupon'])."'";
}

if(isset($_POST['end_date'])&&($_POST['end_date']!="")){
	$update_transaction_q.=",maturity_date='".fotmat_date_for_suggestion($_POST['end_date'])."'";
}
/****************************************************************************************************************/

if(isset($_POST['announced_date'])&&($_POST['announced_date']!="")){
	$transaction_extra_q.=",date_announced='".fotmat_date_for_suggestion($_POST['announced_date'])."'";
}
if(isset($_POST['closed_date'])&&($_POST['closed_date']!="")){
	$transaction_extra_q.=",date_closed='".fotmat_date_for_suggestion($_POST['closed_date'])."'";
}


//in bond
if(isset($_POST['year_to_maturity'])&&($_POST['year_to_maturity']!="")){
	$transaction_extra_q.=",years_to_maturity='".mysql_real_escape_string($_POST['year_to_maturity'])."'";
}
//in loan
if(isset($_POST['tenor'])&&($_POST['tenor']!="")){
	$transaction_extra_q.=",years_to_maturity='".mysql_real_escape_string($_POST['tenor'])."'";
}

/*********
end_date goe to maturity_date in transaction
**************/


/**********
in bond
coupon goes to coupon in transaction
******************/


/*************
in loan
margin goes to coupon of transaction
****************/


//in loan
if(isset($_POST['margin_including_ratchet'])&&($_POST['margin_including_ratchet']!="")){
	$transaction_extra_q.=",margin_including_ratchet='".mysql_real_escape_string($_POST['margin_including_ratchet'])."'";
}

/**********
for bond
current_rating goes to current_rating of transaction
**************/

if(isset($_POST['bond_format'])&&($_POST['bond_format']!="")){
	$transaction_extra_q.=",format='".mysql_real_escape_string($_POST['bond_format'])."'";
}

if(isset($_POST['collateral'])&&($_POST['collateral']!="")){
	$transaction_extra_q.=",collateral='".mysql_real_escape_string($_POST['collateral'])."'";
}
if(isset($_POST['seniority'])&&($_POST['seniority']!="")){
	$transaction_extra_q.=",seniority='".mysql_real_escape_string($_POST['seniority'])."'";
}

if(isset($_POST['guarantor'])&&($_POST['guarantor']!="")){
	$transaction_extra_q.=",guarantor='".mysql_real_escape_string($_POST['guarantor'])."'";
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

/************
gross fee for debt bond
fee_gross goes to base_fee of transaction
***************/

//the 4 fees for debt loan
if(isset($_POST['fee_upfront'])&&($_POST['fee_upfront']!="")){
	$transaction_extra_q.=",fee_upfront='".(float)$_POST['fee_upfront']."'";
}
if(isset($_POST['fee_commitment'])&&($_POST['fee_commitment']!="")){
	$transaction_extra_q.=",fee_commitment='".(float)$_POST['fee_commitment']."'";
}
if(isset($_POST['fee_utilisation'])&&($_POST['fee_utilisation']!="")){
	$transaction_extra_q.=",fee_utilisation='".(float)$_POST['fee_utilisation']."'";
}
if(isset($_POST['fee_arrangement'])&&($_POST['fee_arrangement']!="")){
	$transaction_extra_q.=",fee_arrangement='".(float)$_POST['fee_arrangement']."'";
}
?>