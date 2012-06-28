<?php
/*********************
sng:5/mar/2012
We now show admin verified deals with an icon
*************************/
$g_view['has_verified_deal'] = false;
?>
<script type="text/javascript">
function goto_suggest_deal(){
	window.location="suggest_a_deal.php";
}
</script>
<?php
include("deal_search_filter_form_view.php");
?>
<?php
/***************************************
sng:13/jan/2011
In case the member is trying to add self to the deal on behalf of the firm (where the member works)
********/
?>
<script type="text/javascript">
function add_self_to_deal(the_deal_id,the_partner_id){
	$.post("ajax/add_self_to_deal.php", {deal_id: ""+the_deal_id+"",partner_id: ""+the_partner_id+""}, function(data){
			alert(data);
		});
}
</script>
<table width="100%" cellpadding="0" cellspacing="0">
<?php
/************************************************
sng:14/apr/2011
client does not want the suggest a deal button here
if($g_account->is_site_member_logged()){
?>
<tr>
<td style="text-align:right;"><input type="button" class="btn_auto" value="suggest a deal" onclick="goto_suggest_deal();" /></td>
<td style="height:40px">&nbsp;</td>
</tr>
<?php
}
******************************************************/
?>
</table>
<table width="100%" cellpadding="0" cellspacing="0" class="company">
<tr>

