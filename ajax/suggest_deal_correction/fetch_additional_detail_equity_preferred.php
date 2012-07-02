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
<!--************************************************************-->
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
<td>Years to maturity:</td>
<td>
<?php if($g_view['deal_data']['years_to_maturity']=="") echo "n/a"; else echo $g_view['deal_data']['years_to_maturity'];?>
</td>

<?php
if(0==$g_view['suggestion_data_count']){
	?><td class="deal-edit-snippet-mid-col"></td><?php
}else{
	for($q=0;$q<$g_view['suggestion_data_count'];$q++){
	?>
	<td class="deal-edit-snippet-mid-col"><?php if($g_view['suggestion_data_arr'][$q]['years_to_maturity']=="") echo "n/a"; else echo $g_view['suggestion_data_arr'][$q]['years_to_maturity'];?></td>
	<?php
	}
}
?>

<td class="deal-edit-snippet-right-col">
<input type="text" name="years_to_maturity" value="<?php if($g_view['deal_data']['years_to_maturity']=="") echo ""; else echo $g_view['deal_data']['years_to_maturity'];?>" class="deal-edit-snippet-textbox-short" />
</td>
</tr>
<!--************************************************************-->
<tr>
<td>End Date:</td>
<td>
<?php if($g_view['deal_data']['maturity_date']=="0000-00-00"||$g_view['deal_data']['maturity_date']=="") echo "n/a"; else echo ymd_to_dmy($g_view['deal_data']['maturity_date']);?>
</td>

<?php
if(0==$g_view['suggestion_data_count']){
	?><td class="deal-edit-snippet-mid-col"></td><?php
}else{
	for($q=0;$q<$g_view['suggestion_data_count'];$q++){
	?>
	<td class="deal-edit-snippet-mid-col"><?php if($g_view['suggestion_data_arr'][$q]['maturity_date']=="0000-00-00"||$g_view['suggestion_data_arr'][$q]['maturity_date']=="") echo "n/a"; else echo ymd_to_dmy($g_view['suggestion_data_arr'][$q]['maturity_date']);?></td>
	<?php
	}
}
?>

<td class="deal-edit-snippet-right-col">
<input type="text" name="maturity_date" id="maturity_date" class="deal-edit-snippet-textbox-short" value="<?php if($g_view['deal_data']['maturity_date']=="0000-00-00"||$g_view['deal_data']['maturity_date']=="") echo ""; else echo $g_view['deal_data']['maturity_date'];?>" />
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
<!--************************************************************-->
<tr>
<td>Coupon:</td>
<td>
<?php if($g_view['deal_data']['coupon']=="") echo "n/a"; else echo $g_view['deal_data']['coupon'];?>
</td>

<?php
if(0==$g_view['suggestion_data_count']){
	?><td class="deal-edit-snippet-mid-col"></td><?php
}else{
	for($q=0;$q<$g_view['suggestion_data_count'];$q++){
	?>
	<td class="deal-edit-snippet-mid-col"><?php if($g_view['suggestion_data_arr'][$q]['coupon']=="") echo "n/a"; else echo $g_view['suggestion_data_arr'][$q]['coupon'];?></td>
	<?php
	}
}
?>

<td class="deal-edit-snippet-right-col">
<input type="text" name="coupon" value="<?php if($g_view['deal_data']['coupon']=="") echo ""; else echo $g_view['deal_data']['coupon'];?>" class="deal-edit-snippet-textbox-short" />
</td>
</tr>
<!--************************************************************-->
<tr>
<td>Current Rating:</td>
<td>
<?php if($g_view['deal_data']['current_rating']=="") echo "n/a"; else echo $g_view['deal_data']['current_rating'];?>
</td>

<?php
if(0==$g_view['suggestion_data_count']){
	?><td class="deal-edit-snippet-mid-col"></td><?php
}else{
	for($q=0;$q<$g_view['suggestion_data_count'];$q++){
	?>
	<td class="deal-edit-snippet-mid-col"><?php if($g_view['suggestion_data_arr'][$q]['current_rating']=="") echo "n/a"; else echo $g_view['suggestion_data_arr'][$q]['current_rating'];?></td>
	<?php
	}
}
?>

