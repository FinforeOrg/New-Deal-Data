<?php
/*************************
sng:1/oct/2011
we now put these in the container view
<script src="js/jquery-1.4.4.min.js" type="text/javascript"></script>
**********************************/
?>
<script type="text/javascript">
function download_league_table_detail(){
	var frm_obj = document.getElementById('league_table_filter');
	frm_obj.action = "download_league_table_detail.php";
	frm_obj.target = "_blank";
}
</script>
<table width="100%" cellpadding="0" cellspacing="0">
<tr>
<td><h1>Make Me Top Result Chart</h1></td>
<td style="text-align:right;"><a href="" id="share_chart" class="link_as_button">Share</a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="#" class="link_as_button" onclick="return go_page('make_me_top_matches.php?job_id=<?php echo $g_view['job_id'];?>&start=<?php echo $g_view['back_start_offset'];?>');">Back to Make Me Top Results</a></td>
</tr>
</table>
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
</form>
<?php
if(!$g_view['result_found']){
	return;
}
?>
<br /><br />
<table width="100%" cellpadding="0" cellspacing="5">
<tr>
<td>
<!--the parameters-->
<table width="100%" cellpadding="0" cellspacing="0" class="company">
<tr>
<td colspan="2">Your firm is #<?php echo $g_view['result_data']['rank_of_firm'];?> based on the following parameters</td>
</tr>
<tr>
<td>Country</td>
<td><?php echo $g_view['result_data']['country_name'];?></td>
</tr>

<tr>
<td>Sector/Industry</td>
<td><?php echo $g_view['result_data']['sector_name'];?></td>
</tr>

<tr>
<td>Deal Type</td>
<td><?php echo $g_view['result_data']['deal_name'];?></td>
</tr>

<tr>
<td>Size</td>
<td><?php echo $g_view['result_data']['size_name'];?></td>
</tr>

<tr>
<td>Date</td>
<td><?php echo $g_view['result_data']['date_name'];?></td>
</tr>

<tr>
<td>Ranking Criteria</td>
<td>
<?php
if($g_view['result_data']['ranking_criteria']=="num_deals") echo "Total number of tombstones";
if($g_view['result_data']['ranking_criteria']=="total_deal_value") echo "Total tombstone value";
if($g_view['result_data']['ranking_criteria']=="total_adjusted_deal_value") echo "Total adjusted value";
?>
</td>
</tr>
</table>
<!--the parameters-->
</td>
<td style="width:50%;" align="right">
<!--the chart-->
<div id="chart1" style="width: 90%; float: right;">
    <?php echo $chartHtml?> 
