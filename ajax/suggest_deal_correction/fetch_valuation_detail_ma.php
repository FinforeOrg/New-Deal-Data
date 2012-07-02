<tr>
<td>Deal Size (in USD, million):</td>
<td>
<?php echo convert_deal_value_for_display_round($g_view['deal_data']['value_in_billion'],$g_view['deal_data']['value_range_id'],$g_view['deal_data']['fuzzy_value']);?>
</td>

<?php
if(0==$g_view['suggestion_data_count']){
	?><td class="deal-edit-snippet-mid-col"></td><?php
}else{
	for($q=0;$q<$g_view['suggestion_data_count'];$q++){
	//for suggestion, we do not give value range option
	?>
	<td class="deal-edit-snippet-mid-col"><?php if($g_view['suggestion_data_arr'][$q]['value_in_million']==""||$g_view['suggestion_data_arr'][$q]['value_in_million']==0.0) echo "n/a"; else echo convert_million_for_display_round($g_view['suggestion_data_arr'][$q]['value_in_million'])." million USD";?></td>
	<?php
	}
}
?>

<td class="deal-edit-snippet-right-col">
<input type="text" name="value_in_million" value="<?php if($g_view['deal_data']['value_in_billion']==""||$g_view['deal_data']['value_in_billion']==0.0) echo ""; else echo convert_billion_to_million_for_display_round($g_view['deal_data']['value_in_billion']);?>" class="deal-edit-snippet-textbox-short" /> (in million, USD)
</td>

</tr>
<!--************************************************************-->
<tr>
<td colspan="2"><div class="hr_div"></div></td>
<?php
for($q=0;$q<$num_mid_cols;$q++){
?>
<td class="deal-edit-snippet-mid-col"><div class="hr_div"></div></td>
<?php
}
?>
<td class="deal-edit-snippet-right-col"><div class="hr_div"></div></td>
</tr>

<tr>
<td>Local Currency (for the deal):</td>
<td><?php echo $deal_local_currency;?></td>

<?php
if(0==$g_view['suggestion_data_count']){
	?><td class="deal-edit-snippet-mid-col"></td><?php
}else{
	for($q=0;$q<$g_view['suggestion_data_count'];$q++){
	?>
	<td class="deal-edit-snippet-mid-col"><?php echo $g_view['suggestion_data_arr'][$q]['currency'];?></td>
	<?php
	}
}
?>

<td class="deal-edit-snippet-right-col">
<input type="text" name="currency" id="currency" value="<?php echo $deal_local_currency;?>" class="deal-edit-snippet-textbox-short" />
</td>
<!--************************************************************-->
<tr>
<td>Local Currency per 1 USD:</td>
<td>
<?php if($deal_local_currency=="USD") echo 1; else echo $g_view['deal_data']['exchange_rate'];?>
</td>

<?php
if(0==$g_view['suggestion_data_count']){
	?><td class="deal-edit-snippet-mid-col"></td><?php
}else{
	for($q=0;$q<$g_view['suggestion_data_count'];$q++){
	?>
	<td class="deal-edit-snippet-mid-col"><?php if($g_view['suggestion_data_arr'][$q]['currency']=="USD") echo 1; else echo $g_view['suggestion_data_arr'][$q]['exchange_rate'];?></td>
	<?php
	}
}
?>

<td class="deal-edit-snippet-right-col">
<input type="text" name="exchange_rate" value="<?php if($deal_local_currency=="USD") echo 1; else echo $g_view['deal_data']['exchange_rate'];?>" class="deal-edit-snippet-textbox-short" />
</td>

</tr>
<!--************************************************************-->
<tr>
<td>Deal Size (in million, local currency):</td>
<td>
<?php if($g_view['deal_data']['value_in_billion_local_currency']==""||$g_view['deal_data']['value_in_billion_local_currency']==0.0) echo "n/a"; else echo convert_billion_to_million_for_display_round($g_view['deal_data']['value_in_billion_local_currency'])." million ".$deal_local_currency;?>
</td>

