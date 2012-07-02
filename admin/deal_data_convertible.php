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
/*************************************************************************
fetch sector list
**************/
$g_view['sector_list'] = array();
$g_view['sector_count'] = 0;
$success = $g_company->get_all_sector_list($g_view['sector_list'],$g_view['sector_count']);
if(!$success){
	die("Cannot get sector list");
}
/********************************************************************
We need industries for underlying security
****************/
$g_view['security_industry_list'] = array();
$g_view['security_industry_list_count'] = 0;
$success = $g_company->get_all_industry_for_sector($g_view['data']['sector_security'],$g_view['security_industry_list'],$g_view['security_industry_list_count']);
if(!$success){
	die("Cannot get industry list");
}
/*********************************************************************/
?>
<table width="100%" cellpadding="5" cellspacing="0" border="0">
<tr><td colspan="2"><hr noshade="noshade" /></td></tr>
<tr><td colspan="2"><strong>Date of Deal</strong>&nbsp;<span class="err_txt">*</span></td></tr>

<tr>
    <td>Announced / Filed</td>
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
    <td>Closed / Trading</td>
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
<td>Deal Size (local currency)<br />(suggestions are in million) </td>
<td>
<input name="value_in_billion_local_currency" type="text" style="width:200px;" value="<?php if($g_view['data']['value_in_billion_local_currency']!="0.0") echo $g_view['data']['value_in_billion_local_currency'];?>" /> (in <strong>billion</strong>, local currency)&nbsp;<a href="#" onclick="return fetch_correction_suggestion('value_in_million_local_currency');"><img src="has_deal_suggestion.php?deal_id=<?php echo $deal_id;?>&data_name=value_in_million_local_currency" /></a>
</td>
</tr>
<tr><td colspan="2" id="suggestion_value_in_million_local_currency"></td></tr>

<tr>
<td>Deal Size (USD)<br />(suggestions are in million) </td>
<td>
<?php require("deal_data_snippet_value_range.php");?>
</td>
</tr>
<tr><td colspan="2" id="suggestion_value_in_million"></td></tr>

<tr><td colspan="2"><hr noshade="noshade" /></td></tr>
<tr><td colspan="2"><strong>Additional Information</strong></td></tr>

<?php
/*************
sng:14/feb/2012
Now that we have one or more participants, we no longer need to track the security
*************/
?>


<tr>
<td>Years to maturity</td>
<td>
<input name="years_to_maturity" type="text" style="width:200px;" value="<?php echo $g_view['data']['years_to_maturity'];?>" />&nbsp;<a href="#" onclick="return fetch_correction_suggestion('years_to_maturity');"><img src="has_deal_suggestion.php?deal_id=<?php echo $deal_id;?>&data_name=years_to_maturity" /></a>
</td>
</tr>
<tr><td colspan="2" id="suggestion_years_to_maturity"></td></tr>

<tr>
    <td>End Date</td>
    <td>
    <input name="maturity_date" id="maturity_date" type="text" style="width:200px;" value="<?php if($g_view['data']['maturity_date']=='0000-00-00'||$g_view['data']['maturity_date']==''||$g_view['data']['maturity_date']=='n/a') echo ""; else echo date("Y-m-d",date_to_timestamp($g_view['data']['maturity_date']));?>" />&nbsp;<a href="#" onclick="return fetch_correction_suggestion('maturity_date');"><img src="has_deal_suggestion.php?deal_id=<?php echo $deal_id;?>&data_name=maturity_date" /></a>
    <script type="text/javascript">
          // <![CDATA[       
            var opts = {                            
                    formElements:{"maturity_date":"Y-ds-m-ds-d"},
                    showWeeks:false                   
            };      
            datePickerController.createDatePicker(opts);
          // ]]>
          </script>
    </td>
</tr>
<tr><td colspan="2" id="suggestion_maturity_date"></td></tr>

