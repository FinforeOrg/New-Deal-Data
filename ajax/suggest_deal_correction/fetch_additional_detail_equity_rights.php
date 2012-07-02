<?php
/**************
remember that not all deals will have extra data, and even then, the date field may contain the default 0 value

This is Bond deal. If BOTH date_announced AND date_closed are not given BUT date_of_deal is there then:
Completed: is date_of_deal
******************/
$date_announced = "";
$date_announced_for_value = "";
$date_ex_rights = "";
$date_ex_rights_for_value = "";
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
<td>Ex-rights:</td>
<td>
<?php
if($g_view['deal_data']['date_ex_rights']=="0000-00-00"||$g_view['deal_data']['date_ex_rights']==""){
	echo "n/a";
	$date_ex_rights_for_value = "";
}else{
	echo ymd_to_dmy($g_view['deal_data']['date_ex_rights']);
	$date_ex_rights_for_value = $g_view['deal_data']['date_ex_rights'];
}
?>
</td>

<?php
if(0==$g_view['suggestion_data_count']){
	?><td class="deal-edit-snippet-mid-col"></td><?php
}else{
	for($q=0;$q<$g_view['suggestion_data_count'];$q++){
	?>
	<td class="deal-edit-snippet-mid-col"><?php if($g_view['suggestion_data_arr'][$q]['date_ex_rights']=="0000-00-00"||$g_view['suggestion_data_arr'][$q]['date_ex_rights']=="") echo "n/a"; else echo ymd_to_dmy($g_view['suggestion_data_arr'][$q]['date_ex_rights']);?></td>
	<?php
	}
}
?>

<td class="deal-edit-snippet-right-col">
<input type="text" name="date_ex_rights" id="date_ex_rights" class="deal-edit-snippet-textbox-short" value="<?php echo $date_ex_rights_for_value;?>" />
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
<td>Issue Price / Rights Price:</td>
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
<input type="text" name="offer_price" value="<?php if(($g_view['deal_data']['offer_price']=="")||($g_view['deal_data']['offer_price']=="0.0")) echo "";else echo $g_view['deal_data']['offer_price'];?>" class="deal-edit-snippet-textbox-short" /> (in local currency)
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
<td>Current number of shares in issue:</td>
<td>
<?php if(($g_view['deal_data']['curr_num_shares_outstanding_million']=="")||($g_view['deal_data']['curr_num_shares_outstanding_million']==0.0)) echo "n/a"; else echo convert_million_for_display($g_view['deal_data']['curr_num_shares_outstanding_million'])."m";?>
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
<input type="text" name="curr_num_shares_outstanding_million" value="<?php if(($g_view['deal_data']['curr_num_shares_outstanding_million']=="")||($g_view['deal_data']['curr_num_shares_outstanding_million']==0.0)) echo ""; else echo $g_view['deal_data']['curr_num_shares_outstanding_million'];?>" class="deal-edit-snippet-textbox-short" /> (in million)
</td>

</tr>
<!--************************************************************-->
<tr>
<td>Subscription ratio:</td>
<td>
<?php if($g_view['deal_data']['subscription_ratio']=="") echo "n/a"; else echo $g_view['deal_data']['subscription_ratio'];?>
</td>

<?php
if(0==$g_view['suggestion_data_count']){
	?><td class="deal-edit-snippet-mid-col"></td><?php
}else{
	for($q=0;$q<$g_view['suggestion_data_count'];$q++){
	?>
	<td class="deal-edit-snippet-mid-col"><?php if($g_view['suggestion_data_arr'][$q]['subscription_ratio']=="") echo "n/a"; else echo $g_view['suggestion_data_arr'][$q]['subscription_ratio'];?></td>
	<?php
	}
}
?>

