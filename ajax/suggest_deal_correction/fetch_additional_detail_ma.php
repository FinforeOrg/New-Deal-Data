<?php
/****************
for M&A deals, we need the subcategories so that we can show whether the deal is Complete or Pending

sng:1/sep/2012
We no longer use this hack. Now we use the custom list
***
$g_view['subcat1_list'] = array();
$g_view['subcat1_count'] = 0;
$success = $g_trans->get_all_category_subtype1_for_category_type($g_view['deal_data']['deal_cat_name'],$g_view['subcat1_list'],$g_view['subcat1_count']);
*************/
$g_view['status_list'] = NULL;
$g_view['status_list_count'] = 0;
$ok = $trans_support->deal_completion_status_types($g_view['status_list'],$g_view['status_list_count']);

/**********************
we need list of M&A merger type
********/
$g_view['merger_list'] = array();
$g_view['merger_list_count'] = 0;
$success = $deal_support->ma_merger_types($g_view['merger_list'],$g_view['merger_list_count']);


?>

<?php
/************************
PROBLEM: Now that we have split the system, what will be the suggestion_local_currency (in case the member wish to suggest that the deal was not in
USD and disagree with the deal currency given. Problem is deal currency is done from another panel

ALSO, each suggestion may suggest a different currency, so the suggestion_local_currency cannot be common to all suggestion
*********/
?>

<tr>
<td>Marked as:</td>
<td><?php echo $g_view['deal_data']['deal_subcat1_name'];?></td>

<?php
if(0==$g_view['suggestion_data_count']){
	?><td class="deal-edit-snippet-mid-col"></td><?php
}else{
	for($q=0;$q<$g_view['suggestion_data_count'];$q++){
	?>
	<td class="deal-edit-snippet-mid-col"><?php echo $g_view['suggestion_data_arr'][$q]['deal_subcat1_name'];?></td>
	<?php
	}
}
?>

<td class="deal-edit-snippet-right-col">
<?php
/*************************
sng:1/sep/2012
The subtype was a hack so that members can mark a Pending M&A deal as Complete.
Now we use a custom list
<select name="deal_subcat1_name" class="deal-edit-snippet-dropdown">
<option value="">Select</option>
<?php
for($i=0;$i<$g_view['subcat1_count'];$i++){
    ?>
    <option value="<?php echo $g_view['subcat1_list'][$i]['subtype1'];?>" <?php if($g_view['deal_data']['deal_subcat1_name']==$g_view['subcat1_list'][$i]['subtype1']){?>selected="selected"<?php }?>><?php echo $g_view['subcat1_list'][$i]['subtype1'];?></option>
    <?php
}
?>
</select>
***************************/
?>
<?php
/*********************
sng:1/sep/2012
If the deal subtype is already Completed then do not show dropdown
*********************/
if("completed"==strtolower($g_view['deal_data']['deal_subcat1_name'])){
	?>Completed<?php
}else{
	/***********
	not completed deal, so we can show the dropdowns
	we will highlight the 'pending' option as default
	********************/
	?>
	<select name="deal_completion_status" class="deal-edit-snippet-dropdown">
	<?php
	for($i=0;$i<$g_view['status_list_count'];$i++){
		?>
		<option value="<?php echo $g_view['status_list'][$i]['status_code'];?>" <?php if($g_view['status_list'][$i]['status_code']=="pending"){?>selected="selected"<?php }?>><?php echo $g_view['status_list'][$i]['status_name'];?></option>
		<?php
	}
	?>
	</select>
	<?php
}
?>

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

<?php
/**************
remember that not all deals will have extra data, and even then, the date field may contain the default 0 value

This is M&A deal. If BOTH date_announced AND date_closed are not given BUT date_of_deal is there then:
if Completed, Completed: is date_of_deal
If Pending, Announced is date_of_deal
******************/
$date_announced = "";
$date_announced_for_value = "";
$date_completed = "";
$date_completed_for_value = "";

if(($g_view['deal_data']['date_announced']=="0000-00-00"||$g_view['deal_data']['date_announced']=="")&&($g_view['deal_data']['date_closed']=="0000-00-00"||$g_view['deal_data']['date_closed']=="")&&$g_view['deal_data']['date_of_deal']!="0000-00-00"){
	if(strtolower($g_view['deal_data']['deal_subcat1_name'])=="pending"){
		$date_announced = ymd_to_dmy($g_view['deal_data']['date_of_deal']);
		$date_announced_for_value = $g_view['deal_data']['date_of_deal'];
	}
	if(strtolower($g_view['deal_data']['deal_subcat1_name'])=="completed"){
		$date_completed = ymd_to_dmy($g_view['deal_data']['date_of_deal']);
		$date_completed_for_value = $g_view['deal_data']['date_of_deal'];
	}
}
?>

