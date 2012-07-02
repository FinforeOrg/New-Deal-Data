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
    <td>Ex-rights</td>
    <td>
    <input name="date_ex_rights" id="date_ex_rights" type="text" style="width:200px;" value="<?php if($g_view['data']['date_ex_rights']=='0000-00-00'||$g_view['data']['date_ex_rights']=='') echo ""; else echo $g_view['data']['date_ex_rights'];?>" />&nbsp;<a href="#" onclick="return fetch_correction_suggestion('date_ex_rights');"><img src="has_deal_suggestion.php?deal_id=<?php echo $deal_id;?>&data_name=date_ex_rights" /></a>
    <script type="text/javascript">
          // <![CDATA[       
            var opts = {                            
                    formElements:{"date_ex_rights":"Y-ds-m-ds-d"},
                    showWeeks:false                   
            };      
            datePickerController.createDatePicker(opts);
          // ]]>
          </script>
    </td>
</tr>
<tr><td colspan="2" id="suggestion_date_ex_rights"></td></tr>

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
<td>Issue Price / Rights Price</td>
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
<td>Current number of shares in issue</td>
<td>
<input name="curr_num_shares_outstanding_million" type="text" style="width:200px;" value="<?php if($g_view['data']['curr_num_shares_outstanding_million']!="0.0") echo $g_view['data']['curr_num_shares_outstanding_million'];?>" /> (in million)&nbsp;<a href="#" onclick="return fetch_correction_suggestion('curr_num_shares_outstanding_million');"><img src="has_deal_suggestion.php?deal_id=<?php echo $deal_id;?>&data_name=curr_num_shares_outstanding_million" /></a>
</td>
</tr>
<tr><td colspan="2" id="suggestion_curr_num_shares_outstanding_million"></td></tr>

<tr>
<td>Subscription ratio</td>
<td>
<input name="subscription_ratio" type="text" style="width:200px;" value="<?php if($g_view['data']['subscription_ratio']!="0.0") echo $g_view['data']['subscription_ratio'];?>" />&nbsp;<a href="#" onclick="return fetch_correction_suggestion('subscription_ratio');"><img src="has_deal_suggestion.php?deal_id=<?php echo $deal_id;?>&data_name=subscription_ratio" /></a>
</td>
</tr>
<tr><td colspan="2" id="suggestion_subscription_ratio"></td></tr>

<tr>
<td>Free float (%) post transaction</td>
<td>
<input name="free_float_percent" type="text" style="width:200px;" value="<?php if($g_view['data']['free_float_percent']!="0.0") echo $g_view['data']['free_float_percent'];?>" /> %&nbsp;<a href="#" onclick="return fetch_correction_suggestion('free_float_percent');"><img src="has_deal_suggestion.php?deal_id=<?php echo $deal_id;?>&data_name=free_float_percent" /></a>
</td>
</tr>
<tr><td colspan="2" id="suggestion_free_float_percent"></td></tr>

<tr>
<td>Price prior to announcement</td>
<td>
<input name="price_per_share_before_deal_announcement" type="text" style="width:200px;" value="<?php if($g_view['data']['price_per_share_before_deal_announcement']!="0.0") echo $g_view['data']['price_per_share_before_deal_announcement'];?>" />(in local currency)&nbsp;<a href="#" onclick="return fetch_correction_suggestion('price_per_share_before_deal_announcement');"><img src="has_deal_suggestion.php?deal_id=<?php echo $deal_id;?>&data_name=price_per_share_before_deal_announcement" /></a>
</td>
</tr>
<tr><td colspan="2" id="suggestion_price_per_share_before_deal_announcement"></td></tr>

<tr>
    <td>Date prior to announcement</td>
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
<td>TERP</td>
<td>
<input name="terp" type="text" style="width:200px;" value="<?php if($g_view['data']['terp']!="0.0") echo $g_view['data']['terp'];?>" />&nbsp;<a href="#" onclick="return fetch_correction_suggestion('terp');"><img src="has_deal_suggestion.php?deal_id=<?php echo $deal_id;?>&data_name=terp" /></a>
</td>
</tr>
<tr><td colspan="2" id="suggestion_terp"></td></tr>

