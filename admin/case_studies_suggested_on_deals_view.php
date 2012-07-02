<script src="../js/jquery-1.3.2.js" type="text/javascript"></script>
<script src="../js/jquery.form.js" type="text/javascript"></script>
<script type="text/javascript">
jQuery.noConflict()
</script>
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
<td><strong>Case Study</strong></td>
<td colspan="4"><strong>On Deal</strong></td>
<td colspan="3">&nbsp;</td>
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
		<td><?php echo $g_view['data'][$i]['caption'];?><br />[<?php echo $g_view['data'][$i]['filename'];?>]</td>
		<td><?php echo $g_view['data'][$i]['deal_company_name'];?></td>
		<td><?php echo date("M, Y",strtotime($g_view['data'][$i]['date_of_deal']));?></td>
		<td><?php echo $g_view['data'][$i]['value_in_billion'];?></td>
		<td><?php echo $g_view['data'][$i]['deal_cat_name'];?> <?php echo $g_view['data'][$i]['deal_subcat1_name'];?> <?php echo $g_view['data'][$i]['deal_subcat2_name'];?></td>
		<td>
		<form method="post" action="download_case_study.php" target="_blank">
		<input type="hidden" name="case_study_id" value="<?php echo $g_view['data'][$i]['case_study_id'];?>" />
		<input type="submit" name="submit" class="btn_auto" value="Download" />
		</form>
		</td>
		
		<td>
		<form method="post" action="">
		<input type="hidden" name="action" value="approve" />
		<input type="hidden" name="case_study_id" value="<?php echo $g_view['data'][$i]['case_study_id'];?>" />
		<input type="submit" name="submit" value="Approve" />
		</form>
		</td>
		
		<td>
		<input type="button" id="btn_reject" value="Reject" onclick="return open_reject_popup(<?php echo $g_view['data'][$i]['case_study_id'];?>,'<?php echo $g_view['data'][$i]['caption'];?>', '<?php echo $g_view['data'][$i]['deal_company_name'];?>','<?php echo $g_view['data'][$i]['filename'];?>');" />
		</td>
		
		</tr>
		<?php
	}
	?>
	<tr>
	<td colspan="9" style="text-align:right">
	<?php
	if($g_view['start'] > 0){
		$prev_offset = $g_view['start'] - $g_view['num_to_show'];
		?>
		<a href="case_studies_suggested_on_deals.php?start=<?php echo $prev_offset;?>">Prev</a>
		<?php
	}
	if($g_view['data_count'] > $g_view['num_to_show']){
		$next_offset = $g_view['start'] + $g_view['num_to_show'];
		?>
		&nbsp;&nbsp;&nbsp;<a href="case_studies_suggested_on_deals.php?start=<?php echo $next_offset;?>">Next</a>
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
support for reject case study popup
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
	<h1>Reject the Case Study</h1>
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