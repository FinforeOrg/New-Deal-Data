<?php
/******************
sng:31/may/2011

This class will contain methods that provide value addition to transactions. This way, the transaction class is not bloated
********************/
/***********
sng:18/dec/2012
since we already include db.php in include/global. we do not need to load again
*************/
class deal_support{
	/*******************
	we are accepting reference so as not to create another array. But be careful and DO NOT change anything
	copied from function show_deal_type_data() of nifty_functions.php
	rather than changing that, we create a new function for our own purpose and make the function name more explicit
	
	sng:1/feb/2012
	We get rid of the word 'Deal'
	***********************/
	public function deal_page_show_deal_type_data_heading(&$deal_data_arr){
		//we show the deal type, and then subtype1 and subtype2 if they are not blank or not n/a
		$data = $deal_data_arr['deal_cat_name'];
		$deal_subtype = "";
		if(($deal_data_arr['deal_subcat1_name']!="")&&($deal_data_arr['deal_subcat1_name']!="n/a")){
			$deal_subtype.=$deal_data_arr['deal_subcat1_name'];
		}
		if(($deal_data_arr['deal_subcat2_name']!="")&&($deal_data_arr['deal_subcat2_name']!="n/a")){
			if($deal_subtype!=""){
				$deal_subtype.=", ";
			}
			$deal_subtype.=$deal_data_arr['deal_subcat2_name'];
		}
		
		if($deal_subtype!=""){
			$data.= ": ".$deal_subtype;
		}
		return $data;
	}
	public function deal_page_show_company_heading(&$deal_data_arr){
		/************
		sng:31/jan/2012
		No longer needed
		******************/
		return "";
		
		$data = $deal_data_arr['company_name'];
		if($deal_data_arr['deal_cat_name']=="M&A"){
			$data.= " acquired ";
			if($deal_data_arr['target_company_name']!=""){
				$data.= $deal_data_arr['target_company_name'];
			}else{
				$data.= "(company unknown)";
			}
			/********
			if the target is a part of larger company
			*******/
			if($deal_data_arr['seller_company_name']!=""){
				$data.= ", sold by ".$deal_data_arr['seller_company_name'];
			}
		}
		return $data;
	}
	/***************
	sng:1/feb/2012
	We have the participants array. It has name,sector,industry,role
	We get the names in another array and then create a csv
	*******************/
	public function deal_page_show_participants_heading(&$deal_participants_arr){
		$temp = array();
		$cnt = count($deal_participants_arr);
		if(0==$cnt){
			return "";
		}
		for($j=0;$j<$cnt;$j++){
			$temp[] = $deal_participants_arr[$j]['company_name'];
		}
		return implode(", ",$temp);
	}
	/********************
	sng:16/sep/2011
	given a deal id, get the deal type/sub type/sub sub type
	get_deal_type-from_deal_id
	
	sng:10/oct/2012
	Moved to transaction_support::get_deal_type
	public function get_deal_type($deal_id,&$data_arr)
	************************************/
	
	public function ma_merger_types(&$data_arr,&$data_count){
		$db = new db();
		
		$q = "select * from ".TP."takeover_type_master";
		$ok = $db->select_query($q);
		if(!$ok){
			return false;
		}
		$data_count = $db->row_count();
		if($data_count == 0){
			//no recs
			return true;
		}
		//recs so
		$data_arr = $db->get_result_set_as_array();
		return true;
	}
	public function admin_get_all_currency(&$data_arr,&$data_count){
		$db = new db();
		
		$q = "select * from ".TP."currency_master order by name";
		$ok = $db->select_query($q);
		if(!$ok){
			return false;
		}
		$data_count = $db->row_count();
		if($data_count == 0){
			//no recs
			return true;
		}
		//recs so
		$data_arr = $db->get_result_set_as_array();
		return true;
	}
	
	/*********
	sng:20/jun/2011
	for admin autocomplete
	************/
	public function ajax_admin_get_currency_suggestions($input,&$data_arr,&$data_count){
		$db = new db();
		
		$q = "select * from ".TP."currency_master where code like '".mysql_real_escape_string($input)."%' order by name";
		$ok = $db->select_query($q);
		if(!$ok){
			return false;
		}
		$data_count = $db->row_count();
		if($data_count == 0){
			//no recs
			return true;
		}
		//recs so
		$data_arr = $db->get_result_set_as_array();
		return true;
	}
	
	public function admin_add_currency($data_arr,&$validation_passed,&$err_arr){
		$db = new db();
		//validation
		$validation_passed = true;
		if($data_arr['code'] == ""){
			$err_arr['code'] = "Please specify the currency code";
			$validation_passed = false;
		}else{
			//check for duplicate currency code
			//convert to uppercase
			$data_arr['code'] = strtoupper($data_arr['code']);
			$q = "select count(*) as cnt from ".TP."currency_master where code='".$data_arr['code']."'";
			$ok = $db->select_query($q);
			
			if(!$ok){
				return false;
			}
			$row = $db->get_row();
			if($row['cnt'] > 0){
				//this country name exists
				$err_arr['code'] = "This currency has already bee added.";
				$validation_passed = false;
			}
		}
		//no validation for name
		
		/////////////////////////////////////
		if(!$validation_passed){
			//no need to proceed
			return true;
		}
		///////////////////////////////////////////////////////
		//insert data
		$q = "insert into ".TP."currency_master set code='".mysql_real_escape_string($data_arr['code'])."',name='".mysql_real_escape_string($data_arr['name'])."'";
		$ok = $db->mod_query($q);
		if(!$ok){
			return false;
		}
		/////////////////
		//data inserted
		$validation_passed = true;
		return true;
	}
	
	public function admin_edit_currency($id,$data_arr,&$validation_passed){
		$db = new db();
		//validation
		$validation_passed = true;
		if($data_arr['code'] == ""){
			$validation_passed = false;
		}else{
			//check for duplicate currency code
			//convert to uppercase
			$data_arr['code'] = strtoupper($data_arr['code']);
			$q = "select count(*) as cnt from ".TP."currency_master where code='".$data_arr['code']."' and id!='".$id."'";
			$ok = $db->select_query($q);
			
			if(!$ok){
				return false;
			}
			$row = $db->get_row();
			if($row['cnt'] > 0){
				//this country name exists
				$validation_passed = false;
			}
		}
		//no validation for name
		
		/////////////////////////////////////
		if(!$validation_passed){
			//no need to proceed
			return true;
		}
		///////////////////////////////////////////////////////
		//insert data
		$q = "update ".TP."currency_master set code='".mysql_real_escape_string($data_arr['code'])."',name='".mysql_real_escape_string($data_arr['name'])."' where id='".$id."'";
		$ok = $db->mod_query($q);
		if(!$ok){
			return false;
		}
		/////////////////
		//data inserted
		$validation_passed = true;
		return true;
	}
	
