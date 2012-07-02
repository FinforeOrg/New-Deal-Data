<?php
/*******************
24/jun/2011
handles M&A correction

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

if(isset($_POST['date_rumour'])&&($_POST['date_rumour']!="")){
	$suggestion_ins_q.=",date_rumour='".$_POST['date_rumour']."'";
}

if(isset($_POST['date_announced'])&&($_POST['date_announced']!="")){
	$suggestion_ins_q.=",date_announced='".$_POST['date_announced']."'";
}

if(isset($_POST['date_closed'])&&($_POST['date_closed']!="")){
	$suggestion_ins_q.=",date_closed='".$_POST['date_closed']."'";
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

if(isset($_POST['target_company_name'])&&($_POST['target_company_name']!="")){
	$suggestion_ins_q.=",target_company_name='".mysql_real_escape_string($_POST['target_company_name'])."'";
}
if(isset($_POST['target_sector'])&&($_POST['target_sector']!="")){
	$suggestion_ins_q.=",target_sector='".mysql_real_escape_string($_POST['target_sector'])."'";
}
if(isset($_POST['target_industry'])&&($_POST['target_industry']!="")){
	$suggestion_ins_q.=",target_industry='".mysql_real_escape_string($_POST['target_industry'])."'";
}
if(isset($_POST['target_country'])&&($_POST['target_country']!="")){
	$suggestion_ins_q.=",target_country='".mysql_real_escape_string($_POST['target_country'])."'";
}

if(isset($_POST['seller_company_name'])&&($_POST['seller_company_name']!="")){
	$suggestion_ins_q.=",seller_company_name='".mysql_real_escape_string($_POST['seller_company_name'])."'";
}
if(isset($_POST['seller_sector'])&&($_POST['seller_sector']!="")){
	$suggestion_ins_q.=",seller_sector='".mysql_real_escape_string($_POST['seller_sector'])."'";
}
if(isset($_POST['seller_industry'])&&($_POST['seller_industry']!="")){
	$suggestion_ins_q.=",seller_industry='".mysql_real_escape_string($_POST['seller_industry'])."'";
}
if(isset($_POST['seller_country'])&&($_POST['seller_country']!="")){
	$suggestion_ins_q.=",seller_country='".mysql_real_escape_string($_POST['seller_country'])."'";
}


/*********************
sng:15/mar/2012
Now we will have only one note box
**********************/


if(isset($_POST['implied_equity_value_in_million_local_currency'])&&($_POST['implied_equity_value_in_million_local_currency']!="")){
	$suggestion_ins_q.=",implied_equity_value_in_million_local_currency='".$_POST['implied_equity_value_in_million_local_currency']."'";
}

if(isset($_POST['acquisition_percentage'])&&($_POST['acquisition_percentage']!="")){
	$suggestion_ins_q.=",acquisition_percentage='".$_POST['acquisition_percentage']."'";
}

if(isset($_POST['net_debt_in_million_local_currency'])&&($_POST['net_debt_in_million_local_currency']!="")){
	$suggestion_ins_q.=",net_debt_in_million_local_currency='".$_POST['net_debt_in_million_local_currency']."'";
}

if(isset($_POST['dividend_on_top_of_equity_million_local_curency'])&&($_POST['dividend_on_top_of_equity_million_local_curency']!="")){
	$suggestion_ins_q.=",dividend_on_top_of_equity_million_local_curency='".$_POST['dividend_on_top_of_equity_million_local_curency']."'";
}

if(isset($_POST['enterprise_value_million_local_currency'])&&($_POST['enterprise_value_million_local_currency']!="")){
	$suggestion_ins_q.=",enterprise_value_million_local_currency='".$_POST['enterprise_value_million_local_currency']."'";
}
if(isset($_POST['enterprise_value_million'])&&($_POST['enterprise_value_million']!="")){
	$suggestion_ins_q.=",enterprise_value_million='".$_POST['enterprise_value_million']."'";
}
//implied deal size in local currency
if(isset($_POST['value_in_million_local_currency'])&&($_POST['value_in_million_local_currency']!="")){
	$suggestion_ins_q.=",value_in_million_local_currency='".$_POST['value_in_million_local_currency']."'";
}
//implied deal size in USD
if(isset($_POST['value_in_million'])&&($_POST['value_in_million']!="")){
	$suggestion_ins_q.=",value_in_million='".$_POST['value_in_million']."'";
}else{
	if(isset($_POST['value_range_id'])){
		$suggestion_ins_q.=",value_range_id='".$_POST['value_range_id']."'";
	}
}
if(isset($_POST['payment_type'])&&($_POST['payment_type']!="")){
	$suggestion_ins_q.=",payment_type='".$_POST['payment_type']."'";
}
//takeover type, we expect the id
if(isset($_POST['takeover_id'])&&($_POST['takeover_id']!="")){
	$suggestion_ins_q.=",takeover_id='".$_POST['takeover_id']."'";
}

if(isset($_POST['equity_payment_percent'])&&($_POST['equity_payment_percent']!="")){
	$suggestion_ins_q.=",equity_payment_percent='".$_POST['equity_payment_percent']."'";
}
if(isset($_POST['currency'])&&($_POST['currency']!="")){
	$suggestion_ins_q.=",currency='".$_POST['currency']."'";
}
if(isset($_POST['exchange_rate'])&&($_POST['exchange_rate']!="")){
	$suggestion_ins_q.=",exchange_rate='".$_POST['exchange_rate']."'";
}

