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
<table cellpadding="0" cellspacing="5" border="0" >
<tr>
<td>Preset Name</td>
<td><input type="text" name="name" value="" style="width:100px;" /><span class="err_txt"> *</span></td>
<td><input type="submit" name="submit" value="Add" />
</tr>
<tr>
<td colspan="2"><span class="err_txt"><?php echo $g_view['err']['name'];?></span></td>
<td>&nbsp;</td>
</tr>
</table>
</form>
</td>
</tr>

<tr bgcolor="#dec5b3" style="height:20px;">

<td><strong>Preset Name</strong></td>
<td>&nbsp;</td>
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
		<td><?php echo $g_view['data'][$i]['name'];?></td>
		<td><a href="preset_sector_industry_edit.php?preset_id=<?php echo $g_view['data'][$i]['preset_id'];?>">Edit</a>
		<td>
		<a href="preset_sector_industry_detail.php?preset_id=<?php echo $g_view['data'][$i]['preset_id'];?>">View</a>
		</td>
		</tr>
		<?php
	}
}
?>
</table>