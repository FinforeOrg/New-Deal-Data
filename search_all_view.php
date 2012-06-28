<?php die("not used");?>
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
		<form method="post" action="company.php">
		<input type="hidden" name="show_company_id" value="<?php echo $g_view['company_data'][$j]['company_id'];?>" />
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
	<tr>
	<td colspan="4" style="text-align:right;">
	
		<a class="link_as_button" href="#" onclick="return search_company();">Show All</a>
		
	</td>
	</tr>
	<?php
}
?>
</table>
<h3><?php echo $g_view['deal_search_heading'];?></h3>
<table width="100%" cellpadding="0" cellspacing="0" class="company">
<tr>
<th style="width:150px;">Company</th>
<th style="width:60px;">Date</th>
<th>Type</th>
<th>Value (in million USD)</th>
<th style="width:170px;">Bank(s)</th>
<th style="width:170px;">Law Firm(s)</th>
<th>&nbsp;</th>
</tr>
<?php
if(0==$g_view['deal_data_count']){
	?>
	<tr>
	<td colspan="7">
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
		<td><a href="company.php?show_company_id=<?php echo $g_view['deal_data'][$j]['company_id'];?>"><?php echo $g_view['deal_data'][$j]['company_name'];?></a>
        
        </td>
		<td><?php echo $g_view['deal_data'][$j]['date_of_deal'];?></td>
		<td>
		<?php
		echo $g_view['deal_data'][$j]['deal_cat_name'];
		if(($g_view['deal_data'][$j]['deal_cat_name']=="M&A")&&($g_view['deal_data'][$j]['target_company_name']!="")){
			/************************************************
			sng:28/july/2010
			check if the subtype is Completed or not
			**********/
			if(strtolower($g_view['deal_data'][$j]['deal_subcat1_name'])=="completed"){
				echo ". Acquisition of ".$g_view['deal_data'][$j]['target_company_name'];
			}else{
				echo ". Proposed acquisition of ".$g_view['deal_data'][$j]['target_company_name'];
			}
			/******************************************/
			/*****
			sng:20/aug/2010
			if there is a seller, show that
			**/
			if($g_view['deal_data'][$j]['seller_company_name']!=""){
				echo ".<br />Sold by ".$g_view['deal_data'][$j]['seller_company_name'];
			}
			/***********************************/
			
		}
		?>
		
		</td>
		<td><?php
		/****
		sng:10/jul/2010
		if the deal value is 0, then deal value is not disclosed 
		********/
		if($g_view['deal_data'][$j]['value_in_billion']==0){
			?>
			not disclosed
			<?php
		}else{
			echo convert_billion_to_million_for_display_round($g_view['deal_data'][$j]['value_in_billion']);
		}
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
	<tr>
	<td colspan="7" style="text-align:right;">
	
		<a class="link_as_button" href="#" onclick="return search_deal();">Show All</a>
		
	</td>
	</tr>
	<?php
}
?>
</table>
<h3><?php echo $g_view['target_search_heading'];?></h3>
<table width="100%" cellpadding="0" cellspacing="0" class="company">
<tr>
<th style="width:150px;">Company</th>
<th style="width:60px;">Date</th>
<th>Type</th>
<th>Value (in million USD)</th>
<th style="width:170px;">Bank(s)</th>
<th style="width:170px;">Law Firm(s)</th>
<th>&nbsp;</th>
</tr>
<?php
if(0==$g_view['target_data_count']){
	?>
	<tr>
	<td colspan="7">
	No M&amp;A deal target / seller found.
	</td>
	</tr>
	<?php
}else{
	
	////////////////////////////////////////////////////////////////////
	for($j=0;$j<$g_view['target_data_count'];$j++){
		/*******************************************/
		?>
		<tr>
		<td><a href="company.php?show_company_id=<?php echo $g_view['target_data'][$j]['company_id'];?>"><?php echo $g_view['target_data'][$j]['company_name'];?></a>
        
        </td>
		<td><?php echo $g_view['target_data'][$j]['date_of_deal'];?></td>
		<td>
		<?php
		echo $g_view['target_data'][$j]['deal_cat_name'];
		if(($g_view['target_data'][$j]['deal_cat_name']=="M&A")&&($g_view['target_data'][$j]['target_company_name']!="")){
			/************************************************
			sng:28/july/2010
			check if the subtype is Completed or not
			**********/
			if(strtolower($g_view['target_data'][$j]['deal_subcat1_name'])=="completed"){
				echo ". Acquisition of ".$g_view['target_data'][$j]['target_company_name'];
			}else{
				echo ". Proposed acquisition of ".$g_view['target_data'][$j]['target_company_name'];
			}
			/******************************************/
			/*****
			sng:20/aug/2010
			if there is a seller, show that
			**/
			if($g_view['target_data'][$j]['seller_company_name']!=""){
				echo ".<br />Sold by ".$g_view['target_data'][$j]['seller_company_name'];
			}
			/***********************************/
		}
		?>
		
		</td>
		<td><?php
		/****
		sng:10/jul/2010
		if the deal value is 0, then deal value is not disclosed 
		********/
		if($g_view['target_data'][$j]['value_in_billion']==0){
			?>
			not disclosed
			<?php
		}else{
			echo convert_billion_to_million_for_display_round($g_view['target_data'][$j]['value_in_billion']);
		}
		?></td>
		<td>
		<?php
		$banks_csv = "";
		$bank_cnt = count($g_view['target_data'][$j]['banks']);
		for($banks_i=0;$banks_i<$bank_cnt;$banks_i++){
			$banks_csv.=", ".$g_view['target_data'][$j]['banks'][$banks_i]['name'];
		}
		$banks_csv = substr($banks_csv,1);
		echo $banks_csv;
		?>
		</td>
		<td>
		<?php
		$law_csv = "";
		$law_cnt = count($g_view['target_data'][$j]['law_firms']);
		for($law_i=0;$law_i<$law_cnt;$law_i++){
			$law_csv.=", ".$g_view['target_data'][$j]['law_firms'][$law_i]['name'];
		}
		$law_csv = substr($law_csv,1);
		echo $law_csv;
		?>
		</td>
		<td>
		<form method="get" action="deal_detail.php">
		<input type="hidden" name="deal_id" value="<?php echo $g_view['target_data'][$j]['deal_id'];?>" />
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
		<form id="target_search_helper" method="post" action="deal_search.php">
			<input type="hidden" name="myaction" value="search" />
			<input type="hidden" name="top_search_area" value="deal" />
			<input type="hidden" name="top_search_term" value="<?php echo $g_view['search_form_input'];?>" />
			<input type="hidden" name="deal_cat_name" value="M&A" />
			<input type="hidden" name="search_target" value="y" />
			<input type="hidden" name="start" id="pagination_helper_start" value="0" />
		</form>
		
	<script type="text/javascript">
	function search_ma_deal_target(){
		
		document.getElementById('target_search_helper').submit();
		return false;
	}
	</script>
	<tr>
	<td colspan="7" style="text-align:right;">
	
		<a class="link_as_button" href="#" onclick="return search_ma_deal_target();">Show All</a>
		
	</td>
	</tr>
	<?php
}
?>
</table>
<h3><?php echo $g_view['bank_search_heading'];?></h3>
<table width="100%" cellpadding="0" cellspacing="0" class="company">
<tr>
<th>Name</th>
<th>&nbsp;</th>
</tr>
<?php
if(0==$g_view['bank_data_count']){
	?>
	<tr>
	<td colspan="2">
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
		<a class="link_as_button" href="showcase_firm.php?id=<?php echo $g_view['bank_data'][$j]['company_id'];?>">View</a>
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
	<tr>
	<td colspan="2" style="text-align:right;">
	
		<a class="link_as_button" href="#" onclick="return search_bank();">Show All</a>
		
	</td>
	</tr>
	<?php
}
?>
</table>
<h3><?php echo $g_view['law_firm_search_heading'];?></h3>
<table width="100%" cellpadding="0" cellspacing="0" class="company">
<tr>
<th>Name</th>
<th>&nbsp;</th>
</tr>
<?php
if(0==$g_view['law_firm_data_count']){
	?>
	<tr>
	<td colspan="2">
	No law firms found.
	</td>
	</tr>
	<?php
}else{
	
	////////////////////////////////////////////////////////////////////
	for($j=0;$j<$g_view['law_firm_data_count'];$j++){
		?>
		<tr>
		<td><?php echo $g_view['law_firm_data'][$j]['name'];?></td>
		
		<td>
		<a class="link_as_button" href="showcase_firm.php?id=<?php echo $g_view['law_firm_data'][$j]['company_id'];?>">View</a>
		</td>
		</tr>
		<?php
	}
	/////////////////////////////////////////
	//for pagination we use form submit with post data
	////////////////////////////////////////
	?>
	<form id="law_firm_search_helper" method="post" action="law_firm_search.php">
		<input type="hidden" name="myaction" value="search" />
		<?php
		/***
		sng:19/may/2010
		we need to send the params for default search form
		*****/
		?>
		<input type="hidden" name="top_search_area" value="law firm" />
		<input type="hidden" name="top_search_term" value="<?php echo $g_view['search_form_input'];?>" />
		<input type="hidden" name="start" id="pagination_helper_start" value="0" />
	</form>
	<script type="text/javascript">
	function search_law_firm(){
		
		document.getElementById('law_firm_search_helper').submit();
		return false;
	}
	</script>
	<tr>
	<td colspan="2" style="text-align:right;">
	
		<a class="link_as_button" href="#" onclick="return search_law_firm();">Show All</a>
		
	</td>
	</tr>
	<?php
}
?>
</table>