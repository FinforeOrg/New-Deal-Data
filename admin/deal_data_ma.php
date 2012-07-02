<?php
/***************
get the deal data since this is called in ajax, we need to fetch the lists and data again

Since this is called in ajax, use the session based flash for error msg
****************/
$g_view['data_count'] = 0;
$g_view['data'] = NULL;
$success = $g_trans->get_deal_edit($deal_id,$g_view['data'],$g_view['data_count']);
if(!$success){
	die("Cannot get deal data");
}
//fetch headquarter_country names
$g_view['country_list'] = array();
$g_view['country_count'] = 0;
$success = $g_country->get_all_country_list($g_view['country_list'],$g_view['country_count']);
if(!$success){
	die("Cannot get country list");
}
/***
sng:12/may/2010
for m and a deals, there will be target sector and we store sector in target_sector field
***/
//fetch sector list
$g_view['sector_list'] = array();
$g_view['sector_count'] = 0;
$success = $g_company->get_all_sector_list($g_view['sector_list'],$g_view['sector_count']);
if(!$success){
	die("Cannot get sector list");
}
/*********************************
sng:7/apr/2011
We need industries for seller and target
***/
$g_view['target_industry_list'] = array();
$g_view['target_industry_list_count'] = 0;
$success = $g_company->get_all_industry_for_sector($g_view['data']['target_sector'],$g_view['target_industry_list'],$g_view['target_industry_list_count']);
if(!$success){
	die("Cannot get industry list");
}
$g_view['seller_industry_list'] = array();
$g_view['seller_industry_list_count'] = 0;
$success = $g_company->get_all_industry_for_sector($g_view['data']['seller_sector'],$g_view['seller_industry_list'],$g_view['seller_industry_list_count']);
if(!$success){
	die("Cannot get industry list");
}

/**********************
we need list of M&A merger type
********/
$g_view['merger_list'] = array();
$g_view['merger_list_count'] = 0;
$success = $g_deal_support->ma_merger_types($g_view['merger_list'],$g_view['merger_list_count']);
if(!$success){
	die("Cannot get merger type list");
}
?>
<table width="100%" cellpadding="5" cellspacing="0" border="0">
<tr><td colspan="2"><hr noshade="noshade" /></td></tr>
<tr><td colspan="2"><strong>Date of Deal</strong>&nbsp;<span class="err_txt">*</span></td></tr>

<tr>
    <td>Rumoured</td>
    <td>
    <input name="date_rumour" id="date_rumour" type="text" style="width:200px;" value="<?php if($g_view['data']['date_rumour']=='0000-00-00'||$g_view['data']['date_rumour']=='') echo ""; else echo $g_view['data']['date_rumour'];?>" />&nbsp;<a href="#" onclick="return fetch_correction_suggestion('date_rumour');"><img src="has_deal_suggestion.php?deal_id=<?php echo $deal_id;?>&data_name=date_rumour" /></a>
    <script type="text/javascript">
          // <![CDATA[       
            var opts = {                            
                    formElements:{"date_rumour":"Y-ds-m-ds-d"},
                    showWeeks:false                   
            };      
            datePickerController.createDatePicker(opts);
          // ]]>
          </script>
    </td>
</tr>

<tr><td colspan="2" id="suggestion_date_rumour"></td></tr>

<tr>
    <td>Announced</td>
    <td>
    <input name="date_announced" id="date_announced" type="text" style="width:200px;" value="<?php if($g_view['data']['date_announced']=='0000-00-00'||$g_view['data']['date_announced']=='') echo ""; else echo $g_view['data']['date_announced'];?>" />&nbsp;<a href="#" onclick="return fetch_correction_suggestion('date_announced');"><img src="has_deal_suggestion.php?deal_id=<?php echo $deal_id;?>&data_name=date_announced" /></a>
    <script type="text/javascript">
          // <![CDATA[       
            var opts = {                            
                    formElements:{"date_announced":"Y-ds-m-ds-d"},
                    showWeeks:false                   
            };      
            datePickerController.createDatePicker(opts);
          // ]]>
          </script>
    </td>
</tr>
<tr><td colspan="2" id="suggestion_date_announced"></td></tr>