<td class="deal-edit-snippet-right-col">
<input type="text" name="subscription_ratio" value="<?php if($g_view['deal_data']['subscription_ratio']=="") echo ""; else echo $g_view['deal_data']['subscription_ratio'];?>" class="deal-edit-snippet-textbox-short" /> (ex 4:3)
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
<td>Price prior to announcement:</td>
<td>
<?php if(($g_view['deal_data']['price_per_share_before_deal_announcement']=="")||($g_view['deal_data']['price_per_share_before_deal_announcement']=="0.0")) echo "n/a";else echo convert_million_for_display($g_view['deal_data']['price_per_share_before_deal_announcement'])." ".$deal_local_currency;?>
</td>

<?php
if(0==$g_view['suggestion_data_count']){
	?><td class="deal-edit-snippet-mid-col"></td><?php
}else{
	for($q=0;$q<$g_view['suggestion_data_count'];$q++){
	?>
	<td class="deal-edit-snippet-mid-col"><?php if($g_view['suggestion_data_arr'][$q]['price_per_share_before_deal_announcement']=="0.0") echo "n/a"; else echo convert_million_for_display($g_view['suggestion_data_arr'][$q]['price_per_share_before_deal_announcement'])." ".$xxx_suggestion_currency;?></td>
	<?php
	}
}
?>

<td class="deal-edit-snippet-right-col">
<input type="text" name="price_per_share_before_deal_announcement" value="<?php if(($g_view['deal_data']['price_per_share_before_deal_announcement']=="")||($g_view['deal_data']['price_per_share_before_deal_announcement']=="0.0")) echo "";else echo $g_view['deal_data']['price_per_share_before_deal_announcement'];?>" class="deal-edit-snippet-textbox-short" /> (in local currency)
</td>
</tr>
<!--************************************************************-->
<tr>
<td>Date prior to announcement:</td>
<td>
<?php
if($g_view['deal_data']['date_price_per_share_before_deal_announcement']=="0000-00-00"||$g_view['deal_data']['date_price_per_share_before_deal_announcement']==""){
	echo "n/a";
}else{
	echo ymd_to_dmy($g_view['deal_data']['date_price_per_share_before_deal_announcement']);
}
?>
</td>

<?php
if(0==$g_view['suggestion_data_count']){
	?><td class="deal-edit-snippet-mid-col"></td><?php
}else{
	for($q=0;$q<$g_view['suggestion_data_count'];$q++){
	?>
	<td class="deal-edit-snippet-mid-col"><?php if($g_view['suggestion_data_arr'][$q]['date_price_per_share_before_deal_announcement']=="0000-00-00"||$g_view['suggestion_data_arr'][$q]['date_price_per_share_before_deal_announcement']=="") echo "n/a"; else echo ymd_to_dmy($g_view['suggestion_data_arr'][$q]['date_price_per_share_before_deal_announcement']);?></td>
	<?php
	}
}
?>

<td class="deal-edit-snippet-right-col">
<input type="text" name="date_price_per_share_before_deal_announcement" id="date_price_per_share_before_deal_announcement" class="deal-edit-snippet-textbox-short" value="<?php
if($g_view['deal_data']['date_price_per_share_before_deal_announcement']=="0000-00-00"||$g_view['deal_data']['date_price_per_share_before_deal_announcement']==""){
	echo "";
}else{
	echo $g_view['deal_data']['date_price_per_share_before_deal_announcement'];
}
?>" />
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
<!--************************************************************-->
<tr>
<td>Theoretical ex-rights price (TERP):</td>
<td>
<?php if(($g_view['deal_data']['terp']=="")||($g_view['deal_data']['terp']=="0.0")) echo "n/a";else echo $g_view['deal_data']['terp']." ".$deal_local_currency;?>
</td>

<?php
if(0==$g_view['suggestion_data_count']){
	?><td class="deal-edit-snippet-mid-col"></td><?php
}else{
	for($q=0;$q<$g_view['suggestion_data_count'];$q++){
	?>
	<td class="deal-edit-snippet-mid-col"><?php if($g_view['suggestion_data_arr'][$q]['terp']=="0.0") echo "n/a"; else echo $g_view['suggestion_data_arr'][$q]['terp']." ".$xxx_suggestion_currency;?></td>
	<?php
	}
}
?>

