<?php
/***********************
sng:17/nov/2011
This is now ajaxified
**************************/
?>
<script>
function update_case_study(){
	$.blockUI({ message: '<h1>updating...</h1><img src="/images/loader.gif" />' });
	$.get(
		'ajax/fetch_case_study.php?deal_id=<?php echo $g_view['deal_id'];?>',
		function(data) {
			$.unblockUI();
			$('#case_study_content').html(data);
		}
	)
}
</script>
<div id="case_study_content">

</div>
<table width="100%" cellpadding="0" cellspacing="0">
<?php
if($g_view['can_upload_case_study']){
	?>
	<tr><td><input type="button" class="btn_auto" id="btn_case_study" value="Submit case study" /></td></tr>
	<?php
}else{
	?>
	<tr><td>Your firm was not involved in the deal, so you cannot upload a case study.</td></tr>
	<?php
}
?>
</table>
<?php
/*************************************************
sng:9/mar/2011
support for submit case study popup
**/
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
	height:270px;
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
		/***********
		sng:17/nov/2011
		re-fetch case studies
		*****************/
		update_case_study();
	}
}
//centering popup
function center_case_study_Popup(){
	//request data for centering
	var windowWidth = $(window).width();
	var windowHeight = $(window).height();
	var case_study_popupHeight = jQuery("#popup_case_study").height();
	var case_study_popupWidth = jQuery("#popup_case_study").width();
	//centering
   jQuery("#popup_case_study").css({
       "position": "absolute",
       "top": (windowHeight/2-case_study_popupHeight/2 + $(window).scrollTop()),
       "left": windowWidth/2-case_study_popupWidth/2
   });
   //only need force for IE6

   jQuery("#background_case_study_popup").css({
       "height": windowHeight
   });

}

function validate_case_study(formData, jqForm, options) {
	if(jQuery("#caption").val()==""){
		jQuery("#popup_case_study_result").html("Please specify a caption.");
		return false;
	}
	var fileToUploadValue = jQuery('#case_study_file').fieldValue();
	if (!fileToUploadValue[0]) {
		jQuery("#popup_case_study_result").html("Please select a file.");
		return false;
	} 

	return true;
}
function case_study_uploaded(data, statusText)  {
	if (statusText == 'success') {
		if (data.uploaded == 'y') {
			jQuery("#popup_case_study_result").html("Case study uploaded");
		} else {
			jQuery("#popup_case_study_result").html(data.error);
		}
	} else {
		jQuery("#popup_case_study_result").html('Unknown error!');
	}
}
</script>
<div id="popup_case_study">
	<a id="popup_case_study_close">x</a>
	<h1>Submit a Case Study</h1>
	<div id="case_study_popup_content">
	<form id="frm_upload_case_study" method="post" action="" enctype="multipart/form-data">
	<input type="hidden" name="transaction_id" value="<?php echo $g_view['deal_id'];?>" />
	<?php
	if($_SESSION['member_type']=="banker") $case_study_partner_type = "bank";
	else $case_study_partner_type = "law firm";
	//we also use the id of the firm where this member works
	?>
	<input name="partner_type" type="hidden" value="<?php echo $case_study_partner_type;?>" />
	<input name="partner_id" type="hidden" value="<?php echo $_SESSION['company_id'];?>" />
	<table width="100%" border="0">
	<tr>
	<td>Caption</td>
	</tr>
	<tr>
	<td><input type="text" name="caption" id="caption" style="width: 400px;" /></td>
	</tr>
	<tr>
	<td>File</td>
	</tr>
	<tr>
	<td><input type="file" name="case_study_file" id="case_study_file" style="width: 400px;" /><br />
	Only <?php echo implode(" or ",$g_view['valid_file_extensions_for_case_study_doc']);?> files are accepted</td>
	</tr>
	<?php
	/******************
	sng:18/nov/2011
	We now have rules that specify who can see the case study
	**/
	foreach($g_view['case_study_view_rules'] as $rule){
		?>
		<tr><td><input type="radio" name="access_rule_code" value="<?php echo $rule['rule_code'];?>" <?php if($rule['is_default']==1){?>checked="checked"<?php }?> />&nbsp;<?php echo $rule['rule_name'];?></td></tr>
		<?php
	}
	?>
	<tr><td><input type="Submit" value="Send" class="btn_auto" /></td></tr>
	</table>
	</form>
	</div>
	<div id="popup_case_study_result"></div>
</div>
<div id="background_case_study_popup"></div>
<script type="text/javascript">
//LOADING POPUP
//Click the button event!