<td class="deal-edit-snippet-right-col">
<input type="text" name="current_rating" value="<?php if($g_view['deal_data']['current_rating']=="") echo ""; else echo $g_view['deal_data']['current_rating'];?>" class="deal-edit-snippet-textbox-short" />
</td>
</tr>
<!--************************************************************-->
<tr>
<td>Format:</td>
<td>
<?php if($g_view['deal_data']['format']=="") echo "n/a"; else echo $g_view['deal_data']['format'];?>
</td>

<?php
if(0==$g_view['suggestion_data_count']){
	?><td class="deal-edit-snippet-mid-col"></td><?php
}else{
	for($q=0;$q<$g_view['suggestion_data_count'];$q++){
	?>
	<td class="deal-edit-snippet-mid-col"><?php if($g_view['suggestion_data_arr'][$q]['format']=="") echo "n/a"; else echo $g_view['suggestion_data_arr'][$q]['format'];?></td>
	<?php
	}
}
?>

<td class="deal-edit-snippet-right-col">
<input type="text" name="format" value="<?php if($g_view['deal_data']['format']=="") echo ""; else echo $g_view['deal_data']['format'];?>" class="deal-edit-snippet-textbox-short" />
</td>
</tr>
<!--************************************************************-->
<tr>
<td>Guarantor:</td>
<td>
<?php if($g_view['deal_data']['guarantor']=="") echo "n/a"; else echo $g_view['deal_data']['guarantor'];?>
</td>

<?php
if(0==$g_view['suggestion_data_count']){
	?><td class="deal-edit-snippet-mid-col"></td><?php
}else{
	for($q=0;$q<$g_view['suggestion_data_count'];$q++){
	?>
	<td class="deal-edit-snippet-mid-col"><?php if($g_view['suggestion_data_arr'][$q]['guarantor']=="") echo "n/a"; else echo $g_view['suggestion_data_arr'][$q]['guarantor'];?></td>
	<?php
	}
}
?>

<td class="deal-edit-snippet-right-col">
<input type="text" name="guarantor" value="<?php if($g_view['deal_data']['guarantor']=="") echo ""; else echo $g_view['deal_data']['guarantor'];?>" class="deal-edit-snippet-textbox-short" />
</td>
</tr>
<!--************************************************************-->
<tr>
<td>Collateral:</td>
<td>
<?php if($g_view['deal_data']['collateral']=="") echo "n/a"; else echo $g_view['deal_data']['collateral'];?>
</td>

<?php
if(0==$g_view['suggestion_data_count']){
	?><td class="deal-edit-snippet-mid-col"></td><?php
}else{
	for($q=0;$q<$g_view['suggestion_data_count'];$q++){
	?>
	<td class="deal-edit-snippet-mid-col"><?php if($g_view['suggestion_data_arr'][$q]['collateral']=="") echo "n/a"; else echo $g_view['suggestion_data_arr'][$q]['collateral'];?></td>
	<?php
	}
}
?>

<td class="deal-edit-snippet-right-col">
<input type="text" name="collateral" value="<?php if($g_view['deal_data']['collateral']=="") echo ""; else echo $g_view['deal_data']['collateral'];?>" class="deal-edit-snippet-textbox-short" />
</td>
</tr>
<!--************************************************************-->
<tr>
<td>Seniority:</td>
<td>
<?php if($g_view['deal_data']['seniority']=="") echo "n/a"; else echo $g_view['deal_data']['seniority'];?>
</td>

<?php
if(0==$g_view['suggestion_data_count']){
	?><td class="deal-edit-snippet-mid-col"></td><?php
}else{
	for($q=0;$q<$g_view['suggestion_data_count'];$q++){
	?>
	<td class="deal-edit-snippet-mid-col"><?php if($g_view['suggestion_data_arr'][$q]['seniority']=="") echo "n/a"; else echo $g_view['suggestion_data_arr'][$q]['seniority'];?></td>
	<?php
	}
}
?>