<tr>
<td>Rumoured:</td>
<td>
<?php if($g_view['deal_data']['date_rumour']=="0000-00-00"||$g_view['deal_data']['date_rumour']=="") echo "n/a"; else echo ymd_to_dmy($g_view['deal_data']['date_rumour']);?>
</td>

<?php
if(0==$g_view['suggestion_data_count']){
	?><td class="deal-edit-snippet-mid-col"></td><?php
}else{
	for($q=0;$q<$g_view['suggestion_data_count'];$q++){
	?>
	<td class="deal-edit-snippet-mid-col"><?php if($g_view['suggestion_data_arr'][$q]['date_rumour']=="0000-00-00"||$g_view['suggestion_data_arr'][$q]['date_rumour']=="") echo "n/a"; else echo ymd_to_dmy($g_view['suggestion_data_arr'][$q]['date_rumour']);?></td>
	<?php
	}
}
?>

<td class="deal-edit-snippet-right-col">
<input type="text" name="date_rumour" id="date_rumour" class="deal-edit-snippet-textbox-short" value="<?php if($g_view['deal_data']['date_rumour']=="0000-00-00"||$g_view['deal_data']['date_rumour']=="") echo ""; else echo $g_view['deal_data']['date_rumour'];?>" />
<script type="text/javascript">
// <![CDATA[       
var opts = {                            
formElements:{"date_rumour":"Y-ds-m-ds-d"},
showWeeks:false                   
};      
datePickerController.createDatePicker(opts);
// ]]>
</script>
</td>

</tr>
<!--************************************************************-->
<tr>
<td>Announced:</td>
<td>
<?php 
if($date_announced != ""){
	echo $date_announced;
	//no change in date_announced_for_value
}elseif($g_view['deal_data']['date_announced']=="0000-00-00"||$g_view['deal_data']['date_announced']==""){
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
<td>Closed:</td>
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
<td>Payment Type:</td>
<td>
<?php
$equity_percent_value = "";
if($g_view['deal_data']['payment_type']==""){
	echo 'n/a';
	//we are not sure about $equity_percent_value
	$equity_percent_value = "";
}else if($g_view['deal_data']['payment_type']=="cash"){
	echo "Cash";
	//all cash, no equity
	$equity_percent_value = 0;
}else if($g_view['deal_data']['payment_type']=="equity"){
	echo "Equity";
	//all equity, no cash
	$equity_percent_value = 100;
}else if($g_view['deal_data']['payment_type']=="part_cash_part_quity"){
	echo "Part Cash / Part Equity";
	//part equity so value is whatever stored
	if($g_view['deal_data']['equity_payment_percent']==""||$g_view['deal_data']['equity_payment_percent']==0.0){
		$equity_percent_value = "";
	}else{
		$equity_percent_value = $g_view['deal_data']['equity_payment_percent'];
	}
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
	if($g_view['suggestion_data_arr'][$q]['payment_type']==""){
		echo 'n/a';
	}else if($g_view['suggestion_data_arr'][$q]['payment_type']=="cash"){
		echo "Cash";
	}else if($g_view['suggestion_data_arr'][$q]['payment_type']=="equity"){
		echo "Equity";
	}else if($g_view['suggestion_data_arr'][$q]['payment_type']=="part_cash_part_quity"){
		echo "Part Cash / Part Equity";
	}
	?>
	</td>
	<?php
	}
}
?>

<td class="deal-edit-snippet-right-col">
<input type="radio" name="payment_type" value="cash" <?php if($g_view['deal_data']['payment_type']=="cash"){?>checked="checked"<?php }?> >Cash&nbsp;&nbsp;
<input type="radio" name="payment_type" value="equity" <?php if($g_view['deal_data']['payment_type']=="equity"){?>checked="checked"<?php }?> >Equity&nbsp;&nbsp;
<input type="radio" name="payment_type" value="part_cash_part_quity" <?php if($g_view['deal_data']['payment_type']=="part_cash_part_quity"){?>checked="checked"<?php }?> >Part Cash/ Part Equity
</td>