jQuery("#btn_case_study").click(function(){
	//centering with css
	center_case_study_Popup();
	//load popup
	load_case_study_Popup();
	return false;
});
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
		beforeSubmit:  validate_case_study,
		success:       case_study_uploaded,
		url:       'ajax/upload_case_study.php',  // your upload script
		dataType:  'json'
	};
	jQuery('#frm_upload_case_study').submit(function() {
		//alert("upload");
		jQuery("#popup_case_study_result").html("uploading...");
		jQuery(this).ajaxSubmit(options);
		return false;
	});
	
	
	///////////////////
});
</script>
<?php
/***********************************************************/
?>
<?php
/**************************************************************
sng:19/nov/2011
support for flag case study popup
**/
?>
<style type="text/css">
#background_flag_case_study_popup{
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
#popup_flag_case_study{
	display:none;
	position:fixed;
	_position:absolute; /* hack for internet explorer 6*/
	height:270px;
	width:408px;
	background:#FFFFFF;
	border:2px solid #cecece;
	z-index:2;
	padding:12px;
	font-size:13px;
}
#popup_flag_case_study_close{
	font-size:14px;
	line-height:14px;
	right:6px;
	top:4px;
	position:absolute;
	color:#6fa5fd;
	font-weight:700;
	display:block;
}
#popup_flag_case_study_result{
	font-size:12px;
	color:#3366FF;
}
</style>
<div id="popup_flag_case_study">
	<a id="popup_flag_case_study_close">x</a>
	<h1>Flag the Case Study</h1>
	<div id="flag_case_study_popup_content">
	<form id="frm_flag_case_study" method="post" action="" enctype="multipart/form-data">
	<input type="hidden" name="case_study_id" id="flag_case_study_id" value="" />
	
	<table width="100%" border="0">
	<tr>
	<td>Reason</td>
	</tr>
	<tr>
	<td><textarea name="flag_reason" id="flag_reason" style="width:400px; height:100px;"></textarea></td>
	</tr>
	
	<tr><td><input type="Submit" value="Send" class="btn_auto" /></td></tr>
	</table>
	</form>
	</div>
	<div id="popup_flag_case_study_result"></div>
</div>
<div id="background_flag_case_study_popup"></div>
<script>
var flag_case_study_popup_status = 0;
//centering popup
function center_flag_case_study_Popup(){
	//request data for centering
	var windowWidth = $(window).width();
	var windowHeight = $(window).height();
	var case_study_popupHeight = jQuery("#popup_case_study").height();
	var case_study_popupWidth = jQuery("#popup_case_study").width();
	//centering
   jQuery("#popup_flag_case_study").css({
       "position": "absolute",
       "top": (windowHeight/2-case_study_popupHeight/2 + $(window).scrollTop()),
       "left": windowWidth/2-case_study_popupWidth/2
   });
   //only need force for IE6

   jQuery("#background_flag_case_study_popup").css({
       "height": windowHeight
   });

}
//loading popup with jQuery magic!
function load_flag_case_study_Popup(){
	//loads popup only if it is disabled
	if(flag_case_study_popup_status==0){
		jQuery("#background_flag_case_study_popup").css({
		"opacity": "0.7"
		});
		jQuery("#background_flag_case_study_popup").fadeIn("slow");
		jQuery("#popup_flag_case_study").fadeIn("slow");
		flag_case_study_popup_status = 1;
	}
}
//disabling popup with jQuery magic!
function disable_flag_case_study_Popup(){
	//disables popup only if it is enabled
	if(flag_case_study_popup_status==1){
		jQuery("#background_flag_case_study_popup").fadeOut("slow");
		jQuery("#popup_flag_case_study").fadeOut("slow");
		flag_case_study_popup_status = 0;
		update_case_study();
	}
}
</script>
<script>
//LOADING POPUP
function open_flag_popup(case_study_id){
	//centering with css
	center_flag_case_study_Popup();
	
	//load popup
	load_flag_case_study_Popup();
	//set the hidden field
	jQuery('#flag_case_study_id').val(case_study_id);
	return false;
}
//CLOSING POPUP
//Click the x event!
jQuery("#popup_flag_case_study_close").click(function(){
	disable_flag_case_study_Popup();
});
//Click out event!
jQuery("#background_flag_case_study_popup").click(function(){
	disable_flag_case_study_Popup();
});
jQuery(function() {
	var flag_options = {
		beforeSubmit:  validate_flag_case_study,
		success:       flag_case_study_submitted,
		url:       'ajax/flag_case_study.php',  // your upload script
		dataType:  'json'
	};
	jQuery('#frm_flag_case_study').submit(function() {
		//alert("upload");
		jQuery("#popup_flag_case_study_result").html("submitting...");
		jQuery(this).ajaxSubmit(flag_options);
		return false;
	});
	
	
	///////////////////
});
</script>
<script>
function validate_flag_case_study(formData, jqForm, options) {
	if(jQuery("#flag_reason").val()==""){
		jQuery("#popup_flag_case_study_result").html("Please specify a reason.");
		return false;
	}
	return true;
}
function flag_case_study_submitted(data, statusText)  {
	if (statusText == 'success') {
		if (data.submitted == 'y') {
			jQuery("#popup_flag_case_study_result").html("Case study flagged");
			//close
			disable_flag_case_study_Popup();
		} else {
			jQuery("#popup_flag_case_study_result").html(data.error);
		}
	} else {
		jQuery("#popup_flag_case_study_result").html('Unknown error!');
	}
}
</script>
<?php
/***********************************************************/
?>