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
<table width="100%" cellpadding="5" cellspacing="0" border="1" style="border-collapse:collapse;">
<?php
if($g_view['msg']!=""){
?>
<tr>
<td colspan="6"><span class="msg_txt"><?php echo $g_view['msg'];?></span></td>
</tr>
<?php
}
?>
<tr>
<td colspan="6">
<form method="post" action="" >
<input name="action" type="hidden" value="add" />
<table width="100%" cellpadding="0" cellspacing="5" border="0" >
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
</select>
<span class="err_txt"> *</span></td>
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
</select>
</td>

<td><input type="submit" name="submit" value="Add" />
</tr>
<tr>
<td colspan="2"><span class="err_txt"><?php echo $g_view['err']['sector'];?></span></td>
<td colspan="2"><span class="err_txt"><?php echo $g_view['err']['industry'];?></span></td>
</tr>
</table>
</form>
</td>
</tr>

<tr bgcolor="#dec5b3" style="height:20px;">

<td><strong>Sector</strong></td>
<td><strong>Industry</strong></td>

<td>&nbsp;</td>
</tr>
<?php
if($g_view['data_count']==0){
	?>
	<tr>
	  <td colspan="6">None found</td>
	</tr>
	<?php
}else{
	for($i=0;$i<$g_view['data_count'];$i++){
		?>
		<tr>
		<td><?php echo $g_view['data'][$i]['sector'];?></td>
		<td><?php echo $g_view['data'][$i]['industry'];?></td>
		
		<td>
		<form method="post" action="">
		<input name="action" type="hidden" value="delete" />
		<input name="value_id" type="hidden" value="<?php echo $g_view['data'][$i]['id'];?>" />
		<input type="submit" name="submit" value="Remove" />
		</form>
		</td>
		</tr>
		<?php
	}
}
?>
</table>