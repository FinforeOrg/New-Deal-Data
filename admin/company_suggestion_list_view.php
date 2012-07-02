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
<td><strong>Name</strong></td>
<td><strong>HQ</strong></td>
<td><strong>Sector/Industry</strong></td>
<td><strong>Indentifiers</strong></td>
<td><strong>By</strong></td>
<td>&nbsp;</td>
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
		<td><?php echo $g_view['data'][$i]['name'];?></td>
		<td><?php echo $g_view['data'][$i]['hq_country'];?></td>
		<td><?php echo $g_view['data'][$i]['sector'];?><br /><?php echo $g_view['data'][$i]['industry'];?></td>
		<td>
		<?php
		if($g_view['data'][$i]['identifier_count'] > 0){
			?>
			<table width="100%" cellpadding="5" cellspacing="0">
			<?php
			for($k=0;$k<$g_view['data'][$i]['identifier_count'];$k++){
				?>
				<tr>
				<td><?php echo $g_view['data'][$i]['identifiers'][$k]['name'];?></td>
				<td><?php echo $g_view['data'][$i]['identifiers'][$k]['value'];?></td>
				</tr>
				<?php
			}
			?>
			</table>
			<?php
		}
		?>
		</td>
		<td>
		<?php echo $g_view['data'][$i]['f_name'];?> <?php echo $g_view['data'][$i]['l_name'];?> [<?php echo $g_view['data'][$i]['designation'];?>] <?php echo $g_view['data'][$i]['work_company'];?><br /><br />on <?php echo date("Y-m-d",strtotime($g_view['data'][$i]['date_suggested']));?>
		</td>
		<td>
		<form method="post" action="">
		<input type="hidden" name="action" value="accept" />
		<input type="hidden" name="company_suggestion_id" value="<?php echo $g_view['data'][$i]['company_suggestion_id'];?>" />
		<input type="submit" name="submit" value="Accept" />
		</form>
		<br />
		<form method="post" action="">
		<input type="hidden" name="action" value="reject" />
		<input type="hidden" name="company_suggestion_id" value="<?php echo $g_view['data'][$i]['company_suggestion_id'];?>" />
		<input type="submit" name="submit" value="Reject" />
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
		<a href="company_suggestion_list.php?start=<?php echo $prev_offset;?>">Prev</a>
		<?php
	}
	if($g_view['data_count'] > $g_view['num_to_show']){
		$next_offset = $g_view['start'] + $g_view['num_to_show'];
		?>
		&nbsp;&nbsp;&nbsp;<a href="company_suggestion_list.php?start=<?php echo $next_offset;?>">Next</a>
		<?php
	}
	?>
	</td>
	</tr>
	<?php
}
?>
</table>