<?php
if(0==$g_view['suggestion_data_count']){
	?><td class="deal-edit-snippet-mid-col"></td><?php
}else{
	for($q=0;$q<$g_view['suggestion_data_count'];$q++){
	?>
	<td class="deal-edit-snippet-mid-col"><?php if($g_view['suggestion_data_arr'][$q]['value_in_million_local_currency']==""||$g_view['suggestion_data_arr'][$q]['value_in_million_local_currency']==0.0) echo "n/a"; else echo convert_million_for_display_round($g_view['suggestion_data_arr'][$q]['value_in_million_local_currency'])." million ". $g_view['suggestion_data_arr'][$q]['currency'];?></td>
	<?php
	}
}
?>

<td class="deal-edit-snippet-right-col">
<input type="text" name="value_in_million_local_currency" value="<?php if($g_view['deal_data']['value_in_billion_local_currency']==""||$g_view['deal_data']['value_in_billion_local_currency']==0.0) echo ""; else echo convert_billion_to_million_for_display_round($g_view['deal_data']['value_in_billion_local_currency']);?>" class="deal-edit-snippet-textbox-short" /> (in million, local currency)
</td>

</tr>
<!--************************************************************-->
<tr>
<td>Stake acquired (%):</td>
<td>
<?php if($g_view['deal_data']['acquisition_percentage']==""||$g_view['deal_data']['acquisition_percentage']==0.0) echo "n/a"; else echo $g_view['deal_data']['acquisition_percentage']."%";?>
</td>

<?php
if(0==$g_view['suggestion_data_count']){
	?><td class="deal-edit-snippet-mid-col"></td><?php
}else{
	for($q=0;$q<$g_view['suggestion_data_count'];$q++){
	?>
	<td class="deal-edit-snippet-mid-col"><?php if($g_view['suggestion_data_arr'][$q]['acquisition_percentage']==""||$g_view['suggestion_data_arr'][$q]['acquisition_percentage']==0.0) echo "n/a"; else echo $g_view['suggestion_data_arr'][$q]['acquisition_percentage']."%";?></td>
	<?php
	}
}
?>

<td class="deal-edit-snippet-right-col">
<input type="text" name="acquisition_percentage" value="<?php if($g_view['deal_data']['acquisition_percentage']==""||$g_view['deal_data']['acquisition_percentage']==0.0) echo ""; else echo $g_view['deal_data']['acquisition_percentage'];?>" class="deal-edit-snippet-textbox-short" />&nbsp;%
</td>

</tr>
<!--************************************************************-->
<tr>
<td colspan="2"><div class="hr_div"></div></td>
<?php
for($q=0;$q<$num_mid_cols;$q++){
?>
<td class="deal-edit-snippet-mid-col"><div class="hr_div"></div></td>
<?php
}
?>
<td class="deal-edit-snippet-right-col"><div class="hr_div"></div></td>
</tr>

<tr>
<td>Enterprise Value (in million, local currency):</td>
<td>
<?php if($g_view['deal_data']['enterprise_value_million_local_currency']==""||$g_view['deal_data']['enterprise_value_million_local_currency']==0.0) echo "n/a"; else echo convert_million_for_display_round($g_view['deal_data']['enterprise_value_million_local_currency'])." million ".$deal_local_currency;?>
</td>

<?php
if(0==$g_view['suggestion_data_count']){
	?><td class="deal-edit-snippet-mid-col"></td><?php
}else{
	for($q=0;$q<$g_view['suggestion_data_count'];$q++){
	?>
	<td class="deal-edit-snippet-mid-col"><?php if($g_view['suggestion_data_arr'][$q]['enterprise_value_million_local_currency']==""||$g_view['suggestion_data_arr'][$q]['enterprise_value_million_local_currency']==0.0) echo "n/a"; else echo convert_million_for_display_round($g_view['suggestion_data_arr'][$q]['enterprise_value_million_local_currency'])." million ". $g_view['suggestion_data_arr'][$q]['currency'];?></td>
	<?php
	}
}
?>

<td class="deal-edit-snippet-right-col">
<input type="text" name="enterprise_value_million_local_currency" value="<?php if($g_view['deal_data']['enterprise_value_million_local_currency']==""||$g_view['deal_data']['enterprise_value_million_local_currency']==0.0) echo ""; else echo $g_view['deal_data']['enterprise_value_million_local_currency'];?>" class="deal-edit-snippet-textbox-short" /> (in million, local currency)
</td>

</tr>
<!--************************************************************-->
<tr>
<td>Total Debt (in million, local currency):</td>
<td>
<?php if($g_view['deal_data']['total_debt_million_local_currency']==""||$g_view['deal_data']['total_debt_million_local_currency']==0.0) echo "n/a"; else echo convert_million_for_display_round($g_view['deal_data']['total_debt_million_local_currency'])." million ".$deal_local_currency;?>
</td>

