<tr>
<td>Deal Type:</td>
<td><?php echo $g_view['data']['deal_cat_name'];?></td>
</tr>

<tr>
<td>Deal Sub-type:</td>
<td><?php echo $g_view['data']['deal_subcat1_name'];?></td>
</tr>

<tr>
<td>Deal Sub sub-type:</td>
<td><?php echo $g_view['data']['deal_subcat2_name'];?></td>
</tr>

<tr>
<td>Date Announced / Filed:</td>
<td><?php if($g_view['data']['date_announced']!="0000-00-00") echo ymd_to_dmy($g_view['data']['date_announced']);?></td>
</tr>

<tr>
<td>Date Closed / Trading:</td>
<td><?php if($g_view['data']['date_closed']!="0000-00-00") echo ymd_to_dmy($g_view['data']['date_closed']);?></td>
</tr>

<tr>
<td>Name of Parent Company:</td>
<td><?php echo $g_view['data']['deal_company_name'];?></td>
</tr>

<tr>
<td>Sector of parent Company:</td>
<td><?php echo $g_view['data']['deal_company_sector'];?></td>
</tr>

<tr>
<td>Industry of parent Company:</td>
<td><?php echo $g_view['data']['deal_company_industry'];?></td>
</tr>

<tr>
<td>Country of Headquarters:</td>
<td><?php echo $g_view['data']['deal_company_country'];?></td>
</tr>

<tr>
<td>Name of subsidiary:</td>
<td><?php echo $g_view['data']['buyer_subsidiary_name'];?></td>
</tr>

<tr>
<td>Sector of Subsidiary:</td>
<td><?php echo $g_view['data']['buyer_subsidiary_sector'];?></td>
</tr>

<tr>
<td>Industry of Subsidiary:</td>
<td><?php echo $g_view['data']['buyer_subsidiary_industry'];?></td>
</tr>

<tr>
<td>Country of Headquarters:</td>
<td><?php echo $g_view['data']['buyer_subsidiary_country'];?></td>
</tr>

<tr>
<td>Sources:</td>
<td>
<?php
/************
sources are separated by comma
*****************/
if($g_view['data']['sources']!=""){
	$source_urls = explode(",",$g_view['data']['sources']);
	?>
	<ol>
	<?php
	foreach($source_urls as $source){
		$source = trim($source);
		?>
		<li><?php echo $source;?></li>
		<?php
	}
	?>
	</ol>
	<?php
}
?>
</td>
</tr>

<tr>
<td>Local Currency (for the deal):</td>
<td><?php echo $g_view['data']['currency'];?></td>
</tr>

<tr>
<td>Local Currency per 1 USD:</td>
<td><?php if($g_view['data']['exchange_rate']!=0.0) echo $g_view['data']['exchange_rate'];?></td>
</tr>

<tr>
<td>Deal value (in million, local currency):</td>
<td><?php if($g_view['data']['value_in_million_local_currency']!=0.0) echo convert_million_for_display($g_view['data']['value_in_million_local_currency']);?></td>
</tr>

<tr>
<td>Deal value in USD (in million):</td>
<td><?php if($g_view['data']['value_in_million']!=0.0) echo convert_million_for_display($g_view['data']['value_in_million']);?></td>
</tr>

<tr><td colspan="2"><strong>Additional Information</strong></td></tr>

<tr>
<td>Transaction price:</td>
<td><?php if($g_view['data']['offer_price']!=0.0) echo $g_view['data']['offer_price'];?></td>
</tr>

<tr>
<td>Number of shares sold (in million):</td>
<td><?php if($g_view['data']['num_shares_underlying_million']!=0.0) echo $g_view['data']['num_shares_underlying_million'];?></td>
</tr>

<tr>
<td>Primary shares sold (in million):</td>
<td><?php if($g_view['data']['num_primary_shares_million']!=0.0) echo $g_view['data']['num_primary_shares_million'];?></td>
</tr>

<tr>
<td>Secondary shares sold (in million):</td>
<td><?php if($g_view['data']['num_secondary_shares_million']!=0.0) echo $g_view['data']['num_secondary_shares_million'];?></td>
</tr>

<tr>
<td>Number of shares post transaction (in million):</td>
<td><?php if($g_view['data']['num_shares_outstanding_after_deal_million']!=0.0) echo $g_view['data']['num_shares_outstanding_after_deal_million'];?></td>
</tr>

<tr>
<td>Free float post transaction (%):</td>
<td><?php if($g_view['data']['free_float_percent']!=0.0) echo $g_view['data']['free_float_percent'];?></td>
</tr>

<tr>
<td>Greenshoe included:</td>
<td><?php if($g_view['data']['greenshoe_included'] == 'y') echo "Yes"; else echo "No";?></td>
</tr>

<tr>
<td colspan="2">Names of selling shareholders and details on greenshoe: </td>
</tr>
<tr>
<td colspan="2"><textarea style="width:500px; height:100px;"><?php echo $g_view['data']['note_on_selling_shareholders'];?></textarea></td>
</tr>

<tr>
<td>Listed on which stock exchange:</td>
<td><?php echo $g_view['data']['ipo_stock_exchange'];?></td>
</tr>

<tr>
<td>Price at end of first day's trading:</td>
<td><?php if($g_view['data']['price_at_end_of_first_day']!=0.0) echo convert_million_for_display($g_view['data']['price_at_end_of_first_day']);?></td>
</tr>

<tr>
<td>First day of trading:</td>
<td><?php if($g_view['data']['date_first_trading']!="0000-00-00") echo ymd_to_dmy($g_view['data']['date_first_trading']);?></td>
</tr>

<tr>
<td>Performance on first day (%):</td>
<td><?php if($g_view['data']['1_day_price_change']!=0.0) echo $g_view['data']['1_day_price_change'];?></td>
</tr>

<tr>
<td>Fee (Base) (%):</td>
<td><?php if($g_view['data']['base_fee']!=0.0) echo $g_view['data']['base_fee'];?></td>
</tr>

<tr>
<td>Fee (Incentive) (%):</td>
<td><?php if($g_view['data']['incentive_fee']!=0.0) echo $g_view['data']['incentive_fee'];?></td>
</tr>

<tr>
<td colspan="2">Additional info on this deal: </td>
</tr>
<tr>
<td colspan="2"><textarea style="width:500px; height:100px;"><?php echo $g_view['data']['note_on_deal'];?></textarea></td>
</tr>

<tr>
<td>Email partipating Syndicate Desks/ PR teams:</td>
<td><?php if($g_view['data']['email_participating_syndicates'] == 'y') echo "Yes"; else echo "No";?></td>
</tr>