<script src="js/jquery.form.js"></script>


<script>
function company_sector_changed(){
	var sector_obj = document.getElementById('company_sector');
	var offset_selected = sector_obj.selectedIndex;
	if(offset_selected != 0){
		var sector_selected = sector_obj.options[offset_selected].value;
		//fetch the list of industries
		$.post("admin/ajax/industry_list_for_sector.php", {sector: ""+sector_selected+""}, function(data){
				if(data.length >0) {
					$('#company_industry').html(data);
					$('#company_industry').selectmenu();
				}
		});
	}
}
</script>

<table width="100%" cellpadding="0" cellspacing="0">
<tr>
<td>
<!--/////////////////////////////details///////////////////////////////////-->
<div id="company-detail">
<form id="company_data_frm" method="post" action="">
<?php
/******************
sng:8/dec/2011
for new company, company_id is 0
**********************/
?>
<input type="hidden" name="company_id" value="0" />
<?php
for($j=0;$j<$g_view['identifiers_cnt'];$j++){
?>
<input type="hidden" name="identifier_ids[]" value="<?php echo $g_view['identifiers'][$j]['identifier_id'];?>" />
<?php
}
?>
<table width="100%" cellpadding="0" cellspacing="0" border="0">



<tr>
<td colspan="2"><strong>Company Details:</strong></td>
</tr>

<tr>
<td class="left-label">Name (Short):</td>
<td class="right-input"><input type="text" name="name" class="txtinput" /><span class="err_txt">*</span><br />
<span id="err_name" class="err_txt"></span></td>
</tr>

<tr>
<td colspan="2" class="vseparation"></td>
</tr>

<tr>
<td class="left-label">Country of HQ:</td>
<td class="right-input">
<select name="country_of_headquarters" style="width: 200px;">
<option value="">Select</option>
<?php
for($i=0;$i<$g_view['country_count'];$i++){
	?>
	<option value="<?php echo $g_view['country_list'][$i]['name'];?>" ><?php echo $g_view['country_list'][$i]['name'];?></option>
	<?php
}
?>
</select><span class="err_txt">*</span><br />
<span id="err_country_of_headquarters" class="err_txt"></span>
</td>
</tr>

<tr>
<td colspan="2" class="vseparation"></td>
</tr>

<tr>
<td class="left-label">Sector:</td>
<td class="right-input">
<select name="company_sector" id="company_sector" onChange="return company_sector_changed();" style="width: 200px;">
<option value="">Select</option>
<?php
for($i=0;$i<$g_view['sector_count'];$i++){
	?>
	<option value="<?php echo $g_view['sector_list'][$i]['sector'];?>"><?php echo $g_view['sector_list'][$i]['sector'];?></option>
	<?php
}
?>
</select><span class="err_txt">*</span><br />
<span id="err_company_sector" class="err_txt"></span>
</td>
</tr>

<tr>
<td colspan="2" class="vseparation"></td>
</tr>

<tr>
<td class="left-label">Industry:</td>

<td class="right-input">
<select name="company_industry" id="company_industry" style="width: 200px;">
<option value=""> Select industry </option>                                             
</select><span class="err_txt">*</span><br />
<span id="err_company_industry" class="err_txt"></span>
</td>
</tr>
<?php
/********************
sng:18/may/2012
We now allow to specify the logo
*****/
?>
<tr>
<td colspan="2" class="vseparation"></td>
</tr>

<tr>
<td class="left-label">Logo:</td>
<td class="right-input"><input type="file" name="logo" class="txtinput" /><br />
<span id="err_logo" class="err_txt"></span></td>
</tr>

<tr>
<td colspan="2" class="vseparation"></td>
</tr>

<tr>
<td colspan="2"><strong>Company Identifiers:</strong></td>
</tr>

<?php
for($j=0;$j<$g_view['identifiers_cnt'];$j++){
	$field_name = "identifier_id_".$g_view['identifiers'][$j]['identifier_id'];
	?>
	<tr>
	<td class="left-label"><?php echo $g_view['identifiers'][$j]['name'];?>:</td>
	
	<td class="right-input"><input type="text" name="<?php echo $field_name;?>" class="txtinput" /></td>
	</tr>
	<tr>
	<td colspan="2" class="vseparation"></td>
	</tr>
	<?php
}
?>





<tr><td style="height:20px;" colspan="4"></td></tr>
<tr><td style="height:20px;" colspan="4"><div id="company_data_frm_msg" class="msg_txt"></div></td></tr>
<tr>
<td></td>
<td>
<?php
/****************
sng:18/may/2012
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


<!--/////////////////////////////////////////suggestion/////////////////////////////////////-->
<script>
jQuery(function(){
	var suggestion_options = {
		beforeSubmit:  validate_suggestion_posting,
		success:       suggestion_posted,
		url:       'ajax/post_company_suggestion.php',  // your upload script
		dataType:  'json'
	};
	jQuery('#company_data_frm').submit(function() {
		jQuery(this).ajaxSubmit(suggestion_options);
		return false;
	});
});

function validate_suggestion_posting(formData, jqForm, options) {
	//nothing to validate for now
	$('#err_name').html("");
	$('#err_country_of_headquarters').html("");
	$('#err_company_sector').html("");
	$('#err_company_industry').html("");
	jQuery("#company_data_frm_msg").html("submitting...");
	return true;
}
function suggestion_posted(data, statusText)  {
	if (statusText == 'success') {
		if (data.posted == 'y') {
			jQuery("#company_data_frm_msg").html(data.msg);
		} else {
			//one or more error msg
			$('#err_name').html(data.err_arr.name);
			$('#err_country_of_headquarters').html(data.err_arr.country_of_headquarters);
			$('#err_company_sector').html(data.err_arr.company_sector);
			$('#err_company_industry').html(data.err_arr.company_industry);
			
			jQuery("#company_data_frm_msg").html(data.msg);
		}
	} else {
		jQuery("#company_data_frm_msg").html('Unknown error!');
	}
}

</script>
<!--/////////////////////////////////////////suggestion/////////////////////////////////////-->
<script>
jQuery(function(){
	$('select').selectmenu();
});
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
<script>
function show_non_login_alert(){
	apprise("Please login to suggest a company",{'textOk':'OK'});
}
</script>