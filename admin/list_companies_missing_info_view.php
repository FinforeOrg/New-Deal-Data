<table cellspacing="0" cellpadding="0" border="0">
<tr>
<td>
<form method="post" action="">
<input type="hidden" name="myaction" value="search_company" />
<input type="hidden" name="type" value="company" />
<table width="100%" cellspacing="0" cellpadding="10" border="0">
<tr>
<td>
<input name="field_name" type="radio" value="hq_country" <?php if(($_POST['field_name']=="hq_country")||($_POST['field_name']=="")){?>checked<?php }?>>&nbsp;Country
</td>
<td>
<input name="field_name" type="radio" value="sector" <?php if($_POST['field_name']=="sector"){?>checked<?php }?>>&nbsp;Sector
</td>
</tr>
<tr>
<td colspan="2">
<input type="submit" name="submit" value="List" />
</td>
</tr>
</table>
</form>
</td>
</tr>
</table>

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
<td>&nbsp;</td>
</tr>
<?php
if($g_view['data_count']==0){
	?>
	<tr>
	  <td colspan="8">No company data found</td>
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
		<td>
		<form method="post" action="company_edit.php">
		<input type="hidden" name="company_id" value="<?php echo $g_view['data'][$i]['company_id'];?>" />
		<input type="submit" value="Edit" />
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
		<a href="#" onClick="return go_page(<?php echo $prev_offset;?>);">Prev</a>
		<?php
	}
	if($g_view['data_count'] > $g_view['num_to_show']){
		$next_offset = $g_view['start'] + $g_view['num_to_show'];
		?>
		&nbsp;&nbsp;&nbsp;<a href="#" onClick="return go_page(<?php echo $next_offset;?>);">Next</a>
		<?php
	}
	?>
	</td>
	</tr>
	<?php
}
?>
<form id="pagination_helper" method="post" action="list_companies_missing_info.php">
<input type="hidden" name="myaction" value="search_company" />
<input type="hidden" name="type" value="company" />
<input type="hidden" name="field_name" value="<?php echo $_POST['field_name'];?>" />
<input type="hidden" name="start" id="pagination_helper_start" value="0" />
</form>
<script type="text/javascript">
function go_page(offset){
	document.getElementById('pagination_helper_start').value = offset;
	document.getElementById('pagination_helper').submit();
	return false;
}
</script>
</table>