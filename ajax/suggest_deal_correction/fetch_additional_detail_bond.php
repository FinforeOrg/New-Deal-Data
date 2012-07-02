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
<td>Bond Format:</td>
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