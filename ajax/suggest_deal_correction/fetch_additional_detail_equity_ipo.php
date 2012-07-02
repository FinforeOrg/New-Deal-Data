<?php
/**************
remember that not all deals will have extra data, and even then, the date field may contain the default 0 value

This is Bond deal. If BOTH date_announced AND date_closed are not given BUT date_of_deal is there then:
Completed: is date_of_deal
******************/
$date_announced = "";
$date_announced_for_value = "";

$date_completed = "";
$date_completed_for_value = "";

if(($g_view['deal_data']['date_announced']=="0000-00-00"||$g_view['deal_data']['date_announced']=="")&&($g_view['deal_data']['date_closed']=="0000-00-00"||$g_view['deal_data']['date_closed']=="")&&$g_view['deal_data']['date_of_deal']!="0000-00-00"){
	$date_completed = ymd_to_dmy($g_view['deal_data']['date_of_deal']);
	$date_completed_for_value = $g_view['deal_data']['date_of_deal'];
}
?>
<!--************************************************************-->
<tr>
<td>Announced / Filed:</td>
<td>
<?php
if($g_view['deal_data']['date_announced']=="0000-00-00"||$g_view['deal_data']['date_announced']==""){
	echo "n/a";
	$date_announced_for_value = "";
}else{
	echo ymd_to_dmy($g_view['deal_data']['date_announced']);
	$date_announced_for_value = $g_view['deal_data']['date_announced'];
}
?>
</td>

<?php
if(0==$g_view['suggestion_data_count']){
	?><td class="deal-edit-snippet-mid-col"></td><?php
}else{
	for($q=0;$q<$g_view['suggestion_data_count'];$q++){
	?>
	<td class="deal-edit-snippet-mid-col"><?php if($g_view['suggestion_data_arr'][$q]['date_announced']=="0000-00-00"||$g_view['suggestion_data_arr'][$q]['date_announced']=="") echo "n/a"; else echo ymd_to_dmy($g_view['suggestion_data_arr'][$q]['date_announced']);?></td>
	<?php
	}
}
?>

<td class="deal-edit-snippet-right-col">
<input type="text" name="date_announced" id="date_announced" class="deal-edit-snippet-textbox-short" value="<?php echo $date_announced_for_value;?>" />
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
<!--***********************************************************************-->
<tr>
<td>Closed / Trading:</td>
<td>
<?php
if($date_completed!=""){
	echo $date_completed;
	//date_completed_for_value doe not change
}elseif($g_view['deal_data']['date_closed']=="0000-00-00"||$g_view['deal_data']['date_closed']==""){
	echo "n/a";
	$date_completed_for_value = "";
}else{
	echo ymd_to_dmy($g_view['deal_data']['date_closed']);
	$date_completed_for_value = $g_view['deal_data']['date_closed'];
}?>
</td>

<?php
if(0==$g_view['suggestion_data_count']){
	?><td class="deal-edit-snippet-mid-col"></td><?php
}else{
	for($q=0;$q<$g_view['suggestion_data_count'];$q++){
	?>
	<td class="deal-edit-snippet-mid-col"><?php if($g_view['suggestion_data_arr'][$q]['date_closed']=="0000-00-00"||$g_view['suggestion_data_arr'][$q]['date_closed']=="") echo "n/a"; else echo ymd_to_dmy($g_view['suggestion_data_arr'][$q]['date_closed']);?></td>
	<?php
	}
}
?>

<td class="deal-edit-snippet-right-col">
<input type="text" name="date_closed" id="date_closed" class="deal-edit-snippet-textbox-short" value="<?php echo $date_completed_for_value;?>" />
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
<!--************************************************************-->
<tr>
<td>Transaction Price:</td>
<td>
<?php if(($g_view['deal_data']['offer_price']=="")||($g_view['deal_data']['offer_price']=="0.0")) echo "n/a";else echo convert_million_for_display($g_view['deal_data']['offer_price'])." ".$deal_local_currency;?>
</td>