<?php
if(0==$g_view['suggestion_data_count']){
	?><td class="deal-edit-snippet-mid-col"></td><?php
}else{
	for($q=0;$q<$g_view['suggestion_data_count'];$q++){
	?>
	<td class="deal-edit-snippet-mid-col"><?php if($g_view['suggestion_data_arr'][$q]['total_debt_million_local_currency']==""||$g_view['suggestion_data_arr'][$q]['total_debt_million_local_currency']==0.0) echo "n/a"; else echo convert_million_for_display_round($g_view['suggestion_data_arr'][$q]['total_debt_million_local_currency'])." million ". $g_view['suggestion_data_arr'][$q]['currency'];?></td>
	<?php
	}
}
?>

<td class="deal-edit-snippet-right-col">
<input type="text" name="total_debt_million_local_currency" value="<?php if($g_view['deal_data']['total_debt_million_local_currency']==""||$g_view['deal_data']['total_debt_million_local_currency']==0.0) echo ""; else echo $g_view['deal_data']['total_debt_million_local_currency'];?>" class="deal-edit-snippet-textbox-short" /> (in million, local currency)
</td>

</tr>
<!--************************************************************-->
<tr>
<td>Cash (in million, local currency):</td>
<td>
<?php if($g_view['deal_data']['cash_million_local_currency']==""||$g_view['deal_data']['cash_million_local_currency']==0.0) echo "n/a"; else echo convert_million_for_display_round($g_view['deal_data']['cash_million_local_currency'])." million ".$deal_local_currency;?>
</td>

<?php
if(0==$g_view['suggestion_data_count']){
	?><td class="deal-edit-snippet-mid-col"></td><?php
}else{
	for($q=0;$q<$g_view['suggestion_data_count'];$q++){
	?>
	<td class="deal-edit-snippet-mid-col"><?php if($g_view['suggestion_data_arr'][$q]['cash_million_local_currency']==""||$g_view['suggestion_data_arr'][$q]['cash_million_local_currency']==0.0) echo "n/a"; else echo convert_million_for_display_round($g_view['suggestion_data_arr'][$q]['cash_million_local_currency'])." million ". $g_view['suggestion_data_arr'][$q]['currency'];?></td>
	<?php
	}
}
?>

<td class="deal-edit-snippet-right-col">
<input type="text" name="cash_million_local_currency" value="<?php if($g_view['deal_data']['cash_million_local_currency']==""||$g_view['deal_data']['cash_million_local_currency']==0.0) echo ""; else echo $g_view['deal_data']['cash_million_local_currency'];?>" class="deal-edit-snippet-textbox-short" /> (in million, local currency)
</td>

</tr>
<!--************************************************************-->
<tr>
<td>Adjustments (in million, local currency):</td>
<td>
<?php if($g_view['deal_data']['adjustments_million_local_currency']==""||$g_view['deal_data']['adjustments_million_local_currency']==0.0) echo "n/a"; else echo convert_million_for_display_round($g_view['deal_data']['adjustments_million_local_currency'])." million ".$deal_local_currency;?>
</td>

<?php
if(0==$g_view['suggestion_data_count']){
	?><td class="deal-edit-snippet-mid-col"></td><?php
}else{
	for($q=0;$q<$g_view['suggestion_data_count'];$q++){
	?>
	<td class="deal-edit-snippet-mid-col"><?php if($g_view['suggestion_data_arr'][$q]['adjustments_million_local_currency']==""||$g_view['suggestion_data_arr'][$q]['adjustments_million_local_currency']==0.0) echo "n/a"; else echo convert_million_for_display_round($g_view['suggestion_data_arr'][$q]['adjustments_million_local_currency'])." million ". $g_view['suggestion_data_arr'][$q]['currency'];?></td>
	<?php
	}
}
?>

<td class="deal-edit-snippet-right-col">
<input type="text" name="adjustments_million_local_currency" value="<?php if($g_view['deal_data']['adjustments_million_local_currency']==""||$g_view['deal_data']['adjustments_million_local_currency']==0.0) echo ""; else echo $g_view['deal_data']['adjustments_million_local_currency'];?>" class="deal-edit-snippet-textbox-short" /> (in million, local currency)
</td>

