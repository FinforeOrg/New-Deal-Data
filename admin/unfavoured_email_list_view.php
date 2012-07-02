<table width="100%" cellpadding="5" cellspacing="0" border="1" style="border-collapse:collapse;">
<tr>
<td colspan="5"><span class="msg_txt"><?php echo $g_view['msg'];?></span></td>
</tr>
<tr>
<td colspan="5">
<form method="post" action="">
<input type="hidden" name="action" value="add"/>
<table  cellpadding="5" cellspacing="0" border="0">
<tr>
<td>Email suffix</td>
<td>
<input name="email_suffix" type="text" style="width:200px;" value="<?php echo $g_view['input']['email_suffix'];?>" /><span class="err_txt"> *</span><br />(Ex: @gmail.com)<br />
<span class="err_txt"><?php echo $g_view['err']['email_suffix'];?></span>
</td>
<td><input type="submit" name="submit" value="Add" /></td>
</tr>



</table>
</form>
</td>
</tr>
<tr bgcolor="#dec5b3" style="height:20px;">
<td><strong>Email suffix</strong></td>
<td colspan="2"><strong>Action</strong></td>
</tr>
<?php
if($g_view['data_count']==0){
	?>
	<tr>
	  <td colspan="5">None found</td>
	</tr>
	<?php
}else{
	if($g_view['data_count'] > $g_view['num_to_show']){
		$total = $g_view['num_to_show'];
	}else{
		$total = $g_view['data_count'];
	}
	for($i=0;$i<$total;$i++){
		?>
		<tr>
		
		<td><?php echo $g_view['data'][$i]['email_suffix']; ?></td>
		
		
		<td>
		<form method="post" action="">
		<input type="hidden" name="action" value="delete" />
		<input type="hidden" name="id" value="<?php echo $g_view['data'][$i]['id'];?>" />
		<input type="submit" value="Delete" />
		</form>
		</td>
		</tr>
		<?php
	}
	?>
	
	<tr>
	<td colspan="5" style="text-align:right">
	<?php
	if($g_view['start'] > 0){
		$prev_offset = $g_view['start'] - $g_view['num_to_show'];
		?>
		<a href="unfavoured_email_list.php?start=<?php echo $prev_offset;?>">Prev</a>
		<?php
	}
	if($g_view['data_count'] > $g_view['num_to_show']){
		$next_offset = $g_view['start'] + $g_view['num_to_show'];
		?>
		&nbsp;&nbsp;&nbsp;<a href="unfavoured_email_list.php?start=<?php echo $next_offset;?>">Next</a>
		<?php
	}
	?>
	</td>
	</tr>
	<?php
}
?>
</table>