<td class="deal-edit-snippet-right-col">
<input type="text" name="terp" value="<?php if(($g_view['deal_data']['terp']=="")||($g_view['deal_data']['terp']=="0.0")) echo "";else echo $g_view['deal_data']['terp'];?>" class="deal-edit-snippet-textbox-short" /> (in local currency)
</td>
</tr>
<!--************************************************************-->
<tr>
<td>Premium / Discount to TERP:</td>
<td>
<?php if($g_view['deal_data']['discount_to_terp']==""||$g_view['deal_data']['discount_to_terp']==0.0) echo "n/a"; else echo $g_view['deal_data']['discount_to_terp']."%";?>
</td>

<?php
if(0==$g_view['suggestion_data_count']){
	?><td class="deal-edit-snippet-mid-col"></td><?php
}else{
	for($q=0;$q<$g_view['suggestion_data_count'];$q++){
	?>
	<td class="deal-edit-snippet-mid-col"><?php if($g_view['suggestion_data_arr'][$q]['discount_to_terp']==0.0) echo "n/a"; else echo $g_view['suggestion_data_arr'][$q]['discount_to_terp']."%";?></td>
	<?php
	}
}
?>

<td class="deal-edit-snippet-right-col">
<input type="text" name="discount_to_terp" value="<?php if($g_view['deal_data']['discount_to_terp']==""||$g_view['deal_data']['discount_to_terp']==0.0) echo ""; else echo $g_view['deal_data']['discount_to_terp']."%";?>" class="deal-edit-snippet-textbox-short" />&nbsp;%
</td>

</tr>
<!--************************************************************-->
<tr>
<td>Subscription rate:</td>
<td>
<?php if($g_view['deal_data']['subscription_rate_percent']==""||$g_view['deal_data']['subscription_rate_percent']==0.0) echo "n/a"; else echo $g_view['deal_data']['subscription_rate_percent']."%";?>
</td>

<?php
if(0==$g_view['suggestion_data_count']){
	?><td class="deal-edit-snippet-mid-col"></td><?php
}else{
	for($q=0;$q<$g_view['suggestion_data_count'];$q++){
	?>
	<td class="deal-edit-snippet-mid-col"><?php if($g_view['suggestion_data_arr'][$q]['subscription_rate_percent']==0.0) echo "n/a"; else echo $g_view['suggestion_data_arr'][$q]['subscription_rate_percent']."%";?></td>
	<?php
	}
}
?>

<td class="deal-edit-snippet-right-col">
<input type="text" name="subscription_rate_percent" value="<?php if($g_view['deal_data']['subscription_rate_percent']==""||$g_view['deal_data']['subscription_rate_percent']==0.0) echo ""; else echo $g_view['deal_data']['subscription_rate_percent']."%";?>" class="deal-edit-snippet-textbox-short" />&nbsp;%
</td>

</tr>
<!--************************************************************-->
<tr>
<td>Rump placement:</td>
<td><?php if($g_view['deal_data']['rump_placement'] == 'y'){?>Yes<?php }else{?>No<?php }?></td>

<?php
if(0==$g_view['suggestion_data_count']){
	?><td class="deal-edit-snippet-mid-col"></td><?php
}else{
	for($q=0;$q<$g_view['suggestion_data_count'];$q++){
	?>
	<td class="deal-edit-snippet-mid-col"><?php if($g_view['suggestion_data_arr'][$q]['rump_placement'] == 'y'){?>Yes<?php }else{?>No<?php }?></td>
	<?php
	}
}
?>