</tr>
<!--************************************************************-->
<tr>
<td>Equity Payment %:</td>
<td><?php if($equity_percent_value == "") echo "n/a"; else echo $equity_percent_value; ?> %</td>

<?php
if(0==$g_view['suggestion_data_count']){
	?><td class="deal-edit-snippet-mid-col"></td><?php
}else{
	for($q=0;$q<$g_view['suggestion_data_count'];$q++){
	?>
	<td class="deal-edit-snippet-mid-col">
	<?php
	$suggested_equity_percent_value = "";
	if($g_view['suggestion_data_arr'][$q]['payment_type']==""){
		//we are not sure about $suggested_equity_percent_value
		$suggested_equity_percent_value = "";
	}else if($g_view['suggestion_data_arr'][$q]['payment_type']=="cash"){
		//all cash, no equity
		$suggested_equity_percent_value = 0;
	}else if($g_view['suggestion_data_arr'][$q]['payment_type']=="equity"){
		//all equity, no cash
		$suggested_equity_percent_value = 100;
	}else if($g_view['suggestion_data_arr'][$q]['payment_type']=="part_cash_part_quity"){
		//part equity so value is whatever stored
		if($g_view['suggestion_data_arr'][$q]['equity_payment_percent']==""||$g_view['suggestion_data_arr'][$q]['equity_payment_percent']==0.0){
			$suggested_equity_percent_value = "";
		}else{
			$suggested_equity_percent_value = $g_view['suggestion_data_arr'][$q]['equity_payment_percent'];
		}
	}
	?>
	<?php if($suggested_equity_percent_value == "") echo "n/a"; else echo $suggested_equity_percent_value; ?> %
	</td>
	<?php
	}
}
?>

<td class="deal-edit-snippet-right-col">
<input type="text" name="equity_payment_percent" id="equity_payment_percent" class="deal-edit-snippet-textbox-short" /> %
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
<td>Target Publicly Listed:</td>
<td><?php if($g_view['deal_data']['target_listed_in_stock_exchange'] == 'y'){?>Yes<?php }else{?>No<?php }?></td>

<?php
if(0==$g_view['suggestion_data_count']){
	?><td class="deal-edit-snippet-mid-col"></td><?php
}else{
	for($q=0;$q<$g_view['suggestion_data_count'];$q++){
	?>
	<td class="deal-edit-snippet-mid-col"><?php if($g_view['suggestion_data_arr'][$q]['target_listed_in_stock_exchange'] == 'y'){?>Yes<?php }else{?>No<?php }?></td>
	<?php
	}
}
?>

<td class="deal-edit-snippet-right-col">
<input type="radio" name="target_listed_in_stock_exchange" value="y" <?php if($g_view['deal_data']['target_listed_in_stock_exchange'] == 'y'){?>checked="checked"<?php }?> >Yes&nbsp;&nbsp;
<input type="radio" name="target_listed_in_stock_exchange" value="n" <?php if($g_view['deal_data']['target_listed_in_stock_exchange'] == 'n'){?>checked="checked"<?php }?> >No
</td>

</tr>
<!--************************************************************-->
<tr>
<td>Name of the stock exchange:</td>
<td><?php echo $g_view['deal_data']['target_stock_exchange_name'];?></td>

<?php
if(0==$g_view['suggestion_data_count']){
	?><td class="deal-edit-snippet-mid-col"></td><?php
}else{
	for($q=0;$q<$g_view['suggestion_data_count'];$q++){
	?>
	<td class="deal-edit-snippet-mid-col"><?php echo $g_view['suggestion_data_arr'][$q]['target_stock_exchange_name'];?></td>
	<?php
	}
}
?>

<td class="deal-edit-snippet-right-col">
<input type="text" name="target_stock_exchange_name" id="target_stock_exchange_name" class="deal-edit-snippet-textbox" />
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
<td>Merger Type:</td>
<td><?php if($g_view['deal_data']['takeover_id']==0) echo 'n/a'; else echo $g_view['deal_data']['takeover_name'];?></td>

<?php
if(0==$g_view['suggestion_data_count']){
	?><td class="deal-edit-snippet-mid-col"></td><?php
}else{
	for($q=0;$q<$g_view['suggestion_data_count'];$q++){
	?>
	<td class="deal-edit-snippet-mid-col"><?php if($g_view['suggestion_data_arr'][$q]['takeover_id']==0) echo 'n/a'; else echo $g_view['suggestion_data_arr'][$q]['takeover_name'];?></td>
	<?php
	}
}
?>

