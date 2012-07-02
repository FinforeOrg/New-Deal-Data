<script type="text/javascript" src="../js/jquery-1.2.1.pack.js"></script>
<script type="text/javascript">
function sector_changed(){
	var sector_obj = document.getElementById('sector');
	var offset_selected = sector_obj.selectedIndex;
	if(offset_selected != 0){
		var sector_name_selected = sector_obj.options[offset_selected].value;
		//fetch the list of industries for this sector
		$.post("ajax/industry_list_for_sector.php", {sector: ""+sector_name_selected+""}, function(data){
				if(data.length >0) {
					$('#industry').html(data);
				}
		});
	}
}
</script>
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

function fetch_identifier_correction_suggestion(identifier_id){
	var show_in_id = "suggestion_"+identifier_id;
	jQuery('#'+show_in_id).html("fetching...");
	//fire ajax
	jQuery.post('ajax/fetch_suggestion_for_company_identifier.php',{company_id: <?php echo $_POST['company_id'];?>,data_name: identifier_id},function(data){
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
<td>Company Name</td>
<td>
<input name="name" type="text" style="width:200px;" value="<?php echo $g_view['data']['name'];?>" /><span class="err_txt"> *</span><a href="#" onclick="return fetch_correction_suggestion('name');"><img src="has_company_suggestion.php?company_id=<?php echo $_POST['company_id'];?>&data_name=name" /></a><br />
<span class="err_txt"><?php echo $g_view['err']['name'];?></span>
</td>
</tr>
<tr><td colspan="2" id="suggestion_name"></td></tr>

<tr>
<td>Type</td>
<td>
<select name="type">
<option value="company" <?php if(($g_view['data']['type']=="")||($g_view['data']['type']=="company")){?> selected="selected"<?php }?>>Company</option>
<option value="law firm" <?php if($g_view['data']['type']=="law firm"){?> selected="selected"<?php }?>>Law Firm</option>
<option value="bank" <?php if($g_view['data']['type']=="bank"){?> selected="selected"<?php }?>>Bank</option>
</select><span class="err_txt"> *</span><br />
<span class="err_txt"><?php echo $g_view['err']['type'];?></span>
</td>
</tr>

<tr>
<td>Sector</td>
<td>
<select name="sector" id="sector" onchange="return sector_changed();">
<option value="">Select</option>
<?php
for($i=0;$i<$g_view['sector_count'];$i++){
	?>
	<option value="<?php echo $g_view['sector_list'][$i]['sector'];?>" <?php if($g_view['sector_list'][$i]['sector']==$g_view['data']['sector']){?>selected="selected"<?php }?>><?php echo $g_view['sector_list'][$i]['sector'];?></option>
	<?php
}
?>
</select><span class="err_txt"> *</span><a href="#" onclick="return fetch_correction_suggestion('sector');"><img src="has_company_suggestion.php?company_id=<?php echo $_POST['company_id'];?>&data_name=sector" /></a><br />
<span class="err_txt"><?php echo $g_view['err']['sector'];?></span>
</td>
</tr>
<tr><td colspan="2" id="suggestion_sector"></td></tr>

<tr>
<td>Industry</td>
<td>
<select name="industry" id="industry">
<option value="">Select</option>
<?php
for($i=0;$i<$g_view['industry_count'];$i++){
	?>
	<option value="<?php echo $g_view['industry_list'][$i]['industry'];?>" <?php if($g_view['industry_list'][$i]['industry']==$g_view['data']['industry']){?>selected="selected"<?php }?>><?php echo $g_view['industry_list'][$i]['industry'];?></option>
	<?php
}
?>
</select><span class="err_txt"> *</span><a href="#" onclick="return fetch_correction_suggestion('industry');"><img src="has_company_suggestion.php?company_id=<?php echo $_POST['company_id'];?>&data_name=industry" /></a><br />
<span class="err_txt"><?php echo $g_view['err']['industry'];?></span>
</td>
</tr>
<tr><td colspan="2" id="suggestion_industry"></td></tr>

<tr>
<td>Company Headquarter</td>
<td>
<select name="hq_country">
<option value="">Select</option>
<?php
for($i=0;$i<$g_view['country_count'];$i++){
	?>
	<option value="<?php echo $g_view['country_list'][$i]['name'];?>" <?php if($g_view['country_list'][$i]['name']==$g_view['data']['hq_country']){?>selected="selected"<?php }?>><?php echo $g_view['country_list'][$i]['name'];?></option>
	<?php
}
?>
</select><span class="err_txt"> *</span><a href="#" onclick="return fetch_correction_suggestion('hq_country');"><img src="has_company_suggestion.php?company_id=<?php echo $_POST['company_id'];?>&data_name=hq_country" /></a><br />
<span class="err_txt"><?php echo $g_view['err']['hq_country'];?></span>
</td>
</tr>
<tr><td colspan="2" id="suggestion_hq_country"></td></tr>

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
	<img src="../uploaded_img/logo/thumbnails/<?php echo $g_view['data']['logo'];?>" border="0" />
	<?php
}
?>
<br />
<input type="file" name="logo" style="width:200px;" /><br />
<input type="hidden" name="backup_logo" style="width:200px;" value="<?php echo $g_view['data']['logo'];?>"/>
</td>
</tr>

<tr>
<td>Brief description</td>
<td>
<textarea name="brief_desc" style="width:300px; height:100px;"><?php echo $g_view['data']['brief_desc'];?></textarea>
</td>
</tr>

<?php
/************
sng:6/feb/2011
support for private note
***************/
?>
<tr>
<td>Private Admin</td>
<td>
<textarea name="private_note" style="width:300px; height:100px;"><?php echo $g_view['data']['private_note'];?></textarea>
</td>
</tr>

<tr>
<td></td>
<td><input type="submit" name="submit" value="Update" /></td>
</tr>

</table>
</form>
</td>
</tr>


</table>
<?php
/**************************
sng:7/sep/2011
show the company identifiers
************/
?>
<table width="100%" cellpadding="5" cellspacing="0" border="1" style="border-collapse:collapse;">
<tr bgcolor="#dec5b3" style="height:20px;">
<td><strong>Identifier</strong></td>
<td><strong>Value</strong></td>
</tr>
<?php
if(0==$g_view['identifiers_cnt']){
	?>
	<tr><td colspan="3">None</td></tr>
	<?php
}else{
	for($j=0;$j<$g_view['identifiers_cnt'];$j++){
		?>
		<tr>
		<td><?php echo $g_view['identifiers'][$j]['name'];?></td>
		<td>
		<?php
		if($g_view['identifiers'][$j]['value']!=NULL){
			//the company has this identifier in company_identifiers
			//so we show the edit form
			?>
			<form method="post" action="">
			<input type="hidden" name="action" value="edit_identifier" />
			<input type="hidden" name="company_id" value="<?php echo $_POST['company_id'];?>" />
			<input type="hidden" name="identifier_id" value="<?php echo $g_view['identifiers'][$j]['identifier_id'];?>" />
			<input type="text" name="value" value="<?php echo $g_view['identifiers'][$j]['value'];?>" />
			<input type="submit" value="Update" /><a href="#" onclick="return fetch_identifier_correction_suggestion('<?php echo $g_view['identifiers'][$j]['identifier_id'];?>');"><img src="has_company_identifier_suggestion.php?company_id=<?php echo $_POST['company_id'];?>&identifier_id=<?php echo $g_view['identifiers'][$j]['identifier_id'];?>" /></a>
			</form>
			<?php
		}else{
			//this identifier is not present in company_identifiers for this company
			//so we show the add form
			?>
			<form method="post" action="">
			<input type="hidden" name="action" value="add_identifier" />
			<input type="hidden" name="company_id" value="<?php echo $_POST['company_id'];?>" />
			<input type="hidden" name="identifier_id" value="<?php echo $g_view['identifiers'][$j]['identifier_id'];?>" />
			<input type="text" name="value" value="" />
			<input type="submit" value="Add" /><a href="#" onclick="return fetch_identifier_correction_suggestion('<?php echo $g_view['identifiers'][$j]['identifier_id'];?>');"><img src="has_company_identifier_suggestion.php?company_id=<?php echo $_POST['company_id'];?>&identifier_id=<?php echo $g_view['identifiers'][$j]['identifier_id'];?>" /></a>
			</form>
			<?php
		}
		?>
		<div id="suggestion_<?php echo $g_view['identifiers'][$j]['identifier_id'];?>"></div>
		</td>
		<?php
		/*************************
		sng:9/dec/2011
		We do not show the delete identifier.
		**********************/
		?>
		</tr>
		<?php
	}
}
?>
</table>