	public function admin_get_all_stock_exchange(&$data_arr,&$data_count){
		$db = new db();
		
		$q = "select * from ".TP."stock_exchange_master order by name";
		$ok = $db->select_query($q);
		if(!$ok){
			return false;
		}
		$data_count = $db->row_count();
		if($data_count == 0){
			//no recs
			return true;
		}
		//recs so
		$data_arr = $db->get_result_set_as_array();
		return true;
	}
	
	/*********
	sng:20/jun/2011
	for admin autocomplete
	************/
	public function ajax_admin_get_stock_exchange_suggestions($input,&$data_arr,&$data_count){
		$db = new db();
		
		$q = "select * from ".TP."stock_exchange_master where name like '".mysql_real_escape_string($input)."%' order by name";
		$ok = $db->select_query($q);
		if(!$ok){
			//echo $q;
			return false;
		}
		$data_count = $db->row_count();
		if($data_count == 0){
			//no recs
			return true;
		}
		//recs so
		$data_arr = $db->get_result_set_as_array();
		return true;
	}
	
	public function admin_add_stock_exchange($data_arr,&$validation_passed,&$err_arr){
		$db = new db();
		//validation
		$validation_passed = true;
		if($data_arr['name'] == ""){
			$err_arr['name'] = "Please specify the stock exchange name";
			$validation_passed = false;
		}else{
			//check for duplicate
			$q = "select count(*) as cnt from ".TP."stock_exchange_master where name='".mysql_real_escape_string($data_arr['name'])."'";
			$ok = $db->select_query($q);
			
			if(!$ok){
				return false;
			}
			$row = $db->get_row();
			if($row['cnt'] > 0){
				//this name exists
				$err_arr['name'] = "This stock exchange has already been added.";
				$validation_passed = false;
			}
		}
		
		
		/////////////////////////////////////
		if(!$validation_passed){
			//no need to proceed
			return true;
		}
		///////////////////////////////////////////////////////
		//insert data
		$q = "insert into ".TP."stock_exchange_master set name='".mysql_real_escape_string($data_arr['name'])."'";
		$ok = $db->mod_query($q);
		if(!$ok){
			return false;
		}
		/////////////////
		//data inserted
		$validation_passed = true;
		return true;
	}
	
