<script src="../js/jquery-1.3.2.js" type="text/javascript"></script>
<script src="../js/jquery.form.js" type="text/javascript"></script>
<script src="util.js" type="text/javascript"></script>
<script type="text/javascript">
jQuery.noConflict()
</script>
<div>
<form method="get" action="">
<table>
<tr>
<td><input name="filterby" type="checkbox" value="flagged" <?php if($g_view['filterby']=="flagged"){?>checked="checked"<?php }?> /></td>
<td>Show flagged items only</td>
<td>Order by</td>
<td>
<select name="orderby">
<option value="" <?php if($g_view['orderby']==""){?>selected="selected"<?php }?>>Default</option>
<option value="downloaded" <?php if($g_view['orderby']=="downloaded"){?>selected="selected"<?php }?>>Number of times downloaded</option>
<option value="date_uploaded" <?php if($g_view['orderby']=="date_uploaded"){?>selected="selected"<?php }?>>Date uploaded</option>
</select>
</td>
<td><input type="submit" value="Go" /></td>
</tr>
</table>
</form>
</div>
<table width="100%" cellpadding="5" cellspacing="0" border="1" style="border-collapse:collapse;">
<?php
if($g_view['msg']!=""){
?>
<tr>
<td colspan="9"><span class="msg_txt"><?php echo $g_view['msg'];?></span></td>
</tr>
<?php
}
?>
<tr bgcolor="#dec5b3" style="height:20px;">
<td><strong>Mem / Firm</strong></td>
<td></td>
<td><strong>Case Study</strong></td>
<td><strong>Uploaded on</strong></td>
<td><strong>Downloaded</strong></td>
<td><strong>On Deal</strong></td>
<td colspan="3"></td>
</tr>
<?php
if($g_view['data_count']==0){
	?>
	<tr>
	  <td colspan="9">None found</td>
	</tr>
	<?php
}else{
	if($g_view['data_count'] > $g_view['num_to_show']){
		$total = $g_view['num_to_show'];
	}else{
		$total = $g_view['data_count'];
	}
	for($i=0;$i<$total;$i++){
		?>
		<tr>
		<td><?php echo $g_view['data'][$i]['f_name'];?> <?php echo $g_view['data'][$i]['l_name'];?><br /><?php echo $g_view['data'][$i]['partner_name'];?></td>
		<td>
		<?php
		if($g_view['data'][$i]['flag_count'] > 0){
			?>
			<img src="images/icon_red_flag.gif" />&nbsp;<?php echo $g_view['data'][$i]['flag_count'];?><br />
			<input type="button" value="View" onclick="case_study_flags_popup(<?php echo $g_view['data'][$i]['case_study_id'];?>)" /><br />
			<input type="submit" value="Clear" onclick="clear_case_study(<?php echo $g_view['data'][$i]['case_study_id'];?>)" /><br />
			<div id="clear<?php echo $g_view['data'][$i]['case_study_id'];?>"></div>
			<?php
		}
		?>
		</td>
		<?php
		/**************************
		sng:18/nov/2011
		since we have access rules now, we show it, just a hack
		*******************/
		?>
		<td><?php echo $g_view['data'][$i]['caption'];?><br />[<?php echo $g_view['data'][$i]['filename'];?>]<br />[<?php echo $g_view['access_rule_name'][$g_view['data'][$i]['access_rule_code']];?>]</td>
		<td><?php echo $g_view['data'][$i]['uploaded_on'];?></td>
		<td><?php echo $g_view['data'][$i]['download_count'];?></td>
		<td><?php echo $g_view['data'][$i]['deal_company_name'];?><br />
		<?php echo date("M, Y",strtotime($g_view['data'][$i]['date_of_deal']));?><br />
		<?php echo $g_view['data'][$i]['value_in_billion'];?><br />
		<?php echo $g_view['data'][$i]['deal_cat_name'];?> <?php echo $g_view['data'][$i]['deal_subcat1_name'];?> <?php echo $g_view['data'][$i]['deal_subcat2_name'];?>
		</td>
		
		<td>
		<form method="post" action="download_case_study.php" target="_blank">
			<input type="hidden" name="case_study_id" value="<?php echo $g_view['data'][$i]['case_study_id'];?>" />
			<input type="submit" name="submit" class="btn_auto" value="Download" />
			</form>
		</td>
		<td>
		<input type="button" id="btn_reject" value="Delete" onclick="return open_reject_popup(<?php echo $g_view['data'][$i]['case_study_id'];?>,'<?php echo $g_view['data'][$i]['caption'];?>', '<?php echo $g_view['data'][$i]['deal_company_name'];?>','<?php echo $g_view['data'][$i]['filename'];?>');" />
		</td>
		
		<td>
		<?php
			if('n'==$g_view['data'][$i]['is_approved']){
				/*********************
				sng:19/sep/2011
				We are ajaxifying the approval process
				*************************/
				?>
				<input type="button" value="Approve" onclick="approve_case_study(<?php echo $g_view['data'][$i]['case_study_id'];?>);" /><br />
				<div id="approve<?php echo $g_view['data'][$i]['case_study_id'];?>"></div>
				<?php
			}else{
				?>
				<img src="images/approved.png" />
				<?php
			}
			?>
			</td>
		
		
		
		</tr>
		
		<?php
	}
	?>
	<tr>
	<td colspan="9" style="text-align:right">
	<?php
	/********************************
	sng:9/dec/2011
	Support for filter and order in pagination.
	************************/
	$pagination_url_base = "list_case_studies.php?filterby=".$g_view['filterby']."&orderby=".$g_view['orderby'];
	if($g_view['start'] > 0){
		$prev_offset = $g_view['start'] - $g_view['num_to_show'];
		?>
		<a href="<?php echo $pagination_url_base;?>&start=<?php echo $prev_offset;?>">Prev</a>
		<?php
	}
	if($g_view['data_count'] > $g_view['num_to_show']){
		$next_offset = $g_view['start'] + $g_view['num_to_show'];
		?>
		&nbsp;&nbsp;&nbsp;<a href="<?php echo $pagination_url_base;?>&start=<?php echo $next_offset;?>">Next</a>
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
/*****************************
support for delete case study popup
*********************/
?>
<style type="text/css">
#background_case_study_popup{
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
#popup_case_study{
	display:none;
	position:fixed;
	_position:absolute; /* hack for internet explorer 6*/
	height:300px;
	width:408px;
	background:#FFFFFF;
	border:2px solid #cecece;
	z-index:2;
	padding:12px;
	font-size:13px;
}
#popup_case_study_close{
	font-size:14px;
	line-height:14px;
	right:6px;
	top:4px;
	position:absolute;
	color:#6fa5fd;
	font-weight:700;
	display:block;
}
#popup_case_study_result{
	font-size:12px;
	color:#3366FF;
}
</style>
<script type="text/javascript">
var case_study_popup_status = 0;
//loading popup with jQuery magic!
function load_case_study_Popup(){
	//loads popup only if it is disabled
	if(case_study_popup_status==0){
		jQuery("#background_case_study_popup").css({
		"opacity": "0.7"
		});
		jQuery("#background_case_study_popup").fadeIn("slow");
		jQuery("#popup_case_study").fadeIn("slow");
		case_study_popup_status = 1;
	}
}
//disabling popup with jQuery magic!
function disable_case_study_Popup(){
	//disables popup only if it is enabled
	if(case_study_popup_status==1){
		jQuery("#background_case_study_popup").fadeOut("slow");
		jQuery("#popup_case_study").fadeOut("slow");
		case_study_popup_status = 0;
	}
}
//centering popup
function center_case_study_Popup(){
	//request data for centering
	var windowWidth = document.documentElement.clientWidth;
	var windowHeight = document.documentElement.clientHeight;
	var case_study_popupHeight = jQuery("#popup_case_study").height();
	var case_study_popupWidth = jQuery("#popup_case_study").width();
	//centering
	jQuery("#popup_case_study").css({
		"position": "absolute",
		"top": (windowHeight/2-case_study_popupHeight/2),
		"left": windowWidth/2-case_study_popupWidth/2
	});
	//only need force for IE6

	jQuery("#background_case_study_popup").css({
		"height": windowHeight
	});

}


