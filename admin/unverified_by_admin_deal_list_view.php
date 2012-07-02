<table width="100%" cellpadding="5" cellspacing="0" border="1" style="border-collapse:collapse;">
<?php
if($g_view['msg']!=""){
?>
<tr>
<td colspan="8"><span class="msg_txt"><?php echo $g_view['msg'];?></span></td>
</tr>
<?php
}
?>
<?php
/******************
sng:30/aug/2011
I think a description here will make things easier
********************/
if($g_view['data_count']!=0){
?>
<p>

</p>
<?
}
?>
<tr bgcolor="#dec5b3" style="height:20px;">
<td><strong>Deal ID</strong></td>
<td><strong>Company</strong></td>
<td><strong>Date</strong></td>
<td><strong>Value in $m</strong></td>
<td><strong>Type</strong></td>
<td><strong>By</strong></td>
<td colspan="2">&nbsp;</td>
</tr>
<?php
if($g_view['data_count']==0){
	?>
	<tr>
	  <td colspan="8">None found</td>
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
		<td><?php echo $g_view['data'][$i]['id'];?></td>
		<td><?php echo Util::deal_participants_to_csv($g_view['data'][$i]['participants']);?></td>
		<td><?php echo $g_view['data'][$i]['date_of_deal'];?></td>
		<td><?php echo convert_deal_value_for_display_round($g_view['data'][$i]['value_in_billion'],$g_view['data'][$i]['value_range_id'],$g_view['data'][$i]['fuzzy_value']);?></td>
		<td><?php echo $g_view['data'][$i]['deal_cat_name'];?> <?php echo $g_view['data'][$i]['deal_subcat1_name'];?> <?php echo $g_view['data'][$i]['deal_subcat2_name'];?></td>
		
		<td>
		<?php echo $g_view['data'][$i]['f_name'];?> <?php echo $g_view['data'][$i]['l_name'];?> [<?php echo $g_view['data'][$i]['designation'];?>] <?php echo $g_view['data'][$i]['work_company'];?><br /><br />on <?php echo date("Y-m-d",strtotime($g_view['data'][$i]['added_on']));?>
		</td>
		<td>
		<form method="post" action="deal_edit.php">
		<input type="hidden" name="deal_id" value="<?php echo $g_view['data'][$i]['id'];?>" />
		<input type="submit" value="Edit" />
		</form>
		</td>
		<td>
		<form method="post" action="unverified_by_admin_deal_list.php?start=<?php echo $g_view['start'];?>">
		<input name="my_action" type="hidden" value="delete" />
		<input type="hidden" name="deal_id" value="<?php echo $g_view['data'][$i]['id'];?>" />
		<input type="submit" value="Delete" />
		</form>
		</td>
		</tr>
		
		
		
		<?php
	}
	?>
	<tr>
	<td colspan="8" style="text-align:right">
	<?php
	if($g_view['start'] > 0){
		$prev_offset = $g_view['start'] - $g_view['num_to_show'];
		?>
		<a href="unverified_by_admin_deal_list.php?start=<?php echo $prev_offset;?>">Prev</a>
		<?php
	}
	if($g_view['data_count'] > $g_view['num_to_show']){
		$next_offset = $g_view['start'] + $g_view['num_to_show'];
		?>
		&nbsp;&nbsp;&nbsp;<a href="unverified_by_admin_deal_list.php?start=<?php echo $next_offset;?>">Next</a>
		<?php
	}
	?>
	</td>
	</tr>
	<?php
}
?>
</table>