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
/*************************************************************************/
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


<tr>
<td>Transaction price</td>
<td>
<input name="offer_price" type="text" style="width:200px;" value="<?php if($g_view['data']['offer_price']!="0.0") echo $g_view['data']['offer_price'];?>" /> (in local currency)&nbsp;<a href="#" onclick="return fetch_correction_suggestion('offer_price');"><img src="has_deal_suggestion.php?deal_id=<?php echo $deal_id;?>&data_name=offer_price" /></a>
</td>
</tr>
<tr><td colspan="2" id="suggestion_offer_price"></td></tr>

<tr>
<td>Number of shares sold</td>
<td>
<input name="num_shares_underlying_million" type="text" style="width:200px;" value="<?php if($g_view['data']['num_shares_underlying_million']!="0.0") echo $g_view['data']['num_shares_underlying_million'];?>" /> (in million)&nbsp;<a href="#" onclick="return fetch_correction_suggestion('num_shares_underlying_million');"><img src="has_deal_suggestion.php?deal_id=<?php echo $deal_id;?>&data_name=num_shares_underlying_million" /></a>
</td>
</tr>
<tr><td colspan="2" id="suggestion_num_shares_underlying_million"></td></tr>

<tr>
<td>Number of primary shares sold</td>
<td>
<input name="num_primary_shares_million" type="text" style="width:200px;" value="<?php if($g_view['data']['num_primary_shares_million']!="0.0") echo $g_view['data']['num_primary_shares_million'];?>" /> (in million)&nbsp;<a href="#" onclick="return fetch_correction_suggestion('num_primary_shares_million');"><img src="has_deal_suggestion.php?deal_id=<?php echo $deal_id;?>&data_name=num_primary_shares_million" /></a>
</td>
</tr>
<tr><td colspan="2" id="suggestion_num_primary_shares_million"></td></tr>

<tr>
<td>Number of secondary shares sold</td>
<td>
<input name="num_secondary_shares_million" type="text" style="width:200px;" value="<?php if($g_view['data']['num_secondary_shares_million']!="0.0") echo $g_view['data']['num_secondary_shares_million'];?>" /> (in million)&nbsp;<a href="#" onclick="return fetch_correction_suggestion('num_secondary_shares_million');"><img src="has_deal_suggestion.php?deal_id=<?php echo $deal_id;?>&data_name=num_secondary_shares_million" /></a>
</td>
</tr>
<tr><td colspan="2" id="suggestion_num_secondary_shares_million"></td></tr>

<tr>
<td>Number of shares post transaction</td>
<td>
<input name="num_shares_outstanding_after_deal_million" type="text" style="width:200px;" value="<?php if($g_view['data']['num_shares_outstanding_after_deal_million']!="0.0") echo $g_view['data']['num_shares_outstanding_after_deal_million'];?>" /> (in million)&nbsp;<a href="#" onclick="return fetch_correction_suggestion('num_shares_outstanding_after_deal_million');"><img src="has_deal_suggestion.php?deal_id=<?php echo $deal_id;?>&data_name=num_shares_outstanding_after_deal_million" /></a>
</td>
</tr>
<tr><td colspan="2" id="suggestion_num_shares_outstanding_after_deal_million"></td></tr>

<tr>
<td>Free float (%) post transaction</td>
<td>
<input name="free_float_percent" type="text" style="width:200px;" value="<?php if($g_view['data']['free_float_percent']!="0.0") echo $g_view['data']['free_float_percent'];?>" /> %&nbsp;<a href="#" onclick="return fetch_correction_suggestion('free_float_percent');"><img src="has_deal_suggestion.php?deal_id=<?php echo $deal_id;?>&data_name=free_float_percent" /></a>
</td>
</tr>
<tr><td colspan="2" id="suggestion_free_float_percent"></td></tr>

<tr>
<td>Greenshoe included</td>
<td>
<input type="radio" name="greenshoe_included" value="y" <?php if($g_view['data']['greenshoe_included']=="y"){?>checked="checked"<?php }?>>Yes&nbsp;&nbsp;
<input type="radio" name="greenshoe_included" value="n" <?php if($g_view['data']['greenshoe_included']=="n"){?>checked="checked"<?php }?>>No&nbsp;<a href="#" onclick="return fetch_correction_suggestion('greenshoe_included');"><img src="has_deal_suggestion.php?deal_id=<?php echo $deal_id;?>&data_name=greenshoe_included" /></a>
</td>
</tr>
<tr><td colspan="2" id="suggestion_greenshoe_included"></td></tr>