	public function admin_edit_stock_exchange($id,$data_arr,&$validation_passed){
		$db = new db();
		//validation
		$validation_passed = true;
		if($data_arr['name'] == ""){
			$validation_passed = false;
		}else{
			//check for duplicate
			$q = "select count(*) as cnt from ".TP."stock_exchange_master where name='".mysql_real_escape_string($data_arr['name'])."' and id!='".$id."'";
			$ok = $db->select_query($q);
			
			if(!$ok){
				return false;
			}
			$row = $db->get_row();
			if($row['cnt'] > 0){
				//this country name exists
				$validation_passed = false;
			}
		}
		//no validation for name
		
		/////////////////////////////////////
		if(!$validation_passed){
			//no need to proceed
			return true;
		}
		///////////////////////////////////////////////////////
		//insert data
		$q = "update ".TP."stock_exchange_master set name='".mysql_real_escape_string($data_arr['name'])."' where id='".$id."'";
		$ok = $db->mod_query($q);
		if(!$ok){
			return false;
		}
		/////////////////
		//data inserted
		$validation_passed = true;
		return true;
	}
	/************
	given a deal, there can be many corrections posted. when editing the deal, admin wants to know all the corrections, sent
	for the given deal, for the particular data, say date_announced
	
	This can be used ONLY for a single field
	************/
	public function admin_fetch_data_correction_on_deal($deal_id,$data_name,&$result_arr,&$result_count){
		$db = new db();
		
		$where = "";
		$nl2br_required = false;
		/********
		it may happen that the member has not given any suggestion for the particular field for this deal
		********/
		$q = "select";
		/*****************
		map data to columns
		****************/
		if($data_name == "deal_type"){
			/*********
			this is used only for M&A, and that too, to get the subtype, Completed or Pending
			/deal_page_detail_ma.php
			********/
			$q.=" deal_subcat1_name as data";
			$where = "deal_subcat1_name !=''";
		}elseif($data_name == "date_rumour"){
			$q.=" date_rumour as data";
			$where = "date_rumour !='0000-00-00'";
		}elseif($data_name == "date_announced"){
			$q.=" date_announced as data";
			$where = "date_announced !='0000-00-00'";
		}elseif($data_name == "date_ex_rights"){
			$q.=" date_ex_rights as data";
			$where = "date_ex_rights !='0000-00-00'";
		}elseif($data_name == "date_closed"){
			$q.=" date_closed as data";
			$where = "date_closed !='0000-00-00'";
		}elseif($data_name == "deal_company"){
			$q.=" concat(deal_company_name,', ',deal_company_country,', ',deal_company_sector,', ',deal_company_industry) as data";
			$where = "deal_company_name !=''";
		}elseif($data_name == "buyer_subsidiary_name"){
			$q.=" buyer_subsidiary_name as data";
			$where = "buyer_subsidiary_name !=''";
		}elseif($data_name == "buyer_subsidiary_country"){
			$q.=" buyer_subsidiary_country as data";
			$where = "buyer_subsidiary_country !=''";
		}elseif($data_name == "buyer_subsidiary_sector"){
			$q.=" buyer_subsidiary_sector as data";
			$where = "buyer_subsidiary_sector !=''";
		}elseif($data_name == "buyer_subsidiary_industry"){
			$q.=" buyer_subsidiary_industry as data";
			$where = "buyer_subsidiary_industry !=''";
		}elseif($data_name == "sources"){
			$q.=" sources as data";
			$where = "sources !=''";
			$nl2br_required = true;
		}elseif($data_name == "note"){
			$q.=" concat('additional detail on buyer/seller/target<br />',note_on_buyer_target_seller,'<br />additional detail on deal valuation<br />',additional_text_on_deal_value,'<br />additional detail on termination<br />',text_on_termination_fee,'<br />Note on deal<br />',note_on_deal,'<br />Call/put option<br />',note_on_call_put) as data";
			$where = "(note_on_buyer_target_seller !='' OR additional_text_on_deal_value!='' OR text_on_termination_fee!='' OR note_on_deal!='' OR note_on_call_put!='')";
			$nl2br_required = true;
		}elseif($data_name == "additional_partners"){
			$q.=" additional_partners as data";
			$where = "additional_partners !=''";
			$nl2br_required = true;
		}elseif($data_name == "target_company_name"){
			$q.=" target_company_name as data";
			$where = "target_company_name !=''";
		}elseif($data_name == "target_country"){
			$q.=" target_country as data";
			$where = "target_country !=''";
		}elseif($data_name == "target_sector"){
			$q.=" target_sector as data";
			$where = "target_sector !=''";
		}elseif($data_name == "target_industry"){
			$q.=" target_industry as data";
			$where = "target_industry !=''";
		}elseif($data_name == "seller_company_name"){
			$q.=" seller_company_name as data";
			$where = "seller_company_name !=''";
		}elseif($data_name == "seller_country"){
			$q.=" seller_country as data";
			$where = "seller_country !=''";
		}elseif($data_name == "seller_sector"){
			$q.=" seller_sector as data";
			$where = "seller_sector !=''";
		}elseif($data_name == "seller_industry"){
			$q.=" seller_industry as data";
			$where = "seller_industry !=''";
		}elseif($data_name == "takeover"){
			$q.=" takeover_name as data";
			$where = "s.takeover_id !='0'";
		}elseif($data_name == "termination_fee_million"){
			$q.=" termination_fee_million as data";
			$where = "termination_fee_million !='0'";
		}elseif($data_name == "end_date_termination_fee"){
			$q.=" end_date_termination_fee as data";
			$where = "end_date_termination_fee !='0000-00-00'";
		}elseif($data_name == "fee_percent_to_sellside_advisor"){
			$q.=" fee_percent_to_sellside_advisor as data";
			$where = "fee_percent_to_sellside_advisor !='0'";
		}elseif($data_name == "fee_percent_to_buyside_advisor"){
			$q.=" fee_percent_to_buyside_advisor as data";
			$where = "fee_percent_to_buyside_advisor !='0'";
		}elseif($data_name == "revenue_ltm_million"){
			$q.=" revenue_ltm_million as data";
			$where = "revenue_ltm_million !='0'";
		}elseif($data_name == "revenue_mry_million"){
			$q.=" revenue_mry_million as data";
			$where = "revenue_mry_million !='0'";
		}elseif($data_name == "revenue_ny_million"){
			$q.=" revenue_ny_million as data";
			$where = "revenue_ny_million !='0'";
		}elseif($data_name == "ebitda_ltm_million"){
			$q.=" ebitda_ltm_million as data";
			$where = "ebitda_ltm_million !='0'";
		}elseif($data_name == "ebitda_mry_million"){
			$q.=" ebitda_mry_million as data";
			$where = "ebitda_mry_million !='0'";
		}elseif($data_name == "ebitda_ny_million"){
			$q.=" ebitda_ny_million as data";
			$where = "ebitda_ny_million !='0'";
		}elseif($data_name == "net_income_ltm_million"){
			$q.=" net_income_ltm_million as data";
			$where = "net_income_ltm_million !='0'";
		}elseif($data_name == "net_income_mry_million"){
			$q.=" net_income_mry_million as data";
			$where = "net_income_mry_million !='0'";
		}elseif($data_name == "net_income_ny_million"){
			$q.=" net_income_ny_million as data";
			$where = "net_income_ny_million !='0'";
		}elseif($data_name == "date_year_end_of_recent_financial_year"){
			$q.=" date_year_end_of_recent_financial_year as data";
			$where = "date_year_end_of_recent_financial_year !='0000-00-00'";
		}elseif($data_name == "payment_type"){
			$q.=" payment_type as data";
			$where = "payment_type !=''";
		}elseif($data_name == "equity_payment_percent"){
			$q.=" equity_payment_percent as data";
			$where = "equity_payment_percent !='0'";
		}elseif($data_name == "currency"){
			$q.=" currency as data";
			$where = "currency !=''";
		}elseif($data_name == "exchange_rate"){
			$q.=" exchange_rate as data";
			$where = "exchange_rate !='0'";
		}elseif($data_name == "target_listed_in_stock_exchange"){
			$q.=" target_listed_in_stock_exchange as data";
			$where = "target_listed_in_stock_exchange !=''";
		}elseif($data_name == "target_stock_exchange_name"){
			$q.=" target_stock_exchange_name as data";
			$where = "target_stock_exchange_name !=''";
		}elseif($data_name == "currency_price_per_share"){
			$q.=" currency_price_per_share as data";
			$where = "currency_price_per_share !=''";
		}elseif($data_name == "deal_price_per_share"){
			$q.=" deal_price_per_share as data";
			$where = "deal_price_per_share !='0'";
		}elseif($data_name == "price_per_share_before_deal_announcement"){
			$q.=" price_per_share_before_deal_announcement as data";
			$where = "price_per_share_before_deal_announcement !='0'";
		}elseif($data_name == "date_price_per_share_before_deal_announcement"){
			$q.=" date_price_per_share_before_deal_announcement as data";
			$where = "date_price_per_share_before_deal_announcement !='0000-00-00'";
		}elseif($data_name == "implied_premium_percentage"){
			$q.=" implied_premium_percentage as data";
			$where = "implied_premium_percentage !='0'";
		}elseif($data_name == "total_shares_outstanding_million"){
			$q.=" total_shares_outstanding_million as data";
			$where = "total_shares_outstanding_million !='0'";
		}elseif($data_name == "implied_equity_value_in_million_local_currency"){
			$q.=" implied_equity_value_in_million_local_currency as data";
			$where = "implied_equity_value_in_million_local_currency !='0'";
		}elseif($data_name == "acquisition_percentage"){
			$q.=" acquisition_percentage as data";
			$where = "acquisition_percentage !='0'";
		}elseif($data_name == "net_debt_in_million_local_currency"){
			$q.=" net_debt_in_million_local_currency as data";
			$where = "net_debt_in_million_local_currency !='0'";
		}elseif($data_name == "dividend_on_top_of_equity_million_local_curency"){
			$q.=" dividend_on_top_of_equity_million_local_curency as data";
			$where = "dividend_on_top_of_equity_million_local_curency !='0'";
		}elseif($data_name == "enterprise_value_million_local_currency"){
			$q.=" enterprise_value_million_local_currency as data";
			$where = "enterprise_value_million_local_currency !='0'";
		}elseif($data_name == "enterprise_value_million"){
			$q.=" enterprise_value_million as data";
			$where = "enterprise_value_million !='0'";
		}elseif($data_name == "value_in_million_local_currency"){
			$q.=" value_in_million_local_currency as data";
			$where = "value_in_million_local_currency !='0'";
		}elseif($data_name == "value_in_million"){
			/******************
			sng:15/mar/2012
			For deal value suggestion, member can send exact value or deal value range.
			We get both and later, set the 'data'
			Be sure to group the WHERE clause because of the OR inside the clause
			******************/
			$q.=" value_in_million,s.value_range_id,vrm.display_text as fuzzy_value,'v' as data";
			$where = "(value_in_million !='0' OR s.value_range_id!='0')";
		}elseif($data_name == "years_to_maturity"){
			$q.=" years_to_maturity as data";
			$where = "years_to_maturity !=''";
		}elseif($data_name == "maturity_date"){
			$q.=" maturity_date as data";
			/**********************************************
			sng:18/aug/2011
			maturity_date !='' OR maturity_date!='n/a' OR maturity_date!='0000-00-00'
			is incorrect. all 3 conditions has to be true	
			*****************************************************/
			$where = "(maturity_date !='' AND maturity_date!='n/a' AND maturity_date!='0000-00-00')";
		}elseif($data_name == "coupon"){
			$q.=" coupon as data";
			$where = "coupon !=''";
		}elseif($data_name == "margin_including_ratchet"){
			$q.=" margin_including_ratchet as data";
			$where = "margin_including_ratchet !=''";
		}elseif($data_name == "current_rating"){
			$q.=" current_rating as data";
			$where = "current_rating !=''";
		}elseif($data_name == "format"){
			$q.=" format as data";
			$where = "format !=''";
		}elseif($data_name == "guarantor"){
			$q.=" guarantor as data";
			$where = "guarantor !=''";
		}elseif($data_name == "collateral"){
			$q.=" collateral as data";
			$where = "collateral !=''";
		}elseif($data_name == "seniority"){
			$q.=" seniority as data";
			$where = "seniority !=''";
		}elseif($data_name == "base_fee"){
			$q.=" base_fee as data";
			$where = "base_fee !='0.0'";
		}elseif($data_name == "incentive_fee"){
			$q.=" incentive_fee as data";
			$where = "incentive_fee !='0.0'";
		}elseif($data_name == "fee_upfront"){
			$q.=" fee_upfront as data";
			$where = "fee_upfront !='0.0'";
		}elseif($data_name == "fee_commitment"){
			$q.=" fee_commitment as data";
			$where = "fee_commitment !='0.0'";
		}elseif($data_name == "fee_utilisation"){
			$q.=" fee_utilisation as data";
			$where = "fee_utilisation !='0.0'";
		}elseif($data_name == "fee_arrangement"){
			$q.=" fee_arrangement as data";
			$where = "fee_arrangement !='0.0'";
		}elseif($data_name == "year_to_call"){
			$q.=" year_to_call as data";
			$where = "year_to_call !=''";
		}elseif($data_name == "call_date"){
			$q.=" call_date as data";
			$where = "call_date !='0000-00-00'";
		}elseif($data_name == "redemption_price"){
			$q.=" redemption_price as data";
			$where = "redemption_price !=''";
		}elseif($data_name == "underlying_security_is_different"){
			$q.=" underlying_security_is_different as data";
			$where = "underlying_security_is_different !=''";
		}elseif($data_name == "company_security"){
			$q.=" company_security as data";
			$where = "company_security !=''";
		}elseif($data_name == "sector_security"){
			$q.=" sector_security as data";
			$where = "sector_security !=''";
		}elseif($data_name == "industry_security"){
			$q.=" industry_security as data";
			$where = "industry_security !=''";
		}elseif($data_name == "dividend_protection"){
			$q.=" dividend_protection as data";
			$where = "dividend_protection !=''";
		}elseif($data_name == "reference_price"){
			$q.=" reference_price as data";
			$where = "reference_price !='0.0'";
		}elseif($data_name == "conversion_price"){
			$q.=" conversion_price as data";
			$where = "conversion_price !='0.0'";
		}elseif($data_name == "conversion_premia_percent"){
			$q.=" conversion_premia_percent as data";
			$where = "conversion_premia_percent !='0.0'";
		}elseif($data_name == "num_shares_underlying_million"){
			$q.=" num_shares_underlying_million as data";
			$where = "num_shares_underlying_million !='0.0'";
		}elseif($data_name == "curr_num_shares_outstanding_million"){
			$q.=" curr_num_shares_outstanding_million as data";
			$where = "curr_num_shares_outstanding_million !='0.0'";
		}elseif($data_name == "avg_daily_trading_vol_million"){
			$q.=" avg_daily_trading_vol_million as data";
			$where = "avg_daily_trading_vol_million !='0.0'";
		}elseif($data_name == "shares_underlying_vs_adtv_ratio"){
			$q.=" shares_underlying_vs_adtv_ratio as data";
			$where = "shares_underlying_vs_adtv_ratio !='0.0'";
		}elseif($data_name == "discount_to_last"){
			$q.=" discount_to_last as data";
			$where = "discount_to_last !='0.0'";
		}elseif($data_name == "discount_to_terp"){
			$q.=" discount_to_terp as data";
			$where = "discount_to_terp !='0.0'";
		}elseif($data_name == "free_float_percent"){
			$q.=" free_float_percent as data";
			$where = "free_float_percent !='0.0'";
		}elseif($data_name == "num_shares_outstanding_after_deal_million"){
			$q.=" num_shares_outstanding_after_deal_million as data";
			$where = "num_shares_outstanding_after_deal_million !='0.0'";
		}elseif($data_name == "num_secondary_shares_million"){
			$q.=" num_secondary_shares_million as data";
			$where = "num_secondary_shares_million !='0.0'";
		}elseif($data_name == "num_primary_shares_million"){
			$q.=" num_primary_shares_million as data";
			$where = "num_primary_shares_million !='0.0'";
		}elseif($data_name == "offer_price"){
			$q.=" offer_price as data";
			$where = "offer_price !='0.0'";
		}elseif($data_name == "1_day_price_change"){
			$q.=" 1_day_price_change as data";
			$where = "1_day_price_change !='0.0'";
		}elseif($data_name == "date_first_trading"){
			$q.=" date_first_trading as data";
			$where = "date_first_trading !='0000-00-00'";
		}elseif($data_name == "price_at_end_of_first_day"){
			$q.=" price_at_end_of_first_day as data";
			$where = "price_at_end_of_first_day !='0.0'";
		}elseif($data_name == "greenshoe_included"){
			$q.=" greenshoe_included as data";
			$where = "greenshoe_included !=''";
		}elseif($data_name == "subscription_ratio"){
			$q.=" subscription_ratio as data";
			$where = "subscription_ratio !=''";
		}elseif($data_name == "terp"){
			$q.=" terp as data";
			$where = "terp !='0.0'";
		}elseif($data_name == "subscription_rate_percent"){
			$q.=" subscription_rate_percent as data";
			$where = "subscription_rate_percent !='0.0'";
		}elseif($data_name == "rump_placement"){
			$q.=" rump_placement as data";
			$where = "rump_placement !=''";
		}elseif($data_name == "num_shares_sold_in_rump_million"){
			$q.=" num_shares_sold_in_rump_million as data";
			$where = "num_shares_sold_in_rump_million !='0.0'";
		}elseif($data_name == "price_per_share_in_rump"){
			$q.=" price_per_share_in_rump as data";
			$where = "price_per_share_in_rump !='0.0'";
		}elseif($data_name == "ipo_stock_exchange"){
			$q.=" ipo_stock_exchange as data";
			$where = "ipo_stock_exchange !=''";
		}elseif($data_name == "currency_reference_price"){
			$q.=" currency_reference_price as data";
			$where = "currency_reference_price !=''";
		}
		/************
		sng:15/mar/2012
		We do a left join on transaction_value_range_master since we now have members specifying deal value as a range instead of number
		*******************/
		$q.=",date_suggested,m.f_name,m.l_name,m.designation,w.name as work_company from ".TP."transaction_suggestions as s left join ".TP."member as m on(s.suggested_by=m.mem_id) left join ".TP."company as w on(m.company_id=w.company_id) left join ".TP."takeover_type_master as tm on(s.takeover_id=tm.takeover_id)  left join ".TP."transaction_value_range_master as vrm on(s.value_range_id=vrm.value_range_id) where deal_id='".$deal_id."' AND ".$where." order by date_suggested";
		$success = $db->select_query($q);
		
		if(!$success){
			//db error
			//echo $q;
			//echo $db->error();
			return false;
		}
		$result_count = $db->row_count();
		if(0 == $result_count){
			//no data
			return true;
		}
		$result_arr = $db->get_result_set_as_array();
		if($nl2br_required){
			for($i=0;$i<$result_count;$i++){
				$result_arr[$i]['data'] = nl2br($result_arr[$i]['data']);
			}
		}
		/**********************
		sng:15/mar/2012
		if the requeat is for deal value, we can have specific number or a value range
		We give precedence to exact value
		*************************/
		if($data_name == "value_in_million"){
			for($i=0;$i<$result_count;$i++){
				if($result_arr[$i]['value_in_million'] > 0){
					$result_arr[$i]['data'] = $result_arr[$i]['value_in_million'];
				}else{
					$result_arr[$i]['data'] = $result_arr[$i]['fuzzy_value'];
				}
			}
		}
		return true;
	}
	
