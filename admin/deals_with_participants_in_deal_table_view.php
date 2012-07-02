<script type="text/javascript" src="util.js"></script>
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

<tr bgcolor="#dec5b3" style="height:20px;">
<td><strong>id</strong></td>
<td><strong>target company</strong></td>
<td><strong>target country</strong></td>
<td><strong>target sector</strong></td>
<td><strong>target industry</strong></td>
<td><strong>seller company</strong></td>
<td><strong>seller country</strong></td>
<td><strong>seller sector</strong></td>
<td><strong>seller industry</strong></td>
<td><strong>buyer subsidiary</strong></td>
<td><strong>buyer subsidiary country</strong></td>
<td><strong>buyer subsidiary sector</strong></td>
<td><strong>buyer subsidiary industry</strong></td>
<td><strong>buyer company</strong></td>
<td><strong>buyer country</strong></td>
<td><strong>buyer sector</strong></td>
<td><strong>buyer industry</strong></td>
</tr>
<?php
if($g_view['data_count']==0){
	?>
	<tr>
	  <td colspan="17">None found</td>
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
		<td><?php echo $g_view['data'][$i]['id'];?><br><a onclick="return deal_participant_popup('<?php echo $g_view['data'][$i]['id'];?>');" href="">Manage Participants</a></td>
		<td><?php echo $g_view['data'][$i]['target_company_name'];?></td>
		<td><?php echo $g_view['data'][$i]['target_company_country'];?></td>
		<td><?php echo $g_view['data'][$i]['target_company_sector'];?></td>
		<td><?php echo $g_view['data'][$i]['target_company_industry'];?></td>
		<td><?php echo $g_view['data'][$i]['seller_company_name'];?></td>
		<td><?php echo $g_view['data'][$i]['seller_company_country'];?></td>
		<td><?php echo $g_view['data'][$i]['seller_company_sector'];?></td>
		<td><?php echo $g_view['data'][$i]['seller_company_industry'];?></td>
		<td><?php echo $g_view['data'][$i]['buyer_subsidiary_name'];?></td>
		<td><?php echo $g_view['data'][$i]['buyer_subsidiary_country'];?></td>
		<td><?php echo $g_view['data'][$i]['buyer_subsidiary_sector'];?></td>
		<td><?php echo $g_view['data'][$i]['buyer_subsidiary_industry'];?></td>
		
		<td><?php echo $g_view['data'][$i]['buyer_company_name'];?></td>
		<td><?php echo $g_view['data'][$i]['buyer_company_country'];?></td>
		<td><?php echo $g_view['data'][$i]['buyer_company_sector'];?></td>
		<td><?php echo $g_view['data'][$i]['buyer_company_industry'];?></td>
		
		</tr>
		<?php
	}
	?>
	<tr>
	<td colspan="17" style="text-align:left">
	<?php
	if($g_view['start'] > 0){
		$prev_offset = $g_view['start'] - $g_view['num_to_show'];
		?>
		<a href="deals_with_participants_in_deal_table.php?start=<?php echo $prev_offset;?>">Prev</a>
		<?php
	}
	if($g_view['data_count'] > $g_view['num_to_show']){
		$next_offset = $g_view['start'] + $g_view['num_to_show'];
		?>
		&nbsp;&nbsp;&nbsp;<a href="deals_with_participants_in_deal_table.php?start=<?php echo $next_offset;?>">Next</a>
		<?php
	}
	?>
	</td>
	</tr>
	<?php
}
?>
</table>