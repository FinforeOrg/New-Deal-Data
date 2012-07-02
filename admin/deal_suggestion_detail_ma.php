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
<td>Date Rumour:</td>
<td><?php if($g_view['data']['date_rumour']!="0000-00-00") echo ymd_to_dmy($g_view['data']['date_rumour']);?></td>
</tr>

<tr>
<td>Date Announced:</td>
<td><?php if($g_view['data']['date_announced']!="0000-00-00") echo ymd_to_dmy($g_view['data']['date_announced']);?></td>
</tr>

<tr>
<td>Date Closed:</td>
<td><?php if($g_view['data']['date_closed']!="0000-00-00") echo ymd_to_dmy($g_view['data']['date_closed']);?></td>
</tr>

<tr>
<td>Name of Parent Company (BUYER):</td>
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
<td>Name of subsidiary (BUYER):</td>
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
<td>Name of TARGET Company / Asset:</td>
<td><?php echo $g_view['data']['target_company_name'];?></td>
</tr>

<tr>
<td>Sector of TARGET Company / Asset:</td>
<td><?php echo $g_view['data']['target_sector'];?></td>
</tr>

<tr>
<td>Industry of TARGET Company / Asset:</td>
<td><?php echo $g_view['data']['target_industry'];?></td>
</tr>

<tr>
<td>Country of Headquarters:</td>
<td><?php echo $g_view['data']['target_country'];?></td>
</tr>


<tr>
<td>Name of SELLER:</td>
<td><?php echo $g_view['data']['seller_company_name'];?></td>
</tr>

<tr>
<td>Sector of SELLER:</td>
<td><?php echo $g_view['data']['seller_sector'];?></td>
</tr>

<tr>
<td>Industry of SELLER:</td>
<td><?php echo $g_view['data']['seller_industry'];?></td>
</tr>

<tr>
<td>Country of Headquarters:</td>
<td><?php echo $g_view['data']['seller_country'];?></td>
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
<td colspan="2">Additional text on Buyer, Target and/or Seller: </td>
</tr>
<tr>
<td colspan="2"><textarea style="width:500px; height:100px;"><?php echo $g_view['data']['note_on_buyer_target_seller'];?></textarea></td>
</tr>

<tr><td colspan="2"><strong>Deal Valuation</strong></td></tr>

<tr>
<td>Payment Type:</td>
<td><?php if($g_view['data']['payment_type']!=""){
	if($g_view['data']['payment_type']=="cash") echo "Cash";
	elseif($g_view['data']['payment_type']=="equity") echo "Equity";
	elseif($g_view['data']['payment_type']=="part_cash_part_quity") echo "Part Cash / Part Equity";
}
?></td>
</tr>

<tr>
<td>Equity Payment %:</td>
<td><?php if($g_view['data']['equity_payment_percent']!=0.0) echo $g_view['data']['equity_payment_percent'];?></td>
</tr>

<tr>
<td>Local Currency (for the deal):</td>
<td><?php echo $g_view['data']['currency'];?></td>
</tr>

<tr>
<td>Local Currency per 1 USD (G):</td>
<td><?php if($g_view['data']['exchange_rate']!=0.0) echo $g_view['data']['exchange_rate'];?></td>
</tr>

<tr>
<td>Target Publicly Listed:</td>
<td><?php if($g_view['data']['target_listed_in_stock_exchange'] == 'y'){?>Yes<?php }else{?>No<?php }?></td>
</tr>

<tr>
<td>Name of the stock exchange:</td>
<td><?php echo $g_view['data']['target_stock_exchange_name'];?></td>
</tr>

<tr>
<td>Local Currency of Share Price:</td>
<td><?php echo $g_view['data']['currency_price_per_share'];?></td>
</tr>

<tr>
<td>Deal price per share (in local currency, per share) (I):</td>
<td><?php if($g_view['data']['deal_price_per_share']!=0.0) echo convert_million_for_display($g_view['data']['deal_price_per_share']);?></td>
</tr>

