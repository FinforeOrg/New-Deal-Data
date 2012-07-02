<?php
/*******************
8/july/2011
handles Debt correction (both Loan and Bond)

We do not perform any validation since this is just a correction

The deal cat name, subcat name, sub sub cat name all come from hidden field
********************/

$suggestion_ins_q = "insert into ".TP."transaction_suggestions set
id='".$suggestion_id."',
deal_id='".$deal_id."',
suggested_by='".$suggestion_mem_id."',
date_suggested='".$suggestion_date."',
deal_cat_name='".$_POST['deal_cat_name']."',
deal_subcat1_name='".$_POST['deal_subcat1_name']."',
deal_subcat2_name='".$_POST['deal_subcat2_name']."'";

if(isset($_POST['date_announced'])&&($_POST['date_announced']!="")){
	$suggestion_ins_q.=",date_announced='".$_POST['date_announced']."'";
}
if(isset($_POST['date_closed'])&&($_POST['date_closed']!="")){
	$suggestion_ins_q.=",date_closed='".$_POST['date_closed']."'";
}

if(isset($_POST['parent_company_name'])&&($_POST['parent_company_name']!="")){
	$suggestion_ins_q.=",deal_company_name='".mysql_real_escape_string($_POST['parent_company_name'])."'";
}
if(isset($_POST['parent_company_sector'])&&($_POST['parent_company_sector']!="")){
	$suggestion_ins_q.=",deal_company_sector='".mysql_real_escape_string($_POST['parent_company_sector'])."'";
}
if(isset($_POST['parent_company_industry'])&&($_POST['parent_company_industry']!="")){
	$suggestion_ins_q.=",deal_company_industry='".mysql_real_escape_string($_POST['parent_company_industry'])."'";
}
if(isset($_POST['country_of_headquarters_buyer'])&&($_POST['country_of_headquarters_buyer']!="")){
	$suggestion_ins_q.=",deal_company_country='".mysql_real_escape_string($_POST['country_of_headquarters_buyer'])."'";
}

if(isset($_POST['buyer_subsidiary_name'])&&($_POST['buyer_subsidiary_name']!="")){
	$suggestion_ins_q.=",buyer_subsidiary_name='".mysql_real_escape_string($_POST['buyer_subsidiary_name'])."'";
}
if(isset($_POST['buyer_subsidiary_sector'])&&($_POST['buyer_subsidiary_sector']!="")){
	$suggestion_ins_q.=",buyer_subsidiary_sector='".mysql_real_escape_string($_POST['buyer_subsidiary_sector'])."'";
}
if(isset($_POST['buyer_subsidiary_industry'])&&($_POST['buyer_subsidiary_industry']!="")){
	$suggestion_ins_q.=",buyer_subsidiary_industry='".mysql_real_escape_string($_POST['buyer_subsidiary_industry'])."'";
}
if(isset($_POST['buyer_subsidiary_country'])&&($_POST['buyer_subsidiary_country']!="")){
	$suggestion_ins_q.=",buyer_subsidiary_country='".mysql_real_escape_string($_POST['buyer_subsidiary_country'])."'";
}

$deal_sources = "";
for($i=1;$i<=4;$i++){
	if(isset($_POST['regulatory_links'.$i])&&($_POST['regulatory_links'.$i]!="")){
		$deal_sources.=",".$_POST['regulatory_links'.$i];
	}
}
if($deal_sources!=""){
	//get rid of the initial ,
	$deal_sources = substr($deal_sources,1);
	//create query
	$suggestion_ins_q.=",sources='".mysql_real_escape_string($deal_sources)."'";
}

//local currency
if(isset($_POST['currency'])&&($_POST['currency']!="")){
	$suggestion_ins_q.=",currency='".$_POST['currency']."'";
}
//local currency rate
if(isset($_POST['exchange_rate'])&&($_POST['exchange_rate']!="")){
	$suggestion_ins_q.=",exchange_rate='".$_POST['exchange_rate']."'";
}
//deal value in local currency million
if(isset($_POST['value_in_million_local_currency'])&&($_POST['value_in_million_local_currency']!="")){
	$suggestion_ins_q.=",value_in_million_local_currency='".$_POST['value_in_million_local_currency']."'";
}
//deal value in USD million
if(isset($_POST['value_in_million'])&&($_POST['value_in_million']!="")){
	$suggestion_ins_q.=",value_in_million='".$_POST['value_in_million']."'";
}else{
	if(isset($_POST['value_range_id'])){
		$suggestion_ins_q.=",value_range_id='".$_POST['value_range_id']."'";
	}
}