<td class="deal-edit-snippet-right-col">
<select name="takeover_id" class="deal-edit-snippet-dropdown">
<option value="">Select</option>
<?php
for($i=0;$i<$g_view['merger_list_count'];$i++){
    ?>
    <option value="<?php echo $g_view['merger_list'][$i]['takeover_id'];?>" <?php if($g_view['deal_data']['takeover_id']==$g_view['merger_list'][$i]['takeover_id']){?>selected="selected"<?php }?>><?php echo $g_view['merger_list'][$i]['takeover_name'];?></option>
    <?php
}
?>
</select>
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
<td>Termination Fee: (in million, local currency)</td>
<td>
<?php if($g_view['deal_data']['termination_fee_million']==""||$g_view['deal_data']['termination_fee_million']==0.0) echo "n/a"; else echo convert_million_for_display_round($g_view['deal_data']['termination_fee_million'])."m ".$deal_local_currency;?>
</td>

<?php
if(0==$g_view['suggestion_data_count']){
	?><td class="deal-edit-snippet-mid-col"></td><?php
}else{
	for($q=0;$q<$g_view['suggestion_data_count'];$q++){
	?>
	<td class="deal-edit-snippet-mid-col"><?php if($g_view['suggestion_data_arr'][$q]['termination_fee_million']==""||$g_view['suggestion_data_arr'][$q]['termination_fee_million']==0.0) echo "n/a"; else echo convert_million_for_display_round($g_view['suggestion_data_arr'][$q]['termination_fee_million'])."m ".$suggestion_local_currency;?></td>
	<?php
	}
}
?>

<td class="deal-edit-snippet-right-col">
<input type="text" name="termination_fee_million" value="<?php if($g_view['deal_data']['termination_fee_million']==""||$g_view['deal_data']['termination_fee_million']==0.0) echo ""; else echo $g_view['deal_data']['termination_fee_million'];?>" class="deal-edit-snippet-textbox-short" /> (in million, local currency)
</td>

</tr>
<!--************************************************************-->
<tr>
<td>End Date for Termination Fee:</td>
<td>
<?php if($g_view['deal_data']['end_date_termination_fee']=="0000-00-00"||$g_view['deal_data']['end_date_termination_fee']=="") echo "n/a"; else echo ymd_to_dmy($g_view['deal_data']['end_date_termination_fee']);?>
</td>

<?php
if(0==$g_view['suggestion_data_count']){
	?><td class="deal-edit-snippet-mid-col"></td><?php
}else{
	for($q=0;$q<$g_view['suggestion_data_count'];$q++){
	?>
	<td class="deal-edit-snippet-mid-col"><?php if($g_view['suggestion_data_arr'][$q]['end_date_termination_fee']=="0000-00-00"||$g_view['suggestion_data_arr'][$q]['end_date_termination_fee']=="") echo "n/a"; else echo ymd_to_dmy($g_view['suggestion_data_arr'][$q]['end_date_termination_fee']);?></td>
	<?php
	}
}
?>

<td class="deal-edit-snippet-right-col">
<input type="text" name="end_date_termination_fee" id="end_date_termination_fee" class="deal-edit-snippet-textbox-short" value="<?php if($g_view['deal_data']['end_date_termination_fee']=="0000-00-00"||$g_view['deal_data']['end_date_termination_fee']=="") echo ""; else echo $g_view['deal_data']['end_date_termination_fee'];?>" />
<script type="text/javascript">
// <![CDATA[       
var opts = {                            
formElements:{"end_date_termination_fee":"Y-ds-m-ds-d"},
showWeeks:false                   
};      
datePickerController.createDatePicker(opts);
// ]]>
</script>
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
<td>Fee (%) to Sellside Advisors:</td>
<td>
<?php if($g_view['deal_data']['fee_percent_to_sellside_advisor']==""||$g_view['deal_data']['fee_percent_to_sellside_advisor']==0.0) echo "n/a"; else echo $g_view['deal_data']['fee_percent_to_sellside_advisor']."%";?>
</td>

<?php
if(0==$g_view['suggestion_data_count']){
	?><td class="deal-edit-snippet-mid-col"></td><?php
}else{
	for($q=0;$q<$g_view['suggestion_data_count'];$q++){
	?>
	<td class="deal-edit-snippet-mid-col"><?php if($g_view['suggestion_data_arr'][$q]['fee_percent_to_sellside_advisor']==""||$g_view['suggestion_data_arr'][$q]['fee_percent_to_sellside_advisor']==0.0) echo "n/a"; else echo $g_view['suggestion_data_arr'][$q]['fee_percent_to_sellside_advisor']."%";?></td>
	<?php
	}
}
?>

