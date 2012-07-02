<?php
/******************
sng:26/mar/2012

This is a bit different. This fetches the deal data and the suggestions and create the whole UI

This is called in ajax. That means, we will have to fetch the extra details, but only related to valuation data
regarding the deal
*******************/
require_once("../../include/global.php");
require_once("classes/class.transaction.php");
require_once("classes/class.deal_support.php");
require_once("classes/class.transaction_suggestion.php");
$trans_suggestion = new transaction_suggestion();
$deal_support = new deal_support();

$g_view['deal_id'] = $_GET['deal_id'];
$g_view['deal_found'] = false;
$g_view['deal_data'] = array();
$success = $trans_suggestion->get_deal_detail_extra($g_view['deal_id'],$g_view['deal_data'],$g_view['deal_found']);
if(!$success){
	die("Cannot get the deal");
}

if(!$g_view['deal_found']){
	echo "Deal data not found";
	return;
}
/*******************************
find out who submitted the deal. This is copy of the logic in deal_page_detail_suggestion.php

It may happen that the added_on date is not there. In that case show N/A
****************/
if($g_view['deal_data']['added_on'] == '0000-00-00 00:00:00'){
	$g_view['submisson_date'] = 'N/A';
}else{
	$g_view['submisson_date'] = date('jS M Y',strtotime($g_view['deal_data']['added_on']));
}
/**************************************************/
if($g_view['deal_data']['added_by_mem_id']!=0){
	$submitter_work_email_tokens = explode('@',$g_view['deal_data']['work_email']);
	$submitter_work_email_suffix = $submitter_work_email_tokens[1];
	$g_view['deal_submitter'] = $g_view['deal_data']['member_type']." @".$submitter_work_email_suffix;
}else{
	$g_view['deal_submitter'] = "Admin";
}

/**********************
get the suggestions
*********************/
$g_view['suggestion_data_arr'] = NULL;
$g_view['suggestion_data_count'] = 0;

$ok = $trans_suggestion->fetch_valuation($g_view['deal_id'],$g_view['suggestion_data_arr'],$g_view['suggestion_data_count']);
if(!$ok){
	/*echo mysql_error();*/
	echo "Error";
	return;
}


/*************
If the deal is in USD, the local currency will be blank. In that case, we assume local currency as USD.
**************/
if($g_view['deal_data']['currency']==""){
	$deal_local_currency = "USD";
}else{
	$deal_local_currency = $g_view['deal_data']['currency'];
}

/***********************
for each suggestions, we need to set the currency. If there is suggestion, we use that one else assume that it is USD

sng:4/apr/2012
correction: If there is suggestion, we use that one. Else we assume that the submitter is satisfied with the local currency set for the deal and use that
***************************/
for($q=0;$q<$g_view['suggestion_data_count'];$q++){
	if($g_view['suggestion_data_arr'][$q]['currency']==''){
		$g_view['suggestion_data_arr'][$q]['currency'] = $deal_local_currency;
	}
}
/*********
sng:5/apr/2012
We move the styles to css file
****************/
if(0==$g_view['suggestion_data_count']){
	$num_mid_cols = 1;
	//a blank
}else{
	$num_mid_cols = $g_view['suggestion_data_count'];
}
?>
<form id="frm_edit_valuation_details">
<input type="hidden" name="deal_id" value="<?php echo $g_view['deal_data']['deal_id'];?>" />
<table cellspacing="0" cellpadding="0">
<tr>
<td colspan="2" class="deal-edit-snippet-header">Original Submission:</td>
<td colspan="<?php echo $num_mid_cols;?>" class="deal-edit-snippet-header" >Edits / Additions:</td>
<td class="deal-edit-snippet-header" style="min-width:200px;">Your Suggestions:</td>
</tr>

<tr>
<td style="min-width:250px;"></td>
<td style="min-width:150px;"></td>
<td colspan="<?php echo $num_mid_cols;?>"></td>
<td style="min-width:200px;"></td>
</tr>

