<?php
/*********************
sng:5/mar/2012
We now show admin verified deals with an icon
*************************/
$g_view['has_verified_deal'] = false;
?>
<h3><?php echo $g_view['deal_search_heading'];?></h3>
<table width="100%" cellpadding="0" cellspacing="0" class="company">
<tr>

<th style="width:150px;">Participant</th>
<th style="width:60px;">Date</th>
<th>Type</th>
<th>Value</th>
<th style="width:170px;">Bank(s)</th>
<th style="width:170px;">Law Firm(s)</th>
<th></th>
<th>&nbsp;</th>
</tr>
<?php
if(0==$g_view['deal_data_count']){
	?>
	<tr>
	<td colspan="8">
	No deals found.
	</td>
	</tr>
	<?php
}else{
	
	////////////////////////////////////////////////////////////////////
	for($j=0;$j<$g_view['deal_data_count'];$j++){
		/*******************************************/
		?>
		<tr>
		
		<td>
		<?php
		/***************************
		sng:3/feb/2012
		We now have multiple companies per deal
		<a href="company.php?show_company_id=<?php echo $g_view['deal_data'][$j]['company_id'];?>"><?php echo $g_view['deal_data'][$j]['company_name'];?></a>
        ***********************/
		echo Util::deal_participants_to_csv_with_links($g_view['deal_data'][$j]['participants']);
		?>
        </td>
		<td><?php echo $g_view['deal_data'][$j]['date_of_deal'];?></td>
		<td>
		<?php
		echo $g_view['deal_data'][$j]['deal_cat_name'];
		
		?>
		
		</td>
		<td><?php
		/****
		sng:10/jul/2010
		if the deal value is 0, then deal value is not disclosed 
		
		sng:23/jan2012
		If we have exact value, we show that else we show the range
		********
		if($g_view['deal_data'][$j]['value_in_billion']==0){
			?>
			not disclosed
			<?php
		}else{
			echo convert_billion_to_million_for_display_round($g_view['deal_data'][$j]['value_in_billion']);
		}
		*************************/
		echo convert_deal_value_for_display_round($g_view['deal_data'][$j]['value_in_billion'],$g_view['deal_data'][$j]['value_range_id'],$g_view['deal_data'][$j]['fuzzy_value']);
		?></td>
		<td>
		<?php
		$banks_csv = "";
		$bank_cnt = count($g_view['deal_data'][$j]['banks']);
		for($banks_i=0;$banks_i<$bank_cnt;$banks_i++){
			$banks_csv.=", ".$g_view['deal_data'][$j]['banks'][$banks_i]['name'];
		}
		$banks_csv = substr($banks_csv,1);
		echo $banks_csv;
		?>
		</td>
		<td>
		<?php
		$law_csv = "";
		$law_cnt = count($g_view['deal_data'][$j]['law_firms']);
		for($law_i=0;$law_i<$law_cnt;$law_i++){
			$law_csv.=", ".$g_view['deal_data'][$j]['law_firms'][$law_i]['name'];
		}
		$law_csv = substr($law_csv,1);
		echo $law_csv;
		?>
		</td>
		<td>
		<?php 
		if($g_view['deal_data'][$j]['admin_verified']=='y'){
			?><img src="images/tick_ok.gif" /><?php
			$g_view['has_verified_deal'] = true;
		}
		?>
		</td>
		<td>
		<form method="get" action="deal_detail.php">
		<input type="hidden" name="deal_id" value="<?php echo $g_view['deal_data'][$j]['deal_id'];?>" />
		<input name="submit" type="submit" class="btn_auto" id="button" value="Detail" />
		</form>
		</td>
		</tr>
		<?php
	}
	
	/***
	we assume that this came from top search
	*********/
	
		
		?>
		<form id="deal_search_helper" method="post" action="deal_search.php">
			<input type="hidden" name="myaction" value="search" />
			<input type="hidden" name="top_search_area" value="deal" />
			<input type="hidden" name="top_search_term" value="<?php echo $g_view['search_form_input'];?>" />
			<input type="hidden" name="start" id="pagination_helper_start" value="0" />
		</form>
		
	<script type="text/javascript">
	function search_deal(){
		
		document.getElementById('deal_search_helper').submit();
		return false;
	}
	</script>
	<?php
	/****************************
	sng:22/nov/2011
	We show this only if we have more records. Since we tried to fetch one extra, let us see
	****************/
	if($g_view['deal_data_count'] > $g_view['num_to_show']){
		?>
		<tr>
		<td colspan="8" style="text-align:right;">
		
			<a class="link_as_button" href="#" onclick="return search_deal();">Show All</a>
			
		</td>
		</tr>
		<?php
	}
}
?>
</table>
<?php
if($g_view['has_verified_deal']){
	?><div><img src="images/tick_ok.gif" /> Verified deal</div><?php
}
?>