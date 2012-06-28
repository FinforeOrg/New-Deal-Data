<table width="100%" cellpadding="0" cellspacing="0">
<tr>
<td><h1>Make Me Top Results</h1></td>
<td style="text-align:right;"><a href="make_me_top.php" class="link_as_button">Back to Make Me Top</a></td>
<?php
if($g_view['request_found']){
?>
<td><div style="float:right"><input type="button" id="help" class="btn_auto" value="Show Help" onclick="toggle_help()" /></div></td>
<?php
}
?>
</tr>
</table>
<?php
if(!$g_view['request_found']){
	?>
	<p>The request data was not found</p>
	<?php
	return;
}
?>

<table width="100%" cellpadding="0" cellspacing="0" class="company">
<tr>
<th>Country</th>
<th>Industry</th>
<th>Deal</th>
<th>Date submitted</th>
<th>Type</th>
<th>Status</th>
</tr>
<td><?php echo $g_view['request_data']['country_name'];?></td>
<td><?php echo $g_view['request_data']['industry_name'];?></td>
<td><?php echo $g_view['request_data']['deal_name'];?></td>
<td><?php echo $g_view['request_data']['submitted_on'];?></td>
<td>
<?php
/***************
sng: 18/sep/2010
if extended search is n, it means member chose fast search
else all search
*************/
if($g_view['request_data']['extended_search']=='y') echo "Full search";
if($g_view['request_data']['extended_search']=='n') echo "Fast search";
/***
sng:28/sep/2010
Now that user can choose top 5 or top 3, we show that also
***/
echo " [top ".$g_view['request_data']['rank_requested']."]";
?>
</td>
<td><?php echo $g_view['request_data']['status'];?></td>
<tr>
</tr>
</table>
<br /><br />
<div id="explanation">
<p>The list of results below show charts where your firm has a top ranking. However, given the length of the list, we suggest you use the Filters to help identify what chart is most suitable to your presentation.</p>
<p>Also, of particular interest is the third last column: Datapoint, which gives you a sneak preview of your firm's ranking.</p>
<p>Click though to the details using the &quot;Chart&quot; link, and then you can analyse any specific league table and download the data behind the chart.</p>
<?php
/*******************************************************************
sng:18/jan/2011
explanation for sorting
******/
?>
<p>
Move the mouse over to the column headers. If the column is sortable, the colour will change. Click on the column heading to sort the data by that column.
If you want to sort data by multiple columns, press CTRL key and then click the column headers in the order you want to sort. After clicking the last column heading, release
the CTRL key and the data is sorted by multiple columns.
</p>
<?php
/*************************************************/
?>
</div>
<?php
/*************************
sng:2/sep/2010
Since there can be 1000 or more hits, allow member to filter within the hits.
***************************/
?>
<form method="POST" action="make_me_top_matches.php?job_id=<?php echo $g_view['job_id'];?>&start=0">
<table with="100%" cellpadding="0" cellspacing="5">
<tr>
<td colspan="7"><strong>Filter</strong></td>
</tr>
<tr>
<td>
<select name="rank_of_firm" id="rank_of_firm">
<option value="">Rank</option>
<?php
for($i=0;$i<$g_view['rank_count'];$i++){
	?>
	<option value="<?php echo $g_view['rank_data'][$i]['preset_id'];?>" <?php if($g_view['rank_data'][$i]['preset_id']==$_POST['rank_of_firm']){?>selected="selected"<?php }?>><?php echo $g_view['rank_data'][$i]['preset_name'];?></option>
	<?php
}
?>
</select>
</td>
<td>
<select name="country_preset_id"  id="country_preset_id">
<option value="">Country</option>
<?php
for($i=0;$i<$g_view['country_count'];$i++){
	?>
	<option value="<?php echo $g_view['country_data'][$i]['preset_id'];?>" <?php if($g_view['country_data'][$i]['preset_id']==$_POST['country_preset_id']){?>selected="selected"<?php }?>><?php echo $g_view['country_data'][$i]['preset_name'];?></option>
	<?php
}
?>
</select>
</td>
<td>
<select name="sector_industry_preset_id" id="sector_industry_preset_id">
<option value="">Sector/Industry</option>
<?php
for($i=0;$i<$g_view['sector_count'];$i++){
	?>
	<option value="<?php echo $g_view['sector_data'][$i]['preset_id'];?>" <?php if($g_view['sector_data'][$i]['preset_id']==$_POST['sector_industry_preset_id']){?>selected="selected"<?php }?>><?php echo $g_view['sector_data'][$i]['preset_name'];?></option>
	<?php
}
?>
</select>
</td>
<td>
<select name="deal_type_preset_id" id="deal_type_preset_id">
<option value="">Deal Type</option>
<?php
for($i=0;$i<$g_view['deal_type_count'];$i++){
	?>
	<option value="<?php echo $g_view['deal_type_data'][$i]['preset_id'];?>" <?php if($g_view['deal_type_data'][$i]['preset_id']==$_POST['deal_type_preset_id']){?>selected="selected"<?php }?>><?php echo $g_view['deal_type_data'][$i]['preset_name'];?></option>
	<?php
}
?>
</select>
</td>
<td>
<select name="deal_size_preset_id" id="deal_size_preset_id">
<option value="">Size</option>
<?php
for($i=0;$i<$g_view['deal_size_count'];$i++){
	?>
	<option value="<?php echo $g_view['deal_size_data'][$i]['preset_id'];?>" <?php if($g_view['deal_size_data'][$i]['preset_id']==$_POST['deal_size_preset_id']){?>selected="selected"<?php }?>><?php echo $g_view['deal_size_data'][$i]['preset_name'];?></option>
	<?php
}
?>
</select>
</td>
<td>
<select name="deal_date_preset_id" id="deal_date_preset_id">
<option value="">Deal Date</option>
<?php
for($i=0;$i<$g_view['deal_date_count'];$i++){
	?>
	<option value="<?php echo $g_view['deal_date_data'][$i]['preset_id'];?>" <?php if($g_view['deal_date_data'][$i]['preset_id']==$_POST['deal_date_preset_id']){?>selected="selected"<?php }?>><?php echo $g_view['deal_date_data'][$i]['preset_name'];?></option>
	<?php
}
?>
</select>
</td>
<td>
<select name="ranking_criteria" id="ranking_criteria">
<option value="">Ranking Criteria</option>
<option value="num_deals" <?php if($_POST['ranking_criteria']=="num_deals"){?>selected="selected"<?php }?>>Total number of tombstones</option>
<option value="total_deal_value" <?php if($_POST['ranking_criteria']=="total_deal_value"){?>selected="selected"<?php }?>>Total tombstone value</option>
<option value="total_adjusted_deal_value" <?php if($_POST['ranking_criteria']=="total_adjusted_deal_value"){?>selected="selected"<?php }?>>Total adjusted value</option>
</select>
</td>
<td>
<input type="submit" value="Filter" class="btn_auto" />
</td>
</tr>
</table>
</form>
<br /><br />