<tr>
<td>Coupon</td>
<td>
<input name="coupon" type="text" style="width:200px;" value="<?php echo $g_view['data']['coupon'];?>" />&nbsp;<a href="#" onclick="return fetch_correction_suggestion('coupon');"><img src="has_deal_suggestion.php?deal_id=<?php echo $deal_id;?>&data_name=coupon" /></a>
</td>
</tr>
<tr><td colspan="2" id="suggestion_coupon"></td></tr>

<tr>
<td>Current Rating</td>
<td>
<input name="current_rating" type="text" style="width:200px;" value="<?php echo $g_view['data']['current_rating'];?>" />&nbsp;<a href="#" onclick="return fetch_correction_suggestion('current_rating');"><img src="has_deal_suggestion.php?deal_id=<?php echo $deal_id;?>&data_name=current_rating" /></a>
</td>
</tr>
<tr><td colspan="2" id="suggestion_current_rating"></td></tr>

<tr>
<td>Format</td>
<td>
<input name="format" type="text" style="width:200px;" value="<?php echo $g_view['data']['format'];?>" />&nbsp;<a href="#" onclick="return fetch_correction_suggestion('format');"><img src="has_deal_suggestion.php?deal_id=<?php echo $deal_id;?>&data_name=format" /></a>
</td>
</tr>
<tr><td colspan="2" id="suggestion_format"></td></tr>

<tr>
<td>Guarantor</td>
<td>
<input name="guarantor" type="text" style="width:200px;" value="<?php echo $g_view['data']['guarantor'];?>" />&nbsp;<a href="#" onclick="return fetch_correction_suggestion('guarantor');"><img src="has_deal_suggestion.php?deal_id=<?php echo $deal_id;?>&data_name=guarantor" /></a>
</td>
</tr>
<tr><td colspan="2" id="suggestion_guarantor"></td></tr>

<tr>
<td>Collateral</td>
<td>
<input name="collateral" type="text" style="width:200px;" value="<?php echo $g_view['data']['collateral'];?>" />&nbsp;<a href="#" onclick="return fetch_correction_suggestion('collateral');"><img src="has_deal_suggestion.php?deal_id=<?php echo $deal_id;?>&data_name=date_announced" /></a>
</td>
</tr>
<tr><td colspan="2" id="suggestion_collateral"></td></tr>

<tr>
<td>Seniority</td>
<td>
<input name="seniority" type="text" style="width:200px;" value="<?php echo $g_view['data']['seniority'];?>" />&nbsp;<a href="#" onclick="return fetch_correction_suggestion('seniority');"><img src="has_deal_suggestion.php?deal_id=<?php echo $deal_id;?>&data_name=seniority" /></a>
</td>
</tr>
<tr><td colspan="2" id="suggestion_seniority"></td></tr>

<tr><td colspan="2"><hr noshade="noshade" /></td></tr>
<tr><td colspan="2"><strong>Fees</strong></td></tr>

<tr>
<td>Fee (Gross)</td>
<td>
<input name="base_fee" type="text" style="width:200px;" value="<?php if($g_view['data']['base_fee']!="0.0") echo $g_view['data']['base_fee'];?>" />%&nbsp;<a href="#" onclick="return fetch_correction_suggestion('base_fee');"><img src="has_deal_suggestion.php?deal_id=<?php echo $deal_id;?>&data_name=base_fee" /></a>
</td>
</tr>
<tr><td colspan="2" id="suggestion_base_fee"></td></tr>

<tr><td colspan="2"><hr noshade="noshade" /></td></tr>
<tr><td colspan="2"><strong>Call Option</strong></td></tr>
<tr>
<td>Years to call</td>
<td>
<input name="year_to_call" type="text" style="width:200px;" value="<?php echo $g_view['data']['year_to_call'];?>" />&nbsp;<a href="#" onclick="return fetch_correction_suggestion('year_to_call');"><img src="has_deal_suggestion.php?deal_id=<?php echo $deal_id;?>&data_name=year_to_call" /></a>
</td>
</tr>
<tr><td colspan="2" id="suggestion_year_to_call"></td></tr>