<tr>
<td>Premium / discount to TERP</td>
<td>
<input name="discount_to_terp" type="text" style="width:200px;" value="<?php if($g_view['data']['discount_to_terp']!="0.0") echo $g_view['data']['discount_to_terp'];?>" /> %&nbsp;<a href="#" onclick="return fetch_correction_suggestion('discount_to_terp');"><img src="has_deal_suggestion.php?deal_id=<?php echo $deal_id;?>&data_name=discount_to_terp" /></a>
</td>
</tr>
<tr><td colspan="2" id="suggestion_discount_to_terp"></td></tr>

<tr>
<td>Subscription rate</td>
<td>
<input name="subscription_rate_percent" type="text" style="width:200px;" value="<?php if($g_view['data']['subscription_rate_percent']!="0.0") echo $g_view['data']['subscription_rate_percent'];?>" />%&nbsp;<a href="#" onclick="return fetch_correction_suggestion('subscription_rate_percent');"><img src="has_deal_suggestion.php?deal_id=<?php echo $deal_id;?>&data_name=subscription_rate_percent" /></a>
</td>
</tr>
<tr><td colspan="2" id="suggestion_subscription_rate_percent"></td></tr>

<tr>
<td>Rump placement</td>
<td>
<input type="radio" name="rump_placement" value="y" <?php if($g_view['data']['rump_placement']=="y"){?>checked="checked"<?php }?>>Yes&nbsp;&nbsp;
<input type="radio" name="rump_placement" value="n" <?php if($g_view['data']['rump_placement']=="n"){?>checked="checked"<?php }?>>No&nbsp;<a href="#" onclick="return fetch_correction_suggestion('rump_placement');"><img src="has_deal_suggestion.php?deal_id=<?php echo $deal_id;?>&data_name=rump_placement" /></a>
</td>
</tr>
<tr><td colspan="2" id="suggestion_rump_placement"></td></tr>

<tr>
<td>Number of shares sold in rump</td>
<td>
<input name="num_shares_sold_in_rump_million" type="text" style="width:200px;" value="<?php if($g_view['data']['num_shares_sold_in_rump_million']!="0.0") echo $g_view['data']['num_shares_sold_in_rump_million'];?>" /> (in million)&nbsp;<a href="#" onclick="return fetch_correction_suggestion('num_shares_sold_in_rump_million');"><img src="has_deal_suggestion.php?deal_id=<?php echo $deal_id;?>&data_name=num_shares_sold_in_rump_million" /></a>
</td>
</tr>
<tr><td colspan="2" id="suggestion_num_shares_sold_in_rump_million"></td></tr>

<tr>
<td>Price of rump placement</td>
<td>
<input name="price_per_share_in_rump" type="text" style="width:200px;" value="<?php if($g_view['data']['price_per_share_in_rump']!="0.0") echo $g_view['data']['price_per_share_in_rump'];?>" />&nbsp;<a href="#" onclick="return fetch_correction_suggestion('price_per_share_in_rump');"><img src="has_deal_suggestion.php?deal_id=<?php echo $deal_id;?>&data_name=price_per_share_in_rump" /></a>
</td>
</tr>
<tr><td colspan="2" id="suggestion_price_per_share_in_rump"></td></tr>

<tr>
<td>Fee (Gross)</td>
<td>
<input name="base_fee" type="text" style="width:200px;" value="<?php if($g_view['data']['base_fee']!="0.0") echo $g_view['data']['base_fee'];?>" />%&nbsp;<a href="#" onclick="return fetch_correction_suggestion('base_fee');"><img src="has_deal_suggestion.php?deal_id=<?php echo $deal_id;?>&data_name=base_fee" /></a>
</td>
</tr>
<tr><td colspan="2" id="suggestion_base_fee"></td></tr>

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