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
<td><strong>New Work Email</strong></td>
<td><strong>Company name</strong></td>
<td><strong>New Company name</strong></td>
<td><strong>Requested On</strong></td>
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
		<td><?php echo $g_view['data'][$i]['f_name']."&nbsp;".$g_view['data'][$i]['l_name']; ?></td>
		<td><?php echo $g_view['data'][$i]['work_email'];?></td>
		<td><?php echo $g_view['data'][$i]['future_work_email'];?></td>
		<td><?php echo $g_view['data'][$i]['curr_company_name'];?></td>
		<td><?php echo $g_view['data'][$i]['future_company_name'];?></td>
		<td><?php echo $g_view['data'][$i]['future_requested_on'];?></td>
		</tr>
		<tr>
		<td colspan="6" align="right">
		<table cellpadding="5" cellspacing="0">
		<tr>
		
		<td>
		<form action="" method="post">
		<input type="hidden" name="myaction" value="resend" />
		<input type="hidden" name="mem_id" value="<?php echo $g_view['data'][$i]['mem_id'];?>" />
		<input type="submit" name="submit" value="Resend Email" />
		</form>
		</td>
		
		<td>
		<form action="" method="post">
		<input type="hidden" name="myaction" value="update" />
		<input type="hidden" name="mem_id" value="<?php echo $g_view['data'][$i]['mem_id'];?>" />
		<input type="submit" name="submit" value="Update" />
		</form>
		</td>
		</tr>
		</table>
		</td>
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
		<a href="unactivated_company_email_change_request_list.php?start=<?php echo $prev_offset;?>">Prev</a>
		<?php
	}
	if($g_view['data_count'] > $g_view['num_to_show']){
		$next_offset = $g_view['start'] + $g_view['num_to_show'];
		?>
		&nbsp;&nbsp;&nbsp;<a href="unactivated_company_email_change_request_list.php?start=<?php echo $next_offset;?>">Next</a>
		<?php
	}
	?>
	</td>
	</tr>
	<?php
}
?>
</table>