<tr>
    <td>Call Date</td>
    <td>
    <input name="call_date" id="call_date" type="text" style="width:200px;" value="<?php if($g_view['data']['call_date']=='0000-00-00'||$g_view['data']['call_date']=='') echo ""; else echo $g_view['data']['call_date'];?>" />&nbsp;<a href="#" onclick="return fetch_correction_suggestion('call_date');"><img src="has_deal_suggestion.php?deal_id=<?php echo $deal_id;?>&data_name=call_date" /></a>
    <script type="text/javascript">
          // <![CDATA[       
            var opts = {                            
                    formElements:{"call_date":"Y-ds-m-ds-d"},
                    showWeeks:false                   
            };      
            datePickerController.createDatePicker(opts);
          // ]]>
          </script>
    </td>
</tr>
<tr><td colspan="2" id="suggestion_call_date"></td></tr>

<tr>
<td>Redemption price</td>
<td>
<input name="redemption_price" type="text" style="width:200px;" value="<?php echo $g_view['data']['redemption_price'];?>" />&nbsp;<a href="#" onclick="return fetch_correction_suggestion('redemption_price');"><img src="has_deal_suggestion.php?deal_id=<?php echo $deal_id;?>&data_name=redemption_price" /></a>
</td>
</tr>
<tr><td colspan="2" id="suggestion_redemption_price"></td></tr>

<tr>
<td>Reference price</td>
<td>
<input name="reference_price" type="text" style="width:200px;" value="<?php if($g_view['data']['reference_price']!="0.0") echo $g_view['data']['reference_price'];?>" />&nbsp;<a href="#" onclick="return fetch_correction_suggestion('reference_price');"><img src="has_deal_suggestion.php?deal_id=<?php echo $deal_id;?>&data_name=reference_price" /></a>
</td>
</tr>
<tr><td colspan="2" id="suggestion_reference_price"></td></tr>

<tr>
<td>Conversion price</td>
<td>
<input name="conversion_price" type="text" style="width:200px;" value="<?php if($g_view['data']['conversion_price']!="0.0") echo $g_view['data']['conversion_price'];?>" />&nbsp;<a href="#" onclick="return fetch_correction_suggestion('conversion_price');"><img src="has_deal_suggestion.php?deal_id=<?php echo $deal_id;?>&data_name=conversion_price" /></a>
</td>
</tr>
<tr><td colspan="2" id="suggestion_conversion_price"></td></tr>

<tr>
<td>Currency of Reference price<br />(if different from local currency for the deal)</td>
<td>
<input name="currency_reference_price" id="currency_reference_price" type="text" style="width:200px;" value="<?php echo $g_view['data']['currency_reference_price'];?>" />&nbsp;<a href="#" onclick="return fetch_correction_suggestion('currency_reference_price');"><img src="has_deal_suggestion.php?deal_id=<?php echo $deal_id;?>&data_name=currency_reference_price" /></a>
</td>
</tr>
<tr><td colspan="2" id="suggestion_currency_reference_price"></td></tr>

<tr>
<td>Conversion premium</td>
<td>
<input name="conversion_premia_percent" type="text" style="width:200px;" value="<?php if($g_view['data']['conversion_premia_percent']!="0.0") echo $g_view['data']['conversion_premia_percent'];?>" /> %&nbsp;<a href="#" onclick="return fetch_correction_suggestion('conversion_premia_percent');"><img src="has_deal_suggestion.php?deal_id=<?php echo $deal_id;?>&data_name=conversion_premia_percent" /></a>
</td>
</tr>
<tr><td colspan="2" id="suggestion_conversion_premia_percent"></td></tr>