</tr>
<!--************************************************************-->
<tr>
<td>Net Debt (in million, local currency):</td>
<td>
<?php if($g_view['deal_data']['net_debt_in_million_local_currency']==""||$g_view['deal_data']['net_debt_in_million_local_currency']==0.0) echo "n/a"; else echo convert_million_for_display_round($g_view['deal_data']['net_debt_in_million_local_currency'])." million ".$deal_local_currency;?>
</td>

<?php
if(0==$g_view['suggestion_data_count']){
	?><td class="deal-edit-snippet-mid-col"></td><?php
}else{
	for($q=0;$q<$g_view['suggestion_data_count'];$q++){
	?>
	<td class="deal-edit-snippet-mid-col"><?php if($g_view['suggestion_data_arr'][$q]['net_debt_in_million_local_currency']==""||$g_view['suggestion_data_arr'][$q]['net_debt_in_million_local_currency']==0.0) echo "n/a"; else echo convert_million_for_display_round($g_view['suggestion_data_arr'][$q]['net_debt_in_million_local_currency'])." million ". $g_view['suggestion_data_arr'][$q]['currency'];?></td>
	<?php
	}
}
?>

<td class="deal-edit-snippet-right-col">
<input type="text" name="net_debt_in_million_local_currency" value="<?php if($g_view['deal_data']['net_debt_in_million_local_currency']==""||$g_view['deal_data']['net_debt_in_million_local_currency']==0.0) echo ""; else echo $g_view['deal_data']['net_debt_in_million_local_currency'];?>" class="deal-edit-snippet-textbox-short" /> (in million, local currency)
</td>

</tr>
<!--************************************************************-->
<tr>
<td>Equity Value (in million, local currency):</td>
<td>
<?php if($g_view['deal_data']['implied_equity_value_in_million_local_currency']==""||$g_view['deal_data']['implied_equity_value_in_million_local_currency']==0.0) echo "n/a"; else echo convert_million_for_display_round($g_view['deal_data']['implied_equity_value_in_million_local_currency'])." million ".$deal_local_currency;?>
</td>

<?php
if(0==$g_view['suggestion_data_count']){
	?><td class="deal-edit-snippet-mid-col"></td><?php
}else{
	for($q=0;$q<$g_view['suggestion_data_count'];$q++){
	?>
	<td class="deal-edit-snippet-mid-col"><?php if($g_view['suggestion_data_arr'][$q]['implied_equity_value_in_million_local_currency']==""||$g_view['suggestion_data_arr'][$q]['implied_equity_value_in_million_local_currency']==0.0) echo "n/a"; else echo convert_million_for_display_round($g_view['suggestion_data_arr'][$q]['implied_equity_value_in_million_local_currency'])." million ". $g_view['suggestion_data_arr'][$q]['currency'];?></td>
	<?php
	}
}
?>

<td class="deal-edit-snippet-right-col">
<input type="text" name="implied_equity_value_in_million_local_currency" value="<?php if($g_view['deal_data']['implied_equity_value_in_million_local_currency']==""||$g_view['deal_data']['implied_equity_value_in_million_local_currency']==0.0) echo ""; else echo $g_view['deal_data']['implied_equity_value_in_million_local_currency'];?>" class="deal-edit-snippet-textbox-short" /> (in million, local currency)
</td>

</tr>
<!--************************************************************-->
<tr>
<td colspan="2"><div class="hr_div"></div></td>
<?php
for($q=0;$q<$num_mid_cols;$q++){
?>
<td class="deal-edit-snippet-mid-col"><div class="hr_div"></div></td>
<?php
}
?>
<td class="deal-edit-snippet-right-col"><div class="hr_div"></div></td>
</tr>

<tr>
<td>Dividend, Other (in million, local currency):</td>
<td>
<?php if($g_view['deal_data']['dividend_on_top_of_equity_million_local_curency']==""||$g_view['deal_data']['dividend_on_top_of_equity_million_local_curency']==0.0) echo "n/a"; else echo convert_million_for_display_round($g_view['deal_data']['dividend_on_top_of_equity_million_local_curency'])." million ".$deal_local_currency;?>
</td>

