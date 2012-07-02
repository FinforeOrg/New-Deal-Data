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
<td>Type</td>
<td><input type="text" name="type" value="" style="width:100px;" /><span class="err_txt"> *</span></td>
<td>Sub type</td>
<td><input type="text" name="subtype1" value="" style="width:100px;" /><span class="err_txt"> *</span></td>
<td>Sub sub type</td>
<td><input type="text" name="subtype2" value="" style="width:100px;" /><span class="err_txt"> *</span></td>
<td><input type="submit" name="submit" value="Add" />
</tr>
<tr>
<td colspan="2"><span class="err_txt"><?php echo $g_view['err']['type'];?></span></td>
<td colspan="2"><span class="err_txt"><?php echo $g_view['err']['subtype1'];?></span></td>
<td colspan="2"><span class="err_txt"><?php echo $g_view['err']['subtype2'];?></span></td>
</tr>
</table>
</form>
</td>
</tr>

<tr bgcolor="#dec5b3" style="height:20px;">

<td><strong>Type</strong></td>
<td><strong>Subtype</strong></td>
<td><strong>Sub sub type</strong></td>
</tr>
<?php
if($g_view['data_count']==0){
	?>
	<tr>
	  <td colspan="6">No category data found</td>
	</tr>
	<?php
}else{
	for($i=0;$i<$g_view['data_count'];$i++){
		?>
		<tr>
		<td><?php echo $g_view['data'][$i]['type'];?></td>
		<td><?php echo $g_view['data'][$i]['subtype1'];?></td>
		<td><?php echo $g_view['data'][$i]['subtype2'];?></td>
		</tr>
		<?php
	}
}
?>
</table>