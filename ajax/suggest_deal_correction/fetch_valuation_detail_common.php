<!--************************************************************-->
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
<td>
<?php
if((strtolower($g_view['deal_data']['deal_cat_name']) == "debt")&&(strtolower($g_view['deal_data']['deal_subcat1_name']) == "loan")){?>Facility Size (in million, local currency):<?php }else{?>Deal Size (in million, local currency): <?php }?></td>
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
<td>
<?php
if((strtolower($g_view['deal_data']['deal_cat_name']) == "debt")&&(strtolower($g_view['deal_data']['deal_subcat1_name']) == "loan")){?>Facility Size (in USD, million):<?php }else{?>Deal Size (in USD, million): <?php }?>
</td>
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