	/************
	sng: 17/aug/2011
	given a deal, there can be many corrections posted. when editing the deal, admin wants to know whether there is any corrections, sent
	for the given deal, for the particular data, say date_announced
	
	This can be used ONLY for a single field
	************/
	public function admin_has_data_correction_on_deal($deal_id,$data_name){
		$db = new db();
		
		$where = "";
		
		/********
		it may happen that the member has not given any suggestion for the particular field for this deal
		********/
		$q = "select count(*) as cnt";
		/*****************
		map data to columns
		****************/
		if($data_name == "deal_type"){
			/*********
			this is used only for M&A, and that too, to get the subtype, Completed or Pending
			/deal_page_detail_ma.php
			********/
			
			$where = "deal_subcat1_name !=''";
		}elseif($data_name == "date_rumour"){
			
			$where = "date_rumour !='0000-00-00'";
		}elseif($data_name == "date_announced"){
			$where = "date_announced !='0000-00-00'";
		}elseif($data_name == "date_ex_rights"){
			$where = "date_ex_rights !='0000-00-00'";
		}elseif($data_name == "date_closed"){
			$where = "date_closed !='0000-00-00'";
		}elseif($data_name == "deal_company"){
			$where = "deal_company_name !=''";
		}elseif($data_name == "buyer_subsidiary_name"){
			$where = "buyer_subsidiary_name !=''";
		}elseif($data_name == "buyer_subsidiary_country"){
			$where = "buyer_subsidiary_country !=''";
		}elseif($data_name == "buyer_subsidiary_sector"){
			$where = "buyer_subsidiary_sector !=''";
		}elseif($data_name == "buyer_subsidiary_industry"){
			$where = "buyer_subsidiary_industry !=''";
		}elseif($data_name == "sources"){
			$where = "sources !=''";
			
		}elseif($data_name == "additional_partners"){
			$where = "additional_partners !=''";
			
		}elseif($data_name == "target_company_name"){
			$where = "target_company_name !=''";
		}elseif($data_name == "target_country"){
			$where = "target_country !=''";
		}elseif($data_name == "target_sector"){
			$where = "target_sector !=''";
		}elseif($data_name == "target_industry"){
			$where = "target_industry !=''";
		}elseif($data_name == "seller_company_name"){
			$where = "seller_company_name !=''";
		}elseif($data_name == "seller_country"){
			$where = "seller_country !=''";
		}elseif($data_name == "seller_sector"){
			$where = "seller_sector !=''";
		}elseif($data_name == "seller_industry"){
			$where = "seller_industry !=''";
		}elseif($data_name == "takeover"){
			$where = "s.takeover_id !='0'";
		}elseif($data_name == "termination_fee_million"){
			$where = "termination_fee_million !='0'";
		}elseif($data_name == "end_date_termination_fee"){
			$where = "end_date_termination_fee !='0000-00-00'";
		}elseif($data_name == "fee_percent_to_sellside_advisor"){
			$where = "fee_percent_to_sellside_advisor !='0'";
		}elseif($data_name == "fee_percent_to_buyside_advisor"){
			$where = "fee_percent_to_buyside_advisor !='0'";
		}elseif($data_name == "revenue_ltm_million"){
			$where = "revenue_ltm_million !='0'";
		}elseif($data_name == "revenue_mry_million"){
			$where = "revenue_mry_million !='0'";
		}elseif($data_name == "revenue_ny_million"){
			$where = "revenue_ny_million !='0'";
		}elseif($data_name == "ebitda_ltm_million"){
			$where = "ebitda_ltm_million !='0'";
		}elseif($data_name == "ebitda_mry_million"){
			$where = "ebitda_mry_million !='0'";
		}elseif($data_name == "ebitda_ny_million"){
			$where = "ebitda_ny_million !='0'";
		}elseif($data_name == "net_income_ltm_million"){
			$where = "net_income_ltm_million !='0'";
		}elseif($data_name == "net_income_mry_million"){
			$where = "net_income_mry_million !='0'";
		}elseif($data_name == "net_income_ny_million"){
			$where = "net_income_ny_million !='0'";
		}elseif($data_name == "date_year_end_of_recent_financial_year"){
			$where = "date_year_end_of_recent_financial_year !='0000-00-00'";
		}elseif($data_name == "payment_type"){
			$where = "payment_type !=''";
		}elseif($data_name == "equity_payment_percent"){
			$where = "equity_payment_percent !='0'";
		}elseif($data_name == "currency"){
			$where = "currency !=''";
		}elseif($data_name == "exchange_rate"){
			$where = "exchange_rate !='0'";
		}elseif($data_name == "target_listed_in_stock_exchange"){
			$where = "target_listed_in_stock_exchange !=''";
		}elseif($data_name == "target_stock_exchange_name"){
			$where = "target_stock_exchange_name !=''";
		}elseif($data_name == "currency_price_per_share"){
			$where = "currency_price_per_share !=''";
		}elseif($data_name == "deal_price_per_share"){
			$where = "deal_price_per_share !='0'";
		}elseif($data_name == "price_per_share_before_deal_announcement"){
			$where = "price_per_share_before_deal_announcement !='0'";
		}elseif($data_name == "date_price_per_share_before_deal_announcement"){
			$where = "date_price_per_share_before_deal_announcement !='0000-00-00'";
		}elseif($data_name == "implied_premium_percentage"){
			$where = "implied_premium_percentage !='0'";
		}elseif($data_name == "total_shares_outstanding_million"){
			$where = "total_shares_outstanding_million !='0'";
		}elseif($data_name == "implied_equity_value_in_million_local_currency"){
			$where = "implied_equity_value_in_million_local_currency !='0'";
		}elseif($data_name == "acquisition_percentage"){
			$where = "acquisition_percentage !='0'";
		}elseif($data_name == "net_debt_in_million_local_currency"){
			$where = "net_debt_in_million_local_currency !='0'";
		}elseif($data_name == "dividend_on_top_of_equity_million_local_curency"){
			$where = "dividend_on_top_of_equity_million_local_curency !='0'";
		}elseif($data_name == "enterprise_value_million_local_currency"){
			$where = "enterprise_value_million_local_currency !='0'";
		}elseif($data_name == "enterprise_value_million"){
			$where = "enterprise_value_million !='0'";
		}elseif($data_name == "value_in_million_local_currency"){
			$where = "value_in_million_local_currency !='0'";
		}elseif($data_name == "value_in_million"){
			/*****************
			sng:15/mar/2012
			Now we can have value range instead of specific value.
			Note: we do not consider the suggestions where deal value range was specified as 'undefined'
			because we cannot distinguish between 'undefined' and no suggestion for deal value
			Be sure to group the WHERE clause because of the OR inside the clause
			*******************/
			$where = "(value_in_million !='0' OR value_range_id!='0')";
		}elseif($data_name == "years_to_maturity"){
			$where = "years_to_maturity !=''";
		}elseif($data_name == "maturity_date"){
			/***************************************
			sng:18/aug/2011
			maturity_date !='' OR maturity_date!='n/a' OR maturity_date!='0000-00-00'
			is incorrect. all 3 conditions has to be true
			*********************************************/
			$where = "(maturity_date !='' AND maturity_date!='n/a' AND maturity_date!='0000-00-00')";
		}elseif($data_name == "coupon"){
			$where = "coupon !=''";
		}elseif($data_name == "margin_including_ratchet"){
			$where = "margin_including_ratchet !=''";
		}elseif($data_name == "current_rating"){
			$where = "current_rating !=''";
		}elseif($data_name == "format"){
			$where = "format !=''";
		}elseif($data_name == "guarantor"){
			$where = "guarantor !=''";
		}elseif($data_name == "collateral"){
			$where = "collateral !=''";
		}elseif($data_name == "seniority"){
			$where = "seniority !=''";
		}elseif($data_name == "base_fee"){
			$where = "base_fee !='0.0'";
		}elseif($data_name == "incentive_fee"){
			$where = "incentive_fee !='0.0'";
		}elseif($data_name == "fee_upfront"){
			$where = "fee_upfront !='0.0'";
		}elseif($data_name == "fee_commitment"){
			$where = "fee_commitment !='0.0'";
		}elseif($data_name == "fee_utilisation"){
			$where = "fee_utilisation !='0.0'";
		}elseif($data_name == "fee_arrangement"){
			$where = "fee_arrangement !='0.0'";
		}elseif($data_name == "year_to_call"){
			$where = "year_to_call !=''";
		}elseif($data_name == "call_date"){
			$where = "call_date !='0000-00-00'";
		}elseif($data_name == "redemption_price"){
			$where = "redemption_price !=''";
		}elseif($data_name == "underlying_security_is_different"){
			$where = "underlying_security_is_different !=''";
		}elseif($data_name == "company_security"){
			$where = "company_security !=''";
		}elseif($data_name == "sector_security"){
			$where = "sector_security !=''";
		}elseif($data_name == "industry_security"){
			$where = "industry_security !=''";
		}elseif($data_name == "dividend_protection"){
			$where = "dividend_protection !=''";
		}elseif($data_name == "reference_price"){
			$where = "reference_price !='0.0'";
		}elseif($data_name == "conversion_price"){
			$where = "conversion_price !='0.0'";
		}elseif($data_name == "conversion_premia_percent"){
			$where = "conversion_premia_percent !='0.0'";
		}elseif($data_name == "num_shares_underlying_million"){
			$where = "num_shares_underlying_million !='0.0'";
		}elseif($data_name == "curr_num_shares_outstanding_million"){
			$where = "curr_num_shares_outstanding_million !='0.0'";
		}elseif($data_name == "avg_daily_trading_vol_million"){
			$where = "avg_daily_trading_vol_million !='0.0'";
		}elseif($data_name == "shares_underlying_vs_adtv_ratio"){
			$where = "shares_underlying_vs_adtv_ratio !='0.0'";
		}elseif($data_name == "discount_to_last"){
			$where = "discount_to_last !='0.0'";
		}elseif($data_name == "discount_to_terp"){
			$where = "discount_to_terp !='0.0'";
		}elseif($data_name == "free_float_percent"){
			$where = "free_float_percent !='0.0'";
		}elseif($data_name == "num_shares_outstanding_after_deal_million"){
			$where = "num_shares_outstanding_after_deal_million !='0.0'";
		}elseif($data_name == "num_secondary_shares_million"){
			$where = "num_secondary_shares_million !='0.0'";
		}elseif($data_name == "num_primary_shares_million"){
			$where = "num_primary_shares_million !='0.0'";
		}elseif($data_name == "offer_price"){
			$where = "offer_price !='0.0'";
		}elseif($data_name == "1_day_price_change"){
			$where = "1_day_price_change !='0.0'";
		}elseif($data_name == "date_first_trading"){
			$where = "date_first_trading !='0000-00-00'";
		}elseif($data_name == "price_at_end_of_first_day"){
			$where = "price_at_end_of_first_day !='0.0'";
		}elseif($data_name == "greenshoe_included"){
			$where = "greenshoe_included !=''";
		}elseif($data_name == "subscription_ratio"){
			$where = "subscription_ratio !=''";
		}elseif($data_name == "terp"){
			$where = "terp !='0.0'";
		}elseif($data_name == "subscription_rate_percent"){
			$where = "subscription_rate_percent !='0.0'";
		}elseif($data_name == "rump_placement"){
			$where = "rump_placement !=''";
		}elseif($data_name == "num_shares_sold_in_rump_million"){
			$where = "num_shares_sold_in_rump_million !='0.0'";
		}elseif($data_name == "price_per_share_in_rump"){
			$where = "price_per_share_in_rump !='0.0'";
		}elseif($data_name == "ipo_stock_exchange"){
			$where = "ipo_stock_exchange !=''";
		}elseif($data_name == "currency_reference_price"){
			$where = "currency_reference_price !=''";
		}
		
		$q.=" from ".TP."transaction_suggestions  where deal_id='".$deal_id."' AND ".$where;
		$success = $db->select_query($q);
		if(!$success){
			//db error
			//echo $q;
			return false;
		}
		$row = $db->get_row();
		if($row['cnt'] == 0) return false;
		else return true;
	}
	
