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
<tr bgcolor="#dec5b3" style="height:20px;">
<td><strong>Company</strong></td>
<td><strong>Date</strong></td>
<td><strong>Associate</strong></td>
<td><strong>Member</strong></td>
<td><strong>Flagged By</strong></td>
<td><strong>On</strong></td>
</tr>
<?php
if($g_view['data_count']==0){
	?>
	<tr>
	  <td colspan="6">None found</td>
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
		<td><?php echo $g_view['data'][$i]['deal_company_name'];?></td>
		<td><?php echo $g_view['data'][$i]['deal_year'];?></td>
		<td><?php echo $g_view['data'][$i]['associate'];?></td>
		<td><?php echo $g_view['data'][$i]['flagged_f_name'];?> <?php echo $g_view['data'][$i]['flagged_l_name'];?></td>
		<td>
		<?php echo $g_view['data'][$i]['flagger_f_name'];?> <?php echo $g_view['data'][$i]['flagger_l_name'];?>
		</td>
		<td>
		<?php echo $g_view['data'][$i]['date_flagged'];?>
		</td>
		</tr>
		<tr>
		<td>View Deal</td>
		<td>View Team</td>
		<td>
		<form method="post" action="">
		<input type="hidden" name="action" value="remove" />
		<input type="hidden" name="deal_id" value="<?php echo $g_view['data'][$i]['transaction_id'];?>" />
		<input type="hidden" name="partner_id" value="<?php echo $g_view['data'][$i]['partner_id'];?>" />
		<input type="hidden" name="member_id" value="<?php echo $g_view['data'][$i]['flagged_mem_id'];?>" />
		<input type="submit" name="submit" value="Remove member from this deal team" />
		</form>
		</td>
		<td>
		<form method="post" action="">
		<input type="hidden" name="action" value="unflag" />
		<input type="hidden" name="deal_id" value="<?php echo $g_view['data'][$i]['transaction_id'];?>" />
		<input type="hidden" name="partner_id" value="<?php echo $g_view['data'][$i]['partner_id'];?>" />
		<input type="hidden" name="member_id" value="<?php echo $g_view['data'][$i]['flagged_mem_id'];?>" />
		<input type="submit" name="submit" value="Unflag" />
		</form>
		</td>
		<td colspan="2">&nbsp;</td>
		</tr>
		<tr><td colspan="6">&nbsp;</td></tr>
		<?php
	}
	?>
	<tr>
	<td colspan="8" style="text-align:right">
	<?php
	if($g_view['start'] > 0){
		$prev_offset = $g_view['start'] - $g_view['num_to_show'];
		?>
		<a href="deal_team_flagged_members_list.php?start=<?php echo $prev_offset;?>">Prev</a>
		<?php
	}
	if($g_view['data_count'] > $g_view['num_to_show']){
		$next_offset = $g_view['start'] + $g_view['num_to_show'];
		?>
		&nbsp;&nbsp;&nbsp;<a href="deal_team_flagged_members_list?start=<?php echo $next_offset;?>">Next</a>
		<?php
	}
	?>
	</td>
	</tr>
	<?php
}
?>
</table>