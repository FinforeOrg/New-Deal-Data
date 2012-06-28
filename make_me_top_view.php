<?php
/*********************
sng:29/sep/2011
we now include jquery in container view
<script type="text/javascript" src="js/jquery-1.3.2.js"></script>
***********************/
?>
<script type="text/javascript" src="js/tooltip.js"></script>
<div>
Enter parameters related to your meeting / upcoming presentation:
</div>
<form method="post" action="">
<input type="hidden" name="action" value="search" />
<?php
/***
sng:28/sep/2010
since the member is to be given the option to specify rank requested, this is now changed to default
*******/
?>
<input type="hidden" name="default_rank_requested" value="5" />
<table width="100%">
<tr>
<td colspan="2">
	<table>
	<tr>
	<td>
	<select name="top_search_option_country" id="top_search_option_country">
	<option value="">Which country?</option>
	<?php
	for($i=0;$i<$g_view['country_data_count'];$i++){
		?>
		<option value="<?php echo $g_view['country_data'][$i]['option_id'];?>" <?php if($_POST['top_search_option_country']==$g_view['country_data'][$i]['option_id']){?>selected="selected"<?php }?>><?php echo $g_view['country_data'][$i]['name'];?></option>
		<?php
	}
	?>
	</select><span class="err_txt"> *</span>
	</td>
	<td>
	<select name="top_search_option_sector_industry" id="top_search_option_sector_industry">
	<option value="">Which industry?</option>
	<?php
	/******************
	sng:20/july/2011
	We have the group_name for all the items. We get the data ordered by group_name.
	We can create a tree, shouing the group_name and all the options under it
	****************************/
	$curr_group_name = "";
	$opt_group_started = false;
	for($i=0;$i<$g_view['sector_data_count'];$i++){
		if($g_view['sector_data'][$i]['group_name'] != $curr_group_name){
			if($opt_group_started){
				?>
				</optgroup>
				<?php
			}
			//start a new grouping
			?>
			<optgroup label="<?php echo $g_view['sector_data'][$i]['group_name'];?>">
			<?php
			$curr_group_name = $g_view['sector_data'][$i]['group_name'];
			$opt_group_started = true;
		}
		?>
		<option value="<?php echo $g_view['sector_data'][$i]['option_id'];?>" <?php if($_POST['top_search_option_sector_industry']==$g_view['sector_data'][$i]['option_id']){?>selected="selected"<?php }?>><?php echo $g_view['sector_data'][$i]['name'];?></option>
		<?php
	}
	if($opt_group_started){
		?>
		</optgroup>
		<?php
	}
	?>
	</select><span class="err_txt"> *</span>
	</td>
	<td>
	<select name="top_search_option_deal_type" id="top_search_option_deal_type">
	<option value="">Which type of deal?</option>
	<?php
	/******************
	sng:23/july/2011
	We have the group_name for all the items. We get the data ordered by group_name.
	We can create a tree, shouing the group_name and all the options under it
	****************************/
	$curr_group_name = "";
	$opt_group_started = false;
	for($i=0;$i<$g_view['deal_type_data_count'];$i++){
		if($g_view['deal_type_data'][$i]['group_name'] != $curr_group_name){
			if($opt_group_started){
				?>
				</optgroup>
				<?php
			}
			//start a new grouping
			?>
			<optgroup label="<?php echo $g_view['deal_type_data'][$i]['group_name'];?>">
			<?php
			$curr_group_name = $g_view['deal_type_data'][$i]['group_name'];
			$opt_group_started = true;
		}
		?>
		<option value="<?php echo $g_view['deal_type_data'][$i]['option_id'];?>" <?php if($_POST['top_search_option_deal_type']==$g_view['deal_type_data'][$i]['option_id']){?>selected="selected"<?php }?>><?php echo $g_view['deal_type_data'][$i]['name'];?></option>
		<?php
	}
	if($opt_group_started){
		?>
		</optgroup>
		<?php
	}
	?>
	</select><span class="err_txt"> *</span>
	</td>
	<?php
	/****
	sng:18/sep/2010
	Now we allow member to decide whether to do a fast search (which consider
	only the primary presets (that is non extended search)
	or full search which considers all (extended search)
	By default, always tick this box for a fast search
	***/
	?>
	<td>
	<input name="extended_search" type="checkbox" value="n" checked="checked" />Fast Search
	</td>
	<?php
	/***
	sng:28/sep/2010
	whether to show only those results where my firm came at top 3
	We do this by sending the rank. Of course, if this is unchecked, then the
	default_rank_requested is used
	********/
	?>
	<td>
	<input name="rank_requested" type="checkbox" value="3" checked="checked" />only show top 3
	</td>
	</tr>
	<tr>
	<td>
	<span class="err_txt"><?php echo $g_view['err']['top_search_option_country'];?></span>
	</td>
	<td>
	<span class="err_txt"><?php echo $g_view['err']['top_search_option_sector_industry'];?></span>
	</td>
	<td>
	<span class="err_txt"><?php echo $g_view['err']['top_search_option_deal_type'];?></span>
	</td>
	</tr>
	</table>