	/************************
	sng:2/aug/2011
	function to get the notes sent as part of deal edit by the members
	
	sng:15/mar/2012
	Now we no longer store deal submission as suggestion.
	As for sending correction, we now use only one 'note' textbox.
	The front end code store the note detail in note_on_deal
	*****************************/
	public function admin_fetch_note_correction_on_deal($deal_id,&$result_arr,&$result_count){
		
		$db = new db();
		
		
		//get the proper columns based on deal type, but first the common fragment
		$q = "select 'Note on deal' as `label`,note_on_deal as `data`,date_suggested,m.f_name,m.l_name,m.designation,w.name as work_company from ".TP."transaction_suggestions as s left join ".TP."member as m on(s.suggested_by=m.mem_id) left join ".TP."company as w on(m.company_id=w.company_id) where deal_id='".$deal_id."' AND note_on_deal!='' order by `label`,date_suggested";
		
		
		 
		$success = $db->select_query($q);
		if(!$success){
			//db error
			//echo $q;
			//echo $db->error();
			return false;
		}
		$result_count = $db->row_count();
		if(0 == $result_count){
			//no data
			return true;
		}
		$result_arr = $db->get_result_set_as_array();
		
		return true;
	}
	
	/**************************
	sng:26/aug/2011
	see if there is any notes for this deal. Since each kind of deal suggestion store different notes, we check the deal type and then decide
	it may happen that the member has not given any suggestion for the particular field for this deal
	
	sng:15/mar/2012
	Now we no longer store deal submission as suggestion.
	As for sending correction, we now use only one 'note' textbox.
	The front end code store the note detail in note_on_deal
	****************************/
	public function admin_has_note_correction_on_deal($deal_id){
		$db = new db();
		
		$q = "select count(*) as cnt from ".TP."transaction_suggestions  where deal_id='".$deal_id."' AND note_on_deal!=''";
		
		$success = $db->select_query($q);
		if(!$success){
			//db error
			//echo $q;
			return false;
		}
		$row = $db->get_row();
		if($row['cnt'] == 0) return false;
		else return true;
		
	}
	