<?php
if($g_view['result_count'] > 0){
	if($g_view['result_count'] > $g_view['num_to_show']){
		$end = $g_view['start']+$g_view['num_to_show'];
	}else{
		$end = $g_view['start']+$g_view['result_count'];
	}
	?>
	<h3>Showing <?php echo $g_view['start']+1;?> - <?php echo $end;?> of <?php echo $g_view['total_count'];?></h3>
	<?php
}
?>
<table width="100%" cellpadding="0" cellspacing="0" class="company">
<tr>
<?php
/*************************************************************************
sng:18/jan/2011
The columns that are sortable has a special class sortable-col. There is also id attrib so that we will know
which col header was clicked

When sort is triggered, the col ids are passed as csv in hidden form field. During pagination, when we go to next page,
$_POST['sort_by_cols'] holds this csv. So we parse it here and set the class for the columns so that
they are highlighted
**********/
?>
<th id="rank_of_firm" class="sortable-col<?php if(strpos($_POST['sort_by_cols'],"rank_of_firm")!==false){?> sortable-col-selected<?php }?>">Rank</th>
<th id="country_preset_id" class="sortable-col<?php if(strpos($_POST['sort_by_cols'],"country_preset_id")!==false){?> sortable-col-selected<?php }?>">Country</th>
<th id="sector_industry_preset_id" class="sortable-col<?php if(strpos($_POST['sort_by_cols'],"sector_industry_preset_id")!==false){?>,sortable-col-selected<?php }?>">Sector/Industry</th>
<th id="deal_type_preset_id" class="sortable-col<?php if(strpos($_POST['sort_by_cols'],"deal_type_preset_id")!==false){?> sortable-col-selected<?php }?>">Deal Type</th>
<th id="deal_size_preset_id" class="sortable-col<?php if(strpos($_POST['sort_by_cols'],"deal_size_preset_id")!==false){?> sortable-col-selected<?php }?>">Size</th>
<th id="deal_date_preset_id" class="sortable-col<?php if(strpos($_POST['sort_by_cols'],"deal_date_preset_id")!==false){?> sortable-col-selected<?php }?>">Date</th>
<?php
/***********************************************************************/
?>
<th id="ranking_criteria" class="sortable-col<?php if(strpos($_POST['sort_by_cols'],"ranking_criteria")!==false){?> sortable-col-selected<?php }?>">Ranking Criteria</th>
<th id="stat_value_of_firm" class="sortable-col<?php if(strpos($_POST['sort_by_cols'],"stat_value_of_firm")!==false){?> sortable-col-selected<?php }?>">Datapoint</th>
<th>Generated On</th>
<th></th>
</tr>
<?php
if(0==$g_view['result_count']){
	?>
	<tr><td colspan="10">None found</td></tr>
	<?php
}else{
	if($g_view['result_count'] > $g_view['num_to_show']){
		$total = $g_view['num_to_show'];
	}else{
		$total = $g_view['result_count'];
	}
	for($i=0;$i<$total;$i++){
		?>
		<tr>
		<td><?php echo $g_view['result'][$i]['rank_of_firm'];?></td>
		<td><?php echo $g_view['result'][$i]['country_name'];?></td>
		<td><?php echo $g_view['result'][$i]['sector_name'];?></td>
		<td><?php echo $g_view['result'][$i]['deal_name'];?></td>
		<td><?php echo $g_view['result'][$i]['size_name'];?></td>
		<td><?php echo $g_view['result'][$i]['date_name'];?></td>
		<td>
		<?php
		if($g_view['result'][$i]['ranking_criteria']=="num_deals") echo "Total number of tombstones";
		if($g_view['result'][$i]['ranking_criteria']=="total_deal_value") echo "Total tombstone value";
		if($g_view['result'][$i]['ranking_criteria']=="total_adjusted_deal_value") echo "Total adjusted value";
		?>
		</td>
		<?php
		/***
		sng:7/oct/2010
		We show the stat value for this firm, given the presets
		
		sng:9/oct/2010
		If this is nmber of deals, show like 4 deals
		otherwise, total deal value or total adjusted value, show like $2.45bn correct to 2 decimal points
		********/
		?>
		<td>
		<?php
		if($g_view['result'][$i]['ranking_criteria']=="num_deals"){
			echo $g_view['result'][$i]['stat_value_of_firm']." deals";
		}else{
			?>$ <?php echo round($g_view['result'][$i]['stat_value_of_firm'],2);?> bn<?php
		}
		?>
		</td>
		<td><?php echo date("Y-m-d",strtotime($g_view['result'][$i]['date_generated']));?>
		<td><a class="link_as_button" href="#" onclick="return go_page('make_me_top_match_chart.php?result_id=<?php echo $g_view['result'][$i]['id'];?>&job_id=<?php echo $g_view['job_id'];?>&start=<?php echo $g_view['start'];?>');">Chart</a></td>
		</tr>
		<?php
	}
	?>
	<script type="text/javascript">
	function go_page(url){
		document.getElementById("pagination_helper").action=url;
		document.getElementById("pagination_helper").submit();
		return false;
	}
	
	</script>
	<form id="pagination_helper" method="post" action="">
	<input type="hidden" name="rank_of_firm" value="<?php echo $_POST['rank_of_firm'];?>" />
	<input type="hidden" name="country_preset_id" value="<?php echo $_POST['country_preset_id'];?>" />
	<input type="hidden" name="sector_industry_preset_id" value="<?php echo $_POST['sector_industry_preset_id'];?>" />
	<input type="hidden" name="deal_type_preset_id" value="<?php echo $_POST['deal_type_preset_id'];?>" />
	<input type="hidden" name="deal_size_preset_id" value="<?php echo $_POST['deal_size_preset_id'];?>" />
	<input type="hidden" name="deal_date_preset_id" value="<?php echo $_POST['deal_date_preset_id'];?>" />
	<input type="hidden" name="ranking_criteria" value="<?php echo $_POST['ranking_criteria'];?>" />
	<?php
	/************************************************
	sng:18/jan/2011
	now member can select one or more cols on which to sort. We can send the ids via csv
	********/
	?>
	<input type="hidden" name="sort_by_cols" id="sort_by_cols" value="<?php echo $_POST['sort_by_cols'];?>" />
	<?php
	/*********************************************************/
	?>
	</form>
	<tr>
	<td colspan="10" style="text-align:right;">
	<?php
	if($g_view['start']>0){
		//has prev
		$prev_offset = $g_view['start']-$g_view['num_to_show'];
		?>
		<a class="link_as_button" href="#" onclick="return go_page('make_me_top_matches.php?job_id=<?php echo $g_view['job_id'];?>&start=<?php echo $prev_offset;?>');">Prev</a>
		<?php
	}
	if($g_view['result_count']>$g_view['num_to_show']){
		//has next
		$next_offset = $g_view['start']+$g_view['num_to_show'];
		?>
		&nbsp;&nbsp;&nbsp;&nbsp;<a class="link_as_button" href="#" onclick="return go_page('make_me_top_matches.php?job_id=<?php echo $g_view['job_id'];?>&start=<?php echo $next_offset;?>');">Next</a>
		<?php
	}
	?>
	</td>
	</tr>
	<?php
}
?>
</table>
<?php
/***********************************************
sng:18/jan/2011
support for multi col sort. We make the marked th clickable and create a csv of col ids on which the data
is to be sorted. We pass it to hidden field in pagination helper and start from the page 0
******/
?>
<script type="text/javascript">
var sort_cols = "";
function make_sortable(){
	//bind the click event on all the elements with class sortable-col
	$(".sortable-col").bind("click",function(event){
		//get the element that was clicked
		var elem = $(this);
		//get the id of the table header which was clicked
		var col_id = elem.attr("id");
		//highlight that table header, but first, remove the class from all the headers.
		//clear sort cols list and sort on the particular field
		//However, it can happen that this is a ctrl+click. In that case, keep the existing selection.
		//Do not fire sorting till the ctrl key is released
		if(event.ctrlKey){
			if(sort_cols==""){
				$(".sortable-col").removeClass("sortable-col-selected");
			}
			elem.addClass("sortable-col-selected");
			sort_cols = sort_cols+","+col_id;
			//do not fire sorting now
		}else{
			$(".sortable-col").removeClass("sortable-col-selected");
			elem.addClass("sortable-col-selected");
			sort_cols = "";
			sort_cols = sort_cols+","+col_id;
			//fire sorting
			trigger_sorting(sort_cols);
			//clear sort_cols so that spurious sorting is not triggered
			sort_cols = "";
		}
	});
	$(document).bind("keyup",function(event){
		if(event.keyCode==17){
			//ctrl key
			if(sort_cols!=""){
				trigger_sorting(sort_cols);
				//clear sort_cols so that spurious sorting is not triggered
				sort_cols = "";
			}
		}
	});
}
make_sortable();

function trigger_sorting(col_names){
	//the col names start with a ','. Remove that first ','.
	var cols = col_names.substring(1);
	//alert(cols);
	document.getElementById("sort_by_cols").value = cols;
	go_page('make_me_top_matches.php?job_id=<?php echo $g_view['job_id'];?>&start=0');
}
</script>
<?php
/*********************************************************************/
?>
<script>
$('#rank_of_firm').selectmenu();
$('#country_preset_id').selectmenu();
$('#sector_industry_preset_id').selectmenu();
$('#deal_type_preset_id').selectmenu();
$('#deal_size_preset_id').selectmenu();
$('#deal_date_preset_id').selectmenu();
$('#ranking_criteria').selectmenu();
</script>