<tr>
    <td>Closed</td>
    <td>
    <input name="date_closed" id="date_closed" type="text" style="width:200px;" value="<?php if($g_view['data']['date_closed']=='0000-00-00'||$g_view['data']['date_closed']=='') echo ""; else echo $g_view['data']['date_closed'];?>" />&nbsp;<a href="#" onclick="return fetch_correction_suggestion('date_closed');"><img src="has_deal_suggestion.php?deal_id=<?php echo $deal_id;?>&data_name=date_closed" /></a><br />
	<span class="err_txt"><?php display_flash("date_of_deal");?></span>
    <script type="text/javascript">
          // <![CDATA[       
            var opts = {                            
                    formElements:{"date_closed":"Y-ds-m-ds-d"},
                    showWeeks:false                   
            };      
            datePickerController.createDatePicker(opts);
          // ]]>
          </script>
    </td>
</tr>
<tr><td colspan="2" id="suggestion_date_closed"></td></tr>

<tr><td colspan="2"><hr noshade="noshade" /></td></tr>
<?php
/*****************
sng:13/feb/2012
we now have one or more companies with roles. We no longer need this
******************/
?>
<tr><td colspan="2"><strong>Valuation</strong></td></tr>

<tr>
<td>Local Currency for the deal</td>
<td>
<input name="currency" id="currency" type="text" style="width:200px;" value="<?php echo $g_view['data']['currency'];?>" />&nbsp;<a href="#" onclick="return fetch_correction_suggestion('currency');"><img src="has_deal_suggestion.php?deal_id=<?php echo $deal_id;?>&data_name=currency" /></a>
</td>
</tr>
<tr><td colspan="2" id="suggestion_currency"></td></tr>

<tr>
<td>Local Currency per 1 USD</td>
<td>
<input name="exchange_rate" type="text" style="width:200px;" value="<?php if($g_view['data']['exchange_rate']!="0.0") echo $g_view['data']['exchange_rate'];?>" />&nbsp;<a href="#" onclick="return fetch_correction_suggestion('exchange_rate');"><img src="has_deal_suggestion.php?deal_id=<?php echo $deal_id;?>&data_name=exchange_rate" /></a>
</td>
</tr>
<tr><td colspan="2" id="suggestion_exchange_rate"></td></tr>

<tr>
<td>Implied Equity Value</td>
<td>
<input name="implied_equity_value_in_million_local_currency" type="text" style="width:200px;" value="<?php if($g_view['data']['implied_equity_value_in_million_local_currency']!="0.0") echo $g_view['data']['implied_equity_value_in_million_local_currency'];?>" /> (in million, local currency)&nbsp;<a href="#" onclick="return fetch_correction_suggestion('implied_equity_value_in_million_local_currency');"><img src="has_deal_suggestion.php?deal_id=<?php echo $deal_id;?>&data_name=implied_equity_value_in_million_local_currency" /></a>
</td>
</tr>
<tr><td colspan="2" id="suggestion_implied_equity_value_in_million_local_currency"></td></tr>

<tr>
<td>Acquisition of what %</td>
<td>
<input name="acquisition_percentage" type="text" style="width:60px;" value="<?php if($g_view['data']['acquisition_percentage']!="0.0") echo $g_view['data']['acquisition_percentage'];?>" /> %&nbsp;<a href="#" onclick="return fetch_correction_suggestion('acquisition_percentage');"><img src="has_deal_suggestion.php?deal_id=<?php echo $deal_id;?>&data_name=acquisition_percentage" /></a>
</td>
</tr>
<tr><td colspan="2" id="suggestion_acquisition_percentage"></td></tr>


<tr>
<td>Net Debt</td>
<td>
<input name="net_debt_in_million_local_currency" type="text" style="width:200px;" value="<?php if($g_view['data']['net_debt_in_million_local_currency']!="0.0") echo $g_view['data']['net_debt_in_million_local_currency'];?>" /> (in million, local currency)&nbsp;<a href="#" onclick="return fetch_correction_suggestion('net_debt_in_million_local_currency');"><img src="has_deal_suggestion.php?deal_id=<?php echo $deal_id;?>&data_name=net_debt_in_million_local_currency" /></a>
</td>
</tr>
<tr><td colspan="2" id="suggestion_net_debt_in_million_local_currency"></td></tr>

