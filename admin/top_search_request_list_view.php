<table width="100%" cellpadding="5" cellspacing="0" border="1" style="border-collapse:collapse;">
<tr>
<td colspan="9"><span class="msg_txt"><?php echo $g_view['msg'];?></span></td>
</tr>
<tr bgcolor="#dec5b3" style="height:20px;">
<td><strong>Country</strong></td>
<td><strong>Industry</strong></td>
<td><strong>Deal</strong></td>
<td><strong>Date Submitted</strong></td>
<td><strong>Started On</strong></td>
<td><strong>Status</strong></td>
<td><strong>Progress</strong></td>
<td><strong>Last activity on</strong></td>
<td><strong>Time elapsed</strong></td>
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
		
		<td><?php echo $g_view['data'][$i]['country_name']; ?></td>
		<td><?php echo $g_view['data'][$i]['industry_name']; ?></td>
		<td><?php echo $g_view['data'][$i]['deal_name']; ?></td>
		<td><?php echo $g_view['data'][$i]['submitted_on']; ?></td>
		<td><?php echo $g_view['data'][$i]['started_on']; ?></td>
		<td><?php echo $g_view['data'][$i]['status']; ?></td>
		<td><?php echo $g_view['data'][$i]['dbg_status']; ?></td>
		<td><?php echo $g_view['data'][$i]['dbg_last_processing_time']; ?></td>
		<td>
		<?php
		/****
		sng:28/sep/2010
		We show how much time has been taken till now, provided, last processing time has value
		*******/
		if($g_view['data'][$i]['dbg_last_processing_time']!="0000-00-00 00:00:00"){
			echo $g_view['data'][$i]['time_elapsed'];
		}
		?>
		</td>
		</tr>
		<?php
	}
	?>
	
	<tr>
	<td colspan="9" style="text-align:right">
	<?php
	if($g_view['start'] > 0){
		$prev_offset = $g_view['start'] - $g_view['num_to_show'];
		?>
		<a href="top_search_request_list.php?status=<?php echo $g_view['status'];?>&start=<?php echo $prev_offset;?>">Prev</a>
		<?php
	}
	if($g_view['data_count'] > $g_view['num_to_show']){
		$next_offset = $g_view['start'] + $g_view['num_to_show'];
		?>
		&nbsp;&nbsp;&nbsp;<a href="top_search_request_list.php?status=<?php echo $g_view['status'];?>&start=<?php echo $next_offset;?>">Next</a>
		<?php
	}
	?>
	</td>
	</tr>
	<?php
}
?>
</table>