	/********************************************deal watch section*******************************************/
	/****************
	sng:9/sep/2011
	get the deals that are being watched by the member
	
	sng:12/sep/2011
	We now use a filter
	show all all
	show all changed in lst 7 days d|7
	show all changed in last 48 hours h|48
	
	sng:25/jan/2012
	We now have value range id for each deal that show the fuzzy deal value. These are predefined.
	Sometime, we only have value range id and deal value is 0
	If both deal value and value range id is 0, the deal value is undisclosed.
	
	sng:3/feb/2012
	We no longer have a single company associated with a deal. Now we have multiple companies
	
	sng:5/mar/2012
	We need the admin_verified flag because based on that, we show a tick mark for the deals
	*****************/
	public function get_watched_deals_for_members($mem_id,$filter,&$data_arr,&$data_count){
		$db = new db();
		
		$time_now = date("Y-m-d H:i:s");
		
		$q = "select w.*,value_in_billion,date_of_deal,deal_cat_name,deal_subcat1_name,deal_subcat2_name,last_edited,t.value_range_id,t.admin_verified,vrm.short_caption as fuzzy_value_short_caption,vrm.display_text as fuzzy_value from ".TP."transaction_watchlist as w left join ".TP."transaction as t on(w.deal_id=t.id) left join ".TP."company as c on(t.company_id=c.company_id) LEFT JOIN ".TP."transaction_value_range_master as vrm ON (t.value_range_id=vrm.value_range_id) where mem_id='".$mem_id."'";
		
		if(($filter!="")&&($filter!="all")){
			//split
			$filter_tokens = explode("|",$filter);
			switch($filter_tokens[0]){
				case 'd':
				$q.=" and TIMESTAMPDIFF(DAY,last_edited,'".$time_now."')<=".$filter_tokens[1];
				break;
				case 'h':
				$q.=" and TIMESTAMPDIFF(HOUR,last_edited,'".$time_now."')<=".$filter_tokens[1];
				break;
			}
		}
		
		$q.=" order by last_edited desc";
		$success = $db->select_query($q);
		if(!$success){
			return false;
		}
		$data_count = $db->row_count();
		if(0 == $data_count){
			//no deals are being watched by this member
			return true;
		}
		$data_arr = $db->get_result_set_as_array();
		/**************************
		sng:3/feb/2012
		get the deal participants, just the names
		*************************/
		require_once("classes/class.transaction_company.php");
		$g_trans_comp = new transaction_company();
		
		for($k=0;$k<$data_count;$k++){
			$data_arr[$k]['participants'] = NULL;
			$success = $g_trans_comp->get_deal_participants($data_arr[$k]['deal_id'],$data_arr[$k]['participants']);
			if(!$success){
				return false;
			}
		}
		return true;
	}
	