<td class="deal-edit-snippet-right-col">
<input type="text" name="seniority" value="<?php if($g_view['deal_data']['seniority']=="") echo ""; else echo $g_view['deal_data']['seniority'];?>" class="deal-edit-snippet-textbox-short" />
</td>
</tr>
<!--************************************************************-->
<tr>
<td>Years to call:</td>
<td>
<?php if($g_view['deal_data']['year_to_call']=="") echo "n/a"; else echo $g_view['deal_data']['year_to_call'];?>
</td>

<?php
if(0==$g_view['suggestion_data_count']){
	?><td class="deal-edit-snippet-mid-col"></td><?php
}else{
	for($q=0;$q<$g_view['suggestion_data_count'];$q++){
	?>
	<td class="deal-edit-snippet-mid-col"><?php if($g_view['suggestion_data_arr'][$q]['year_to_call']=="") echo "n/a"; else echo $g_view['suggestion_data_arr'][$q]['year_to_call'];?></td>
	<?php
	}
}
?>

<td class="deal-edit-snippet-right-col">
<input type="text" name="year_to_call" value="<?php if($g_view['deal_data']['year_to_call']=="") echo ""; else echo $g_view['deal_data']['year_to_call'];?>" class="deal-edit-snippet-textbox-short" />
</td>
</tr>
<!--************************************************************-->
<tr>
<td>Call Date:</td>
<td>
<?php if($g_view['deal_data']['call_date']=="0000-00-00"||$g_view['deal_data']['call_date']=="") echo "n/a"; else echo ymd_to_dmy($g_view['deal_data']['call_date']);?>
</td>

<?php
if(0==$g_view['suggestion_data_count']){
	?><td class="deal-edit-snippet-mid-col"></td><?php
}else{
	for($q=0;$q<$g_view['suggestion_data_count'];$q++){
	?>
	<td class="deal-edit-snippet-mid-col"><?php if($g_view['suggestion_data_arr'][$q]['call_date']=="0000-00-00"||$g_view['suggestion_data_arr'][$q]['call_date']=="") echo "n/a"; else echo ymd_to_dmy($g_view['suggestion_data_arr'][$q]['call_date']);?></td>
	<?php
	}
}
?>

<td class="deal-edit-snippet-right-col">
<input type="text" name="call_date" id="call_date" class="deal-edit-snippet-textbox-short" value="<?php if($g_view['deal_data']['call_date']=="0000-00-00"||$g_view['deal_data']['call_date']=="") echo ""; else echo $g_view['deal_data']['call_date'];?>" />
<script type="text/javascript">
// <![CDATA[       
var opts = {                            
formElements:{"call_date":"Y-ds-m-ds-d"},
showWeeks:false                   
};      
datePickerController.createDatePicker(opts);
// ]]>
</script>
</td>

</tr>
<!--************************************************************-->
<tr>
<td>Redemption Price:</td>
<td>
<?php if($g_view['deal_data']['redemption_price']=="") echo "n/a"; else echo $g_view['deal_data']['redemption_price'];?>
</td>

<?php
if(0==$g_view['suggestion_data_count']){
	?><td class="deal-edit-snippet-mid-col"></td><?php
}else{
	for($q=0;$q<$g_view['suggestion_data_count'];$q++){
	?>
	<td class="deal-edit-snippet-mid-col"><?php if($g_view['suggestion_data_arr'][$q]['redemption_price']=="") echo "n/a"; else echo $g_view['suggestion_data_arr'][$q]['redemption_price'];?></td>
	<?php
	}
}
?>

<td class="deal-edit-snippet-right-col">
<input type="text" name="redemption_price" value="<?php if($g_view['deal_data']['redemption_price']=="") echo ""; else echo $g_view['deal_data']['redemption_price'];?>" class="deal-edit-snippet-textbox-short" />
</td>
</tr>
<?php
/******************
sng:26/aug/2011
if currency for reference price is given, use that else use currency for the deal
*****************/
if($g_view['deal_data']['currency_reference_price'] == ""){
	$reference_price_currency = $deal_local_currency;
}else{
	$reference_price_currency = $g_view['deal_data']['currency_reference_price'];
}
?>
<!--************************************************************-->
<tr>
<td>Reference Price:</td>
<td>
<?php if($g_view['deal_data']['reference_price']=="0.0") echo "n/a"; echo convert_million_for_display($g_view['deal_data']['reference_price'])." ".$reference_price_currency;?>
</td>