<td class="deal-edit-snippet-right-col">
<input type="text" name="fee_percent_to_sellside_advisor" value="<?php if($g_view['deal_data']['fee_percent_to_sellside_advisor']==""||$g_view['deal_data']['fee_percent_to_sellside_advisor']==0.0) echo ""; else echo $g_view['deal_data']['fee_percent_to_sellside_advisor'];?>" class="deal-edit-snippet-textbox-short" />&nbsp;%
</td>

</tr>
<!--************************************************************-->
<tr>
<td>Fee (%) to Buyside Advisors:</td>
<td>
<?php if($g_view['deal_data']['fee_percent_to_buyside_advisor']==""||$g_view['deal_data']['fee_percent_to_buyside_advisor']==0.0) echo "n/a"; else echo $g_view['deal_data']['fee_percent_to_buyside_advisor']."%";?>
</td>

<?php
if(0==$g_view['suggestion_data_count']){
	?><td class="deal-edit-snippet-mid-col"></td><?php
}else{
	for($q=0;$q<$g_view['suggestion_data_count'];$q++){
	?>
	<td class="deal-edit-snippet-mid-col"><?php if($g_view['suggestion_data_arr'][$q]['fee_percent_to_buyside_advisor']==""||$g_view['suggestion_data_arr'][$q]['fee_percent_to_buyside_advisor']==0.0) echo "n/a"; else echo $g_view['suggestion_data_arr'][$q]['fee_percent_to_buyside_advisor']."%";?></td>
	<?php
	}
}
?>

<td class="deal-edit-snippet-right-col">
<input type="text" name="fee_percent_to_buyside_advisor" value="<?php if($g_view['deal_data']['fee_percent_to_buyside_advisor']==""||$g_view['deal_data']['fee_percent_to_buyside_advisor']==0.0) echo ""; else echo $g_view['deal_data']['fee_percent_to_buyside_advisor'];?>" class="deal-edit-snippet-textbox-short" />&nbsp;%
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

<tr><td colspan="2"><strong>Financial Metrics</strong></td></tr>

<tr>
<td>Revenues Last 12 Months:</td>
<td>
<?php if($g_view['deal_data']['revenue_ltm_million']==""||$g_view['deal_data']['revenue_ltm_million']==0.0) echo "n/a"; else echo convert_million_for_display_round($g_view['deal_data']['revenue_ltm_million'])."m ".$deal_local_currency;?>
</td>

<?php
if(0==$g_view['suggestion_data_count']){
	?><td class="deal-edit-snippet-mid-col"></td><?php
}else{
	for($q=0;$q<$g_view['suggestion_data_count'];$q++){
	?>
	<td class="deal-edit-snippet-mid-col"><?php if($g_view['suggestion_data_arr'][$q]['revenue_ltm_million']==""||$g_view['suggestion_data_arr'][$q]['revenue_ltm_million']==0.0) echo "n/a"; else echo convert_million_for_display_round($g_view['suggestion_data_arr'][$q]['revenue_ltm_million'])."m ".$suggestion_local_currency;?></td>
	<?php
	}
}
?>

<td class="deal-edit-snippet-right-col">
<input type="text" name="revenue_ltm_million" class="deal-edit-snippet-textbox-short" value="<?php if($g_view['deal_data']['revenue_ltm_million']==""||$g_view['deal_data']['revenue_ltm_million']==0.0) echo ""; else echo $g_view['deal_data']['revenue_ltm_million'];?>" /> (in million, local currency)
</td>

</tr>
<!--************************************************************-->
<tr>
<td>Revenues Most Recent Year:</td>
<td>
<?php if($g_view['deal_data']['revenue_mry_million']==""||$g_view['deal_data']['revenue_mry_million']==0.0) echo "n/a"; else echo convert_million_for_display_round($g_view['deal_data']['revenue_mry_million'])."m ".$deal_local_currency;?>
</td>

