<table width="100%" cellpadding="0" cellspacing="0" class="company">
<tr>
<th>Date</th>
<th>Client</th>
<th>Deal Type</th>
<th>Size ($m)</th>
<th>Firm</th>
<th>Designation</th>
<th>&nbsp;</th>
</tr>
<?php
if($g_view['deal_count'] == 0){
?>
<tr><td colspan="7">None yet</td></tr>
<?php
}else{
	//we fetched one extra
	if($g_view['deal_count'] > $g_view['num_to_show']){
		$total = $g_view['num_to_show'];
	}else{
		$total = $g_view['deal_count'];
	}
	for($i=0;$i<$total;$i++){
		?>
		<tr>
		<td><?php echo date("M, Y",strtotime($g_view['deal_data'][$i]['date_of_deal']));?></td>
		<?php
		/*********************
		sng:19/sep/2012
		We now have participants
		******************/
		?>
		<td><?php echo Util::deal_participants_to_csv_with_links($g_view['deal_data'][$i]['participants']);?></td>
		<?php
		/**************
		sng:19/sep/2012
		We use our utility function here
		***********/
		?>
		<td><?php show_deal_type_for_listing($g_view['deal_data'][$i]['deal_cat_name'],$g_view['deal_data'][$i]['deal_subcat1_name'],$g_view['deal_data'][$i]['deal_subcat2_name']);?></td>
		<?php
		/*************
		sng:19/sep/2012
		We use utility function here since we now have deal range id, that is, the deal can have range instead of definite value
		*************/
		?>
		<td><?php echo convert_deal_value_for_display_round($g_view['deal_data'][$i]['value_in_billion'],$g_view['deal_data'][$i]['value_range_id'],$g_view['deal_data'][$i]['fuzzy_value']);?></td>
		<td><?php echo $g_view['deal_data'][$i]['firm_name'];?></td>
		<td><?php echo $g_view['deal_data'][$i]['designation'];?></td>
		<td>
		<form method="get" action="deal_detail.php">
		<input type="hidden" name="deal_id" value="<?php echo $g_view['deal_data'][$i]['deal_id'];?>" />
		<input name="submit" type="submit" class="btn_auto" id="button" value="Detail" />
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
		$prev_offset = $g_view['start_offset']-$g_view['num_to_show'];
		?>
		<a class="link_as_button" href="edit_profile_my_deals.php?start=<?php echo $prev_offset;?>">Prev</a>
		<?php
	}
	if($g_view['deal_count'] > $g_view['num_to_show']){
		$next_offset = $g_view['start_offset']+$g_view['num_to_show'];
		?>
		&nbsp;&nbsp;&nbsp;<a class="link_as_button" href="edit_profile_my_deals.php?start=<?php echo $next_offset;?>">Next</a>
		<?php
	}
	?>
	</td>
	</tr>
	<?php
}
?>
</table>