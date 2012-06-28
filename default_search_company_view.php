<h3><?php echo $g_view['company_search_heading'];?></h3>
<table width="100%" cellpadding="0" cellspacing="0" class="company">
<tr>
<th>Name</th>
<th>Headquarter</th>
<th>Industry</th>
<th>&nbsp;</th>
</tr>
<?php
if(0==$g_view['company_data_count']){
	?>
	<tr>
	<td colspan="4">
	No companies found.
	</td>
	</tr>
	<?php
}else{
	////////////////////////////////////////////////////////////////////
	for($j=0;$j<$g_view['company_data_count'];$j++){
		?>
		<tr>
		<td><?php echo $g_view['company_data'][$j]['name'];?></td>
		<td><?php echo $g_view['company_data'][$j]['hq_country'];?></td>
		<td><?php echo $g_view['company_data'][$j]['industry'];?></td>
		<td>
		<a href="company.php?show_company_id=<?php echo $g_view['company_data'][$j]['company_id'];?>" class="link_as_button">View</a>
		</td>
		</tr>
		<?php
	}
	/////////////////////////////////////////
	//for pagination we use form submit with post data
	////////////////////////////////////////
	?>
	<form id="search_company_helper" method="post" action="company_search.php">
		<input type="hidden" name="myaction" value="search" />
		<?php
		/***
		sng:19/may/2010
		we need to send the params for default search form
		*****/
		?>
		<input type="hidden" name="top_search_area" value="company" />
		<input type="hidden" name="top_search_term" value="<?php echo $g_view['search_form_input'];?>" />
		<input type="hidden" name="start" id="pagination_helper_start" value="0" />
	</form>
	<script type="text/javascript">
	function search_company(){
		document.getElementById('search_company_helper').submit();
		return false;
	}
	</script>
	<?php
	/***********************************************
	sng:22/nov/2011
	If we have more data, only then we show 'ahow all'
	**************/
	if($g_view['company_total_data_count'] > $g_view['company_data_count']){
		?>
		<tr>
		<td colspan="4" style="text-align:right;">
		
			<a class="link_as_button" href="#" onclick="return search_company();">Show All</a>
			
		</td>
		</tr>
		<?php
	}
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