<?php
if(0==$g_view['suggestion_data_count']){
	?><td class="deal-edit-snippet-mid-col"></td><?php
}else{
	for($q=0;$q<$g_view['suggestion_data_count'];$q++){
	?>
	<td class="deal-edit-snippet-mid-col"><?php if($g_view['suggestion_data_arr'][$q]['revenue_mry_million']==""||$g_view['suggestion_data_arr'][$q]['revenue_mry_million']==0.0) echo "n/a"; else echo convert_million_for_display_round($g_view['suggestion_data_arr'][$q]['revenue_mry_million'])."m ".$suggestion_local_currency;?></td>
	<?php
	}
}
?>

<td class="deal-edit-snippet-right-col">
<input type="text" name="revenue_mry_million" class="deal-edit-snippet-textbox-short" value="<?php if($g_view['deal_data']['revenue_mry_million']==""||$g_view['deal_data']['revenue_mry_million']==0.0) echo ""; else echo $g_view['deal_data']['revenue_mry_million'];?>" /> (in million, local currency)
</td>

</tr>
<!--************************************************************-->
<tr>
<td>Revenues Next Year:</td>
<td>
<?php if($g_view['deal_data']['revenue_ny_million']==""||$g_view['deal_data']['revenue_ny_million']==0.0) echo "n/a"; else echo convert_million_for_display_round($g_view['deal_data']['revenue_ny_million'])."m ".$deal_local_currency;?>
</td>

<?php
if(0==$g_view['suggestion_data_count']){
	?><td class="deal-edit-snippet-mid-col"></td><?php
}else{
	for($q=0;$q<$g_view['suggestion_data_count'];$q++){
	?>
	<td class="deal-edit-snippet-mid-col"><?php if($g_view['suggestion_data_arr'][$q]['revenue_ny_million']==""||$g_view['suggestion_data_arr'][$q]['revenue_ny_million']==0.0) echo "n/a"; else echo convert_million_for_display_round($g_view['suggestion_data_arr'][$q]['revenue_ny_million'])."m ".$suggestion_local_currency;?></td>
	<?php
	}
}
?>

<td class="deal-edit-snippet-right-col">
<input type="text" name="revenue_ny_million" class="deal-edit-snippet-textbox-short" value="<?php if($g_view['deal_data']['revenue_ny_million']==""||$g_view['deal_data']['revenue_ny_million']==0.0) echo ""; else echo $g_view['deal_data']['revenue_ny_million'];?>" /> (in million, local currency)
</td>

</tr>
<!--************************************************************-->
<tr>
<td>EBITDA Last 12 Months:</td>
<td>
<?php if($g_view['deal_data']['ebitda_ltm_million']==""||$g_view['deal_data']['ebitda_ltm_million']==0.0) echo "n/a"; else echo convert_million_for_display_round($g_view['deal_data']['ebitda_ltm_million'])."m ".$deal_local_currency;?>
</td>

<?php
if(0==$g_view['suggestion_data_count']){
	?><td class="deal-edit-snippet-mid-col"></td><?php
}else{
	for($q=0;$q<$g_view['suggestion_data_count'];$q++){
	?>
	<td class="deal-edit-snippet-mid-col"><?php if($g_view['suggestion_data_arr'][$q]['ebitda_ltm_million']==""||$g_view['suggestion_data_arr'][$q]['ebitda_ltm_million']==0.0) echo "n/a"; else echo convert_million_for_display_round($g_view['suggestion_data_arr'][$q]['ebitda_ltm_million'])."m ".$suggestion_local_currency;?></td>
	<?php
	}
}
?>

<td class="deal-edit-snippet-right-col">
<input type="text" name="ebitda_ltm_million" class="deal-edit-snippet-textbox-short" value="<?php if($g_view['deal_data']['ebitda_ltm_million']==""||$g_view['deal_data']['ebitda_ltm_million']==0.0) echo ""; else echo $g_view['deal_data']['ebitda_ltm_million'];?>" /> (in million, local currency)
</td>

</tr>
<!--************************************************************-->
<tr>
<td>EBITDA Most Recent Year:</td>
<td>
<?php if($g_view['deal_data']['ebitda_mry_million']==""||$g_view['deal_data']['ebitda_mry_million']==0.0) echo "n/a"; else echo convert_million_for_display_round($g_view['deal_data']['ebitda_mry_million'])."m ".$deal_local_currency;?>
</td>

