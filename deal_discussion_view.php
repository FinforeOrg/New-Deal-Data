<script src="js/jquery.form.js" type="text/javascript"></script>
<link rel="stylesheet" href="css/deal_discussion.css" type="text/css" />  
<script>
function flag_posting(postingid){
	var id = "#flag_result_"+postingid;
	$.post(
		'ajax/flag_deal_discussion_comment.php',
		{'posting_id' : postingid,'transaction_id' : '<?php echo $g_view['deal_id'];?>'},
		function(data) {
			$(id).html('');
			window.location.replace('deal_discussion.php?deal_id=<?php echo $g_view['deal_id'];?>');
		}
	)
	$(id).html('flagging...');
}
</script>
<?php
if(!$g_view['deal_found']){
	?>
	<tr><td>Deal data not found</td></tr>
	<?php
	return;
}
?>
<!--deal company, value data-->
<table width="100%" cellpadding="0" cellspacing="0" border="0">
<tr>
<td>
<h4><?php echo $g_view['deal_data']['company_name'];?> (<?php echo $g_view['deal_data']['deal_cat_name'];?>)</h4>
</td>
<td>
<h4><?php if($g_view['deal_data']['value_in_billion']==0) echo "Value not disclosed"; else echo "$".convert_billion_to_million_for_display($g_view['deal_data']['value_in_billion'])."m";?></h4>
</td>
<td>
<h4><?php echo date("jS M Y",strtotime($g_view['deal_data']['date_of_deal']));?></h4>
</td>
</tr>
<tr>
<td colspan="3"><input type="button" class="btn_auto" value="Post Your Question" onClick="return open_discussion_posting_popup(0);" /></td>
</tr>
</table>
<!--deal company, value data-->
<!--existing comments-->
<div id="deal_discussion_content">
<?php
if($g_view['data_count'] == 0){
	?>
	None yet
	<?php
}else{
	$curr_tree = $g_view['data'][0]['tree'];
	?>
	<div class="deal_discussion_block">
	<?php
	for($i=0;$i<$g_view['data_count'];$i++){
		$poster_email_tokens = explode("@",$g_view['data'][$i]['work_email']);
		$poster_email = "@".$poster_email_tokens[1];
		
		$use_class = "deal_discussion_posting";
		if(0 != $g_view['data'][$i]['parent_posting_id']){
			$use_class = "deal_discussion_reply";
		}
		if($g_view['data'][$i]['tree']!=$curr_tree){
			$curr_tree = $g_view['data'][$i]['tree'];
			?>
			</div>
			<div class="deal_discussion_block">
			<?php
			
		}
		?>
		<div class="<?php echo $use_class;?>">
			<?php
			if($g_view['data'][$i]['flag_count'] > 0){
				?>
				<div style="float:left;"><img src="images/icon_red_flag.gif" /></div>
				<?php
			}
			?>
			<div><?php echo nl2br($g_view['data'][$i]['posting_txt']);?></div>
			<div><?php echo $poster_email;?> on <?php echo date("jS M Y",strtotime($g_view['data'][$i]['posted_on']));?></div>
			<div class="deal_discussion_toolbar">
				<?php
				/*****************
				if this is a top level posting, it means, it is a question. Show the reply button along with the 'flag as inappropriate' button
				**********/
				if(0 == $g_view['data'][$i]['parent_posting_id']){
					?>
					<input type="button" class="btn_auto" value="Post Reply" onClick="return open_discussion_posting_popup(<?php echo $g_view['data'][$i]['posting_id'];?>);" />&nbsp;&nbsp;&nbsp;&nbsp;
					<?php
				}
				?>
				<input type="button" class="btn_auto" value="Flag" onClick="return flag_posting(<?php echo $g_view['data'][$i]['posting_id'];?>);" />&nbsp;<span id="flag_result_<?php echo $g_view['data'][$i]['posting_id'];?>"></span>
			</div>
		</div>
		<?php
	}
	?>
	</div>
	<?php
}
?>
</div>
<!--existing comments-->
<?php
/*****************************************************************
post a comment
***/
?>
<style type="text/css">
#background_discussion_popup{
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
#popup_discussion{
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
#popup_discussion_close{
	font-size:14px;
	line-height:14px;
	right:6px;
	top:4px;
	position:absolute;
	color:#6fa5fd;
	font-weight:700;
	display:block;
}
#popup_discussion_result{
	font-size:12px;
	color:#3366FF;
}
</style>
<script type="text/javascript">
var discussion_popup_status = 0;
//loading popup with jQuery magic!
function load_discussion_Popup(){
	//loads popup only if it is disabled
	if(discussion_popup_status==0){
		jQuery("#background_discussion_popup").css({
		"opacity": "0.7"
		});
		jQuery("#background_discussion_popup").fadeIn("slow");
		jQuery("#popup_discussion").fadeIn("slow");
		discussion_popup_status = 1;
	}
}
//disabling popup with jQuery magic!
function disable_discussion_Popup(){
	//disables popup only if it is enabled
	if(discussion_popup_status==1){
		jQuery("#background_discussion_popup").fadeOut("slow");
		jQuery("#popup_discussion").fadeOut("slow");
		discussion_popup_status = 0;
	}
}
//centering popup
function center_discussion_Popup(){
	//request data for centering
	var windowWidth = $(window).width();
	var windowHeight = $(window).height();
	var discussion_popupHeight = jQuery("#popup_discussion").height();
	var discussion_popupWidth = jQuery("#popup_discussion").width();
	//centering
	jQuery("#popup_discussion").css({
		"position": "absolute",
		"top": (windowHeight/2-discussion_popupHeight/2 + $(window).scrollTop()),
		"left": windowWidth/2-discussion_popupWidth/2
	});
	//only need force for IE6

	jQuery("#background_discussion_popup").css({
		"height": windowHeight
	});

}

