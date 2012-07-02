<script>
function fetch_correction_suggestion(field){
	var show_in_id = "suggestion_"+field;
	jQuery('#'+show_in_id).html("fetching...");
	//fire ajax
	jQuery.post('ajax/fetch_suggestion_for_company_field.php',{company_id: <?php echo $_POST['company_id'];?>,data_name: field},function(data){
		jQuery('#'+show_in_id).html(data);
	});
	
	return false;
}
</script>
<table width="100%" cellpadding="5" cellspacing="0" border="0">
<tr>
<td><span class="msg_txt"><?php echo $g_view['msg'];?></span></td>
</tr>
<tr>
<td>
<form method="post" action="" enctype="multipart/form-data">
<input type="hidden" name="action" value="edit" />
<input type="hidden" name="company_id" value="<?php echo $_POST['company_id'];?>" />
<table width="100%" cellpadding="5" cellspacing="0" border="0">
<tr>
<td>Name</td>
<td>
<input name="name" type="text" style="width:200px;" value="<?php echo $g_view['data']['name'];?>" /><span class="err_txt"> *</span><a href="#" onclick="return fetch_correction_suggestion('name');"><img src="has_company_suggestion.php?company_id=<?php echo $_POST['company_id'];?>&data_name=name" /></a><br />
<span class="err_txt"><?php echo $g_view['err']['name'];?></span>
</td>
</tr>
<tr><td colspan="2" id="suggestion_name"></td></tr>
<?php
/***
sng:9/jul/2010
although there is a code to generate abbreviated name for a bank/law firm, sometime admin may specify the exact code
*********/
?>
<tr>
<td>Abbreviated Name</td>
<td>
<input name="short_name" type="text" style="width:200px;" value="<?php echo $g_view['data']['short_name'];?>" /><a href="#" onclick="return fetch_correction_suggestion('short_name');"><img src="has_company_suggestion.php?company_id=<?php echo $_POST['company_id'];?>&data_name=short_name" /></a><br />
(Used in chart in place of the actual name.)
</td>
</tr>
<tr><td colspan="2" id="suggestion_short_name"></td></tr>

<tr>
<td>Type</td>
<td>
<select name="type">
<option value="bank" <?php if(($g_view['data']['type']=="")||($g_view['data']['type']=="bank")){?> selected="selected"<?php }?>>Bank</option>
<option value="law firm" <?php if($g_view['data']['type']=="law firm"){?> selected="selected"<?php }?>>Law Firm</option>

</select><span class="err_txt"> *</span><br />
<span class="err_txt"><?php echo $g_view['err']['type'];?></span>
</td>
</tr>

<tr>
<td>Company Logo</td>
<td>
<?php
if($g_view['data']['logo']==""){
	?>
	None uploaded
	<?php
}else{
	?>
	<img src="<?php echo LOGO_IMG_URL;?>/<?php echo $g_view['data']['logo'];?>" border="0" />
	<?php
}
?>
<br />
<input type="file" name="logo" style="width:200px;" /><br />
<input type="hidden" name="backup_logo" style="width:200px;" value="<?php echo $g_view['data']['logo'];?>"/>
</td>
</tr>

<?php
/*************
sng:6/feb/2011
support for private note
*********/
?>
<tr>
<td>Private Admin </td>
<td>
<textarea name="private_note" style="width:300px; height:100px;"><?php echo $g_view['data']['private_note'];?></textarea>
</td>
</tr>

<?php
/***
sng:4/jun/2010
support for additional attribute is_top_firm

sng:22/july/2010
This is not needed now as firms are not marked as top firm and shown. They are now categorised.
We pass the value as hidden field so as not to break the code
***/
?>
<input type="hidden" name="is_top_firm" value="<?php echo $g_view['data']['is_top_firm'];?>" />
<!--
<tr>
<td>Top Firm</td>
<td>
<input type="radio" name="is_top_firm" value="Y" <?php if($g_view['data']['is_top_firm']=='Y'){?>checked="checked" <?php }?> />&nbsp;Yes&nbsp;&nbsp;&nbsp;<input type="radio" name="is_top_firm" value="N" <?php if($g_view['data']['is_top_firm']=='N'){?>checked="checked" <?php }?> />&nbsp;No
</td>
</tr>
-->
<tr>
<td></td>
<td><input type="submit" name="submit" value="Update" /></td>
</tr>

</table>
</form>
</td>
</tr>
</table>