<?php
if(0==$g_view['suggestion_data_count']){
	?><td class="deal-edit-snippet-mid-col"></td><?php
}else{
	for($q=0;$q<$g_view['suggestion_data_count'];$q++){
	?>
	<td class="deal-edit-snippet-mid-col"><?php if($g_view['suggestion_data_arr'][$q]['ebitda_mry_million']==""||$g_view['suggestion_data_arr'][$q]['ebitda_mry_million']==0.0) echo "n/a"; else echo convert_million_for_display_round($g_view['suggestion_data_arr'][$q]['ebitda_mry_million'])."m ".$suggestion_local_currency;?></td>
	<?php
	}
}
?>

<td class="deal-edit-snippet-right-col">
<input type="text" name="ebitda_mry_million" class="deal-edit-snippet-textbox-short" value="<?php if($g_view['deal_data']['ebitda_mry_million']==""||$g_view['deal_data']['ebitda_mry_million']==0.0) echo ""; else echo $g_view['deal_data']['ebitda_mry_million'];?>" /> (in million, local currency)
</td>

</tr>
<!--************************************************************-->
<tr>
<td>EBITDA Next Year:</td>
<td>
<?php if($g_view['deal_data']['ebitda_ny_million']==""||$g_view['deal_data']['ebitda_ny_million']==0.0) echo "n/a"; else echo convert_million_for_display_round($g_view['deal_data']['ebitda_ny_million'])."m ".$deal_local_currency;?>
</td>

<?php
if(0==$g_view['suggestion_data_count']){
	?><td class="deal-edit-snippet-mid-col"></td><?php
}else{
	for($q=0;$q<$g_view['suggestion_data_count'];$q++){
	?>
	<td class="deal-edit-snippet-mid-col"><?php if($g_view['suggestion_data_arr'][$q]['ebitda_ny_million']==""||$g_view['suggestion_data_arr'][$q]['ebitda_ny_million']==0.0) echo "n/a"; else echo convert_million_for_display_round($g_view['suggestion_data_arr'][$q]['ebitda_ny_million'])."m ".$suggestion_local_currency;?></td>
	<?php
	}
}
?>

<td class="deal-edit-snippet-right-col">
<input type="text" name="ebitda_ny_million" class="deal-edit-snippet-textbox-short" value="<?php if($g_view['deal_data']['ebitda_ny_million']==""||$g_view['deal_data']['ebitda_ny_million']==0.0) echo ""; else echo $g_view['deal_data']['ebitda_ny_million'];?>" /> (in million, local currency)
</td>

</tr>
<!--************************************************************-->
<tr>
<td>Net Income Last 12 Months:</td>
<td>
<?php if($g_view['deal_data']['net_income_ltm_million']==""||$g_view['deal_data']['net_income_ltm_million']==0.0) echo "n/a"; else echo convert_million_for_display_round($g_view['deal_data']['net_income_ltm_million'])."m ".$deal_local_currency;?>
</td>

<?php
if(0==$g_view['suggestion_data_count']){
	?><td class="deal-edit-snippet-mid-col"></td><?php
}else{
	for($q=0;$q<$g_view['suggestion_data_count'];$q++){
	?>
	<td class="deal-edit-snippet-mid-col"><?php if($g_view['suggestion_data_arr'][$q]['net_income_ltm_million']==""||$g_view['suggestion_data_arr'][$q]['net_income_ltm_million']==0.0) echo "n/a"; else echo convert_million_for_display_round($g_view['suggestion_data_arr'][$q]['net_income_ltm_million'])."m ".$suggestion_local_currency;?></td>
	<?php
	}
}
?>

<td class="deal-edit-snippet-right-col">
<input type="text" name="net_income_ltm_million" class="deal-edit-snippet-textbox-short" value="<?php if($g_view['deal_data']['net_income_ltm_million']==""||$g_view['deal_data']['net_income_ltm_million']==0.0) echo ""; else echo $g_view['deal_data']['net_income_ltm_million'];?>" /> (in million, local currency)
</td>

</tr>
<!--************************************************************-->
<tr>
<td>Net Income Most Recent Year:</td>
<td>
<?php if($g_view['deal_data']['net_income_mry_million']==""||$g_view['deal_data']['net_income_mry_million']==0.0) echo "n/a"; else echo convert_million_for_display_round($g_view['deal_data']['net_income_mry_million'])."m ".$deal_local_currency;?>
</td>

