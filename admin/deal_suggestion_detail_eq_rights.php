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
<td>Date ex-rights:</td>
<td><?php if($g_view['data']['date_ex_rights']!="0000-00-00") echo ymd_to_dmy($g_view['data']['date_ex_rights']);?></td>
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
<td>Issue Price / Rights Price:</td>
<td><?php if($g_view['data']['offer_price']!=0.0) echo $g_view['data']['offer_price'];?></td>
</tr>

<tr>
<td>Number of shares sold (in million):</td>
<td><?php if($g_view['data']['num_shares_underlying_million']!=0.0) echo $g_view['data']['num_shares_underlying_million'];?></td>
</tr>

<tr>
<td>Currnet number of shares in issue (in million):</td>
<td><?php if($g_view['data']['curr_num_shares_outstanding_million']!=0.0) echo $g_view['data']['curr_num_shares_outstanding_million'];?></td>
</tr>

<tr>
<td>Subscription ratio:</td>
<td><?php echo $g_view['data']['subscription_ratio'];?></td>
</tr>

<tr>
<td colspan="2">Additional information on take-up of rights by existing shareholders:</td>
</tr>
<tr>
<td colspan="2"><textarea style="width:500px; height:100px;"><?php echo $g_view['data']['info_take_up_rights'];?></textarea></td>
</tr>

<tr>
<td>Free float post transaction (%):</td>
<td><?php if($g_view['data']['free_float_percent']!=0.0) echo $g_view['data']['free_float_percent'];?></td>
</tr>

<tr>
<td>Price prior to announcement</td>
<td><?php if($g_view['data']['price_per_share_before_deal_announcement']!=0.0) echo convert_million_for_display($g_view['data']['price_per_share_before_deal_announcement']);?></td>
</tr>

<tr>
<td>Date prior to announcement:</td>
<td><?php if($g_view['data']['date_price_per_share_before_deal_announcement']!="0000-00-00") echo ymd_to_dmy($g_view['data']['date_price_per_share_before_deal_announcement']);?></td>
</tr>

<tr>
<td>Theoretical ex-rights price:</td>
<td><?php if($g_view['data']['terp']!=0.0) echo $g_view['data']['terp'];?></td>
</tr>

<tr>
<td>Premium / discount to TERP (%):</td>
<td><?php if($g_view['data']['discount_to_terp']!=0.0) echo $g_view['data']['discount_to_terp'];?></td>
</tr>

<tr>
<td>Subscription rate (%):</td>
<td><?php if($g_view['data']['subscription_rate_percent']!=0.0) echo $g_view['data']['subscription_rate_percent'];?></td>
</tr>

<tr>
<td>Rump placement:</td>
<td><?php if($g_view['data']['rump_placement']=='y') echo "Yes"; else echo "No";?></td>
</tr>

<?php
if($g_view['data']['rump_placement']=='y'){
?>
<tr>
<td>Number of shares sold in rump (in million):</td>
<td><?php if($g_view['data']['num_shares_sold_in_rump_million']!=0.0) echo $g_view['data']['num_shares_sold_in_rump_million'];?></td>
</tr>

<tr>
<td>Price of rump placement:</td>
<td><?php if($g_view['data']['price_per_share_in_rump']!=0.0) echo $g_view['data']['price_per_share_in_rump'];?></td>
</tr>

<?php
}
?>

<tr>
<td>Fee (Gross) (%):</td>
<td><?php if($g_view['data']['base_fee']!=0.0) echo $g_view['data']['base_fee'];?></td>
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