<?php
if(0==$g_view['suggestion_data_count']){
	?><td class="deal-edit-snippet-mid-col"></td><?php
}else{
	for($q=0;$q<$g_view['suggestion_data_count'];$q++){
	?>
	<td class="deal-edit-snippet-mid-col"><?php if($g_view['suggestion_data_arr'][$q]['offer_price']=="0.0") echo "n/a"; else echo convert_million_for_display($g_view['suggestion_data_arr'][$q]['offer_price'])." ".$xxx_suggestion_currency;?></td>
	<?php
	}
}
?>

<td class="deal-edit-snippet-right-col">
<input type="text" name="offer_price" value="<?php if(($g_view['deal_data']['offer_price']=="")||($g_view['deal_data']['offer_price']=="0.0")) echo "";else echo convert_million_for_display($g_view['deal_data']['offer_price']);?>" class="deal-edit-snippet-textbox-short" /> (in local currency)
</td>
</tr>
<!--************************************************************-->
<tr>
<td>Number of shares sold:</td>
<td>
<?php if(($g_view['deal_data']['num_shares_underlying_million']=="")||($g_view['deal_data']['num_shares_underlying_million']==0.0)) echo "n/a"; else echo convert_million_for_display($g_view['deal_data']['num_shares_underlying_million'])."m";?>
</td>

<?php
if(0==$g_view['suggestion_data_count']){
	?><td class="deal-edit-snippet-mid-col"></td><?php
}else{
	for($q=0;$q<$g_view['suggestion_data_count'];$q++){
	?>
	<td class="deal-edit-snippet-mid-col"><?php if($g_view['suggestion_data_arr'][$q]['num_shares_underlying_million']==0.0) echo "n/a"; else echo convert_million_for_display($g_view['suggestion_data_arr'][$q]['num_shares_underlying_million'])."m";?></td>
	<?php
	}
}
?>

<td class="deal-edit-snippet-right-col">
<input type="text" name="num_shares_underlying_million" value="<?php if(($g_view['deal_data']['num_shares_underlying_million']=="")||($g_view['deal_data']['num_shares_underlying_million']==0.0)) echo ""; else echo $g_view['deal_data']['num_shares_underlying_million'];?>" class="deal-edit-snippet-textbox-short" /> (in million)
</td>

</tr>
<!--************************************************************-->
<tr>
<td>Number of primary shares sold:</td>
<td>
<?php if(($g_view['deal_data']['num_primary_shares_million']=="")||($g_view['deal_data']['num_primary_shares_million']==0.0)) echo "n/a"; else echo convert_million_for_display($g_view['deal_data']['num_primary_shares_million'])."m";?>
</td>

<?php
if(0==$g_view['suggestion_data_count']){
	?><td class="deal-edit-snippet-mid-col"></td><?php
}else{
	for($q=0;$q<$g_view['suggestion_data_count'];$q++){
	?>
	<td class="deal-edit-snippet-mid-col"><?php if($g_view['suggestion_data_arr'][$q]['num_primary_shares_million']==0.0) echo "n/a"; else echo convert_million_for_display($g_view['suggestion_data_arr'][$q]['num_primary_shares_million'])."m";?></td>
	<?php
	}
}
?>

<td class="deal-edit-snippet-right-col">
<input type="text" name="num_primary_shares_million" value="<?php if(($g_view['deal_data']['num_primary_shares_million']=="")||($g_view['deal_data']['num_primary_shares_million']==0.0)) echo ""; else echo $g_view['deal_data']['num_primary_shares_million'];?>" class="deal-edit-snippet-textbox-short" /> (in million)
</td>

</tr>
<!--************************************************************-->
<tr>
<td>Number of secondary shares sold:</td>
<td>
<?php if(($g_view['deal_data']['num_secondary_shares_million']=="")||($g_view['deal_data']['num_secondary_shares_million']==0.0)) echo "n/a"; else echo convert_million_for_display($g_view['deal_data']['num_secondary_shares_million'])."m";?>
</td>