<?php
if(0==$g_view['suggestion_data_count']){
	?><td class="deal-edit-snippet-mid-col"></td><?php
}else{
	for($q=0;$q<$g_view['suggestion_data_count'];$q++){
	?>
	<td class="deal-edit-snippet-mid-col"><?php if($g_view['suggestion_data_arr'][$q]['net_income_mry_million']==""||$g_view['suggestion_data_arr'][$q]['net_income_mry_million']==0.0) echo "n/a"; else echo convert_million_for_display_round($g_view['suggestion_data_arr'][$q]['net_income_mry_million'])."m ".$suggestion_local_currency;?></td>
	<?php
	}
}
?>

<td class="deal-edit-snippet-right-col">
<input type="text" name="net_income_mry_million" class="deal-edit-snippet-textbox-short" value="<?php if($g_view['deal_data']['net_income_mry_million']==""||$g_view['deal_data']['net_income_mry_million']==0.0) echo ""; else echo $g_view['deal_data']['net_income_mry_million'];?>" /> (in million, local currency)
</td>

</tr>
<!--************************************************************-->
<tr>
<td>Net Income Next Year:</td>
<td>
<?php if($g_view['deal_data']['net_income_ny_million']==""||$g_view['deal_data']['net_income_ny_million']==0.0) echo "n/a"; else echo convert_million_for_display_round($g_view['deal_data']['net_income_ny_million'])."m ".$deal_local_currency;?>
</td>

<?php
if(0==$g_view['suggestion_data_count']){
	?><td class="deal-edit-snippet-mid-col"></td><?php
}else{
	for($q=0;$q<$g_view['suggestion_data_count'];$q++){
	?>
	<td class="deal-edit-snippet-mid-col"><?php if($g_view['suggestion_data_arr'][$q]['net_income_ny_million']==""||$g_view['suggestion_data_arr'][$q]['net_income_ny_million']==0.0) echo "n/a"; else echo convert_million_for_display_round($g_view['suggestion_data_arr'][$q]['net_income_ny_million'])."m ".$suggestion_local_currency;?></td>
	<?php
	}
}
?>

<td class="deal-edit-snippet-right-col">
<input type="text" name="net_income_ny_million" class="deal-edit-snippet-textbox-short" value="<?php if($g_view['deal_data']['net_income_ny_million']==""||$g_view['deal_data']['net_income_ny_million']==0.0) echo ""; else echo $g_view['deal_data']['net_income_ny_million'];?>" /> (in million, local currency)
</td>
</tr>
<!--************************************************************-->
<tr>
<td>Year-End Date of Most Recent Financial Year:</td>

<td><?php if($g_view['deal_data']['date_year_end_of_recent_financial_year']=="0000-00-00"||$g_view['deal_data']['date_year_end_of_recent_financial_year']=="") echo "n/a"; else echo ymd_to_dmy($g_view['deal_data']['date_year_end_of_recent_financial_year']);?></td>

<?php
if(0==$g_view['suggestion_data_count']){
	?><td class="deal-edit-snippet-mid-col"></td><?php
}else{
	for($q=0;$q<$g_view['suggestion_data_count'];$q++){
	?>
	<td class="deal-edit-snippet-mid-col"><?php if($g_view['suggestion_data_arr'][$q]['date_year_end_of_recent_financial_year']=="0000-00-00"||$g_view['suggestion_data_arr'][$q]['date_year_end_of_recent_financial_year']=="") echo "n/a"; else echo ymd_to_dmy($g_view['suggestion_data_arr'][$q]['date_year_end_of_recent_financial_year']);?></td>
	<?php
	}
}
?>

<td class="deal-edit-snippet-right-col">
<input type="text" name="date_year_end_of_recent_financial_year" id="date_year_end_of_recent_financial_year" class="deal-edit-snippet-textbox-short" value="<?php if($g_view['deal_data']['date_year_end_of_recent_financial_year']=="0000-00-00"||$g_view['deal_data']['date_year_end_of_recent_financial_year']=="") echo ""; else echo $g_view['deal_data']['date_year_end_of_recent_financial_year'];?>" />
<script type="text/javascript">
// <![CDATA[       
var opts = {                            
		formElements:{"date_year_end_of_recent_financial_year":"Y-ds-m-ds-d"},
		showWeeks:false                   
};      
datePickerController.createDatePicker(opts);
// ]]>
</script>
</td>
</tr>
<!--************************************************************-->
<script>
jQuery('#target_stock_exchange_name').devbridge_autocomplete({
	serviceUrl:'admin/ajax/fetch_stock_exchange_list.php',
	minChars:1,
	noCache: true,
	width:'100%',
	onSelect: function(value, data){
		jQuery('#target_stock_exchange_name').val(data);
	}
});
</script>