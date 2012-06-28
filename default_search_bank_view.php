<h3><?php echo $g_view['bank_search_heading'];?></h3>
<table width="100%" cellpadding="0" cellspacing="0" class="company">
<tr>
<th>Name</th>
<th colspan="2">&nbsp;</th>
</tr>
<?php
if(0==$g_view['bank_data_count']){
	?>
	<tr>
	<td colspan="3">
	No banks found.
	</td>
	</tr>
	<?php
}else{
	
	////////////////////////////////////////////////////////////////////
	for($j=0;$j<$g_view['bank_data_count'];$j++){
		?>
		<tr>
		<td><?php echo $g_view['bank_data'][$j]['name'];?></td>
		
		<td>
		<a class="link_as_button" href="firm.php?id=<?php echo $g_view['bank_data'][$j]['company_id'];?>">View</a>
		</td>
		<td>
		<a class="link_as_button" href="showcase_firm.php?id=<?php echo $g_view['bank_data'][$j]['company_id'];?>&from=savedSearches">Credentials</a>
		</td>
		</tr>
		<?php
	}
	/////////////////////////////////////////
	//for pagination we use form submit with post data
	////////////////////////////////////////
	?>
	<form id="bank_search_helper" method="post" action="bank_search.php">
		<input type="hidden" name="myaction" value="search" />
		<?php
		/***
		sng:19/may/2010
		we need to send the params for default search form
		*****/
		?>
		<input type="hidden" name="top_search_area" value="bank" />
		<input type="hidden" name="top_search_term" value="<?php echo $g_view['search_form_input'];?>" />
		<input type="hidden" name="start" id="pagination_helper_start" value="0" />
	</form>
	<script type="text/javascript">
	function search_bank(){
		document.getElementById('bank_search_helper').submit();
		return false;
	}
	</script>
	<?php
	/***********************************************
	sng:22/nov/2011
	If we have more data, only then we show 'ahow all'
	**************/
	if($g_view['bank_total_data_count'] > $g_view['bank_data_count']){
		?>
		<tr>
		<td colspan="3" style="text-align:right;">
		
			<a class="link_as_button" href="#" onclick="return search_bank();">Show All</a>
			
		</td>
		</tr>
		<?php
	}
}
?>
</table>
<div style="height:20px;"></div>
<div style="text-align:right">
<a href="suggest_a_bank.php" class="link_as_button">SUGGEST A BANK</a>
</div>