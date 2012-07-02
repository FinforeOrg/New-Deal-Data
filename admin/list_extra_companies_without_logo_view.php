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

<td><strong>Type</strong></td>


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
		<td><?php echo $g_view['data'][$i]['company_name'];?></td>
		<td><?php echo $g_view['data'][$i]['company_type'];?></td>
		<td>
		<form method="post" action="extra_company_logo.php">
		<input type="hidden" name="deal_id" value="<?php echo $g_view['data'][$i]['deal_id'];?>" />
		<input type="hidden" name="company_type" value="<?php echo $g_view['data'][$i]['company_type'];?>" />
		<input type="submit" value="Set Logo" />
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
		<a href="list_extra_companies_without_logo.php?start=<?php echo $prev_offset;?>">Prev</a>
		<?php
	}
	if($g_view['data_count'] > $g_view['num_to_show']){
		$next_offset = $g_view['start'] + $g_view['num_to_show'];
		?>
		&nbsp;&nbsp;&nbsp;<a href="list_extra_companies_without_logo.php?start=<?php echo $next_offset;?>">Next</a>
		<?php
	}
	?>
	</td>
	</tr>
	<?php
}
?>
</table>