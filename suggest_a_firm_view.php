<script src="js/jquery.form.js"></script>

<table width="100%" cellpadding="0" cellspacing="0">
<tr>
<td>
<!--/////////////////////////////details///////////////////////////////////-->
<div id="company-detail">
<form id="firm_data_frm" method="post" action="">
<?php
/******************
sng:8/dec/2011
for new firm, company_id is 0
**********************/
?>
<input type="hidden" name="company_id" value="0" />
<input type="hidden" name="type" value="<?php echo $g_view['firm_type'];?>" />
<table width="100%" cellpadding="0" cellspacing="0" border="0">



<tr>
<td colspan="2"><strong><?php if($g_view['firm_type']=="bank"){?>Bank<?php }else{?>Law Firm<?php }?> Details:</strong></td>
</tr>
<tr>
<td colspan="2" class="vseparation"></td>
</tr>

<tr>
<td class="left-label">Name (Short):</td>
<td class="right-input"><input type="text" name="name" class="txtinput" /><span class="err_txt">*</span><br />
<span id="err_name" class="err_txt"></span></td>
</tr>

<tr>
<td colspan="2" class="vseparation"></td>
</tr>

<?php
/************************
sng:7/may/2012
No need for the abbreviated name. That is used only in charts. At any rate, we should generate
our own abbreviation.
************************/
?>


<?php
/************************
sng:7/may/2012
We are now changing the workflow a bit. We now allow the member to upload the logo image also.
*******/
?>
<tr>
<td class="left-label">Logo:</td>
<td class="right-input"><input type="file" name="logo" class="txtinput" /><br />
<span id="err_logo" class="err_txt"></span></td>
</tr>


<tr><td style="height:20px;" colspan="4"></td></tr>
<tr><td style="height:20px;" colspan="4"><div id="firm_data_frm_msg" class="msg_txt"></div></td></tr>
<tr>
<td></td>
<td>
<?php
/****************
sng:21/nov/2011
If not logged in, show alert
*******************/
if(!$g_account->is_site_member_logged()){
	?>
	<input type="button" class="btn_auto" value="Submit" onclick="show_non_login_alert();" />
	<?php
}else{
	?><input type="submit" value="Submit" class="btn_auto" /><?php
}
/**************************/
?>
</td>
</tr>
</table>


</form>
</div>
<!--/////////////////////////////details///////////////////////////////////-->
</td>
</tr>
</table>
<script>
jQuery(function(){
	var suggestion_options = {
		beforeSubmit:  validate_suggestion_posting,
		success:       suggestion_posted,
		url:       'ajax/post_firm_suggestion.php',  // your upload script
		dataType:  'json'
	};
	jQuery('#firm_data_frm').submit(function() {
		jQuery(this).ajaxSubmit(suggestion_options);
		return false;
	});
});

function validate_suggestion_posting(formData, jqForm, options) {
	//nothing to validate for now
	$('#err_name').html("");
	$('#err_logo').html("");
	jQuery("#firm_data_frm_msg").html("submitting...");
	return true;
}
function suggestion_posted(data, statusText)  {
	if (statusText == 'success') {
		if (data.posted == 'y') {
			jQuery("#firm_data_frm_msg").html(data.msg);
		} else {
			//one or more error msg
			$('#err_name').html(data.err_arr.name);
			$('#err_logo').html(data.err_arr.logo);
			jQuery("#firm_data_frm_msg").html(data.msg);
		}
	} else {
		jQuery("#firm_data_frm_msg").html('Unknown error!');
	}
}

</script>
<script>
function show_non_login_alert(){
	apprise("Please login to suggest a <?php echo $g_view['firm_type'];?>",{'textOk':'OK'});
}
</script>
<?php
/******************
sng:13/dec/2011
If the user is not logged in, we show a popup
********************/
if(!$g_account->is_site_member_logged()){
	?>
	<script>
	$(function(){
		apprise("Please log-in before you enter / submit new data, many thanks.",{'textOk':'OK'});
	});
	</script>
	<?php
}
?>