if(isset($_POST['target_listed_in_stock_exchange'])&&($_POST['target_listed_in_stock_exchange']=="y")){
	$suggestion_ins_q.=",target_listed_in_stock_exchange='y'";
}else{
	$suggestion_ins_q.=",target_listed_in_stock_exchange='n'";
}
if(isset($_POST['target_stock_exchange_name'])&&($_POST['target_stock_exchange_name']!="")){
	$suggestion_ins_q.=",target_stock_exchange_name='".mysql_real_escape_string($_POST['target_stock_exchange_name'])."'";
}
if(isset($_POST['deal_price_per_share'])&&($_POST['deal_price_per_share']!="")){
	$suggestion_ins_q.=",deal_price_per_share='".$_POST['deal_price_per_share']."'";
}
if(isset($_POST['currency_price_per_share'])&&($_POST['currency_price_per_share']!="")){
	$suggestion_ins_q.=",currency_price_per_share='".$_POST['currency_price_per_share']."'";
}
if(isset($_POST['price_per_share_before_deal_announcement'])&&($_POST['price_per_share_before_deal_announcement']!="")){
	$suggestion_ins_q.=",price_per_share_before_deal_announcement='".$_POST['price_per_share_before_deal_announcement']."'";
}
if(isset($_POST['date_price_per_share_before_deal_announcement'])&&($_POST['date_price_per_share_before_deal_announcement']!="")){
	$suggestion_ins_q.=",date_price_per_share_before_deal_announcement='".$_POST['date_price_per_share_before_deal_announcement']."'";
}
if(isset($_POST['implied_premium_percentage'])&&($_POST['implied_premium_percentage']!="")){
	$suggestion_ins_q.=",implied_premium_percentage='".$_POST['implied_premium_percentage']."'";
}
if(isset($_POST['total_shares_outstanding_million'])&&($_POST['total_shares_outstanding_million']!="")){
	$suggestion_ins_q.=",total_shares_outstanding_million='".$_POST['total_shares_outstanding_million']."'";
}
/*********************
sng:15/mar/2012
Now we will have only one note box
**********************/

if(isset($_POST['termination_fee_million'])&&($_POST['termination_fee_million']!="")){
	$suggestion_ins_q.=",termination_fee_million='".$_POST['termination_fee_million']."'";
}
if(isset($_POST['end_date_termination_fee'])&&($_POST['end_date_termination_fee']!="")){
	$suggestion_ins_q.=",end_date_termination_fee='".$_POST['end_date_termination_fee']."'";
}

/*********************
sng:15/mar/2012
Now we will have only one note box
**********************/

if(isset($_POST['fee_percent_to_sellside_advisor'])&&($_POST['fee_percent_to_sellside_advisor']!="")){
	$suggestion_ins_q.=",fee_percent_to_sellside_advisor='".$_POST['fee_percent_to_sellside_advisor']."'";
}
if(isset($_POST['fee_percent_to_buyside_advisor'])&&($_POST['fee_percent_to_buyside_advisor']!="")){
	$suggestion_ins_q.=",fee_percent_to_buyside_advisor='".$_POST['fee_percent_to_buyside_advisor']."'";
}

if(isset($_POST['revenue_ltm_million'])&&($_POST['revenue_ltm_million']!="")){
	$suggestion_ins_q.=",revenue_ltm_million='".$_POST['revenue_ltm_million']."'";
}
if(isset($_POST['revenue_mry_million'])&&($_POST['revenue_mry_million']!="")){
	$suggestion_ins_q.=",revenue_mry_million='".$_POST['revenue_mry_million']."'";
}
if(isset($_POST['revenue_ny_million'])&&($_POST['revenue_ny_million']!="")){
	$suggestion_ins_q.=",revenue_ny_million='".$_POST['revenue_ny_million']."'";
}

if(isset($_POST['ebitda_ltm_million'])&&($_POST['ebitda_ltm_million']!="")){
	$suggestion_ins_q.=",ebitda_ltm_million='".$_POST['ebitda_ltm_million']."'";
}
if(isset($_POST['ebitda_mry_million'])&&($_POST['ebitda_mry_million']!="")){
	$suggestion_ins_q.=",ebitda_mry_million='".$_POST['ebitda_mry_million']."'";
}
if(isset($_POST['ebitda_ny_million'])&&($_POST['ebitda_ny_million']!="")){
	$suggestion_ins_q.=",ebitda_ny_million='".$_POST['ebitda_ny_million']."'";
}

if(isset($_POST['net_income_ltm_million'])&&($_POST['net_income_ltm_million']!="")){
	$suggestion_ins_q.=",net_income_ltm_million='".$_POST['net_income_ltm_million']."'";
}
if(isset($_POST['net_income_mry_million'])&&($_POST['net_income_mry_million']!="")){
	$suggestion_ins_q.=",net_income_mry_million='".$_POST['net_income_mry_million']."'";
}
if(isset($_POST['net_income_ny_million'])&&($_POST['net_income_ny_million']!="")){
	$suggestion_ins_q.=",net_income_ny_million='".$_POST['net_income_ny_million']."'";
}
if(isset($_POST['date_year_end_of_recent_financial_year'])&&($_POST['date_year_end_of_recent_financial_year']!="")){
	$suggestion_ins_q.=",date_year_end_of_recent_financial_year='".$_POST['date_year_end_of_recent_financial_year']."'";
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