<tr>
<td>Share price prior to announcement:</td>
<td><?php if($g_view['data']['price_per_share_before_deal_announcement']!=0.0) echo convert_million_for_display($g_view['data']['price_per_share_before_deal_announcement']);?></td>
</tr>

<tr>
<td>Date of share price, prior to announcement:</td>
<td><?php if($g_view['data']['date_price_per_share_before_deal_announcement']!="0000-00-00") echo ymd_to_dmy($g_view['data']['date_price_per_share_before_deal_announcement']);?></td>
</tr>

<tr>
<td>Implied Premium % :</td>
<td><?php if($g_view['data']['implied_premium_percentage']!=0.0) echo $g_view['data']['implied_premium_percentage'];?></td>
</tr>

<tr>
<td>Total Shares Outstanding (million) (II):</td>
<td><?php if($g_view['data']['total_shares_outstanding_million']!=0.0) echo convert_million_for_display($g_view['data']['total_shares_outstanding_million']);?></td>
</tr>

<tr>
<td>Implied Equity Value (in million, local currency) (A):<br />(I x II)</td>
<td><?php if($g_view['data']['implied_equity_value_in_million_local_currency']!=0.0) echo convert_million_for_display($g_view['data']['implied_equity_value_in_million_local_currency']);?></td>
</tr>

<tr>
<td>Net Debt (in million, local currency) (B):</td>
<td><?php if($g_view['data']['net_debt_in_million_local_currency']!=0.0) echo convert_million_for_display($g_view['data']['net_debt_in_million_local_currency']);?></td>
</tr>

<tr>
<td>Enterprise Value (C) (in million, local currency):<br />(A + B)</td>
<td><?php if($g_view['data']['enterprise_value_million_local_currency']!=0.0) echo convert_million_for_display($g_view['data']['enterprise_value_million_local_currency']);?></td>
</tr>

<tr>
<td>Acquisition of what % (D):</td>
<td><?php if($g_view['data']['acquisition_percentage']!=0.0) echo $g_view['data']['acquisition_percentage'];?></td>
</tr>

<tr>
<td>Dividend on Top of Equity (in million, local currency) (E):</td>
<td><?php if($g_view['data']['dividend_on_top_of_equity_million_local_curency']!=0.0) echo convert_million_for_display($g_view['data']['dividend_on_top_of_equity_million_local_curency']);?></td>
</tr>

<tr>
<td>Implied Deal Size (in million, local currency) (F):<br />(if D < 50 := A)<br />(if D > 50 := C + E)</td>
<td><?php if($g_view['data']['value_in_million_local_currency']!=0.0) echo convert_million_for_display($g_view['data']['value_in_million_local_currency']);?></td>
</tr>

<tr>
<td>Implied Deal Size in USD (in million):<br />(F / G)</td>
<td><?php if($g_view['data']['value_in_million']!=0.0) echo convert_million_for_display($g_view['data']['value_in_million']);?></td>
</tr>

<tr>
<td>Implied Enterprise Value in USD (in million):<br />(C / G)</td>
<td><?php if($g_view['data']['enterprise_value_million']!=0.0) echo convert_million_for_display($g_view['data']['enterprise_value_million']);?></td>
</tr>

<tr>
<td colspan="2">Additional text on deal value:</td>
</tr>
<tr>
<td colspan="2"><textarea style="width:500px; height:100px;"><?php echo $g_view['data']['additional_text_on_deal_value'];?></textarea></td>
</tr>

<tr><td colspan="2"><strong>Additional Information</strong></td></tr>

<tr>
<td>Merger Type:</td>
<td><?php if($g_view['data']['takeover_id']!=0) echo $g_view['data']['takeover_name'];?></td>
</tr>

<tr>
<td>Termination Fee (in Local Currency Millions):</td>
<td><?php if($g_view['data']['termination_fee_million']!=0.0) echo convert_million_for_display($g_view['data']['termination_fee_million']);?></td>
</tr>

