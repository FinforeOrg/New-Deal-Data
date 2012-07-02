<table width="100%" cellpadding="5" cellspacing="0" border="1" style="border-collapse:collapse;">
<?php
if($g_view['msg']!=""){
?>
<tr>
<td colspan="9"><span class="msg_txt"><?php echo $g_view['msg'];?></span></td>
</tr>
<?php
}
?>
<tr bgcolor="#dec5b3" style="height:20px;">
<td><strong>Member Name</strong></td>
<td><strong>Work Email</strong></td>
<td><strong>Prev Work Email</strong></td>
<td><strong>Company name</strong></td>
<td><strong>Prev Company name</strong></td>
<td><strong>Changed On</strong></td>
</tr>
<?php
if($g_view['data_count']==0){
	?>
	<tr>
	  <td colspan="6">No member data found</td>
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
		<td><?php echo $g_view['data'][$i]['f_name']."&nbsp;".$g_view['data'][$i]['l_name']; ?></td>
		<td><?php echo $g_view['data'][$i]['work_email'];?></td>
		<td><?php echo $g_view['data'][$i]['prev_work_email'];?></td>
		<td><?php echo $g_view['data'][$i]['curr_company_name'];?></td>
		<td><?php echo $g_view['data'][$i]['prev_company_name'];?></td>
		<td><?php echo $g_view['data'][$i]['changed_on'];?></td>
		</tr>
		<?php
	}
	?>
	
	<tr>
	<td colspan="7" style="text-align:right">
	<?php
	if($g_view['start'] > 0){
		$prev_offset = $g_view['start'] - $g_view['num_to_show'];
		?>
		<a href="profile_change_history.php?start=<?php echo $prev_offset;?>">Prev</a>
		<?php
	}
	if($g_view['data_count'] > $g_view['num_to_show']){
		$next_offset = $g_view['start'] + $g_view['num_to_show'];
		?>
		&nbsp;&nbsp;&nbsp;<a href="profile_change_history.php?start=<?php echo $next_offset;?>">Next</a>
		<?php
	}
	?>
	</td>
	</tr>
	<?php
}
?>
</table>