<tr>
<td>Number of shares underlying</td>
<td>
<input name="num_shares_underlying_million" type="text" style="width:200px;" value="<?php if($g_view['data']['num_shares_underlying_million']!="0.0") echo $g_view['data']['num_shares_underlying_million'];?>" /> (in million)&nbsp;<a href="#" onclick="return fetch_correction_suggestion('num_shares_underlying_million');"><img src="has_deal_suggestion.php?deal_id=<?php echo $deal_id;?>&data_name=num_shares_underlying_million" /></a>
</td>
</tr>
<tr><td colspan="2" id="suggestion_num_shares_underlying_million"></td></tr>


<tr>
<td>Current number of shares in issue</td>
<td>
<input name="curr_num_shares_outstanding_million" type="text" style="width:200px;" value="<?php if($g_view['data']['curr_num_shares_outstanding_million']!="0.0") echo $g_view['data']['curr_num_shares_outstanding_million'];?>" /> (in million)&nbsp;<a href="#" onclick="return fetch_correction_suggestion('curr_num_shares_outstanding_million');"><img src="has_deal_suggestion.php?deal_id=<?php echo $deal_id;?>&data_name=curr_num_shares_outstanding_million" /></a>
</td>
</tr>
<tr><td colspan="2" id="suggestion_curr_num_shares_outstanding_million"></td></tr>

<tr>
<td>Average Daily Trading Volume</td>
<td>
<input name="avg_daily_trading_vol_million" type="text" style="width:200px;" value="<?php if($g_view['data']['avg_daily_trading_vol_million']!="0.0") echo $g_view['data']['avg_daily_trading_vol_million'];?>" /> (in million)&nbsp;<a href="#" onclick="return fetch_correction_suggestion('avg_daily_trading_vol_million');"><img src="has_deal_suggestion.php?deal_id=<?php echo $deal_id;?>&data_name=avg_daily_trading_vol_million" /></a>
</td>
</tr>
<tr><td colspan="2" id="suggestion_avg_daily_trading_vol_million"></td></tr>

<tr>
<td>Underlying shares versus ADTV</td>
<td>
<input name="shares_underlying_vs_adtv_ratio" type="text" style="width:200px;" value="<?php if($g_view['data']['shares_underlying_vs_adtv_ratio']!="0.0") echo $g_view['data']['shares_underlying_vs_adtv_ratio'];?>" />&nbsp;<a href="#" onclick="return fetch_correction_suggestion('shares_underlying_vs_adtv_ratio');"><img src="has_deal_suggestion.php?deal_id=<?php echo $deal_id;?>&data_name=shares_underlying_vs_adtv_ratio" /></a>
</td>
</tr>
<tr><td colspan="2" id="suggestion_shares_underlying_vs_adtv_ratio"></td></tr>

<tr><td colspan="2"><hr noshade="noshade" /></td></tr>
<tr>
<td>Dividend protection mechanism</td>
<td>
<input type="radio" name="dividend_protection" value="y" <?php if($g_view['data']['dividend_protection']=="y"){?>checked="checked"<?php }?>>Yes&nbsp;&nbsp;
<input type="radio" name="dividend_protection" value="n" <?php if($g_view['data']['dividend_protection']=="n"){?>checked="checked"<?php }?>>No&nbsp;<a href="#" onclick="return fetch_correction_suggestion('dividend_protection');"><img src="has_deal_suggestion.php?deal_id=<?php echo $deal_id;?>&data_name=dividend_protection" /></a>
</td>
</tr>
<tr><td colspan="2" id="suggestion_dividend_protection"></td></tr>



</table>
<script>
jQuery('#currency').autocomplete({
	serviceUrl:'ajax/fetch_currency_list.php',
	minChars:1,
	noCache: true,
	onSelect: function(value, data){
		jQuery('#currency').val(data);
	}
});
jQuery('#currency_reference_price').autocomplete({
	serviceUrl:'ajax/fetch_currency_list.php',
	minChars:1,
	noCache: true,
	onSelect: function(value, data){
		jQuery('#currency_reference_price').val(data);
	}
});
</script>