<td class="deal-edit-snippet-right-col">
<input type="radio" name="rump_placement" value="y" <?php if($g_view['deal_data']['rump_placement'] == 'y'){?>checked="checked"<?php }?> >Yes&nbsp;&nbsp;
<input type="radio" name="rump_placement" value="n" <?php if($g_view['deal_data']['rump_placement'] == 'n'){?>checked="checked"<?php }?> >No
</td>

</tr>
<!--************************************************************-->
<tr>
<td>Number of shares sold in rump:</td>
<td>
<?php 
if($g_view['deal_data']['rump_placement']=='n'){
	echo "Not Applicable";
}else{
	if(($g_view['deal_data']['num_shares_sold_in_rump_million']=="")||($g_view['deal_data']['num_shares_sold_in_rump_million']==0.0)) echo "n/a"; else echo convert_million_for_display($g_view['deal_data']['num_shares_sold_in_rump_million'])."m";
}
?>
</td>

<?php
if(0==$g_view['suggestion_data_count']){
	?><td class="deal-edit-snippet-mid-col"></td><?php
}else{
	for($q=0;$q<$g_view['suggestion_data_count'];$q++){
	?>
	<td class="deal-edit-snippet-mid-col">
	<?php 
	if($g_view['suggestion_data_arr'][$q]['rump_placement'] == 'n'){
		echo "Not Applicable";
	}else{
		if($g_view['suggestion_data_arr'][$q]['num_shares_sold_in_rump_million']==0.0) echo "n/a"; else echo convert_million_for_display($g_view['suggestion_data_arr'][$q]['num_shares_sold_in_rump_million'])."m";
	}
	?>
	</td>
	<?php
	}
}
?>

<td class="deal-edit-snippet-right-col">
<input type="text" name="num_shares_sold_in_rump_million" value="<?php 
if($g_view['deal_data']['rump_placement']=='n'){
	echo "";
}else{
	if(($g_view['deal_data']['num_shares_sold_in_rump_million']=="")||($g_view['deal_data']['num_shares_sold_in_rump_million']==0.0)) echo ""; else echo $g_view['deal_data']['num_shares_sold_in_rump_million'];
}
?>" class="deal-edit-snippet-textbox-short" /> (in million)
</td>

</tr>
<!--************************************************************-->
<tr>
<td>Price of rump placement:</td>
<td>
<?php 
if($g_view['deal_data']['rump_placement']=='n'){
	echo "Not Applicable";
}else{
	if(($g_view['deal_data']['price_per_share_in_rump']=="")||($g_view['deal_data']['price_per_share_in_rump']==0.0)) echo "n/a"; else echo $g_view['deal_data']['price_per_share_in_rump']." ".$deal_local_currency;
}
?>
</td>

<?php
if(0==$g_view['suggestion_data_count']){
	?><td class="deal-edit-snippet-mid-col"></td><?php
}else{
	for($q=0;$q<$g_view['suggestion_data_count'];$q++){
	?>
	<td class="deal-edit-snippet-mid-col">
	<?php 
	if($g_view['suggestion_data_arr'][$q]['rump_placement'] == 'n'){
		echo "Not Applicable";
	}else{
		if($g_view['suggestion_data_arr'][$q]['price_per_share_in_rump']==0.0) echo "n/a"; else echo $g_view['suggestion_data_arr'][$q]['price_per_share_in_rump']." ".$xxx_suggestion_currency;
	}
	?>
	</td>
	<?php
	}
}
?>

<td class="deal-edit-snippet-right-col">
<input type="text" name="price_per_share_in_rump" value="<?php 
if($g_view['deal_data']['rump_placement']=='n'){
	echo "";
}else{
	if(($g_view['deal_data']['price_per_share_in_rump']=="")||($g_view['deal_data']['price_per_share_in_rump']==0.0)) echo ""; else echo $g_view['deal_data']['price_per_share_in_rump'];
}
?>" class="deal-edit-snippet-textbox-short" /> (in local currency)
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