<tr>
<td>Dividend on Top of Equity</td>
<td>
<input name="dividend_on_top_of_equity_million_local_curency" type="text" style="width:200px;" value="<?php if($g_view['data']['dividend_on_top_of_equity_million_local_curency']!="0.0") echo $g_view['data']['dividend_on_top_of_equity_million_local_curency'];?>" /> (in million, local currency)&nbsp;<a href="#" onclick="return fetch_correction_suggestion('dividend_on_top_of_equity_million_local_curency');"><img src="has_deal_suggestion.php?deal_id=<?php echo $deal_id;?>&data_name=dividend_on_top_of_equity_million_local_curency" /></a>
</td>
</tr>
<tr><td colspan="2" id="suggestion_dividend_on_top_of_equity_million_local_curency"></td></tr>

<tr>
<td>Enterprise Value (local currency) </td>
<td>
<input name="enterprise_value_million_local_currency" type="text" style="width:200px;" value="<?php if($g_view['data']['enterprise_value_million_local_currency']!="0.0") echo $g_view['data']['enterprise_value_million_local_currency'];?>" /> (in million, local currency)&nbsp;<a href="#" onclick="return fetch_correction_suggestion('enterprise_value_million_local_currency');"><img src="has_deal_suggestion.php?deal_id=<?php echo $deal_id;?>&data_name=enterprise_value_million_local_currency" /></a>
</td>
</tr>
<tr><td colspan="2" id="suggestion_enterprise_value_million_local_currency"></td></tr>

<tr>
<td>Implied Enterprise Value (USD) </td>
<td>
<input name="enterprise_value_million" type="text" style="width:200px;" value="<?php if($g_view['data']['enterprise_value_million']!="0.0") echo $g_view['data']['enterprise_value_million'];?>" /> (in million, USD)&nbsp;<a href="#" onclick="return fetch_correction_suggestion('enterprise_value_million');"><img src="has_deal_suggestion.php?deal_id=<?php echo $deal_id;?>&data_name=enterprise_value_million" /></a>
</td>
</tr>
<tr><td colspan="2" id="suggestion_enterprise_value_million"></td></tr>

<tr>
<td>Implied Deal Size (local currency)<br />(suggestions are in million) </td>
<td>
<input name="value_in_billion_local_currency" type="text" style="width:200px;" value="<?php if($g_view['data']['value_in_billion_local_currency']!="0.0") echo $g_view['data']['value_in_billion_local_currency'];?>" /> (in <strong>billion</strong>, local currency)&nbsp;<a href="#" onclick="return fetch_correction_suggestion('value_in_million_local_currency');"><img src="has_deal_suggestion.php?deal_id=<?php echo $deal_id;?>&data_name=value_in_million_local_currency" /></a>
</td>
</tr>
<tr><td colspan="2" id="suggestion_value_in_million_local_currency"></td></tr>

<tr>
<td>Implied Deal Size (USD)<br />(suggestions are in million) </td>
<td>
<?php require("deal_data_snippet_value_range.php");?>
</td>
</tr>
<tr><td colspan="2" id="suggestion_value_in_million"></td></tr>

<tr>
<td>Transaction Type</td>
<td>
<input type="radio" name="payment_type" value="cash" <?php if($g_view['data']['payment_type']=="cash"){?>checked="checked"<?php }?>>Cash&nbsp;&nbsp;
<input type="radio" name="payment_type" value="equity" <?php if($g_view['data']['payment_type']=="equity"){?>checked="checked"<?php }?>>Equity&nbsp;&nbsp;
<input type="radio" name="payment_type" value="part_cash_part_quity" <?php if($g_view['data']['payment_type']=="part_cash_part_quity"){?>checked="checked"<?php }?>>
Part Cash / Part Equity&nbsp;<a href="#" onclick="return fetch_correction_suggestion('payment_type');"><img src="has_deal_suggestion.php?deal_id=<?php echo $deal_id;?>&data_name=payment_type" /></a>
</td>
</tr>
<tr><td colspan="2" id="suggestion_payment_type"></td></tr>

<tr>
<td>Equity Payment %</td>
<td>
<input name="equity_payment_percent" type="text" style="width:60px;" value="<?php if($g_view['data']['equity_payment_percent']!="0.0") echo $g_view['data']['equity_payment_percent'];?>" /> % (if Part cash / part equity)&nbsp;<a href="#" onclick="return fetch_correction_suggestion('equity_payment_percent');"><img src="has_deal_suggestion.php?deal_id=<?php echo $deal_id;?>&data_name=equity_payment_percent" /></a>
</td>
</tr>
<tr><td colspan="2" id="suggestion_equity_payment_percent"></td></tr>