if(isset($_POST['years_to_maturity'])&&($_POST['years_to_maturity']!="")){
	$suggestion_ins_q.=",years_to_maturity='".mysql_real_escape_string($_POST['years_to_maturity'])."'";
}
if(isset($_POST['maturity_date'])&&($_POST['maturity_date']!="")){
	$suggestion_ins_q.=",maturity_date='".$_POST['maturity_date']."'";
}
if(isset($_POST['coupon'])&&($_POST['coupon']!="")){
	$suggestion_ins_q.=",coupon='".mysql_real_escape_string($_POST['coupon'])."'";
}
if(isset($_POST['current_rating'])&&($_POST['current_rating']!="")){
	$suggestion_ins_q.=",current_rating='".mysql_real_escape_string($_POST['current_rating'])."'";
}
if(isset($_POST['format'])&&($_POST['format']!="")){
	$suggestion_ins_q.=",format='".mysql_real_escape_string($_POST['format'])."'";
}
if(isset($_POST['guarantor'])&&($_POST['guarantor']!="")){
	$suggestion_ins_q.=",guarantor='".mysql_real_escape_string($_POST['guarantor'])."'";
}
if(isset($_POST['margin_including_ratchet'])&&($_POST['margin_including_ratchet']!="")){
	$suggestion_ins_q.=",margin_including_ratchet='".mysql_real_escape_string($_POST['margin_including_ratchet'])."'";
}

if(isset($_POST['collateral'])&&($_POST['collateral']!="")){
	$suggestion_ins_q.=",collateral='".mysql_real_escape_string($_POST['collateral'])."'";
}
if(isset($_POST['seniority'])&&($_POST['seniority']!="")){
	$suggestion_ins_q.=",seniority='".mysql_real_escape_string($_POST['seniority'])."'";
}

if(isset($_POST['year_to_call'])&&($_POST['year_to_call']!="")){
	$suggestion_ins_q.=",year_to_call='".mysql_real_escape_string($_POST['year_to_call'])."'";
}
if(isset($_POST['call_date'])&&($_POST['call_date']!="")){
	$suggestion_ins_q.=",call_date='".$_POST['call_date']."'";
}

/*********************
sng:15/mar/2012
Now we will have only one note box
**********************/

if(isset($_POST['redemption_price'])&&($_POST['redemption_price']!="")){
	$suggestion_ins_q.=",redemption_price='".mysql_real_escape_string($_POST['redemption_price'])."'";
}

if(isset($_POST['base_fee'])&&($_POST['base_fee']!="")){
	$suggestion_ins_q.=",base_fee='".mysql_real_escape_string($_POST['base_fee'])."'";
}
if(isset($_POST['fee_upfront'])&&($_POST['fee_upfront']!="")){
	$suggestion_ins_q.=",fee_upfront='".mysql_real_escape_string($_POST['fee_upfront'])."'";
}
if(isset($_POST['fee_commitment'])&&($_POST['fee_commitment']!="")){
	$suggestion_ins_q.=",fee_commitment='".mysql_real_escape_string($_POST['fee_commitment'])."'";
}
if(isset($_POST['fee_utilisation'])&&($_POST['fee_utilisation']!="")){
	$suggestion_ins_q.=",fee_utilisation='".mysql_real_escape_string($_POST['fee_utilisation'])."'";
}
if(isset($_POST['fee_arrangement'])&&($_POST['fee_arrangement']!="")){
	$suggestion_ins_q.=",fee_arrangement='".mysql_real_escape_string($_POST['fee_arrangement'])."'";
}

/*************
sng:15/mar/2012
The single note data
**************/
if(isset($_POST['additional_deal_details_note'])&&($_POST['additional_deal_details_note']!="")){
	$suggestion_ins_q.=",note_on_deal='".mysql_real_escape_string($_POST['additional_deal_details_note'])."'";
}

if(isset($_POST['additional_partners'])&&($_POST['additional_partners']!="")){
	$suggestion_ins_q.=",additional_partners='".mysql_real_escape_string($_POST['additional_partners'])."'";
}
?>