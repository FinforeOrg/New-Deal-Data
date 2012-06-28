<table width="100%" cellpadding="0" cellspacing="0" class="company">
<tr>
<th>Name</th>
<th>Designation</th>
<th>Company</th>
<th>&nbsp;</th>
</tr>
<?php
if(0==$g_view['data_count']){
	?>
	<tr>
	<td colspan="4">
	None found.
	</td>
	</tr>
	<?php
}else{
	//we fetched one extra
	if($g_view['data_count'] > $g_view['num_to_show']){
		$total = $g_view['num_to_show'];
	}else{
		$total = $g_view['data_count'];
	}
	////////////////////////////////////////////////////////////////////
	for($j=0;$j<$total;$j++){
		?>
		<tr>
		<td><?php echo $g_view['data'][$j]['f_name'];?> <?php echo $g_view['data'][$j]['l_name'];?></td>
		<td><?php echo $g_view['data'][$j]['designation'];?></td>
		<td><?php echo $g_view['data'][$j]['company_name'];?></td>
		<td>
		<form method="post" action="profile.php?mem_id=<?php echo $g_view['data'][$j]['mem_id'];?>">
		<input name="submit" type="submit" class="btn_auto" id="button" value="View" />
		</form>
		</td>
		</tr>
		<?php
	}
	/////////////////////////////////////////
	//for pagination we use form submit with post data
	////////////////////////////////////////
	?>
	<form id="pagination_helper" method="post" action="banker_search.php">
		<input type="hidden" name="action" value="search" />
		<input type="hidden" name="search" value="<?php echo $g_view['search_form_input'];?>" />
		<input type="hidden" name="start" id="pagination_helper_start" value="0" />
	</form>
	<script type="text/javascript">
	function go_page(offset){
		document.getElementById('pagination_helper_start').value = offset;
		document.getElementById('pagination_helper').submit();
		return false;
	}
	</script>
	<tr>
	<td colspan="4" style="text-align:right;">
	<?php
	if($g_view['start_offset'] > 0){
		?>
		<a class="link_as_button" href="#" onclick="return go_page(<?php echo $g_view['start_offset']-$g_view['num_to_show'];?>);">Prev</a>
		<?php
	}
	if($g_view['data_count'] > $g_view['num_to_show']){
		?>
		&nbsp;&nbsp;&nbsp;<a class="link_as_button" href="#" onclick="return go_page(<?php echo $g_view['start_offset']+$g_view['num_to_show'];?>);">Next</a>
		<?php
	}
	?>
	</td>
	</tr>
	<?php
}
?>
</table>