function validate_discussion_posting(formData, jqForm, options) {
	if(jQuery("#posting_txt").val()==""){
		jQuery("#popup_discussion_result").html("Please specify your comment.");
		return false;
	}
	return true;
}
function discussion_posting_uploaded(data, statusText)  {
	if (statusText == 'success') {
		if (data.posted == 'y') {
			jQuery("#popup_discussion_result").html("Comment posted");
			disable_discussion_Popup();
			/***
			hopefully this will prevent the alert 'this page has to be posted with prev data again' and thereby posting same thing twice
			***/
			window.location.replace('deal_discussion.php?deal_id=<?php echo $g_view['deal_id'];?>');
		} else {
			jQuery("#popup_discussion_result").html(data.error);
		}
	} else {
		jQuery("#popup_discussion_result").html('Unknown error!');
	}
}
</script>
<div id="popup_discussion">
	<a id="popup_discussion_close">x</a>
	<h1>Post Your Comment</h1>
	<div id="discussion_popup_content">
	<form id="frm_discussion_post_comment" method="post" action="">
	<input type="hidden" name="transaction_id" value="<?php echo $g_view['deal_id'];?>" />
	<input type="hidden" name="parent_posting_id" id="parent_posting_id" value="0" />
	<table width="100%" border="0">
	<tr>
	<td><textarea id="posting_txt" name="posting_txt" style="width:400px; height: 200px;"></textarea></td>
	</tr>
	
	<tr><td><input type="Submit" value="Send" class="btn_auto" /></td></tr>
	</table>
	</form>
	</div>
	<div id="popup_discussion_result"></div>
</div>
<div id="background_discussion_popup"></div>
<script type="text/javascript">
//LOADING POPUP
function open_discussion_posting_popup(posting_parent_id){
	document.getElementById("parent_posting_id").value = posting_parent_id;
	//centering with css
	center_discussion_Popup();
	//load popup
	load_discussion_Popup();
	return false;
}
//CLOSING POPUP
//Click the x event!
jQuery("#popup_discussion_close").click(function(){
	disable_discussion_Popup();
});
//Click out event!
jQuery("#background_discussion_popup").click(function(){
	disable_discussion_Popup();
});

jQuery(function() {
	var options = {
		beforeSubmit:  validate_discussion_posting,
		success:       discussion_posting_uploaded,
		url:       'ajax/post_deal_discussion_comment.php',  // your upload script
		dataType:  'json'
	};
	jQuery('#frm_discussion_post_comment').submit(function() {
		jQuery("#popup_discussion_result").html("submitting...");
		jQuery(this).ajaxSubmit(options);
		return false;
	});
	
	
	///////////////////
});
</script>