<th style="width:150px;">Participant</th>
<th style="width:60px;">Date</th>
<th>Type</th>
<th><!--Value (in million USD)-->Size</th>
<th style="width:170px;">Bank(s)</th>
<th style="width:170px;">Law Firm(s)</th>
<th></th>
<th>&nbsp;</th>
<!--<th>&nbsp;</th>-->
</tr>
<?php
if(0==$g_view['data_count']){
	?>
	<tr>
	<td colspan="8">
	No deals found.
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
	/****************************************
	sng:20/july/2010
	Now we can have filter like show top 25 deals. In class.transaction.php (front_deal_search_paged), we do not check for the max number. We check
	it here. Of course, if it is show all then no prob.
	The value can be top:10 or recent:25, see deal_search_filter_form_view.php
	We extract the limit and check in the loop (guarded by if clause).
	Beware of boundary condition like showing 10 on a page and asking for top 10.
	
	sng:31/oct/2011
	We have added a dummy number_of_deals called 'size'. We need to watch for that
	**********/
	if(($_POST['number_of_deals']!="")&&($_POST['number_of_deals']!="size")){
		$num_deals_tokens = explode(":",$_POST['number_of_deals']);
		$max_to_show = $num_deals_tokens[1];
	}
	////////////////////////////////////////////////////////////////////
	for($j=0;$j<$total;$j++){
		if(($_POST['number_of_deals']!="")&&($_POST['number_of_deals']!="size")){
			if(($g_view['start_offset']+$j)==$max_to_show){
				$no_more_next = true;
				break;
			}
		}
		/*******************************************/
		?>
		<tr>
		
		<td>
		<?php
		/***************************
		sng:3/feb/2012
		We now have multiple companies per deal
		<a href="company.php?show_company_id=<?php echo $g_view['data'][$j]['company_id'];?>"><?php echo $g_view['data'][$j]['company_name'];?></a>
		*******************************/
		echo Util::deal_participants_to_csv_with_links($g_view['data'][$j]['participants']);
		?>
        <?php 
        
                if (isset($_REQUEST['alert']) && isset($_REQUEST['token'])) {
                    if ((int) $g_view['data'][$j]['deal_id'] > (int) $lastAlertId && $lastAlertId !== false)
                    echo "<img src='images/new.png' style='float:right;width: 24px;' />"; 
                }

        ?>
        </td>
		<td><?php echo $g_view['data'][$j]['date_of_deal'];?></td>
		<td>
		<?php
		echo $g_view['data'][$j]['deal_cat_name'];
		
		?>
		
		</td>
		<td><?php
		/****
		sng:10/jul/2010
		if the deal value is 0, then deal value is not disclosed
		
		sng:23/jan/2012
		We now show the exact value if we have it else the value in range
		********
		if($g_view['data'][$j]['value_in_billion']==0){
			
			not disclosed
			
		}else{
			echo convert_billion_to_million_for_display_round($g_view['data'][$j]['value_in_billion']);
		}
		?>
		**************************************/
		echo convert_deal_value_for_display_round($g_view['data'][$j]['value_in_billion'],$g_view['data'][$j]['value_range_id'],$g_view['data'][$j]['fuzzy_value']);
		?>
		</td>
		<td>
		<?php
		$banks_csv = "";
		$bank_cnt = count($g_view['data'][$j]['banks']);
		for($banks_i=0;$banks_i<$bank_cnt;$banks_i++){
			$banks_csv.=", ".$g_view['data'][$j]['banks'][$banks_i]['name'];
		}
		$banks_csv = substr($banks_csv,1);
		echo $banks_csv;
		?>
		</td>
		<td>
		<?php
		$law_csv = "";
		$law_cnt = count($g_view['data'][$j]['law_firms']);
		for($law_i=0;$law_i<$law_cnt;$law_i++){
			$law_csv.=", ".$g_view['data'][$j]['law_firms'][$law_i]['name'];
		}
		$law_csv = substr($law_csv,1);
		echo $law_csv;
		?>
		</td>
		<td>
		<?php 
		if($g_view['data'][$j]['admin_verified']=='y'){
			?><img src="images/tick_ok.gif" /><?php
			$g_view['has_verified_deal'] = true;
		}
		?>
		</td>
		<td>
		<form method="get" action="deal_detail.php">
		<input type="hidden" name="deal_id" value="<?php echo $g_view['data'][$j]['deal_id'];?>" />
		<input name="submit" type="submit" class="btn_auto" id="button" value="Detail" />
		</form>
		</td>
		<!--<td>
		<?php
		/******************
		sng:13/jan/2011
		If this has the partner_id, it means, I am searching for deals (by my firm) to add myself.
		Show button
		
		sng:10/nov/2011
		We will not implement deal partner team, so let us remove 'add self to deal'
		*********/
		//if(isset($_REQUEST['partner_id'])){
			?>
			<input type="button" class="btn_auto" value="Add Self" onclick="return add_self_to_deal(<?php echo $g_view['data'][$j]['deal_id'];?>,<?php echo $_REQUEST['partner_id'];?>);" />
			<?php
		//}
		?>
		</td>-->
		</tr>
		<?php
	}
	/***********
	sng:20/july/2010
	*********/
	if(($g_view['start_offset']+$j)==$max_to_show){
		$no_more_next = true;
	}
	/////////////////////////////////////////
	//for pagination we use form submit with post data
	////////////////////////////////////////
	?>
	<?php
	/***
	sng:29/apr/2010
	Now we use a different field to differentiate the 2 searches
	sng:19/may/2010
	We use the default search which can search for deals. If that form is used, there will be top_search_area
	in POST
	*********/
	if(isset($_POST['top_search_area'])){
		//top company form search
		?>
		<form id="pagination_helper" method="post" action="deal_search.php">
			<input type="hidden" name="myaction" value="search" />
			<input type="hidden" name="top_search_area" value="<?php echo $_POST['top_search_area'];?>" />
			<input type="hidden" name="top_search_term" value="<?php echo $g_view['deal_company_form_input'];?>" />
			<input type="hidden" name="start" id="pagination_helper_start" value="0" />
		</form>
		<?php
	}else{
		/***
		sng:29/apr/2010
		we now embed the deal company name also, so that is sent in POST when the search is made
		so we must put that in the pagination support fields
		sng:19/may/2010
		We now embed top search term
		
		sng:20/jul/2010
		We have added another field number_of_deals
		
		sng:31/oct/2011
		We have added another field deal_size. Since the options are like >=250, they will get cleaned
		by sanitisation. So, we send it as base64 encoded
		
		sng:20/jan/2012
		We have deal size but now it use range id. In transaction table, each record also has a range id. So we no longer use
		options like >=250. It was dangerous idea anyway
		***/
		?>
		<form id="pagination_helper" method="post" action="deal_search.php<?php if (isset($_GET['token'])) echo "?token=".$_GET['token'];  if (isset($_GET['lid'])) echo "&lid=".$_GET['lid'];  if (isset($_GET['alert'])) echo "&alert=".$_GET['alert'];?>">
			<input type="hidden" name="myaction" value="search" />
			<input type="hidden" name="top_search_term" value="<?php echo $g_view['deal_company_form_input'];?>" />
			<input type="hidden" name="region" value="<?php echo $_POST['region'];?>" />
			<input type="hidden" name="country" value="<?php echo $_POST['country'];?>" />
			<input type="hidden" name="deal_cat_name" value="<?php echo $_POST['deal_cat_name'];?>" />
			<input type="hidden" name="deal_subcat1_name" value="<?php echo $_POST['deal_subcat1_name'];?>" />
			<input type="hidden" name="deal_subcat2_name" value="<?php echo $_POST['deal_subcat2_name'];?>" />
			<input type="hidden" name="sector" value="<?php echo $_POST['sector'];?>" />
			
			<input type="hidden" name="industry" value="<?php echo $_POST['industry'];?>" />
				
			
			<input type="hidden" name="year" value="<?php echo $_POST['year'];?>" />
			<input type="hidden" name="number_of_deals" value="<?php echo $_POST['number_of_deals'];?>" />
			<input type="hidden" name="deal_size" value="<?php echo $_POST['deal_size'];?>" />
			<?php
			/***************************
			sng:13/jan/2011
			Now we may call deal search by passing the partner_id to filter the search, showing the deals
			where the firm (partner_id) was involved)
			***/
			if(isset($_REQUEST['partner_id'])){
				?>
				<input type="hidden" name="partner_id" value="<?php echo $_REQUEST['partner_id'];?>" />
				<?php
			}
			/****************************************/
			?>
			<input type="hidden" name="start" id="pagination_helper_start" value="0" />
		</form>
		<?php
	}
	?>
	<script type="text/javascript">
	function go_page(offset){
		document.getElementById('pagination_helper_start').value = offset;
		document.getElementById('pagination_helper').submit();
		return false;
	}
	</script>
	<tr>
	<td colspan="8" style="text-align:right;">
	<?php
	if($g_view['start_offset'] > 0){
		?>
		<a class="link_as_button" href="#" onclick="return go_page(<?php echo $g_view['start_offset']-$g_view['num_to_show'];?>);">Prev</a>
		<?php
	}
	if($g_view['data_count'] > $g_view['num_to_show']){
		/*****
		sng:20/july/2010
		Now there can be restriction on number of records to be shown. We set a flag when we reach that count
		**********/
		if(isset($no_more_next)&&($no_more_next == true)){
		}else{
			?>
			&nbsp;&nbsp;&nbsp;<a class="link_as_button" href="#" onclick="return go_page(<?php echo $g_view['start_offset']+$g_view['num_to_show'];?>);">Next</a>
			<?php
		}
	}
	?>
	</td>
	</tr>
	<?php
}
?>
</table>
<?php
if($g_view['has_verified_deal']){
	?><div><img src="images/tick_ok.gif" /> Verified deal</div><?php
}
?>