<?php
if(0==$g_view['suggestion_data_count']){
	?><td class="deal-edit-snippet-mid-col"></td><?php
}else{
	for($q=0;$q<$g_view['suggestion_data_count'];$q++){
	?>
	<td class="deal-edit-snippet-mid-col"><?php if($g_view['suggestion_data_arr'][$q]['reference_price']=="0.0") echo "n/a"; else echo convert_million_for_display($g_view['suggestion_data_arr'][$q]['reference_price'])." ".$xxx_suggestion_reference_price_currency;?></td>
	<?php
	}
}
?>

<td class="deal-edit-snippet-right-col">
<input type="text" name="reference_price" value="<?php if($g_view['deal_data']['reference_price']=="0.0") echo "";else echo $g_view['deal_data']['reference_price']." ".$reference_price_currency;?>" class="deal-edit-snippet-textbox-short" />
</td>
</tr>
<!--************************************************************-->
<tr>
<td>Conversion Price:</td>
<td>
<?php if($g_view['deal_data']['conversion_price']=="0.0") echo "n/a"; echo convert_million_for_display($g_view['deal_data']['conversion_price'])." ".$reference_price_currency;?>
</td>

<?php
if(0==$g_view['suggestion_data_count']){
	?><td class="deal-edit-snippet-mid-col"></td><?php
}else{
	for($q=0;$q<$g_view['suggestion_data_count'];$q++){
	?>
	<td class="deal-edit-snippet-mid-col"><?php if($g_view['suggestion_data_arr'][$q]['conversion_price']=="0.0") echo "n/a"; else echo convert_million_for_display($g_view['suggestion_data_arr'][$q]['conversion_price'])." ".$xxx_suggestion_reference_price_currency;?></td>
	<?php
	}
}
?>

<td class="deal-edit-snippet-right-col">
<input type="text" name="conversion_price" value="<?php if($g_view['deal_data']['conversion_price']=="0.0") echo "";else echo $g_view['deal_data']['conversion_price']." ".$reference_price_currency;?>" class="deal-edit-snippet-textbox-short" />
</td>
</tr>
<!--************************************************************-->
<tr>
<td>Currency of Reference price:<br />(if different from local currency for the deal)</td>
<td><?php if($g_view['deal_data']['currency_reference_price'] == "") echo "n/a"; else echo $g_view['deal_data']['currency_reference_price'];?></td>

<?php
if(0==$g_view['suggestion_data_count']){
	?><td class="deal-edit-snippet-mid-col"></td><?php
}else{
	for($q=0;$q<$g_view['suggestion_data_count'];$q++){
	?>
	<td class="deal-edit-snippet-mid-col"><?php if($g_view['suggestion_data_arr'][$q]['currency_reference_price']=="") echo "n/a"; else echo $g_view['suggestion_data_arr'][$q]['currency_reference_price'];?></td>
	<?php
	}
}
?>

<td class="deal-edit-snippet-right-col">
<input type="text" name="currency_reference_price" id="currency_reference_price" value="<?php if($g_view['deal_data']['currency_reference_price'] == "") echo ""; else echo $g_view['deal_data']['currency_reference_price'];?>" class="deal-edit-snippet-textbox-short" />
</td>
<!--************************************************************-->
<tr>
<td>Conversion Premium:</td>
<td>
<?php if($g_view['deal_data']['conversion_premia_percent']==""||$g_view['deal_data']['conversion_premia_percent']==0.0) echo "n/a"; else echo $g_view['deal_data']['conversion_premia_percent']."%";?>
</td>

<?php
if(0==$g_view['suggestion_data_count']){
	?><td class="deal-edit-snippet-mid-col"></td><?php
}else{
	for($q=0;$q<$g_view['suggestion_data_count'];$q++){
	?>
	<td class="deal-edit-snippet-mid-col"><?php if($g_view['suggestion_data_arr'][$q]['conversion_premia_percent']==0.0) echo "n/a"; else echo $g_view['suggestion_data_arr'][$q]['conversion_premia_percent']."%";?></td>
	<?php
	}
}
?>

