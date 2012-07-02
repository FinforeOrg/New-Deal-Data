<tr>
<td>Deal Type:</td>
<td><?php echo $g_view['data']['deal_cat_name'];?></td>
</tr>

<tr>
<td>Deal Sub-type:</td>
<td><?php echo $g_view['data']['deal_subcat1_name'];?></td>
</tr>

<tr>
<td>Deal Sub-subtype:</td>
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
<td>Facility size (in million, local currency):</td>
<td><?php if($g_view['data']['value_in_million_local_currency']!=0.0) echo convert_million_for_display($g_view['data']['value_in_million_local_currency']);?></td>
</tr>

<tr>
<td>Facility size in USD (in million):</td>
<td><?php if($g_view['data']['value_in_million']!=0.0) echo convert_million_for_display($g_view['data']['value_in_million']);?></td>
</tr>

<tr>
<td>Tenor:</td>
<td><?php echo $g_view['data']['years_to_maturity'];?></td>
</tr>

<tr>
<td>End date:</td>
<td><?php if($g_view['data']['maturity_date']!="0000-00-00") echo ymd_to_dmy($g_view['data']['maturity_date']);?></td>
</tr>

<tr>
<td>Margin:</td>
<td><?php echo $g_view['data']['coupon'];?></td>
</tr>

<tr>
<td>Margin (including ratchet):</td>
<td><?php echo $g_view['data']['margin_including_ratchet'];?></td>
</tr>

<tr>
<td>Collateral:</td>
<td><?php echo $g_view['data']['collateral'];?></td>
</tr>

<tr>
<td>Seniority:</td>
<td><?php echo $g_view['data']['seniority'];?></td>
</tr>

<tr><td colspan="2"><strong>Fee</strong></td></tr>

<tr>
<td>Fee upfront (%):</td>
<td><?php if($g_view['data']['fee_upfront']!=0.0) echo $g_view['data']['fee_upfront'];?></td>
</tr>

<tr>
<td>Fee commitment (%):</td>
<td><?php if($g_view['data']['fee_commitment']!=0.0) echo $g_view['data']['fee_commitment'];?></td>
</tr>

<tr>
<td>Fee utilisation (%):</td>
<td><?php if($g_view['data']['fee_utilisation']!=0.0) echo $g_view['data']['fee_utilisation'];?></td>
</tr>
<tr>
<td>Fee arrangement (%):</td>
<td><?php if($g_view['data']['fee_arrangement']!=0.0) echo $g_view['data']['fee_arrangement'];?></td>
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