	public function remove_deal_from_watch($watch_id){
		$db = new db();
		$q = "delete from ".TP."transaction_watchlist where watch_id='".$watch_id."'";
		$success = $db->mod_query($q);
		return $success;
	}
	
	public function ajax_add_deal_to_watch_list($mem_id,$deal_id,&$validation_passed,&$err_msg){
		$db = new db();
		$validation_passed = true;
		
		//check if the deal exists or not
		$q = "select count(*) as cnt from ".TP."transaction where id='".$deal_id."'";
		$success = $db->select_query($q);
		if(!$success){
			return false;
		}
		$row = $db->get_row();
		if($row['cnt'] == 0){
			//the deal does not exists
			$validation_passed = false;
			$err_msg = "The deal does not exists";
			return true;
		}
		//deal exists, check if watching
		$watching = false;
		$success = $this->is_member_watching_deal($mem_id,$deal_id,$watching);
		if(!$success){
			return false;
		}
		if($watching){
			$validation_passed = false;
			$err_msg = "Already watching";
			return true;
		}
		
		//insert
		$q = "insert into ".TP."transaction_watchlist set mem_id='".$mem_id."', deal_id='".$deal_id."'";
		$success = $db->mod_query($q);
		if(!$success){
			return false;
		}
		$validation_passed = true;
		return true;
	}
	
	public function is_member_watching_deal($mem_id,$deal_id,&$watching){
		$db = new db();
		$q = "select count(*) as cnt from ".TP."transaction_watchlist where mem_id='".$mem_id."' and deal_id='".$deal_id."'";
		$success = $db->select_query($q);
		if(!$success){
			return false;
		}
		$row = $db->get_row();
		if($row['cnt'] == 0){
			$watching = false;
		}else{
			$watching = true;
		}
		return true;
	}
	/********************************************deal watch section*******************************************/
	/*********************************************deal participants notification section************************/
	/**********
	14/sep/2011
	get the list of bank / law firm emails who wish to
	receive notification when their firm is added to a deal
	
	sng:16/nov/2012
	Not needed
	admin_list_all_participant_notification_detail_paged
	admin_add_participant_notification_detail
	admin_delete_participant_notification_detail
	notify_participants
	************************/
	/*********************************************deal participants notification section************************/
	/**********************************************deal partner role section**************************************/
	/***************
	sng:27/sep/2011
	given a firm type, get the roles
	
	sng:3/may/2012
	order the roles by role name
	*******************/
	public function front_get_deal_partner_roles($partner_type,$deal_type,&$data_arr,&$data_count){
		global $g_db;
		$q = "select role_id,role_name from ".TP."transaction_partner_role_master where for_deal_type='".$deal_type."' and partner_type='".$partner_type."' order by role_name";
		$success = $g_db->select_query($q);
		if(!$success){
			return false;
		}
		$data_count = $g_db->row_count();
		if(0 == $data_count){
			return true;
		}
		$data_arr = $g_db->get_result_set_as_array();
		return true;
	}
	