<?php
if(0==$g_view['suggestion_data_count']){
	?><td class="deal-edit-snippet-mid-col"></td><?php
}else{
	for($q=0;$q<$g_view['suggestion_data_count'];$q++){
	?>
	<td class="deal-edit-snippet-mid-col"><?php if($g_view['suggestion_data_arr'][$q]['num_secondary_shares_million']==0.0) echo "n/a"; else echo convert_million_for_display($g_view['suggestion_data_arr'][$q]['num_secondary_shares_million'])."m";?></td>
	<?php
	}
}
?>

<td class="deal-edit-snippet-right-col">
<input type="text" name="num_secondary_shares_million" value="<?php if(($g_view['deal_data']['num_secondary_shares_million']=="")||($g_view['deal_data']['num_secondary_shares_million']==0.0)) echo ""; else echo $g_view['deal_data']['num_secondary_shares_million'];?>" class="deal-edit-snippet-textbox-short" /> (in million)
</td>

</tr>
<!--************************************************************-->
<tr>
<td>Number of shares post transaction:</td>
<td>
<?php if(($g_view['deal_data']['num_shares_outstanding_after_deal_million']=="")||($g_view['deal_data']['num_shares_outstanding_after_deal_million']==0.0)) echo "n/a"; else echo convert_million_for_display($g_view['deal_data']['num_shares_outstanding_after_deal_million'])."m";?>
</td>

<?php
if(0==$g_view['suggestion_data_count']){
	?><td class="deal-edit-snippet-mid-col"></td><?php
}else{
	for($q=0;$q<$g_view['suggestion_data_count'];$q++){
	?>
	<td class="deal-edit-snippet-mid-col"><?php if($g_view['suggestion_data_arr'][$q]['num_shares_outstanding_after_deal_million']==0.0) echo "n/a"; else echo convert_million_for_display($g_view['suggestion_data_arr'][$q]['num_shares_outstanding_after_deal_million'])."m";?></td>
	<?php
	}
}
?>

<td class="deal-edit-snippet-right-col">
<input type="text" name="num_shares_outstanding_after_deal_million" value="<?php if(($g_view['deal_data']['num_shares_outstanding_after_deal_million']=="")||($g_view['deal_data']['num_shares_outstanding_after_deal_million']==0.0)) echo ""; else echo $g_view['deal_data']['num_shares_outstanding_after_deal_million'];?>" class="deal-edit-snippet-textbox-short" /> (in million)
</td>

</tr>
<!--************************************************************-->
<tr>
<td>Free-float % post transaction:</td>
<td>
<?php if($g_view['deal_data']['free_float_percent']==""||$g_view['deal_data']['free_float_percent']==0.0) echo "n/a"; else echo $g_view['deal_data']['free_float_percent']."%";?>
</td>

<?php
if(0==$g_view['suggestion_data_count']){
	?><td class="deal-edit-snippet-mid-col"></td><?php
}else{
	for($q=0;$q<$g_view['suggestion_data_count'];$q++){
	?>
	<td class="deal-edit-snippet-mid-col"><?php if($g_view['suggestion_data_arr'][$q]['free_float_percent']==0.0) echo "n/a"; else echo $g_view['suggestion_data_arr'][$q]['free_float_percent']."%";?></td>
	<?php
	}
}
?>

<td class="deal-edit-snippet-right-col">
<input type="text" name="free_float_percent" value="<?php if($g_view['deal_data']['free_float_percent']==""||$g_view['deal_data']['free_float_percent']==0.0) echo ""; else echo $g_view['deal_data']['free_float_percent']."%";?>" class="deal-edit-snippet-textbox-short" />&nbsp;%
</td>

</tr>
<!--************************************************************-->
<tr>
<td>Greenshoe included:</td>
<td><?php if($g_view['deal_data']['greenshoe_included'] == 'y'){?>Yes<?php }else{?>No<?php }?></td>

<?php
if(0==$g_view['suggestion_data_count']){
	?><td class="deal-edit-snippet-mid-col"></td><?php
}else{
	for($q=0;$q<$g_view['suggestion_data_count'];$q++){
	?>
	<td class="deal-edit-snippet-mid-col"><?php if($g_view['suggestion_data_arr'][$q]['greenshoe_included'] == 'y'){?>Yes<?php }else{?>No<?php }?></td>
	<?php
	}
}
?>