<tr>
<td>Listed on which stock exchange</td>
<td>
<input name="ipo_stock_exchange" id="ipo_stock_exchange" type="text" style="width:200px;" value="<?php echo $g_view['data']['ipo_stock_exchange'];?>" />&nbsp;<a href="#" onclick="return fetch_correction_suggestion('ipo_stock_exchange');"><img src="has_deal_suggestion.php?deal_id=<?php echo $deal_id;?>&data_name=ipo_stock_exchange" /></a>
</td>
</tr>
<tr><td colspan="2" id="suggestion_ipo_stock_exchange"></td></tr>

<tr>
<td>Price at end of first day's trading</td>
<td>
<input name="price_at_end_of_first_day" type="text" style="width:200px;" value="<?php if($g_view['data']['price_at_end_of_first_day']!="0.0") echo $g_view['data']['price_at_end_of_first_day'];?>" />(in local currency)&nbsp;<a href="#" onclick="return fetch_correction_suggestion('price_at_end_of_first_day');"><img src="has_deal_suggestion.php?deal_id=<?php echo $deal_id;?>&data_name=price_at_end_of_first_day" /></a>
</td>
</tr>
<tr><td colspan="2" id="suggestion_price_at_end_of_first_day"></td></tr>

<tr>
    <td>First day of trading</td>
    <td>
    <input name="date_first_trading" id="date_first_trading" type="text" style="width:200px;" value="<?php if($g_view['data']['date_first_trading']=='0000-00-00'||$g_view['data']['date_first_trading']=='') echo ""; else echo $g_view['data']['date_first_trading'];?>" />&nbsp;<a href="#" onclick="return fetch_correction_suggestion('date_first_trading');"><img src="has_deal_suggestion.php?deal_id=<?php echo $deal_id;?>&data_name=date_first_trading" /></a>
    <script type="text/javascript">
          // <![CDATA[       
            var opts = {                            
                    formElements:{"date_first_trading":"Y-ds-m-ds-d"},
                    showWeeks:false                   
            };      
            datePickerController.createDatePicker(opts);
          // ]]>
          </script>
    </td>
</tr>
<tr><td colspan="2" id="suggestion_date_first_trading"></td></tr>

<tr>
<td>Performance on first day</td>
<td>
<input name="1_day_price_change" type="text" style="width:200px;" value="<?php if($g_view['data']['1_day_price_change']!="0.0") echo $g_view['data']['1_day_price_change'];?>" />%&nbsp;<a href="#" onclick="return fetch_correction_suggestion('1_day_price_change');"><img src="has_deal_suggestion.php?deal_id=<?php echo $deal_id;?>&data_name=1_day_price_change" /></a>
</td>
</tr>
<tr><td colspan="2" id="suggestion_1_day_price_change"></td></tr>

<tr>
<td>Fee (Base)</td>
<td>
<input name="base_fee" type="text" style="width:200px;" value="<?php if($g_view['data']['base_fee']!="0.0") echo $g_view['data']['base_fee'];?>" />%&nbsp;<a href="#" onclick="return fetch_correction_suggestion('base_fee');"><img src="has_deal_suggestion.php?deal_id=<?php echo $deal_id;?>&data_name=base_fee" /></a>
</td>
</tr>
<tr><td colspan="2" id="suggestion_base_fee"></td></tr>

<tr>
<td>Fee (Incentive)</td>
<td>
<input name="incentive_fee" type="text" style="width:200px;" value="<?php if($g_view['data']['incentive_fee']!="0.0") echo $g_view['data']['incentive_fee'];?>" />%&nbsp;<a href="#" onclick="return fetch_correction_suggestion('incentive_fee');"><img src="has_deal_suggestion.php?deal_id=<?php echo $deal_id;?>&data_name=incentive_fee" /></a>
</td>
</tr>
<tr><td colspan="2" id="suggestion_incentive_fee"></td></tr>

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

jQuery('#ipo_stock_exchange').autocomplete({
	serviceUrl:'ajax/fetch_stock_exchange_list.php',
	minChars:1,
	noCache: true,
	onSelect: function(value, data){
		jQuery('#ipo_stock_exchange').val(data);
	}
});
</script>