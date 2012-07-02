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
<td><input type="text" name="sector" value="" style="width:100px;" /><span class="err_txt"> *</span></td>
<td>Industry</td>
<td><input type="text" name="industry" value="" style="width:100px;" /><span class="err_txt"> *</span></td>
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
<td></td>
</tr>
<?php
if($g_view['data_count']==0){
	?>
	<tr>
	  <td colspan="3">No sector/industry data found</td>
	</tr>
	<?php
}else{
	for($i=0;$i<$g_view['data_count'];$i++){
		?>
		<tr>
		<td><?php echo $g_view['data'][$i]['sector'];?></td>
		<td><?php echo $g_view['data'][$i]['industry'];?></td>
		<td>
		<form method="POST" action="">
		<input type="hidden" name="myaction" value="del" />
		<input type="hidden" name="id" value="<?php echo $g_view['data'][$i]['id'];?>" />
		<input type="submit" value="Delete" />
		</form>
		</td>
		</tr>
		<?php
	}
}
?>
</table>