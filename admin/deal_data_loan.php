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
<td>Facility Size (local currency)<br />(suggestions are in million) </td>
<td>
<input name="value_in_billion_local_currency" type="text" style="width:200px;" value="<?php if($g_view['data']['value_in_billion_local_currency']!="0.0") echo $g_view['data']['value_in_billion_local_currency'];?>" /> (in <strong>billion</strong>, local currency)&nbsp;<a href="#" onclick="return fetch_correction_suggestion('value_in_million_local_currency');"><img src="has_deal_suggestion.php?deal_id=<?php echo $deal_id;?>&data_name=value_in_million_local_currency" /></a>
</td>
</tr>
<tr><td colspan="2" id="suggestion_value_in_million_local_currency"></td></tr>

<tr>
<td>Facility Size (USD)<br />(suggestions are in million) </td>
<td>
<?php require("deal_data_snippet_value_range.php");?>
</td>
</tr>
<tr><td colspan="2" id="suggestion_value_in_million"></td></tr>

<tr><td colspan="2"><hr noshade="noshade" /></td></tr>
<tr><td colspan="2"><strong>Additional Information</strong></td></tr>

<tr>
<td>Tenor</td>
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
<td>Margin</td>
<td>
<input name="coupon" type="text" style="width:200px;" value="<?php echo $g_view['data']['coupon'];?>" />&nbsp;<a href="#" onclick="return fetch_correction_suggestion('coupon');"><img src="has_deal_suggestion.php?deal_id=<?php echo $deal_id;?>&data_name=coupon" /></a>
</td>
</tr>
<tr><td colspan="2" id="suggestion_coupon"></td></tr>

<tr>
<td>Margin (including ratchet)</td>
<td>
<input name="margin_including_ratchet" type="text" style="width:200px;" value="<?php echo $g_view['data']['margin_including_ratchet'];?>" />&nbsp;<a href="#" onclick="return fetch_correction_suggestion('margin_including_ratchet');"><img src="has_deal_suggestion.php?deal_id=<?php echo $deal_id;?>&data_name=margin_including_ratchet" /></a>
</td>
</tr>
<tr><td colspan="2" id="suggestion_margin_including_ratchet"></td></tr>

<tr>
<td>Collateral</td>
<td>
<input name="collateral" type="text" style="width:200px;" value="<?php echo $g_view['data']['collateral'];?>" />&nbsp;<a href="#" onclick="return fetch_correction_suggestion('collateral');"><img src="has_deal_suggestion.php?deal_id=<?php echo $deal_id;?>&data_name=collateral" /></a>
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
<td>Fee Upfront</td>
<td>
<input name="fee_upfront" type="text" style="width:200px;" value="<?php if($g_view['data']['fee_upfront']!="0.0") echo $g_view['data']['fee_upfront'];?>" />%&nbsp;<a href="#" onclick="return fetch_correction_suggestion('fee_upfront');"><img src="has_deal_suggestion.php?deal_id=<?php echo $deal_id;?>&data_name=fee_upfront" /></a>
</td>
</tr>
<tr><td colspan="2" id="suggestion_fee_upfront"></td></tr>

<tr>
<td>Fee Commitment</td>
<td>
<input name="fee_commitment" type="text" style="width:200px;" value="<?php if($g_view['data']['fee_commitment']!="0.0") echo $g_view['data']['fee_commitment'];?>" />%&nbsp;<a href="#" onclick="return fetch_correction_suggestion('fee_commitment');"><img src="has_deal_suggestion.php?deal_id=<?php echo $deal_id;?>&data_name=fee_commitment" /></a>
</td>
</tr>
<tr><td colspan="2" id="suggestion_fee_commitment"></td></tr>

<tr>
<td>Fee Utilisation</td>
<td>
<input name="fee_utilisation" type="text" style="width:200px;" value="<?php if($g_view['data']['fee_utilisation']!="0.0") echo $g_view['data']['fee_utilisation'];?>" />%&nbsp;<a href="#" onclick="return fetch_correction_suggestion('fee_utilisation');"><img src="has_deal_suggestion.php?deal_id=<?php echo $deal_id;?>&data_name=fee_utilisation" /></a>
</td>
</tr>
<tr><td colspan="2" id="suggestion_fee_utilisation"></td></tr>

<tr>
<td>Fee Arrangement</td>
<td>
<input name="fee_arrangement" type="text" style="width:200px;" value="<?php if($g_view['data']['fee_arrangement']!="0.0") echo $g_view['data']['fee_arrangement'];?>" />%&nbsp;<a href="#" onclick="return fetch_correction_suggestion('fee_arrangement');"><img src="has_deal_suggestion.php?deal_id=<?php echo $deal_id;?>&data_name=fee_arrangement" /></a>
</td>
</tr>
<tr><td colspan="2" id="suggestion_fee_arrangement"></td></tr>

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
</script>