<?php
if(0==$g_view['suggestion_data_count']){
	?><td class="deal-edit-snippet-mid-col"></td><?php
}else{
	for($q=0;$q<$g_view['suggestion_data_count'];$q++){
	?>
	<td class="deal-edit-snippet-mid-col"><?php if($g_view['suggestion_data_arr'][$q]['dividend_on_top_of_equity_million_local_curency']==""||$g_view['suggestion_data_arr'][$q]['dividend_on_top_of_equity_million_local_curency']==0.0) echo "n/a"; else echo convert_million_for_display_round($g_view['suggestion_data_arr'][$q]['dividend_on_top_of_equity_million_local_curency'])." million ". $g_view['suggestion_data_arr'][$q]['currency'];?></td>
	<?php
	}
}
?>

<td class="deal-edit-snippet-right-col">
<input type="text" name="dividend_on_top_of_equity_million_local_curency" value="<?php if($g_view['deal_data']['dividend_on_top_of_equity_million_local_curency']==""||$g_view['deal_data']['dividend_on_top_of_equity_million_local_curency']==0.0) echo ""; else echo $g_view['deal_data']['dividend_on_top_of_equity_million_local_curency'];?>" class="deal-edit-snippet-textbox-short" /> (in million, local currency)
</td>

</tr>
<!--************************************************************-->
<tr>
<td>Share Price (in local currency):</td>
<td>
<?php if($g_view['deal_data']['deal_price_per_share']==""||$g_view['deal_data']['deal_price_per_share']==0.0) echo "n/a"; else echo $g_view['deal_data']['deal_price_per_share']." ".$deal_local_currency;?>
</td>

<?php
if(0==$g_view['suggestion_data_count']){
	?><td class="deal-edit-snippet-mid-col"></td><?php
}else{
	for($q=0;$q<$g_view['suggestion_data_count'];$q++){
	?>
	<td class="deal-edit-snippet-mid-col"><?php if($g_view['suggestion_data_arr'][$q]['deal_price_per_share']==""||$g_view['suggestion_data_arr'][$q]['deal_price_per_share']==0.0) echo "n/a"; else echo $g_view['suggestion_data_arr'][$q]['deal_price_per_share']." ". $g_view['suggestion_data_arr'][$q]['currency'];?></td>
	<?php
	}
}
?>

<td class="deal-edit-snippet-right-col">
<input type="text" name="deal_price_per_share" value="<?php if($g_view['deal_data']['deal_price_per_share']==""||$g_view['deal_data']['deal_price_per_share']==0.0) echo ""; else echo $g_view['deal_data']['deal_price_per_share'];?>" class="deal-edit-snippet-textbox-short" /> (in local currency, per share)
</td>

</tr>
<!--************************************************************-->
<tr>
<td>Num of shares (in million):</td>
<td>
<?php if($g_view['deal_data']['total_shares_outstanding_million']==""||$g_view['deal_data']['total_shares_outstanding_million']==0.0) echo "n/a"; else echo convert_million_for_display_round($g_view['deal_data']['total_shares_outstanding_million'])." million";?>
</td>

<?php
if(0==$g_view['suggestion_data_count']){
	?><td class="deal-edit-snippet-mid-col"></td><?php
}else{
	for($q=0;$q<$g_view['suggestion_data_count'];$q++){
	?>
	<td class="deal-edit-snippet-mid-col"><?php if($g_view['suggestion_data_arr'][$q]['total_shares_outstanding_million']==""||$g_view['suggestion_data_arr'][$q]['total_shares_outstanding_million']==0.0) echo "n/a"; else echo convert_million_for_display_round($g_view['suggestion_data_arr'][$q]['total_shares_outstanding_million'])." million";?></td>
	<?php
	}
}
?>

<td class="deal-edit-snippet-right-col">
<input type="text" name="total_shares_outstanding_million" value="<?php if($g_view['deal_data']['total_shares_outstanding_million']==""||$g_view['deal_data']['total_shares_outstanding_million']==0.0) echo ""; else echo $g_view['deal_data']['total_shares_outstanding_million'];?>" class="deal-edit-snippet-textbox-short" /> (in million)
</td>

</tr>
<!--************************************************************-->
<script>
jQuery('#currency').devbridge_autocomplete({
	serviceUrl:'admin/ajax/fetch_currency_list.php',
	minChars:1,
	noCache: true,
	width:'100%',
	onSelect: function(value, data){
		jQuery('#currency').val(data);
	}
});
</script>