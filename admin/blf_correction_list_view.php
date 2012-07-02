<table width="100%" cellpadding="5" cellspacing="0" border="1" style="border-collapse:collapse;">
<?php
if($g_view['msg']!=""){
?>
<tr>
<td colspan="5"><span class="msg_txt"><?php echo $g_view['msg'];?></span></td>
</tr>
<?php
}
?>
<tr bgcolor="#dec5b3" style="height:20px;">
<td><strong>Firm</strong></td>
<td><strong>Type</strong></td>
<td>&nbsp;</td>
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
		<td><?php echo $g_view['data'][$i]['name'];?></td>
		<td><?php echo $g_view['data'][$i]['type'];?></td>
		
		
		<td>
		<form method="post" action="blf_edit.php">
		<input type="hidden" name="company_id" value="<?php echo $g_view['data'][$i]['company_id'];?>" />
		<input type="submit" value="Edit" />
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
		<a href="blf_correction_list.php?start=<?php echo $prev_offset;?>">Prev</a>
		<?php
	}
	if($g_view['data_count'] > $g_view['num_to_show']){
		$next_offset = $g_view['start'] + $g_view['num_to_show'];
		?>
		&nbsp;&nbsp;&nbsp;<a href="blf_correction_list.php?start=<?php echo $next_offset;?>">Next</a>
		<?php
	}
	?>
	</td>
	</tr>
	<?php
}
?>
</table>