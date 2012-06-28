<table width="100%">
<tr>
<td>Submitted Jobs</td>
</tr>
</table>
<table width="100%" cellpadding="0" cellspacing="0" class="company">
<tr>
<th>Country</th>
<th>Industry</th>
<th>Deal</th>
<th>Date submitted</th>
<th>Type</th>
<th>Status</th>
<th></th>
<th>started on </th>
<th>last processing time </th>
<th>completion</th>
<th>finished on </th>
</tr>
<?php
if($g_view['request_count']==0){
?>
<tr><td colspan="7">None found</td></tr>
<?php
}else{
	for($i=0;$i<$g_view['request_count'];$i++){
		?>
		<tr>
		<td><?php echo $g_view['request_data'][$i]['country_name'];?></td>
		<td><?php echo $g_view['request_data'][$i]['industry_name'];?></td>
		<td><?php echo $g_view['request_data'][$i]['deal_name'];?></td>
		<td><?php echo $g_view['request_data'][$i]['submitted_on'];?></td>
		<td>
		<?php
		if($g_view['request_data'][$i]['extended_search']=='y') echo "Full Search";
		if($g_view['request_data'][$i]['extended_search']=='n') echo "Fast Search";
		/***
		sng:28/sep/2010
		Now that user can choose top 5 or top 3, we show that also
		***/
		echo " [top ".$g_view['request_data'][$i]['rank_requested']."]";
		?>
		</td>
		<td><?php echo $g_view['request_data'][$i]['status'];?></td>
		<td>
		<?php
		echo $g_view['request_data'][$i]['is_scheduled'];
		?>
		</td>
		<td><?php echo $g_view['request_data'][$i]['started_on'];?></td>
		<td><?php echo $g_view['request_data'][$i]['dbg_last_processing_time'];?></td>
		<td><?php echo $g_view['request_data'][$i]['dbg_status'];?></td>
		<td><?php echo $g_view['request_data'][$i]['finished_on'];?></td>
		</tr>
		<?php
	}
}
?>
</table>