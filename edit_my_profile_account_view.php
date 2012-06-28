<script type="text/javascript">
function lookup(inputString) {
	//alert(inputString);
	if(inputString.length == 0) {
		// Hide the suggestion box.
		$('#suggestions').hide();
	} else {
		// post data to our php processing page and if there is a return greater than zero
		// show the suggestions box
		<?php
		/*********
		sng:8/mar/2011
		member type comes from session
		********/
		?>
		var type_selected = "<?php echo $_SESSION['member_type'];?>";
		$('#firm_name_searching').html("searching...");
		$.post("ajax/company_list.php", {search_string: ""+inputString+"",type: ""+type_selected+""}, function(data){
			$('#firm_name_searching').html("");
			if(data.length >0) {
				$('#suggestions').show();
				$('#autoSuggestionsList').html(data);
			}else{
				//no matches found, we hide the suggestion list
				setTimeout("$('#suggestions').hide();", 200);
			}
		});
	}
} //end

function fill(thisValue) {
	$('#firm_name').val(thisValue);
	setTimeout("$('#suggestions').hide();", 200);
}
</script>
<p><span class="msg_txt"><?php echo $g_view['msg'];?></span></p>
<table cellpadding="0" cellspacing="0" style="width:400px;">
<tr>
<td>
<form method="post" action="" enctype="multipart/form-data">
<input type="hidden" name="action" value="change_profile" />
<table width="100%" cellpadding="0" cellspacing="0" class="company">
<tr>
<td>First Name</td>
<td>
<input type="text" name="f_name" value="<?php echo $g_view['data']['f_name'];?>" class="txtbox" /><span class="err_txt"> *</span><br /><span class="err_txt"><?php echo $g_view['err']['f_name'];?></span>	
</td>
</tr>

<tr>
<td>Last Name</td>
<td>
<input type="text" name="l_name" value="<?php echo $g_view['data']['l_name'];?>" class="txtbox" /><span class="err_txt"> *</span><br /><span class="err_txt"><?php echo $g_view['err']['l_name'];?></span>	
</td>
</tr>


<tr>
<td>&nbsp;</td>
<td><input type="submit" name="submit" value="Update" class="btn_auto" />
</table>
</form>
</td>
</tr>
</table>
<div style="height:40px;">&nbsp;</div>
<p>
If you change the company, then please specify your new work email. An activation link will be sent in that email for verification. Your profile will be updated only after you click the verification link.</p>
<p>
It may happen that admin may decide to reject your company change request if the work email does not belong to the company.
</p>
<table cellpadding="0" cellspacing="0" style="width:400px;">
<tr>
<td>
<form method="post" action="">
<input type="hidden" name="action" value="change_company" />
<input type="hidden" name="curr_firm_name" value="<?php echo $g_view['company_data']['firm_name'];?>" />

<input type="hidden" name="curr_firm_id" value="<?php echo $g_view['company_data']['company_id'];?>" />
<input type="hidden" name="curr_member_type" value="<?php echo $g_view['company_data']['member_type'];?>" />
<input type="hidden" name="curr_designation" value="<?php echo $g_view['company_data']['designation'];?>" />
<input type="hidden" name="curr_firm_year_joined" value="<?php echo $g_view['company_data']['year_joined'];?>" />

<input type="hidden" name="member_type" value="<?php echo $g_view['company_data']['member_type'];?>" />

<table width="100%" cellpadding="0" cellspacing="0" class="company">
<tr>
<td>Company </td>
<td>
<input name="firm_name" id="firm_name" onkeyup="lookup(this.value);" onblur="fill();" type="text" class="txtbox" value="<?php echo $g_view['company_data']['firm_name'];?>" autocomplete="OFF"/><br /><span id="firm_name_searching"></span><br />
<span class="err_txt"><?php echo $g_view['err']['firm_name'];?></span>
<div class="suggestionsBox" id="suggestions" style="display: none;">
<img src="images/upArrow.png" style="position: relative; top: -18px; left: 30px;" alt="upArrow"  />
<div class="suggestionList" id="autoSuggestionsList"></div>
</div>	
</td>
</tr>

<?php
/**********************************************************************
sng:21/jan/2011
To change company name, we also need to update the work email because verification is done by checking the company name
and the work email address. For ex, if I work for Credit Suisse, my work email will be like @credit-suisse.com
*******/
?>
<tr>
<td>Work Email </td>
<td>
<input name="work_email" type="text" class="txtbox" value="<?php echo $g_view['company_data']['work_email'];?>"/><span class="err_txt"> *</span><br /><span class="err_txt"><?php echo $g_view['err']['work_email'];?></span>
</td>
</tr>
<?php
/************************************************************/
?>

<tr>
<td>Designation </td>
<td>
<select name="designation" id="designation" class="txtbox">
<option value="">Select</option>

<?php

