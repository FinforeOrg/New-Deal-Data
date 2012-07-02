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
if($_POST['date_ex_rights']!=""){
	$extra_edit_q.=",date_ex_rights='".mysql_real_escape_string($_POST['date_ex_rights'])."'";
}

if($_POST['offer_price']!="") $extra_edit_q.=",offer_price='".mysql_real_escape_string($_POST['offer_price'])."'";
if($_POST['num_shares_underlying_million']!="") $extra_edit_q.=",num_shares_underlying_million='".mysql_real_escape_string($_POST['num_shares_underlying_million'])."'";

if($_POST['curr_num_shares_outstanding_million']!="") $extra_edit_q.=",curr_num_shares_outstanding_million='".mysql_real_escape_string($_POST['curr_num_shares_outstanding_million'])."'";

if($_POST['subscription_ratio']!="") $extra_edit_q.=",subscription_ratio='".mysql_real_escape_string($_POST['subscription_ratio'])."'";
if($_POST['free_float_percent']!="") $extra_edit_q.=",free_float_percent='".mysql_real_escape_string($_POST['free_float_percent'])."'";

if($_POST['price_per_share_before_deal_announcement']!="") $extra_edit_q.=",price_per_share_before_deal_announcement='".mysql_real_escape_string($_POST['price_per_share_before_deal_announcement'])."'";

if($_POST['date_price_per_share_before_deal_announcement']!="") $extra_edit_q.=",date_price_per_share_before_deal_announcement='".mysql_real_escape_string($_POST['date_price_per_share_before_deal_announcement'])."'";

if($_POST['terp']!="") $extra_edit_q.=",terp='".mysql_real_escape_string($_POST['terp'])."'";


if($_POST['discount_to_terp']!="") $edit_q.=",discount_to_terp='".mysql_real_escape_string($_POST['discount_to_terp'])."'";
if($_POST['subscription_rate_percent']!="") $extra_edit_q.=",subscription_rate_percent='".mysql_real_escape_string($_POST['subscription_rate_percent'])."'";

if(isset($_POST['rump_placement'])&&($_POST['rump_placement']!="")) $extra_edit_q.=",rump_placement='".$_POST['rump_placement']."'";
if($_POST['num_shares_sold_in_rump_million']!="") $extra_edit_q.=",num_shares_sold_in_rump_million='".mysql_real_escape_string($_POST['num_shares_sold_in_rump_million'])."'";
if($_POST['price_per_share_in_rump']!="") $extra_edit_q.=",price_per_share_in_rump='".mysql_real_escape_string($_POST['price_per_share_in_rump'])."'";

if($_POST['base_fee']!="") $edit_q.=",base_fee='".mysql_real_escape_string($_POST['base_fee'])."'";
?>