<tr>
<td>Date for Termination Fee:</td>
<td><?php if($g_view['data']['end_date_termination_fee'] != "0000-00-00") echo ymd_to_dmy($g_view['data']['end_date_termination_fee']);?></td>
</tr>

<tr>
<td colspan="2">Additional text on termination fee:</td>
</tr>
<tr>
<td colspan="2"><textarea style="width:500px; height:100px;"><?php echo $g_view['data']['text_on_termination_fee'];?></textarea></td>
</tr>

<tr>
<td colspan="2">Text on conditions:</td>
</tr>
<tr>
<td colspan="2"><textarea style="width:500px; height:100px;"><?php echo $g_view['data']['text_on_termination_conditions'];?></textarea></td>
</tr>

<tr>
<td>Fee (%) to Sellside Advisors:</td>
<td><?php if($g_view['data']['fee_percent_to_sellside_advisor']!=0.0) echo $g_view['data']['fee_percent_to_sellside_advisor'];?></td>
</tr>

<tr>
<td>Fee (%) to Buyside Advisors:</td>
<td><?php if($g_view['data']['fee_percent_to_buyside_advisor']!=0.0) echo $g_view['data']['fee_percent_to_buyside_advisor'];?></td>
</tr>

<tr><td colspan="2"><strong>Financial metrics, in local currency million</strong></td></tr>
<tr>
<td>Revenues Last 12 Months:</td>
<td><?php if($g_view['data']['revenue_ltm_million']!=0.0) echo convert_million_for_display($g_view['data']['revenue_ltm_million']);?></td>
</tr>

<tr>
<td>Revenues Most Recent Year:</td>
<td><?php if($g_view['data']['revenue_mry_million']!=0.0) echo convert_million_for_display($g_view['data']['revenue_mry_million']);?></td>
</tr>

<tr>
<td>Revenues Next Year:</td>
<td><?php if($g_view['data']['revenue_ny_million']!=0.0) echo convert_million_for_display($g_view['data']['revenue_ny_million']);?></td>
</tr>

<tr>
<td>EBITDA Last 12 Months:</td>
<td><?php if($g_view['data']['ebitda_ltm_million']!=0.0) echo convert_million_for_display($g_view['data']['ebitda_ltm_million']);?></td>
</tr>

<tr>
<td>EBITDA Most Recent Year:</td>
<td><?php if($g_view['data']['ebitda_mry_million']!=0.0) echo convert_million_for_display($g_view['data']['ebitda_mry_million']);?></td>
</tr>

<tr>
<td>EBITDA Next Year:</td>
<td><?php if($g_view['data']['ebitda_ny_million']!=0.0) echo convert_million_for_display($g_view['data']['ebitda_ny_million']);?></td>
</tr>

<tr>
<td>Net Income Last 12 Months:</td>
<td><?php if($g_view['data']['net_income_ltm_million']!=0.0) echo convert_million_for_display($g_view['data']['net_income_ltm_million']);?></td>
</tr>

<tr>
<td>Net Income Most Recent Year:</td>
<td><?php if($g_view['data']['net_income_mry_million']!=0.0) echo convert_million_for_display($g_view['data']['net_income_mry_million']);?></td>
</tr>

<tr>
<td>Net Income Next Year:</td>
<td><?php if($g_view['data']['net_income_ny_million']!=0.0) echo convert_million_for_display($g_view['data']['net_income_ny_million']);?></td>
</tr>

<tr>
<td>Year-End Date of Most Recent Financial Year:</td>
<td><?php if($g_view['data']['date_year_end_of_recent_financial_year'] != "0000-00-00") echo ymd_to_dmy($g_view['data']['date_year_end_of_recent_financial_year']);?></td>
</tr>

<tr>
<td>Email partipating Syndicate Desks/ PR teams:</td>
<td><?php if($g_view['data']['email_participating_syndicates'] == 'y') echo "Yes"; else echo "No";?></td>
</tr>