</div> 
<? /*
<img src="league_table_renderer.php?t=<?php echo time();?>" />
*/ ?>
<!--the chart-->
</td>
</tr>
</table>
<br /><br />
<table width="100%" cellpadding="0" cellspacing="0" class="company">
<tr>
<th>rank</th>
<th>Firm</th>
<th>
<?php 
if($g_view['result_data']['ranking_criteria']=="num_deals") echo "#tombstones";else echo "in million $";
?></th>
</tr>
<?php
if($g_view['search_result_firms_count']==0){
	?>
	<tr><td colspan="3">None found</td></tr>
	<?php
}else{
	for($i=0;$i<$g_view['search_result_firms_count'];$i++){
		?>
		<tr>
		<td><?php echo $i+1;?></td>
		<td><?php echo $g_view['search_result_firms'][$i]['firm_name'];?></td>
		<td>
		<?php
		if($g_view['result_data']['ranking_criteria']!="num_deals"){
			echo convert_billion_to_million_for_display_round($g_view['search_result_firms'][$i]['stat_value']);
		}else{
			//number of deals, just show the number
			echo $g_view['search_result_firms'][$i]['stat_value'];
		}
		?>		</td>
		</tr>
		<?php
	}
}
?>
</table>
<br /><br />
<?php
/***
sng:31/aug/2010
need a download to excel feature for the result displayed
***/
?>
<table width="100%">
<tr>
<td width="50%">&nbsp;</td>
<td style="text-align:right;">
<form method="post" action="download_make_me_top_result.php" target="_blank">
<input type="hidden" name="job_id" value="<?php echo $g_view['job_id'];?>" />
<input type="hidden" name="search_result_id" value="<?php echo $g_view['search_result_id'];?>" />
<input type="submit" name="download" class="btn_auto" value="Download to Excel" />
</form>
</td>
<td style="text-align:right;">
<?php
/***
sng:20/sep/2010
sng:4/oct/2010
client wants 100
*******/
$num_deals_download = 100;
?>
<form method="post" action="download_make_me_top_result_deals.php" target="_blank">
<input type="hidden" name="number_of_deals" value="<?php echo $num_deals_download;?>"  />
<input type="hidden" name="job_id" value="<?php echo $g_view['job_id'];?>" />
<input type="hidden" name="search_result_id" value="<?php echo $g_view['search_result_id'];?>" />
<input type="submit" name="download" class="btn_auto" value="Download the largest <?php echo $num_deals_download;?> deals to Excel" />
</form>
</td>
</tr>
</table>
<style type="text/css">
#background_popup{
	display:none;
	position:fixed;
	_position:absolute; /* hack for internet explorer 6*/
	height:100%;
	width:100%;
	top:0;
	left:0;
	background:#000000;
	border:1px solid #cecece;
	z-index:1;
}
#popup_share{
	display:none;
	position:fixed;
	_position:absolute; /* hack for internet explorer 6*/
	height:384px;
	width:408px;
	background:#FFFFFF;
	border:2px solid #cecece;
	z-index:2;
	padding:12px;
	font-size:13px;
}
#popup_share_close{
	font-size:14px;
	line-height:14px;
	right:6px;
	top:4px;
	position:absolute;
	color:#6fa5fd;
	font-weight:700;
	display:block;
}
#popup_result{
	font-size:12px;
	color:#3366FF;
}
</style>
<script type="text/javascript">
var share_popup_status = 0;
//loading popup with jQuery magic!
function loadPopup(){
	//loads popup only if it is disabled
	if(share_popup_status==0){
		$("#background_popup").css({
		"opacity": "0.7"
		});
		$("#background_popup").fadeIn("slow");
		$("#popup_share").fadeIn("slow");
		share_popup_status = 1;
	}
}
//disabling popup with jQuery magic!
function disablePopup(){
	//disables popup only if it is enabled
	if(share_popup_status==1){
		$("#background_popup").fadeOut("slow");
		$("#popup_share").fadeOut("slow");
		share_popup_status = 0;
	}
}
//centering popup
function centerPopup(){
	//request data for centering
	var windowWidth = document.documentElement.clientWidth;
	var windowHeight = document.documentElement.clientHeight;
	var popupHeight = $("#popup_share").height();
	var popupWidth = $("#popup_share").width();
	//centering
	$("#popup_share").css({
		"position": "absolute",
		"top": windowHeight/2-popupHeight/2,
		"left": windowWidth/2-popupWidth/2
	});
	//only need force for IE6

	$("#background_popup").css({
		"height": windowHeight
	});

}
$(document).ready(function(){
	//LOADING POPUP
	//Click the button event!
	$("#share_chart").click(function(){
		//centering with css
		centerPopup();
		//load popup
		loadPopup();
		return false;
	});
	//CLOSING POPUP
	//Click the x event!
	$("#popup_share_close").click(function(){
		disablePopup();
	});
	//Click out event!
	$("#background_popup").click(function(){
		disablePopup();
	});
	//Press Escape event!
	$(document).keypress(function(e){
		if(e.keyCode==27 && popupStatus==1){
			disablePopup();
		}
	});
});
</script>
<script type="text/javascript">
function share_chart(){
	$("#popup_result").html("sending...");
	$.post(
        'ajax/share_chart.php',
        $("#share_chart_frm").serialize(),
        function(response) {
            $("#popup_result").html(response);
        }
    )
	return false;
}
</script>
<div id="popup_share">
	<a id="popup_share_close">x</a>
	<h1>Share this chart</h1>
	<div id="popup_content">
	<form method="post" action="#" onsubmit="return share_chart();" id="share_chart_frm">
	<table width="100%" border="0">
	<tr>
	<td>Enter the e-mail adresses to share this chart. (separated by comma)</td>
	</tr>
	<tr>
	<td><textarea name="email_addresses" id="email_addresses" cols="45" rows="5" style="width: 400px;"></textarea></td>
	</tr>
	<tr>
	<td>Message</td>
	</tr>
	<?php
	$txt_msg = "Please see the chart which we are considering for the presentation at ".$g_http_path."/view_make_me_top_match_chart.php?result_id=".$g_view['search_result_id']."&job_id=".$g_view['job_id']."\r\nSent by myTombstones on behalf of ".$_SESSION['f_name']." ".$_SESSION['l_name'];
	?>
	<tr>
	<td><textarea name="email_message" id="email_message" cols="45" rows="5" style="width: 400px;"><?php echo $txt_msg;?></textarea></td>
	</tr>
	<tr><td><input type="submit" name="submit" value="Share" class="btn_auto" /></td></tr>
	</table>
	</form>
	</div>
	<div id="popup_result"></div>
</div>
<div id="background_popup"></div>
<?php
/**************
sng:10/oct/2011
We now have placed the jqplot js files in content_view
***************/
?>
<script src="/js/scripts.js" type="text/javascript" charset="utf-8"></script>
   