<tr>
<td>Merger Type</td>
<td>
<select name="takeover_id">
<option value="0">Select</option>
<?php
for($i=0;$i<$g_view['merger_list_count'];$i++){
	?>
	<option value="<?php echo $g_view['merger_list'][$i]['takeover_id'];?>" <?php if($g_view['data']['takeover_id']==$g_view['merger_list'][$i]['takeover_id']){?>selected="selected"<?php }?>><?php echo $g_view['merger_list'][$i]['takeover_name'];?></option>
	<?php
}
?>
</select>&nbsp;<a href="#" onclick="return fetch_correction_suggestion('takeover');"><img src="has_deal_suggestion.php?deal_id=<?php echo $deal_id;?>&data_name=takeover" /></a>
</td>
</tr>
<tr><td colspan="2" id="suggestion_takeover"></td></tr>

<tr><td colspan="2"><hr noshade="noshade" /></td></tr>
<tr>
<td>Target Publicly Listed</td>
<td>
<input type="radio" name="target_listed_in_stock_exchange" value="y" <?php if($g_view['data']['target_listed_in_stock_exchange']=="y"){?>checked="checked"<?php }?>>Yes&nbsp;&nbsp;
<input type="radio" name="target_listed_in_stock_exchange" value="n" <?php if($g_view['data']['target_listed_in_stock_exchange']=="n"){?>checked="checked"<?php }?>>No&nbsp;<a href="#" onclick="return fetch_correction_suggestion('target_listed_in_stock_exchange');"><img src="has_deal_suggestion.php?deal_id=<?php echo $deal_id;?>&data_name=target_listed_in_stock_exchange" /></a>
</td>
</tr>
<tr><td colspan="2" id="suggestion_target_listed_in_stock_exchange"></td></tr>

<tr>
<td>Name of the stock exchange</td>
<td>
<input name="target_stock_exchange_name" id="target_stock_exchange_name" type="text" style="width:200px;" value="<?php echo $g_view['data']['target_stock_exchange_name'];?>" />&nbsp;<a href="#" onclick="return fetch_correction_suggestion('target_stock_exchange_name');"><img src="has_deal_suggestion.php?deal_id=<?php echo $deal_id;?>&data_name=target_stock_exchange_name" /></a>
</td>
</tr>
<tr><td colspan="2" id="suggestion_target_stock_exchange_name"></td></tr>

<tr>
<td>Local Currency of Share Price (if different)</td>
<td>
<input name="currency_price_per_share" id="currency_price_per_share" type="text" style="width:200px;" value="<?php echo $g_view['data']['currency_price_per_share'];?>" />&nbsp;<a href="#" onclick="return fetch_correction_suggestion('currency_price_per_share');"><img src="has_deal_suggestion.php?deal_id=<?php echo $deal_id;?>&data_name=currency_price_per_share" /></a>
</td>
</tr>
<tr><td colspan="2" id="suggestion_currency_price_per_share"></td></tr>

<tr>
<td>Deal price per share</td>
<td>
<input name="deal_price_per_share" type="text" style="width:200px;" value="<?php if($g_view['data']['deal_price_per_share']!="0.0") echo $g_view['data']['deal_price_per_share'];?>" /> (local currency, per share)&nbsp;<a href="#" onclick="return fetch_correction_suggestion('deal_price_per_share');"><img src="has_deal_suggestion.php?deal_id=<?php echo $deal_id;?>&data_name=deal_price_per_share" /></a>
</td>
</tr>
<tr><td colspan="2" id="suggestion_deal_price_per_share"></td></tr>

<tr>
<td>Share price prior to announcement</td>
<td>
<input name="price_per_share_before_deal_announcement" type="text" style="width:200px;" value="<?php if($g_view['data']['price_per_share_before_deal_announcement']!="0.0") echo $g_view['data']['price_per_share_before_deal_announcement'];?>" /> (local currency, per share)&nbsp;<a href="#" onclick="return fetch_correction_suggestion('price_per_share_before_deal_announcement');"><img src="has_deal_suggestion.php?deal_id=<?php echo $deal_id;?>&data_name=price_per_share_before_deal_announcement" /></a>
</td>
</tr>
<tr><td colspan="2" id="suggestion_price_per_share_before_deal_announcement"></td></tr>

