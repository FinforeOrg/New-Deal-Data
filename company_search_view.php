<table width="100%" cellpadding="0" cellspacing="0" class="company">
<tr>
<th>Name</th>
<th>Headquarter</th>
<th>Industry</th>
<th>&nbsp;</th>
</tr>
<?php
if(0==$g_view['data_count']){
	?>
	<tr>
	<td colspan="4">
	No companies found.
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
		<td><?php echo $g_view['data'][$j]['name'];?></td>
		<td><?php echo $g_view['data'][$j]['hq_country'];?></td>
		<td><?php echo $g_view['data'][$j]['industry'];?></td>
		<td>
		<a href="company.php?show_company_id=<?php echo $g_view['data'][$j]['company_id'];?>" class="link_as_button">View</a>
		</td>
		</tr>
		<?php
	}
	/////////////////////////////////////////
	//for pagination we use form submit with post data
	////////////////////////////////////////
	?>
	<form id="pagination_helper" method="post" action="company_search.php">
		<input type="hidden" name="myaction" value="search" />
		<?php
		/***
		sng:19/may/2010
		we need to send the params for default search form
		*****/
		?>
		<input type="hidden" name="top_search_area" value="<?php echo $_POST['top_search_area'];?>" />
		<input type="hidden" name="top_search_term" value="<?php echo $g_view['search_form_input'];?>" />
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
<?php
/************************
sng:22/nov/2011
Need a link to suggest a company
****************************/
?>
</table>
<div style="height:20px;"></div>
<div style="text-align:right">
<a href="suggest_a_company.php" class="link_as_button">SUGGEST A COMPANY</a>
</div>