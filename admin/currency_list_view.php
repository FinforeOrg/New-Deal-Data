<table width="100%" cellpadding="5" cellspacing="0" border="0">
<tr>
<td><span class="msg_txt"><?php echo $g_view['msg'];?></span></td>
</tr>
<tr>
<td>
<form method="post" action="">
<input type="hidden" name="action" value="add" />
<table width="100%" cellpadding="5" cellspacing="0" border="0">
<tr>
<td>Currency Code</td>
<td><input name="code" type="text" style="width:200px;" value="<?php echo $g_view['input']['code'];?>" /><span class="err_txt"> *</span><br />
<span class="err_txt"><?php echo $g_view['err']['code'];?></span></td>
</tr>

<tr>
<td>Currency Name</td>
<td><input name="name" type="text" style="width:200px;" value="<?php echo $g_view['input']['name'];?>" /><span class="err_txt"> *</span><br />
<span class="err_txt"><?php echo $g_view['err']['name'];?></span></td>
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

<table width="100%" cellpadding="0" cellspacing="0" border="1" style="border-collapse:collapse;">

<?php
if($g_view['data_count']==0){
	?>
	<tr>
	  <td colspan="6">No data found</td>
	</tr>
	<?php
}else{
	?>
	<tr>
	<td>
		<table width="100%" cellpadding="10" cellspacing="0">
		<tr bgcolor="#dec5b3" style="height:20px;">
		<td style="width:100px;">Code</td>
		<td width="300px">Name</td>
		<td></td>
		</tr>
		</table>
	</td>
	</tr>
	<?php
	for($i=0;$i<$g_view['data_count'];$i++){
		?>
		<tr>
		<td>
		<!--record-->
		<form method="post" action="">
		<input type="hidden" name="myaction" value="update" />
		<input type="hidden" name="id" value="<?php echo $g_view['data'][$i]['id'];?>" />
		<table width="100%" cellpadding="10" cellspacing="0">
		<tr>
		<td style="width:100px;">
		<input type="text" name="code" value="<?php echo $g_view['data'][$i]['code'];?>" style="width:200px;" />
		</td>
		<td width="300px">
		<input name="name" value="<?php echo $g_view['data'][$i]['name'];?>" style="width:200px;" />
		</td>
		<td><input type="submit" name="submit" value="Update" />
		</tr>
		</table>
		</form>
		<!--record-->
		</td>
		</tr>
		<?php
	}
}
?>
</table>