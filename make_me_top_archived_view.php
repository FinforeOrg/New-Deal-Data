<?php
/**************************
sng:29/sep/2011
we now include jquery in container view
<script type="text/javascript" src="js/jquery-1.3.2.js"></script>
*****************************/
?>
<script type="text/javascript" src="js/tooltip.js"></script>
<table width="100%">
<tr>
<td><a href="make_me_top.php">Submitted jobs</a>&nbsp;&nbsp;&nbsp;&nbsp;Archived jobs</td>
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
</tr>
<?php
if($g_view['request_count']==0){
?>
<tr><td colspan="7">None found</td></tr>
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
		</tr>
		<?php
	}
}
?>
</table>
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
<form id="rerun_frm" method="post" action="make_me_top.php">
<input type="hidden" name="myaction" value="rerun" />
<input type="hidden" id="rerun_job_id" name="job_id" value="" />
</form>
<?php
/*******************************************/
?>