<tr>
    <td>Date of share price, prior to announcement</td>
    <td>
    <input name="date_price_per_share_before_deal_announcement" id="date_price_per_share_before_deal_announcement" type="text" style="width:200px;" value="<?php if($g_view['data']['date_price_per_share_before_deal_announcement']=='0000-00-00'||$g_view['data']['date_price_per_share_before_deal_announcement']=='') echo ""; else echo $g_view['data']['date_price_per_share_before_deal_announcement'];?>" />&nbsp;<a href="#" onclick="return fetch_correction_suggestion('date_price_per_share_before_deal_announcement');"><img src="has_deal_suggestion.php?deal_id=<?php echo $deal_id;?>&data_name=date_price_per_share_before_deal_announcement" /></a>
    <script type="text/javascript">
          // <![CDATA[       
            var opts = {                            
                    formElements:{"date_price_per_share_before_deal_announcement":"Y-ds-m-ds-d"},
                    showWeeks:false                   
            };      
            datePickerController.createDatePicker(opts);
          // ]]>
          </script>
    </td>
</tr>
<tr><td colspan="2" id="suggestion_date_price_per_share_before_deal_announcement"></td></tr>

<tr>
<td>Implied Premium</td>
<td>
<input name="implied_premium_percentage" type="text" style="width:60px;" value="<?php if($g_view['data']['implied_premium_percentage']!="0.0") echo $g_view['data']['implied_premium_percentage'];?>" /> %&nbsp;<a href="#" onclick="return fetch_correction_suggestion('implied_premium_percentage');"><img src="has_deal_suggestion.php?deal_id=<?php echo $deal_id;?>&data_name=implied_premium_percentage" /></a>
</td>
</tr>
<tr><td colspan="2" id="suggestion_implied_premium_percentage"></td></tr>

<tr>
<td>Total shares outstanding</td>
<td>
<input name="total_shares_outstanding_million" type="text" style="width:200px;" value="<?php if($g_view['data']['total_shares_outstanding_million']!="0.0") echo $g_view['data']['total_shares_outstanding_million'];?>" /> (million)&nbsp;<a href="#" onclick="return fetch_correction_suggestion('total_shares_outstanding_million');"><img src="has_deal_suggestion.php?deal_id=<?php echo $deal_id;?>&data_name=total_shares_outstanding_million" /></a>
</td>
</tr>
<tr><td colspan="2" id="suggestion_total_shares_outstanding_million"></td></tr>

<tr><td colspan="2"><hr noshade="noshade" /></td></tr>
<tr><td colspan="2"><strong>Additional Information</strong></td></tr>
<tr>
<td>Termination Fee</td>
<td>
<input name="termination_fee_million" type="text" style="width:200px;" value="<?php if($g_view['data']['termination_fee_million']!="0.0") echo $g_view['data']['termination_fee_million'];?>" /> (in million, local currency)&nbsp;<a href="#" onclick="return fetch_correction_suggestion('termination_fee_million');"><img src="has_deal_suggestion.php?deal_id=<?php echo $deal_id;?>&data_name=termination_fee_million" /></a>
</td>
</tr>
<tr><td colspan="2" id="suggestion_termination_fee_million"></td></tr>

<tr>
    <td>End Date for Termination Fee</td>
    <td>
    <input name="end_date_termination_fee" id="end_date_termination_fee" type="text" style="width:200px;" value="<?php if($g_view['data']['end_date_termination_fee']=='0000-00-00'||$g_view['data']['end_date_termination_fee']=='') echo ""; else echo $g_view['data']['end_date_termination_fee'];?>" />&nbsp;<a href="#" onclick="return fetch_correction_suggestion('end_date_termination_fee');"><img src="has_deal_suggestion.php?deal_id=<?php echo $deal_id;?>&data_name=end_date_termination_fee" /></a>
    <script type="text/javascript">
          // <![CDATA[       
            var opts = {                            
                    formElements:{"end_date_termination_fee":"Y-ds-m-ds-d"},
                    showWeeks:false                   
            };      
            datePickerController.createDatePicker(opts);
          // ]]>
          </script>
    </td>
</tr>
<tr><td colspan="2" id="suggestion_end_date_termination_fee"></td></tr>

<tr>
<td>Fee (%) to Sellside Advisors</td>
<td>
<input name="fee_percent_to_sellside_advisor" type="text" style="width:60px;" value="<?php if($g_view['data']['fee_percent_to_sellside_advisor']!="0.0") echo $g_view['data']['fee_percent_to_sellside_advisor'];?>" /> %&nbsp;<a href="#" onclick="return fetch_correction_suggestion('fee_percent_to_sellside_advisor');"><img src="has_deal_suggestion.php?deal_id=<?php echo $deal_id;?>&data_name=fee_percent_to_sellside_advisor" /></a>
</td>
</tr>
<tr><td colspan="2" id="suggestion_fee_percent_to_sellside_advisor"></td></tr>

