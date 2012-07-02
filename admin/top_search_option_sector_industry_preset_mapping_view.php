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
<td>Sector Industry Preset</td>
<td>
<select name="preset_id">
<option value="">Select</option>
<?php
for($i=0;$i<$g_view['preset_data_count'];$i++){
	?>
	<option value="<?php echo $g_view['preset_data'][$i]['preset_id'];?>" <?php if($_POST['preset_id']==$g_view['preset_data'][$i]['preset_id']){?>selected="selected"<?php }?>><?php echo $g_view['preset_data'][$i]['name'];?></option>
	<?php
}
?>
</select><span class="err_txt"> *</span>
</td>
<td>Is primary</td>
<td>
<input type="radio" name="is_primary" value="Y" checked="checked" />&nbsp;Yes&nbsp;&nbsp;&nbsp;<input type="radio" name="is_primary" value="N" />&nbsp;No
</td>
<td><input type="submit" name="submit" value="Add" />
</tr>
<tr>
<td colspan="2"><span class="err_txt"><?php echo $g_view['err']['preset_id'];?></span></td>
</tr>
</table>
</form>
</td>
</tr>

<tr bgcolor="#dec5b3" style="height:20px;">

<td><strong>Sector Industry Preset</strong></td>
<td><strong>Primary</strong></td>
<td>&nbsp;</td>
</tr>
<?php
if($g_view['mapping_data_count']==0){
	?>
	<tr>
	  <td colspan="6">None found</td>
	</tr>
	<?php
}else{
	for($i=0;$i<$g_view['mapping_data_count'];$i++){
		?>
		<tr>
		<td><?php echo $g_view['mapping_data'][$i]['name'];?></td>
		<td><?php echo $g_view['mapping_data'][$i]['is_primary'];?></td>
		
		<td>
		<form method="post" action="">
		<input name="action" type="hidden" value="delete" />
		<input name="mapping_id" type="hidden" value="<?php echo $g_view['mapping_data'][$i]['id'];?>" />
		<input type="submit" name="submit" value="Remove" />
		</form>
		</td>
		</tr>
		<?php
	}
}
?>
</table>