	public function set_deal_partner_role($data_arr,$type,&$msg){
		global $g_db;
		//it may happen that admin set a role for the firm and then again setting the role to blank (0)
		$q = "update ".TP."transaction_partners set role_id='".$data_arr['role']."' where transaction_id='".$data_arr['transaction_id']."' and partner_id='".$data_arr['partner_id']."' and partner_type='".$type."'";
		$result = $g_db->mod_query($q);
		if($result){
			if($g_db->has_row()){
				//record updated
				$msg = "updated";
			}
		}
		return $result;
	}
	
	public function admin_get_all_deal_partner_roles(&$data_arr,&$data_count){
		global $g_db;
		
		$q = "select * from ".TP."transaction_partner_role_master order by partner_type desc,for_deal_type,role_name";
		$ok = $g_db->select_query($q);
		if(!$ok){
			return false;
		}
		$data_count = $g_db->row_count();
		if($data_count == 0){
			//no recs
			return true;
		}
		//recs so
		$data_arr = $g_db->get_result_set_as_array();
		return true;
	}
	
	public function admin_add_deal_partner_role($data_arr,&$validation_passed,&$err_arr){
		global $g_db;
		//validation
		$validation_passed = true;
		if($data_arr['role_name'] == ""){
			$err_arr['role_name'] = "Please specify the role name";
			$validation_passed = false;
		}
		
		if($data_arr['for_deal_type'] == ""){
			$err_arr['for_deal_type'] = "Please select the deal type";
			$validation_passed = false;
		}
		
		if($data_arr['partner_type'] == ""){
			$err_arr['partner_type'] = "Please select the partner type";
			$validation_passed = false;
		}
		
		if(!$validation_passed){
			return true;
		}
		//at least all the items are there, so do the duplicate check
		$q = "select count(*) as cnt from ".TP."transaction_partner_role_master where role_name='".mysql_real_escape_string($data_arr['role_name'])."' AND for_deal_type='".mysql_real_escape_string($data_arr['for_deal_type'])."' AND partner_type='".mysql_real_escape_string($data_arr['partner_type'])."'";
		$ok = $g_db->select_query($q);
		
		if(!$ok){
			return false;
		}
		$row = $g_db->get_row();
		if($row['cnt'] > 0){
			//this name exists
			$err_arr['partner_type'] = "This role already exists";
			$validation_passed = false;
		}
		
		
		if(!$validation_passed){
			//no need to proceed
			return true;
		}
		
		//insert data
		$q = "insert into ".TP."transaction_partner_role_master set role_name='".mysql_real_escape_string($data_arr['role_name'])."',for_deal_type='".mysql_real_escape_string($data_arr['for_deal_type'])."',partner_type='".mysql_real_escape_string($data_arr['partner_type'])."'";
		$ok = $g_db->mod_query($q);
		if(!$ok){
			return false;
		}
		/////////////////
		//data inserted
		$validation_passed = true;
		return true;
	}
	
	public function admin_update_deal_partner_role($role_id,$data_arr,&$validation_passed,&$msg){
		global $g_db;
		//validation
		$validation_passed = true;
		if($data_arr['role_name'] == ""){
			$msg = "Please specify the role name";
			$validation_passed = false;
		}
		
		if(!$validation_passed){
			return true;
		}
		
		//at least all the items are there, so do the duplicate check
		$q = "select count(*) as cnt from ".TP."transaction_partner_role_master where role_name='".mysql_real_escape_string($data_arr['role_name'])."' AND for_deal_type='".mysql_real_escape_string($data_arr['for_deal_type'])."' AND partner_type='".mysql_real_escape_string($data_arr['partner_type'])."'";
		$ok = $g_db->select_query($q);
		
		if(!$ok){
			return false;
		}
		$row = $g_db->get_row();
		if($row['cnt'] > 0){
			//this name exists
			$msg = "This role already exists";
			$validation_passed = false;
		}
		
		
		if(!$validation_passed){
			//no need to proceed
			return true;
		}
		
		//update data
		$q = "update ".TP."transaction_partner_role_master set role_name='".mysql_real_escape_string($data_arr['role_name'])."' where role_id='".$role_id."'";
		$ok = $g_db->mod_query($q);
		if(!$ok){
			return false;
		}
		/////////////////
		//data inserted
		$validation_passed = true;
		return true;
	}
	/**********************************************deal partner role section**************************************/
	/************
	sng:24/feb/2012
	We have moved deal company roles code to class transaction_company
	
	/*****************************************deal value filter section*******************************************/
	/************************
	sng:23/july/2010
    There is now filter on deal size
	
	sng:20/jan/2012
	We no longer use the size_filter_master to get the conditions as options.
	Those work only with deals having exact value. Now we can have deals that are tagged with
	range id, like 3 (greater than 1 billion).
	We now use the transaction_value_range_master and the function deal_support::front_get_deal_value_range_list
	*******************************/
	public function front_get_deal_value_range_list(&$data_arr,&$data_count){
        $q = "select * from ".TP."transaction_value_range_master order by display_order";
        $res = mysql_query($q);
        if(!$res){
            return false;
        }
        $data_count = mysql_num_rows($res);
        if($data_count == 0){
            //no recs
            return true;
        }
        //recs so
        while($row = mysql_fetch_assoc($res)){
            $data_arr[] = $row;
        }
        return true;
    }
	
	/************
	sng:18/feb/2012
	Given a deal value in million, we should be able to get the deal_value_range_id
	For the special value of 0, the range id is 0 (undefined)
	*************/
	function front_get_value_range_id_from_value($value_in_million,&$value_range_id){
		if($value_in_million == 0){
			$value_range_id = 0;
			return true;
		}
		$db = new db();
		$q = "select value_range_id,lower_value_limit_in_million from ".TP."transaction_value_range_master order by lower_value_limit_in_million desc";
		$ok = $db->select_query($q);
		if(!$ok){
			return false;
		}
		$slabs = $db->get_result_set_as_array();
		$slab_count = $db->row_count();
		
		$value_range_id = 0;
		for($i=0;$i<$slab_count;$i++){
			if($value_in_million >= $slabs[$i]['lower_value_limit_in_million']){
				$value_range_id = $slabs[$i]['value_range_id'];
				break;
			}
		}
		
		return true;
	}
	/*****************************************deal value filter section*******************************************/
	/*****************************************chosen logo*********************************************************
	sng:23/feb/2012
	Foe a deal, there can be multiple companies and hence multiple logos.
	The user can choose a logo as preferred for a deal (which is displayed in the tombstone
	************/
	public function get_user_chosen_logos() {
        if (!isset($_SESSION['mem_id'])) {
            return array();
        }
        $tableName  = TP.'preferred_logos';       
        $q  = "SELECT logos FROM {$tableName} WHERE mem_id = {$_SESSION['mem_id']} LIMIT 1";
        $res = mysql_query($q);
        $result = mysql_fetch_assoc($res);
        if (is_array($result)) {
            return unserialize($result['logos']);
        }
    }
	/**************************************chosen logo*******************************************************************/
}
?>