<td class="deal-edit-snippet-right-col">
<input type="text" name="conversion_premia_percent" value="<?php if($g_view['deal_data']['conversion_premia_percent']==""||$g_view['deal_data']['conversion_premia_percent']==0.0) echo ""; else echo $g_view['deal_data']['conversion_premia_percent'];?>" class="deal-edit-snippet-textbox-short" />&nbsp;%
</td>

</tr>
<!--************************************************************-->
<tr>
<td>Number of shares underlying:</td>
<td>
<?php if($g_view['deal_data']['num_shares_underlying_million']==""||$g_view['deal_data']['num_shares_underlying_million']==0.0) echo "n/a"; else echo convert_million_for_display($g_view['deal_data']['num_shares_underlying_million'])."m";?>
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
<input type="text" name="num_shares_underlying_million" value="<?php if($g_view['deal_data']['num_shares_underlying_million']==""||$g_view['deal_data']['num_shares_underlying_million']==0.0) echo ""; else echo $g_view['deal_data']['num_shares_underlying_million'];?>" class="deal-edit-snippet-textbox-short" /> (in million)
</td>

</tr>
<!--************************************************************-->
<tr>
<td>Current number of shares in issue:</td>
<td>
<?php if($g_view['deal_data']['curr_num_shares_outstanding_million']==""||$g_view['deal_data']['curr_num_shares_outstanding_million']==0.0) echo "n/a"; else echo convert_million_for_display($g_view['deal_data']['curr_num_shares_outstanding_million'])."m";?>
</td>

<?php
if(0==$g_view['suggestion_data_count']){
	?><td class="deal-edit-snippet-mid-col"></td><?php
}else{
	for($q=0;$q<$g_view['suggestion_data_count'];$q++){
	?>
	<td class="deal-edit-snippet-mid-col"><?php if($g_view['suggestion_data_arr'][$q]['curr_num_shares_outstanding_million']==0.0) echo "n/a"; else echo convert_million_for_display($g_view['suggestion_data_arr'][$q]['curr_num_shares_outstanding_million'])."m";?></td>
	<?php
	}
}
?>

<td class="deal-edit-snippet-right-col">
<input type="text" name="curr_num_shares_outstanding_million" value="<?php if($g_view['deal_data']['curr_num_shares_outstanding_million']==""||$g_view['deal_data']['curr_num_shares_outstanding_million']==0.0) echo ""; else echo $g_view['deal_data']['curr_num_shares_outstanding_million'];?>" class="deal-edit-snippet-textbox-short" /> (in million)
</td>

</tr>
<!--************************************************************-->
<tr>
<td>Average Daily Trading Volume:</td>
<td>
<?php if($g_view['deal_data']['avg_daily_trading_vol_million']==""||$g_view['deal_data']['avg_daily_trading_vol_million']==0.0) echo "n/a"; else echo convert_million_for_display($g_view['deal_data']['avg_daily_trading_vol_million'])."m";?>
</td>

<?php
if(0==$g_view['suggestion_data_count']){
	?><td class="deal-edit-snippet-mid-col"></td><?php
}else{
	for($q=0;$q<$g_view['suggestion_data_count'];$q++){
	?>
	<td class="deal-edit-snippet-mid-col"><?php if($g_view['suggestion_data_arr'][$q]['avg_daily_trading_vol_million']==0.0) echo "n/a"; else echo convert_million_for_display($g_view['suggestion_data_arr'][$q]['avg_daily_trading_vol_million'])."m";?></td>
	<?php
	}
}
?>

<td class="deal-edit-snippet-right-col">
<input type="text" name="avg_daily_trading_vol_million" value="<?php if($g_view['deal_data']['avg_daily_trading_vol_million']==""||$g_view['deal_data']['avg_daily_trading_vol_million']==0.0) echo ""; else echo $g_view['deal_data']['avg_daily_trading_vol_million'];?>" class="deal-edit-snippet-textbox-short" /> (in million)
</td>

