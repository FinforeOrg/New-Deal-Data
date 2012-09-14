<?php
/*********
show the discussion, subjected to restriction, the ajax loading code also check this

sng:14/sep/2012
put an empty implementation of update_discussion if NOT show_discussion.
Otherwise the function is not found when the tab is clicked (and the function is invoked)
**********/
if(!$g_view['show_discussion']){
	?>
	<script>
	function update_discussion(){
	}
	</script>
	<p>These pages are only available to the actual deal participants (banks and law firms) and data providers.</p>
	<?php
	return;
}
?>
<link rel="stylesheet" href="css/deal_discussion.css" type="text/css" />
<script>
function update_discussion(){
	$.blockUI({ message: '<h1>updating...</h1><img src="/images/loader.gif" />' });
	$.get(
		'ajax/fetch_deal_discussion.php?deal_id=<?php echo $g_view['deal_id'];?>',
		function(data) {
			$.unblockUI();
			$('#deal_discussion_content').html(data);
		}
	)
}

function flag_posting(postingid){
	var id = "#flag_result_"+postingid;
	$.post(
		'ajax/flag_deal_discussion_comment.php',
		{'posting_id' : postingid,'transaction_id' : '<?php echo $g_view['deal_id'];?>'},
		function(data) {
			$(id).html('');
			update_discussion();
		}
	)
	$(id).html('flagging...');
}
</script>
<script>
function add_deal_discussion_to_watch_list(this_deal_id){
	jQuery("#stat_add_deal_discussion_to_watch_list").html("adding...");
	jQuery.get(
		'ajax/add_deal_discussion_to_watchlist.php?deal_id='+this_deal_id,
		function(data) {
			if(data.status==0){
				jQuery('#stat_add_deal_discussion_to_watch_list').html(data.reason);
			}else{
				jQuery('#stat_add_deal_discussion_to_watch_list').html('Added to watchlist');
				jQuery('#btn_add_discussion_to_watchlist').remove();
			}
		},
		"json"
	)
}
</script>
<table width="100%" cellpadding="0" cellspacing="0" border="0">
<tr>
<td colspan="3"><input type="button" class="btn_auto" value="Post Your Question" onClick="return open_discussion_posting_popup(0);" /></td>
<td style="text-align:right"><span id="stat_add_deal_discussion_to_watch_list" class="msg_txt"></span><input type="button" class="btn_auto" value="Watch this discussion" id="btn_add_discussion_to_watchlist" onclick="return add_deal_discussion_to_watch_list(<?php echo $g_view['deal_id'];?>);" /></td>
</tr>
</table>
<!--existing comments-->
<div id="deal_discussion_content">

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
			update_discussion();
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
<script>
/******************************
sng:18/july/2011
This is now loaded with the detail page. That means, the script is fired when the overview tab is shown.
But we need to fire this only when this tab has focus. So we trap the spry showPanel
to check if this tab is about to be shown and only then we trigger the script.

Therefore, the code is not needed here
//update_discussion();
************************************/
</script>