function case_study_rejected(data, statusText)  {
	if (statusText == 'success') {
		if (data.rejected == 'y') {
			jQuery("#popup_case_study_result").html("Case study deleted");
			//reload the page after 3 sec
			setTimeout("disable_case_study_Popup();window.location.reload()",3*1000);
		} else {
			jQuery("#popup_case_study_result").html(data.err);
		}
	} else {
		jQuery("#popup_case_study_result").html('Unknown error!');
	}
}
</script>
<div id="popup_case_study">
	<a id="popup_case_study_close">x</a>
	<h1>Delete the Case Study</h1>
	<div id="case_study_popup_content">
	<div id="case_study_detail"></div>
	<form id="frm_reject_case_study" method="post" action="">
	<input type="hidden" name="case_study_id" id="case_study_id" value="" />
	
	<table width="100%" border="0">
	<tr>
	<td>Reason</td>
	</tr>
	<tr>
	<td><textarea name="reason" id="reason" style="height: 100px; width: 400px;"></textarea></td>
	</tr>
	
	<tr><td><input type="Submit" value="Submit" /></td></tr>
	</table>
	</form>
	</div>
	<div id="popup_case_study_result"></div>
</div>
<div id="background_case_study_popup"></div>
<script type="text/javascript">
//LOADING POPUP
function open_reject_popup(case_study_id,case_study_name, case_study_firm,case_study_file){
	//set the case study id
	document.getElementById("case_study_id").value = case_study_id;
	document.getElementById("case_study_detail").innerHTML = case_study_name+" ["+case_study_file+"]<br />for: "+case_study_firm;
	//centering with css
	center_case_study_Popup();
	//load popup
	load_case_study_Popup();
	return false;
}