</tr>
<!--************************************************************-->
<tr>
<td>Underlying shares versus ADTV:</td>
<td>
<?php if($g_view['deal_data']['shares_underlying_vs_adtv_ratio']==""||$g_view['deal_data']['shares_underlying_vs_adtv_ratio']==0.0) echo "n/a"; else echo convert_million_for_display($g_view['deal_data']['shares_underlying_vs_adtv_ratio']);?>
</td>

<?php
if(0==$g_view['suggestion_data_count']){
	?><td class="deal-edit-snippet-mid-col"></td><?php
}else{
	for($q=0;$q<$g_view['suggestion_data_count'];$q++){
	?>
	<td class="deal-edit-snippet-mid-col"><?php if($g_view['suggestion_data_arr'][$q]['shares_underlying_vs_adtv_ratio']==0.0) echo "n/a"; else echo convert_million_for_display($g_view['suggestion_data_arr'][$q]['shares_underlying_vs_adtv_ratio']);?></td>
	<?php
	}
}
?>

<td class="deal-edit-snippet-right-col">
<input type="text" name="shares_underlying_vs_adtv_ratio" value="<?php if($g_view['deal_data']['shares_underlying_vs_adtv_ratio']==""||$g_view['deal_data']['shares_underlying_vs_adtv_ratio']==0.0) echo ""; else echo $g_view['deal_data']['shares_underlying_vs_adtv_ratio'];?>" class="deal-edit-snippet-textbox-short" />
</td>

</tr>
<!--************************************************************-->
<tr>
<td>Fee (Gross):</td>
<td>
<?php if($g_view['deal_data']['base_fee']==""||$g_view['deal_data']['base_fee']==0.0) echo "n/a"; else echo $g_view['deal_data']['base_fee']."%";?>
</td>

<?php
if(0==$g_view['suggestion_data_count']){
	?><td class="deal-edit-snippet-mid-col"></td><?php
}else{
	for($q=0;$q<$g_view['suggestion_data_count'];$q++){
	?>
	<td class="deal-edit-snippet-mid-col"><?php if($g_view['suggestion_data_arr'][$q]['base_fee']==""||$g_view['suggestion_data_arr'][$q]['base_fee']==0.0) echo "n/a"; else echo $g_view['suggestion_data_arr'][$q]['base_fee']."%";?></td>
	<?php
	}
}
?>

<td class="deal-edit-snippet-right-col">
<input type="text" name="base_fee" value="<?php if($g_view['deal_data']['base_fee']==""||$g_view['deal_data']['base_fee']==0.0) echo ""; else echo $g_view['deal_data']['base_fee'];?>" class="deal-edit-snippet-textbox-short" />&nbsp;%
</td>

</tr>
<!--************************************************************-->
<tr>
<td>Dividend protection mechanism:</td>
<td><?php if($g_view['deal_data']['dividend_protection'] == 'y'){?>Yes<?php }else{?>No<?php }?></td>

<?php
if(0==$g_view['suggestion_data_count']){
	?><td class="deal-edit-snippet-mid-col"></td><?php
}else{
	for($q=0;$q<$g_view['suggestion_data_count'];$q++){
	?>
	<td class="deal-edit-snippet-mid-col"><?php if($g_view['suggestion_data_arr'][$q]['dividend_protection'] == 'y'){?>Yes<?php }else{?>No<?php }?></td>
	<?php
	}
}
?>

<td class="deal-edit-snippet-right-col">
<input type="radio" name="dividend_protection" value="y" <?php if($g_view['deal_data']['dividend_protection'] == 'y'){?>checked="checked"<?php }?> >Yes&nbsp;&nbsp;
<input type="radio" name="dividend_protection" value="n" <?php if($g_view['deal_data']['dividend_protection'] == 'n'){?>checked="checked"<?php }?> >No
</td>

</tr>
<!--************************************************************-->
<script>
jQuery('#currency_reference_price').devbridge_autocomplete({
	serviceUrl:'admin/ajax/fetch_currency_list.php',
	minChars:1,
	noCache: true,
	width:'100%',
	onSelect: function(value, data){
		jQuery('#currency').val(data);
	}
});
</script>