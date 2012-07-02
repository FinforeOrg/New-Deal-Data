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
<td><strong>Comapny</strong></td>
<td><strong>Date</strong></td>
<td><strong>Value</strong></td>
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
		<td><?php echo $g_view['data'][$i]['company_name'];?></td>
		<td><?php echo $g_view['data'][$i]['date_of_deal'];?></td>
		<td><?php echo $g_view['data'][$i]['value_in_billion'];?></td>
		<td><?php echo $g_view['data'][$i]['deal_cat_name'];?> : <?php echo $g_view['data'][$i]['deal_subcat1_name'];?> : <?php echo $g_view['data'][$i]['deal_subcat2_name'];?></td>
		
		<td>
		<form method="post" action="deal_edit.php">
		<input type="hidden" name="deal_id" value="<?php echo $g_view['data'][$i]['id'];?>" />
		<input type="submit" value="Edit" />
		</form>
		</td>
		</tr>
		<tr>
		<td colspan="5">
		<?php echo $g_view['data'][$i]['note'];?>
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
		<a href="misc_list_deal_with_url_in_note.php?start=<?php echo $prev_offset;?>">Prev</a>
		<?php
	}
	if($g_view['data_count'] > $g_view['num_to_show']){
		$next_offset = $g_view['start'] + $g_view['num_to_show'];
		?>
		&nbsp;&nbsp;&nbsp;<a href="misc_list_deal_with_url_in_note.php?start=<?php echo $next_offset;?>">Next</a>
		<?php
	}
	?>
	</td>
	</tr>
	<?php
}
?>
</table>