<tr>
<td>Fee (%) to Buyside Advisors</td>
<td>
<input name="fee_percent_to_buyside_advisor" type="text" style="width:60px;" value="<?php if($g_view['data']['fee_percent_to_buyside_advisor']!="0.0") echo $g_view['data']['fee_percent_to_buyside_advisor'];?>" /> %&nbsp;<a href="#" onclick="return fetch_correction_suggestion('fee_percent_to_buyside_advisor');"><img src="has_deal_suggestion.php?deal_id=<?php echo $deal_id;?>&data_name=fee_percent_to_buyside_advisor" /></a>
</td>
</tr>
<tr><td colspan="2" id="suggestion_fee_percent_to_buyside_advisor"></td></tr>

<tr><td colspan="2"><hr noshade="noshade" /></td></tr>
<tr><td colspan="2"><strong>Financial Metrics</strong></td></tr>

<tr>
<td>Revenues Last 12 Months</td>
<td>
<input name="revenue_ltm_million" type="text" style="width:200px;" value="<?php if($g_view['data']['revenue_ltm_million']!="0.0") echo $g_view['data']['revenue_ltm_million'];?>" /> (in million, local currency)&nbsp;<a href="#" onclick="return fetch_correction_suggestion('revenue_ltm_million');"><img src="has_deal_suggestion.php?deal_id=<?php echo $deal_id;?>&data_name=revenue_ltm_million" /></a>
</td>
</tr>
<tr><td colspan="2" id="suggestion_revenue_ltm_million"></td></tr>

<tr>
<td>Revenues Most Recent Year</td>
<td>
<input name="revenue_mry_million" type="text" style="width:200px;" value="<?php if($g_view['data']['revenue_mry_million']!="0.0") echo $g_view['data']['revenue_mry_million'];?>" /> (in million, local currency)&nbsp;<a href="#" onclick="return fetch_correction_suggestion('revenue_mry_million');"><img src="has_deal_suggestion.php?deal_id=<?php echo $deal_id;?>&data_name=revenue_mry_million" /></a>
</td>
</tr>
<tr><td colspan="2" id="suggestion_revenue_mry_million"></td></tr>

<tr>
<td>Revenues Next Year</td>
<td>
<input name="revenue_ny_million" type="text" style="width:200px;" value="<?php if($g_view['data']['revenue_ny_million']!="0.0") echo $g_view['data']['revenue_ny_million'];?>" /> (in million, local currency)&nbsp;<a href="#" onclick="return fetch_correction_suggestion('revenue_ny_million');"><img src="has_deal_suggestion.php?deal_id=<?php echo $deal_id;?>&data_name=revenue_ny_million" /></a>
</td>
</tr>
<tr><td colspan="2" id="suggestion_revenue_ny_million"></td></tr>

<tr>
<td>EBITDA Last 12 Months</td>
<td>
<input name="ebitda_ltm_million" type="text" style="width:200px;" value="<?php if($g_view['data']['ebitda_ltm_million']!="0.0") echo $g_view['data']['ebitda_ltm_million'];?>" /> (in million, local currency)&nbsp;<a href="#" onclick="return fetch_correction_suggestion('ebitda_ltm_million');"><img src="has_deal_suggestion.php?deal_id=<?php echo $deal_id;?>&data_name=ebitda_ltm_million" /></a>
</td>
</tr>
<tr><td colspan="2" id="suggestion_ebitda_ltm_million"></td></tr>

<tr>
<td>EBITDA Most Recent Year</td>
<td>
<input name="ebitda_mry_million" type="text" style="width:200px;" value="<?php if($g_view['data']['ebitda_mry_million']!="0.0") echo $g_view['data']['ebitda_mry_million'];?>" /> (in million, local currency)&nbsp;<a href="#" onclick="return fetch_correction_suggestion('ebitda_mry_million');"><img src="has_deal_suggestion.php?deal_id=<?php echo $deal_id;?>&data_name=ebitda_mry_million" /></a>
</td>
</tr>
<tr><td colspan="2" id="suggestion_ebitda_mry_million"></td></tr>