for($j=0;$j<$g_view['designation_count'];$j++){
?>
<option value="<?php echo $g_view['designation_list'][$j]['designation'];?>" <?php if($g_view['company_data']['designation']==$g_view['designation_list'][$j]['designation']){?>selected="selected"<?php }?>><?php echo $g_view['designation_list'][$j]['designation'];?></option>
<?php
}
?>   
</select><br />
<span class="err_txt"><?php echo $g_view['err']['designation'];?></span>	
</td>
</tr>

<tr>
<td>Location </td>
<td>
<select name="location" class="txtbox">
<option value="">Select</option>
<?php
for($i=0;$i<$g_view['country_count'];$i++){
?>
<option value="<?php echo $g_view['country_list'][$i]['name'];?>" <?php if($g_view['company_data']['posting_country']==$g_view['country_list'][$i]['name']){?>selected="selected"<?php }?>><?php echo $g_view['country_list'][$i]['name'];?></option>
<?php
}
?>
</select><br />
<span class="err_txt"><?php echo $g_view['err']['location'];?></span>	
</td>
</tr>

<tr>
<td>Division </td>
<td>
<input name="division" type="text" class="txtbox" value="<?php echo $g_view['company_data']['division'];?>"/><br /><span class="err_txt"><?php echo $g_view['err']['division'];?></span>
</td>
</tr>

<tr>
<td>&nbsp;</td>
<td><input type="submit" name="submit" value="Update" class="btn_auto" /></td>
</tr>
</table>
</form>
</td>
</tr>
</table>

<?php
/********************************************************************
sng:24/jan/2011
If the future_company_name is not blank or future_work_email is not blank, then it means that there is a company / work email change request. In that case, show
the data
***********/
if(($g_view['company_data']['future_company_name']!="")||($g_view['company_data']['future_work_email']!="")){
?>
<p>Pending company / work email change request</p>
<table cellpadding="0" cellspacing="0" class="company" style="width:400px;">
<tr>
<td>Company </td>
<td>
<?php echo $g_view['company_data']['future_company_name'];?>
</td>
</tr>

<tr>
<td>Work Email </td>
<td>
<?php echo $g_view['company_data']['future_work_email'];?>
</td>
</tr>

<tr>
<td>Designation </td>
<td>
<?php echo $g_view['company_data']['future_designation'];?>
</td>
</tr>

<tr>
<td>Location </td>
<td>
<?php echo $g_view['company_data']['future_country'];?>	
</td>
</tr>

<tr>
<td>Division </td>
<td>
<?php echo $g_view['company_data']['future_division'];?>	
</td>
</tr>
</table>
<?php
}
/***************************************************************/
?>
<div style="height:40px;">&nbsp;</div>
<table cellpadding="0" cellspacing="0" style="width:400px;">
<tr>
<td>
<form method="post" action="">
<input type="hidden" name="action" value="change_password" />
<table width="100%" cellpadding="0" cellspacing="0" class="company">
<tr>
<td>
current Password
</td>
<td>
<input type="password" name="curr_password" class="txtbox" /><span class="err_txt"> *</span><br /><span class="err_txt"><?php echo $g_view['err']['curr_password'];?></span>	
</td>
</tr>
<tr>
<td>
New Password
</td>
<td>
<input type="password" name="new_password" class="txtbox" /><span class="err_txt"> *</span><br /><span class="err_txt"><?php echo $g_view['err']['new_password'];?></span>
</td>
</tr>
<tr>
<td>
Retype Password
</td>
<td>
<input type="password" name="re_password" class="txtbox" /><span class="err_txt"> *</span><br /><span class="err_txt"><?php echo $g_view['err']['re_password'];?></span>
</td>
</tr>
<tr>
<td>&nbsp;</td>
<td><input type="submit" name="submit" value="Change" class="btn_auto" />
</table>
</form>
</td>
</tr>
</table>
<div style="height:40px;">&nbsp;</div>
<table cellpadding="0" cellspacing="0" style="width:400px;">
<tr>
<td>
<form method="post" action="" enctype="multipart/form-data">
<input type="hidden" name="action" value="change_profile_2" />
<table width="100%" cellpadding="0" cellspacing="0" class="company">


<tr>
<td>Home Email</td>
<td>
<input type="text" name="home_email" value="<?php echo $g_view['data']['home_email'];?>" class="txtbox" /><span class="err_txt"> *</span><br /><span class="err_txt"><?php echo $g_view['err']['home_email'];?></span>	
</td>
</tr>
<tr>
<td colspan="2">
<?php
if($g_view['data']['profile_img']==""){
	?>
	<img src="images/no_profile_img.jpg" />
	<?php
}else{
	?>
	<img src="uploaded_img/profile/thumbnails/<?php echo $g_view['data']['profile_img'];?>" />
	<?php
}
?>
</td>
</tr>
<?php
//photo is optional
?>
<tr>
<td>Upload photo </td>
<td>
<input type="file" name="profile_img" class="txtbox" /><br /><span class="err_txt"><?php echo $g_view['err']['profile_img'];?></span>	
</td>
</tr>
<tr>
<td>&nbsp;</td>
<td><input type="submit" name="submit" value="Update" class="btn_auto" />
</table>
</form>
</td>
</tr>
</table>