</td>
</tr>

<tr>
<td>
<div id="explanation">
<p>It is not always possible to present a league table to a client which shows your firm to be number 1. Based on the strictest definition of a meeting (i.e. the exact country, industry and deal discussion) only 1 firm can be number 1 in any chart. This ignores all the other transactions that can be relevant to the client, especially when they look at a broader set of parameters.</p>
<p>This is where our &quot;Make Me Top&quot; search algorithm comes in. We take the meeting inputs you give us (Country, Industry, Deal Type) and we loosen these parameters. We also look at multiple deal sizes and different dates. With this information we run thousands of charts and check each one, to see if your firm comes out number 1.&nbsp;</p>
<p>Once the algorithm has done its work, you click on the View link to the right, to see the results. Then you get to look at the different results, and analyse which one you think is most relevant for your upcoming meeting.</p>
</div>
</td>
<td>
<input name="submit" type="submit" value="Make Me Top" class="btn_auto" />
</td>
</tr>
<tr><td colspan="2"><span class="msg_txt"><?php echo $g_view['msg'];?></span></td></tr>
</table>
</form>

<table width="100%">
<tr>
<td>Submitted jobs&nbsp;&nbsp;&nbsp;&nbsp;<a href="make_me_top_archived.php">Archived jobs</a></td>
</tr>
</table>
<table width="100%" cellpadding="0" cellspacing="0" class="company">
<tr>
<th>Country</th>
<th>Industry</th>
<th>Deal</th>
<th>Date submitted</th>
<th>Type</th>
<th>Status</th>
<th></th>
<th></th>
</tr>
<?php
if($g_view['request_count']==0){
?>
<tr><td colspan="8">None found</td></tr>
<?php
}else{
	for($i=0;$i<$g_view['request_count'];$i++){
		?>
		<tr>
		<td><?php echo $g_view['request_data'][$i]['country_name'];?></td>
		<td><?php echo $g_view['request_data'][$i]['industry_name'];?></td>
		<td><?php echo $g_view['request_data'][$i]['deal_name'];?></td>
		<td><?php echo $g_view['request_data'][$i]['submitted_on'];?></td>
		<td>
		<?php
		if($g_view['request_data'][$i]['extended_search']=='y') echo "Full Search";
		if($g_view['request_data'][$i]['extended_search']=='n') echo "Fast Search";
		/***
		sng:28/sep/2010
		Now that user can choose top 5 or top 3, we show that also
		***/
		echo " [top ".$g_view['request_data'][$i]['rank_requested']."]";
		?>
		</td>
		<td><?php echo $g_view['request_data'][$i]['status'];?><span title="<?php echo $g_view['request_data'][$i]['dbg_status'];?>" class="toolTip"></span></td>
		<td>
		<?php
		/*************************************************************
		sng12/jan/2011
		If finished and older than 30 days, show 're-run'
		*******/
		$days = floor((time()-strtotime($g_view['request_data'][$i]['submitted_on']))/(60*60*24));
		if(($g_view['request_data'][$i]['status'] == "finished")&&($days > 30)){
			?>
			<a class="link_as_button" href="#" onclick="return rerun_request('<?php echo $g_view['request_data'][$i]['job_id'];?>');">Re-run</a>
			<?php
		}else{
			?>
			<a class="link_as_button" href="make_me_top_matches.php?job_id=<?php echo $g_view['request_data'][$i]['job_id'];?>">View</a>
			<?php
		}
		/*****************************************************************/
		?>
		</td>
		<?php
		/***
		sng:5/oct/2010
		the member should be able to mark this as archived so as not to clutter the current page
		provided, the request has finished
		***/
		?>
		<td>
		<?php
		if($g_view['request_data'][$i]['status']=="finished"){
			?>
			<a class="link_as_button" href="#" onclick="return archive_request('<?php echo $g_view['request_data'][$i]['job_id'];?>');">Archive</a>
			<?php
		}else{
			?>
			&nbsp;
			<?php
		}
		?>
		</td>
		</tr>
		<?php
	}
}
?>
</table>
<script type="text/javascript">
function archive_request(job_id){
	document.getElementById("archive_job_id").value = job_id;
	document.getElementById("archive_frm").submit();
	return false;
}
</script>
<form id="archive_frm" method="post" action="">
<input type="hidden" name="myaction" value="archive" />
<input type="hidden" id="archive_job_id" name="job_id" value="" />
</form>

<?php
/**************************************************
sng:11/jan/2011
Support for re-run of request
********/
?>
<script type="text/javascript">
function rerun_request(job_id){
	document.getElementById("rerun_job_id").value = job_id;
	document.getElementById("rerun_frm").submit();
	return false;
}
</script>
<form id="rerun_frm" method="post" action="">
<input type="hidden" name="myaction" value="rerun" />
<input type="hidden" id="rerun_job_id" name="job_id" value="" />
</form>
<?php
/*******************************************/
?>
<script>
$('#top_search_option_country').selectmenu();
$('#top_search_option_sector_industry').selectmenu();
$('#top_search_option_deal_type').selectmenu();
</script>