<td class="deal-edit-snippet-right-col">
<input type="radio" name="greenshoe_included" value="y" <?php if($g_view['deal_data']['greenshoe_included'] == 'y'){?>checked="checked"<?php }?> >Yes&nbsp;&nbsp;
<input type="radio" name="greenshoe_included" value="n" <?php if($g_view['deal_data']['greenshoe_included'] == 'n'){?>checked="checked"<?php }?> >No
</td>

</tr>
<!--************************************************************-->
<tr>
<td>Listed on which stock exchange:</td>
<td><?php if($g_view['deal_data']['ipo_stock_exchange'] == '') echo "n/a"; else echo $g_view['deal_data']['ipo_stock_exchange'];?></td>

<?php
if(0==$g_view['suggestion_data_count']){
	?><td class="deal-edit-snippet-mid-col"></td><?php
}else{
	for($q=0;$q<$g_view['suggestion_data_count'];$q++){
	?>
	<td class="deal-edit-snippet-mid-col"><?php echo $g_view['suggestion_data_arr'][$q]['ipo_stock_exchange'];?></td>
	<?php
	}
}
?>

<td class="deal-edit-snippet-right-col">
<input type="text" name="ipo_stock_exchange" id="ipo_stock_exchange" class="deal-edit-snippet-textbox" />
</td>

</tr>
<!--************************************************************-->
<tr>
<td>Price at end of first day's trading:</td>
<td>
<?php if(($g_view['deal_data']['price_at_end_of_first_day']=="")||($g_view['deal_data']['price_at_end_of_first_day']=="0.0")) echo "n/a";else echo convert_million_for_display($g_view['deal_data']['price_at_end_of_first_day'])." ".$deal_local_currency;?>
</td>

<?php
if(0==$g_view['suggestion_data_count']){
	?><td class="deal-edit-snippet-mid-col"></td><?php
}else{
	for($q=0;$q<$g_view['suggestion_data_count'];$q++){
	?>
	<td class="deal-edit-snippet-mid-col"><?php if($g_view['suggestion_data_arr'][$q]['price_at_end_of_first_day']=="0.0") echo "n/a"; else echo convert_million_for_display($g_view['suggestion_data_arr'][$q]['price_at_end_of_first_day'])." ".$xxx_suggestion_currency;?></td>
	<?php
	}
}
?>

<td class="deal-edit-snippet-right-col">
<input type="text" name="price_at_end_of_first_day" value="<?php if(($g_view['deal_data']['price_at_end_of_first_day']=="")||($g_view['deal_data']['price_at_end_of_first_day']=="0.0")) echo "";else echo $g_view['deal_data']['price_at_end_of_first_day'];?>" class="deal-edit-snippet-textbox-short" /> (in local currency)
</td>
</tr>
<!--************************************************************-->
<tr>
<td>First day of trading:</td>
<td>
<?php if($g_view['deal_data']['date_first_trading']=="0000-00-00"||$g_view['deal_data']['date_first_trading']=="") echo "n/a"; else echo ymd_to_dmy($g_view['deal_data']['date_first_trading']);?>
</td>

<?php
if(0==$g_view['suggestion_data_count']){
	?><td class="deal-edit-snippet-mid-col"></td><?php
}else{
	for($q=0;$q<$g_view['suggestion_data_count'];$q++){
	?>
	<td class="deal-edit-snippet-mid-col"><?php if($g_view['suggestion_data_arr'][$q]['date_first_trading']=="0000-00-00"||$g_view['suggestion_data_arr'][$q]['date_first_trading']=="") echo "n/a"; else echo ymd_to_dmy($g_view['suggestion_data_arr'][$q]['date_first_trading']);?></td>
	<?php
	}
}
?>

