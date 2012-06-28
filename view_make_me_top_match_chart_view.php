<table width="100%" cellpadding="0" cellspacing="0">
<tr>
<td><h1>Make Me Top Result Chart</h1></td>
</tr>
</table>
<?php
if(!$g_view['result_found']){
	return;
}
?>
<br /><br />
<table width="100%" cellpadding="0" cellspacing="5">
<tr>
<td>
<!--the parameters-->
<table width="100%" cellpadding="0" cellspacing="0" class="company">
<tr>
<td colspan="2">The firm is #<?php echo $g_view['result_data']['rank_of_firm'];?> based on the following parameters</td>
</tr>
<tr>
<td>Country</td>
<td><?php echo $g_view['result_data']['country_name'];?></td>
</tr>

<tr>
<td>Sector/Industry</td>
<td><?php echo $g_view['result_data']['sector_name'];?></td>
</tr>

<tr>
<td>Deal Type</td>
<td><?php echo $g_view['result_data']['deal_name'];?></td>
</tr>

<tr>
<td>Size</td>
<td><?php echo $g_view['result_data']['size_name'];?></td>
</tr>

<tr>
<td>Date</td>
<td><?php echo $g_view['result_data']['date_name'];?></td>
</tr>

<tr>
<td>Ranking Criteria</td>
<td>
<?php
if($g_view['result_data']['ranking_criteria']=="num_deals") echo "Total number of tombstones";
if($g_view['result_data']['ranking_criteria']=="total_deal_value") echo "Total tombstone value";
if($g_view['result_data']['ranking_criteria']=="total_adjusted_deal_value") echo "Total adjusted value";
?>
</td>
</tr>
</table>
<!--the parameters-->
</td>
<td style="width:50%;">
<!--the chart-->
<img src="league_table_renderer.php?t=<?php echo time();?>" />
<!--the chart-->
</td>
</tr>
</table>
<br /><br />
<table width="100%" cellpadding="0" cellspacing="0" class="company">
<tr>
<th>rank</th>
<th>Firm</th>
<th>
<?php 
if($g_view['result_data']['ranking_criteria']=="num_deals") echo "#tombstones";else echo "in million $";
?></th>
</tr>
<?php
if($g_view['search_result_firms_count']==0){
	?>
	<tr><td colspan="3">None found</td></tr>
	<?php
}else{
	for($i=0;$i<$g_view['search_result_firms_count'];$i++){
		?>
		<tr>
		<td><?php echo $i+1;?></td>
		<td><?php echo $g_view['search_result_firms'][$i]['firm_name'];?></td>
		<td>
		<?php
		if($g_view['result_data']['ranking_criteria']!="num_deals"){
			echo convert_billion_to_million_for_display_round($g_view['search_result_firms'][$i]['stat_value']);
		}else{
			//number of deals, just show the number
			echo $g_view['search_result_firms'][$i]['stat_value'];
		}
		?>
		</td>
		</tr>
		<?php
	}
}
?>
</table>