<?php
/*************
sng:27/jun/2011
The user has to login to see this pane

sng:11/nov/2011
Allow all
**********/
//if(!$g_account->is_site_member_logged()){
	?>
	<!--<p>Sorry, you need to login to view the deal detail</p>-->
	<?php
	//return;
//}

?>
<div id="deal-detail">
<p>
Here you can see the details of the deal and can send your suggestions / corrections if required.
</p>
<div style="height:20px;"></div>
<form id="correct_deal_data_frm" method="post" action="">
<input type="hidden" name="deal_id" value="<?php echo $g_view['deal_data']['deal_id'];?>" />

<input type="hidden" name="deal_cat_name" value="<?php echo $g_view['deal_data']['deal_cat_name'];?>" />
<input type="hidden" name="deal_subcat1_name" value="<?php echo $g_view['deal_data']['deal_subcat1_name'];?>" />
<input type="hidden" name="deal_subcat2_name" value="<?php echo $g_view['deal_data']['deal_subcat2_name'];?>" />

<table width="100%" cellpadding="0" cellspacing="0" border="0">
<tr>
<td colspan="3" class="deal_page_h2">Deal Detail</td>
<td class="deal_page_h2">Suggestion / Correction</td>
</tr>
<tr>
<td colspan="4" class="vseparation"></td>
</tr>
<?php
if(strtolower($g_view['deal_data']['deal_cat_name']) == "m&a") require("deal_page_detail_ma.php");
elseif((strtolower($g_view['deal_data']['deal_cat_name']) == "debt")&&(strtolower($g_view['deal_data']['deal_subcat1_name']) == "bond")) require("deal_page_detail_bond.php");
elseif((strtolower($g_view['deal_data']['deal_cat_name']) == "debt")&&(strtolower($g_view['deal_data']['deal_subcat1_name']) == "loan")) require("deal_page_detail_loan.php");
elseif((strtolower($g_view['deal_data']['deal_cat_name'])=="equity")&&(strtolower($g_view['deal_data']['deal_subcat1_name'])=="equity")&&(strtolower($g_view['deal_data']['deal_subcat2_name'])=="rights issue")) require("deal_page_detail_equity_rights.php");
elseif((strtolower($g_view['deal_data']['deal_cat_name'])=="equity")&&(strtolower($g_view['deal_data']['deal_subcat1_name'])=="equity")&&(strtolower($g_view['deal_data']['deal_subcat2_name'])=="ipo")) require("deal_page_detail_equity_ipo.php");
elseif((strtolower($g_view['deal_data']['deal_cat_name'])=="equity")&&(strtolower($g_view['deal_data']['deal_subcat1_name'])=="equity")&&(strtolower($g_view['deal_data']['deal_subcat2_name'])=="additional")) require("deal_page_detail_equity_additional.php");
elseif((strtolower($g_view['deal_data']['deal_cat_name'])=="equity")&&(strtolower($g_view['deal_data']['deal_subcat1_name'])=="preferred")) require("deal_page_detail_equity_preferred.php");
elseif((strtolower($g_view['deal_data']['deal_cat_name'])=="equity")&&(strtolower($g_view['deal_data']['deal_subcat1_name'])=="convertible")) require("deal_page_detail_equity_convertible.php");
?>
</table>
<table width="100%" cellpadding="0" cellspacing="0" border="0">
<tr><td style="height:20px;" colspan="2"></td></tr>
<tr><td style="height:20px;" colspan="2"><div id="correct_deal_data_frm_msg" class="msg_txt"></div></td></tr>
<tr><td style="height:20px;" colspan="2"></td></tr>
<tr>
<td>
<input name="public_details" id="public_details" type="checkbox" value="y" />&nbsp;Please tick to confirm all details are public information
</td>
<td>
<?php
/****************
sng:11/nov/2011
If not logged in, show alert
*******************/
if(!$g_account->is_site_member_logged()){
	?>
	<input type="button" class="btn_auto" value="Submit" onclick="show_non_login_alert();" />
	<?php
}else{
	?><input type="submit" name="submit" value="Submit" class="btn_auto" /><?php
}
?>
</td>
</tr>
</table>
</form>
</div>
<script>

jQuery('#correct_deal_data_frm').submit(function() {
	jQuery(this).ajaxSubmit({
		beforeSubmit:  validate_correction_posting,
		success: correction_posted,
		url: 'ajax/post_deal_correction.php',  // your upload script
		dataType:  'json'
	});
	return false;
});
function validate_correction_posting(formData, jqForm, options) {
	if(!jQuery("#public_details").attr('checked')){
		alert("You need to confirm that all the details are public information");
		return false;
	}
	jQuery("#correct_deal_data_frm_msg").html("submitting...");
	return true;
}
function correction_posted(data, statusText)  {
	if (statusText == 'success') {
		if (data.posted == 'y') {
			jQuery("#correct_deal_data_frm_msg").html("Your suggestion / correction has been posted");
		} else {
			jQuery("#correct_deal_data_frm_msg").html(data.error);
		}
	} else {
		jQuery("#correct_deal_data_frm_msg").html('Unknown error!');
	}
}

</script>
<script>
function show_non_login_alert(){
	apprise("Please login to submit deal data correction",{'textOk':'OK'});
}
</script>
<script>
function can_edit_alert(){
	<?php
	/******************
	sng:13/dec/2011
	If the user is not logged in, we show a popup
	********************/
	if(!$g_account->is_site_member_logged()){
		?>
		apprise("Please log-in before you enter / submit new data, many thanks.",{'textOk':'OK'});
		<?php
	}
	?>
}
</script>