<td class="deal-edit-snippet-right-col">
<input type="text" name="date_first_trading" id="date_first_trading" class="deal-edit-snippet-textbox-short" value="<?php if($g_view['deal_data']['date_first_trading']=="0000-00-00"||$g_view['deal_data']['date_first_trading']=="") echo ""; else echo $g_view['deal_data']['date_first_trading'];?>" />
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
<!--************************************************************-->
<tr>
<td>Performance on first day:</td>
<td>
<?php if($g_view['deal_data']['1_day_price_change']==""||$g_view['deal_data']['1_day_price_change']==0.0) echo "n/a"; else echo $g_view['deal_data']['1_day_price_change']."%";?>
</td>

<?php
if(0==$g_view['suggestion_data_count']){
	?><td class="deal-edit-snippet-mid-col"></td><?php
}else{
	for($q=0;$q<$g_view['suggestion_data_count'];$q++){
	?>
	<td class="deal-edit-snippet-mid-col"><?php if($g_view['suggestion_data_arr'][$q]['1_day_price_change']==0.0) echo "n/a"; else echo $g_view['suggestion_data_arr'][$q]['1_day_price_change']."%";?></td>
	<?php
	}
}
?>

<td class="deal-edit-snippet-right-col">
<input type="text" name="1_day_price_change" value="<?php if($g_view['deal_data']['1_day_price_change']==""||$g_view['deal_data']['1_day_price_change']==0.0) echo ""; else echo $g_view['deal_data']['1_day_price_change']."%";?>" class="deal-edit-snippet-textbox-short" />&nbsp;%
</td>

</tr>
<!--************************************************************-->
<tr>
<td>Fee (Base):</td>
<td>
<?php if($g_view['deal_data']['base_fee']==""||$g_view['deal_data']['base_fee']==0.0) echo "n/a"; else echo $g_view['deal_data']['base_fee']."%";?>
</td>

<?php
if(0==$g_view['suggestion_data_count']){
	?><td class="deal-edit-snippet-mid-col"></td><?php
}else{
	for($q=0;$q<$g_view['suggestion_data_count'];$q++){
	?>
	<td class="deal-edit-snippet-mid-col"><?php if($g_view['suggestion_data_arr'][$q]['base_fee']==0.0) echo "n/a"; else echo $g_view['suggestion_data_arr'][$q]['base_fee']."%";?></td>
	<?php
	}
}
?>

<td class="deal-edit-snippet-right-col">
<input type="text" name="base_fee" value="<?php if($g_view['deal_data']['base_fee']==""||$g_view['deal_data']['base_fee']==0.0) echo ""; else echo $g_view['deal_data']['base_fee']."%";?>" class="deal-edit-snippet-textbox-short" />&nbsp;%
</td>

</tr>
<!--************************************************************-->
<tr>
<td>Fee (Incentive):</td>
<td>
<?php if($g_view['deal_data']['incentive_fee']==""||$g_view['deal_data']['incentive_fee']==0.0) echo "n/a"; else echo $g_view['deal_data']['incentive_fee']."%";?>
</td>

<?php
if(0==$g_view['suggestion_data_count']){
	?><td class="deal-edit-snippet-mid-col"></td><?php
}else{
	for($q=0;$q<$g_view['suggestion_data_count'];$q++){
	?>
	<td class="deal-edit-snippet-mid-col"><?php if($g_view['suggestion_data_arr'][$q]['incentive_fee']==0.0) echo "n/a"; else echo $g_view['suggestion_data_arr'][$q]['incentive_fee']."%";?></td>
	<?php
	}
}
?>

<td class="deal-edit-snippet-right-col">
<input type="text" name="incentive_fee" value="<?php if($g_view['deal_data']['incentive_fee']==""||$g_view['deal_data']['incentive_fee']==0.0) echo ""; else echo $g_view['deal_data']['incentive_fee']."%";?>" class="deal-edit-snippet-textbox-short" />&nbsp;%
</td>

</tr>
<!--************************************************************-->
<script>
jQuery('#ipo_stock_exchange').devbridge_autocomplete({
	serviceUrl:'admin/ajax/fetch_stock_exchange_list.php',
	minChars:1,
	noCache: true,
	width:'100%',
	onSelect: function(value, data){
		jQuery('#ipo_stock_exchange').val(data);
	}
});
</script>