<tr>
<td>EBITDA Next Year</td>
<td>
<input name="ebitda_ny_million" type="text" style="width:200px;" value="<?php if($g_view['data']['ebitda_ny_million']!="0.0") echo $g_view['data']['ebitda_ny_million'];?>" /> (in million, local currency)&nbsp;<a href="#" onclick="return fetch_correction_suggestion('ebitda_ny_million');"><img src="has_deal_suggestion.php?deal_id=<?php echo $deal_id;?>&data_name=ebitda_ny_million" /></a>
</td>
</tr>
<tr><td colspan="2" id="suggestion_ebitda_ny_million"></td></tr>

<tr>
<td>Net Income Last 12 Months</td>
<td>
<input name="net_income_ltm_million" type="text" style="width:200px;" value="<?php if($g_view['data']['net_income_ltm_million']!="0.0") echo $g_view['data']['net_income_ltm_million'];?>" /> (in million, local currency)&nbsp;<a href="#" onclick="return fetch_correction_suggestion('net_income_ltm_million');"><img src="has_deal_suggestion.php?deal_id=<?php echo $deal_id;?>&data_name=net_income_ltm_million" /></a>
</td>
</tr>
<tr><td colspan="2" id="suggestion_net_income_ltm_million"></td></tr>

<tr>
<td>Net Income Most Recent Year</td>
<td>
<input name="net_income_mry_million" type="text" style="width:200px;" value="<?php if($g_view['data']['net_income_mry_million']!="0.0") echo $g_view['data']['net_income_mry_million'];?>" /> (in million, local currency)&nbsp;<a href="#" onclick="return fetch_correction_suggestion('net_income_mry_million');"><img src="has_deal_suggestion.php?deal_id=<?php echo $deal_id;?>&data_name=net_income_mry_million" /></a>
</td>
</tr>
<tr><td colspan="2" id="suggestion_net_income_mry_million"></td></tr>

<tr>
<td>Net Income Next Year</td>
<td>
<input name="net_income_ny_million" type="text" style="width:200px;" value="<?php if($g_view['data']['net_income_ny_million']!="0.0") echo $g_view['data']['net_income_ny_million'];?>" /> (in million, local currency)&nbsp;<a href="#" onclick="return fetch_correction_suggestion('net_income_ny_million');"><img src="has_deal_suggestion.php?deal_id=<?php echo $deal_id;?>&data_name=net_income_ny_million" /></a>
</td>
</tr>
<tr><td colspan="2" id="suggestion_net_income_ny_million"></td></tr>

<tr>
    <td>Year-End Date of Most Recent Financial Year</td>
    <td>
    <input name="date_year_end_of_recent_financial_year" id="date_year_end_of_recent_financial_year" type="text" style="width:200px;" value="<?php if($g_view['data']['date_year_end_of_recent_financial_year']=='0000-00-00'||$g_view['data']['date_year_end_of_recent_financial_year']=='') echo ""; else echo $g_view['data']['date_year_end_of_recent_financial_year'];?>" />&nbsp;<a href="#" onclick="return fetch_correction_suggestion('date_year_end_of_recent_financial_year');"><img src="has_deal_suggestion.php?deal_id=<?php echo $deal_id;?>&data_name=date_year_end_of_recent_financial_year" /></a>
    <script type="text/javascript">
          // <![CDATA[       
            var opts = {                            
                    formElements:{"date_year_end_of_recent_financial_year":"Y-ds-m-ds-d"},
                    showWeeks:false                   
            };      
            datePickerController.createDatePicker(opts);
          // ]]>
          </script>
    </td>
</tr>
<tr><td colspan="2" id="suggestion_date_year_end_of_recent_financial_year"></td></tr>

</table>
<script>
jQuery('#currency_price_per_share').autocomplete({
	serviceUrl:'ajax/fetch_currency_list.php',
	minChars:1,
	noCache: true,
	onSelect: function(value, data){
		jQuery('#currency_price_per_share').val(data);
	}
});

jQuery('#currency').autocomplete({
	serviceUrl:'ajax/fetch_currency_list.php',
	minChars:1,
	noCache: true,
	onSelect: function(value, data){
		jQuery('#currency').val(data);
	}
});

jQuery('#target_stock_exchange_name').autocomplete({
	serviceUrl:'ajax/fetch_stock_exchange_list.php',
	minChars:1,
	noCache: true,
	onSelect: function(value, data){
		jQuery('#target_stock_exchange_name').val(data);
	}
});
</script>