//CLOSING POPUP
//Click the x event!
jQuery("#popup_case_study_close").click(function(){
	disable_case_study_Popup();
});
//Click out event!
jQuery("#background_case_study_popup").click(function(){
	disable_case_study_Popup();
});

jQuery(function() {
	var options = {
		success:       case_study_rejected,
		url:       'ajax/reject_case_study.php',  // your upload script
		dataType:  'json'
	};
	jQuery('#frm_reject_case_study').submit(function() {
		//alert("upload");
		jQuery("#popup_case_study_result").html("processing...");
		jQuery(this).ajaxSubmit(options);
		return false;
	});
	
	
	///////////////////
});
</script>
<script>
function approve_case_study(case_study_id){
	jQuery('#approve'+case_study_id).html('sending...');
	jQuery.post('ajax/approve_case_study.php',
	{case_study_id: case_study_id},
	function(data){
		if(data.approved=='y'){
			jQuery('#approve'+case_study_id).html('approved');
		}else{
			jQuery('#approve'+case_study_id).html(data.err);
		}
		//refresh
		setTimeout("window.location.reload()",3*1000);
	},
	'json');
}
function clear_case_study(case_study_id){
	jQuery('#clear'+case_study_id).html('clearing...');
	jQuery.post('ajax/clear_case_study.php',
	{case_study_id: case_study_id},
	function(data){
		if(data.cleared=='y'){
			jQuery('#clear'+case_study_id).html('cleared');
		}else{
			jQuery('#clear'+case_study_id).html(data.err);
		}
		//refresh
		setTimeout("window.location.reload()",3*1000);
	},
	'json');
}
</script>