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
<table width="100%" cellpadding="5" cellspacing="0" border="0">
<tr>
<td><span class="msg_txt"><?php echo $g_view['msg'];?></span></td>
</tr>
<tr>
<td>
<form method="post" action="" enctype="multipart/form-data">
<input type="hidden" name="action" value="add"/>
<table width="100%" cellpadding="5" cellspacing="0" border="0">
<tr>
<td>Company Name</td>
<td>
<input name="name" type="text" style="width:200px;" value="<?php echo $g_view['input']['name'];?>" /><span class="err_txt"> *</span><br />
<span class="err_txt"><?php echo $g_view['err']['name'];?></span>
</td>
</tr>

<tr>
<td>Type</td>
<td>
<select name="type">
<option value="company" <?php if(($g_view['input']['type']=="")||($g_view['input']['type']=="company")){?> selected="selected"<?php }?>>Company</option>
<option value="law firm" <?php if($g_view['input']['type']=="law firm"){?> selected="selected"<?php }?>>Law Firm</option>
<option value="bank" <?php if($g_view['input']['type']=="bank"){?> selected="selected"<?php }?>>Bank</option>
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
	<option value="<?php echo $g_view['sector_list'][$i]['sector'];?>" <?php if($g_view['input']['sector']==$g_view['sector_list'][$i]['sector']){?>selected="selected"<?php }?>><?php echo $g_view['sector_list'][$i]['sector'];?></option>
	<?php
}
?>
</select><span class="err_txt"> *</span><br />
<span class="err_txt"><?php echo $g_view['err']['sector'];?></span>
</td>
</tr>

<tr>
<td>Industry</td>
<td>
<select name="industry" id="industry">
<option value="">Select</option>
<?php
for($i=0;$i<$g_view['industry_count'];$i++){
	?>
	<option value="<?php echo $g_view['industry_list'][$i]['industry'];?>" <?php if($g_view['input']['industry']==$g_view['industry_list'][$i]['industry']){?>selected="selected"<?php }?>><?php echo $g_view['industry_list'][$i]['industry'];?></option>
	<?php
}
?>
</select><span class="err_txt"> *</span><br />
<span class="err_txt"><?php echo $g_view['err']['industry'];?></span>
</td>
</tr>

<tr>
<td>Company Headquarter</td>
<td>
<select name="hq_country">
<option value="">Select</option>
<?php
for($i=0;$i<$g_view['country_count'];$i++){
	?>
	<option value="<?php echo $g_view['country_list'][$i]['name'];?>" <?php if($g_view['input']['hq_country']==$g_view['country_list'][$i]['name']){?>selected="selected"<?php }?>><?php echo $g_view['country_list'][$i]['name'];?></option>
	<?php
}
?>
</select><span class="err_txt"> *</span><br />
<span class="err_txt"><?php echo $g_view['err']['hq_country'];?></span>
</td>
</tr>

<tr>
<td>Company Logo</td>
<td>
<input type="file" name="logo" style="width:200px;" />
</td>
</tr>


<tr>
<td>Brief description</td>
<td>
<textarea name="brief_desc" style="width:300px; height:100px;"><?php echo $g_view['input']['brief_desc'];?></textarea>
</td>
</tr>

<tr>
<td></td>
<td><input type="submit" name="submit" value="Add" /></td>
</tr>

</table>
</form>
</td>
</tr>
</table>