<?php
if(strtolower($g_view['deal_data']['deal_cat_name']) == "m&a") require("fetch_valuation_detail_ma.php");
else require("fetch_valuation_detail_common.php");
/***********
elseif((strtolower($g_view['deal_data']['deal_cat_name']) == "debt")&&(strtolower($g_view['deal_data']['deal_subcat1_name']) == "bond")) require("fetch_valuation_detail_bond.php");
elseif((strtolower($g_view['deal_data']['deal_cat_name']) == "debt")&&(strtolower($g_view['deal_data']['deal_subcat1_name']) == "loan")) require("fetch_valuation_detail_loan.php");
elseif((strtolower($g_view['deal_data']['deal_cat_name'])=="equity")&&(strtolower($g_view['deal_data']['deal_subcat1_name'])=="equity")&&(strtolower($g_view['deal_data']['deal_subcat2_name'])=="rights issue")) require("fetch_valuation_detail_equity_rights.php");
elseif((strtolower($g_view['deal_data']['deal_cat_name'])=="equity")&&(strtolower($g_view['deal_data']['deal_subcat1_name'])=="equity")&&(strtolower($g_view['deal_data']['deal_subcat2_name'])=="ipo")) require("fetch_valuation_detail_equity_ipo.php");
elseif((strtolower($g_view['deal_data']['deal_cat_name'])=="equity")&&(strtolower($g_view['deal_data']['deal_subcat1_name'])=="equity")&&(strtolower($g_view['deal_data']['deal_subcat2_name'])=="additional")) require("fetch_valuation_detail_equity_additional.php");
elseif((strtolower($g_view['deal_data']['deal_cat_name'])=="equity")&&(strtolower($g_view['deal_data']['deal_subcat1_name'])=="preferred")) require("fetch_valuation_detail_equity_preferred.php");
elseif((strtolower($g_view['deal_data']['deal_cat_name'])=="equity")&&(strtolower($g_view['deal_data']['deal_subcat1_name'])=="convertible")) require("fetch_valuation_detail_equity_convertible.php");
*****************/
?>



<tr>
<td colspan="2" style="padding-right:10px;padding-left:10px;">
<div class="hr_div"></div>
<div class="deal-edit-snippet-footer">Submitted <?php echo $g_view['submisson_date'];?></div>
<div class="deal-edit-snippet-footer"><?php echo $g_view['deal_submitter'];?></div>
</td>

<?php
if(0==$g_view['suggestion_data_count']){
	?><td style="padding-right:10px;padding-left:10px;"></td><?php
}else{
	for($q=0;$q<$g_view['suggestion_data_count'];$q++){
		$work_email = $g_view['suggestion_data_arr'][$q]['work_email'];
		$tokens = explode('@',$work_email);
		$work_email_suffix = $tokens[1];
		?>
		<td style="padding-right:10px;padding-left:10px;">
		<div class="hr_div"></div>
		<div class="deal-edit-snippet-footer">Submitted <?php echo date('jS M Y',strtotime($g_view['suggestion_data_arr'][$q]['date_suggested']));?></div>
		<div class="deal-edit-snippet-footer"><?php echo $g_view['suggestion_data_arr'][$q]['member_type'].'@'.$work_email_suffix;?></div>
		</td>
		<?php
	}
}
?>


<td style="padding-right:10px;padding-left:10px;">
<div id="result_frm_edit_valuation_details" class="msg_txt"></div>
<div class="hr_div"></div>
<div style="text-align:right;"><input type="button" id="btn_submit_frm_edit_valuation_detail" value="Submit" class="btn_auto" onClick="submit_frm_edit_valuation_details();" /></div>
</td>
</tr>
</table>
</form>
<script>
$(function(){
	$('.btn_auto').button();
});

function submit_frm_edit_valuation_details(){
	if(can_submit()){
		
		
		$('result_frm_edit_valuation_details').html('sending...');
		$.post('ajax/suggest_deal_correction/valuation_detail.php',$('#frm_edit_valuation_details').serialize(),function(result){
			$('#result_frm_edit_valuation_details').html(result);
		});
	}
}
</script>
