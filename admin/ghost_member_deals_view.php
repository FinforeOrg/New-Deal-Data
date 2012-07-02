<table width="100%" cellpadding="5" cellspacing="0" border="1" style="border-collapse:collapse;">
<?php
if($g_view['msg']!=""){
?>
<tr>
<td colspan="7"><span class="msg_txt"><?php echo $g_view['msg'];?></span></td>
</tr>
<?php
}
?>
<tr bgcolor="#dec5b3" style="height:20px;">
<td><strong>Date</strong></td>
<td><strong>Company</strong></td>
<td><strong>Deal Type</strong></td>
<td><strong>Size ($m)</strong></td>
<td><strong>Firm</strong></td>
<td><strong>Designation</strong></td>
<td>&nbsp;</td>
</tr>
<?php
if($g_view['deal_count']==0){
	?>
	<tr>
	  <td colspan="7">None found</td>
	</tr>
	<?php
}else{
	if($g_view['deal_count'] > $g_view['num_to_show']){
		$total = $g_view['num_to_show'];
	}else{
		$total = $g_view['deal_count'];
	}
	for($i=0;$i<$total;$i++){
		?>
		<tr>
		<td><?php echo date("M, Y",strtotime($g_view['deal_data'][$i]['date_of_deal']));?></td>
		<td><?php echo $g_view['deal_data'][$i]['deal_company_name'];?></td>
		<td><?php echo $g_view['deal_data'][$i]['deal_cat_name'];?></td>
		<td><?php echo convert_billion_to_million_for_display($g_view['deal_data'][$i]['value_in_billion']);?></td>
		<td><?php echo $g_view['deal_data'][$i]['firm_name'];?></td>
		<td><?php echo $g_view['deal_data'][$i]['designation'];?></td>
		<td>
		<form method="POST" action="ghost_member_deals.php?mem_id=<?php echo $g_view['member_id'];?>&start=<?php echo $g_view['start_offset'];?>">
		<input type="hidden" name="action" value="remove_from_deal" />
		<input type="hidden" name="deal_id" value="<?php echo $g_view['deal_data'][$i]['deal_id'];?>" />
		<input type="hidden" name="partner_id" value="<?php echo $g_view['deal_data'][$i]['partner_id'];?>" />
		<input name="submit" type="submit" class="btn" id="button" value="Remove from deal" />
		</form>
		</td>
		</tr>
		
		<?php
	}
	?>
	<tr>
	<td colspan="7" style="text-align:right">
	<?php
	if($g_view['start_offset'] > 0){
		$prev_offset = $g_view['start_offset'] - $g_view['num_to_show'];
		?>
		<a href="ghost_member_deals.php?mem_id=<?php echo $g_view['member_id'];?>&start=<?php echo $prev_offset;?>">Prev</a>
		<?php
	}
	if($g_view['deal_count'] > $g_view['num_to_show']){
		$next_offset = $g_view['start_offset'] + $g_view['num_to_show'];
		?>
		&nbsp;&nbsp;&nbsp;<a href="ghost_member_deals.php?mem_id=<?php echo $g_view['member_id'];?>&start=<?php echo $next_offset;?>">Next</a>
		<?php
	}
	?>
	</td>
	</tr>
	<?php
}
?>
</table>