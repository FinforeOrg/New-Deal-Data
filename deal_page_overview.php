<script src="js/logo_preference.js"></script>
<table width="100%" cellpadding="0" cellspacing="0">
<tr>
<td style="width:200px;">
<!--tombstone-->
<?php
$g_trans->get_tombstone_from_deal_id($g_view['deal_data']['deal_id']);
?>
<!--tombstone-->
<?php
/***********************************************************************
sng:20/july/2010
If the logo is not present, we show the link to send logo suggestion.
This is only for logged in member

sng:12/sep/2012
Now we have one or more participants for a deal, each with its own logo that is to be shown in the tombstone.
If logo for some company is missing, we tell the user to suggest a logo for the company by editing the company data or better
we do not check but show the message anyway

Better not show anything here.
***/
?>


</td>
<td style="width:60px;">&nbsp;</td>
<td>
<!--the overview-->
<div id="deal_overview">
<?php
if($g_view['deal_data']['deal_cat_name'] == "M&A") require("deal_overview_ma.php");
elseif((strtolower($g_view['deal_data']['deal_cat_name'])=="debt")&&(strtolower($g_view['deal_data']['deal_subcat1_name'])=="bond")) require("deal_overview_bond.php");
elseif((strtolower($g_view['deal_data']['deal_cat_name'])=="debt")&&(strtolower($g_view['deal_data']['deal_subcat1_name'])=="loan")) require("deal_overview_loan.php");
elseif((strtolower($g_view['deal_data']['deal_cat_name'])=="equity")&&(strtolower($g_view['deal_data']['deal_subcat1_name'])=="equity")&&(strtolower($g_view['deal_data']['deal_subcat2_name'])=="rights issue")) require("deal_overview_equity_rights.php");
elseif((strtolower($g_view['deal_data']['deal_cat_name'])=="equity")&&(strtolower($g_view['deal_data']['deal_subcat1_name'])=="equity")&&(strtolower($g_view['deal_data']['deal_subcat2_name'])=="ipo")) require("deal_overview_equity_ipo.php");
elseif((strtolower($g_view['deal_data']['deal_cat_name'])=="equity")&&(strtolower($g_view['deal_data']['deal_subcat1_name'])=="equity")&&(strtolower($g_view['deal_data']['deal_subcat2_name'])=="additional")) require("deal_overview_equity_additional.php");
elseif((strtolower($g_view['deal_data']['deal_cat_name'])=="equity")&&(strtolower($g_view['deal_data']['deal_subcat1_name'])=="preferred")) require("deal_overview_equity_preferred.php");
elseif((strtolower($g_view['deal_data']['deal_cat_name'])=="equity")&&(strtolower($g_view['deal_data']['deal_subcat1_name'])=="convertible")) require("deal_overview_equity_convertible.php");
?>
</div>
<!--the overview-->
</td>
</tr>
<tr>
<td colspan="3">
	<table width="100%" cellpadding="0" cellspacing="0">
		<!--notes-->
		<tr><td class="deal_page_h2">Notes:</td></tr>
		<tr><td><?php if($g_view['deal_data']['note']!="") echo nl2br($g_view['deal_data']['note']); else echo "None available";?></td></tr>
		<!--notes-->
		<!--sources-->
		<tr><td class="deal_page_h2">Sources:</td></tr>
		<tr><td>
		<?php
		/***
		sng:8/jul/2010
		If sources are present, we show the sources section.
		sources are just urls separated by comma, so, we split and show in a list as hyperlinked item.
		The page will open in new window
		
		sng:29/feb/2012
		It may happen that the url may contain ','. In that case, there will be another token.
		What we do is, when we check the current token, we check the next token (if there is one) and
		if that one does not start with http, we assume that the next token is part of the current one
		(the case of , in url). We just join those with a , and skip the next token.
		
		sng:2/may/2012
		We do not store source urls as csv now. We store each record in a single row.
		So all we have to do is count the array and generate html
		*********/
		$source_cnt = count($g_view['deal_data']['sources']);
		if($source_cnt > 0){
			?>
			<ol>
			<?php
			for($source_i=0;$source_i<$source_cnt;$source_i++){
				$source = trim($g_view['deal_data']['sources'][$source_i]['source_url']);
				?>
				<li><a href="<?php echo $source;?>" target="_blank"><?php echo $source;?></a></li>
				<?php
			}
			?>
			</ol>
			<?php
		}else{
			?>
			None available
			<?php
		}
		?>
		</td></tr>
		<!--sources-->
		<!--Documents-->
		<tr><td class="deal_page_h2">Documents:</td></tr>
		<?php
		/*******************
		sng:22/feb/2012
		We now make it like case study, with icon for approved docs and ability to flag a deal doc
		and flag count
		***********************/
		
		$doc_count = count($g_view['deal_data']['docs']);
		?>
		<tr>
		<td>
		<?php
		if(0 == $doc_count){
			?>None available<?php
		}else{
			?>
			<table cellpadding="5" cellspacing="5" class="company">
			<?php
			for($dd = 0;$dd < $doc_count;$dd++){
				?>
				<tr>
				<td style="width:50px;">
				<?php if($g_view['deal_data']['docs'][$dd]['flag_count'] > 0){?><img src="images/icon_red_flag.gif" />&nbsp;<?php echo $g_view['deal_data']['docs'][$dd]['flag_count']; }?>
				</td>
				<td style="width:20px;"><?php if('y'==$g_view['deal_data']['docs'][$dd]['is_approved']){?><img src="images/approved.png" /><?php }?></td>
				<td><a href="download_deal_doc.php?doc_id=<?php echo $g_view['deal_data']['docs'][$dd]['file_id'];?>" target="_blank"><?php echo $g_view['deal_data']['docs'][$dd]['caption'];?></a></td>
				<td style="width:50px;">
				<?php
				//only logged in members can flag a deal
				if($g_account->is_site_member_logged()){
				?>
				<input type="button" class="btn_auto" value="Flag" onclick="open_flag_deal_popup(<?php echo $g_view['deal_data']['docs'][$dd]['file_id'];?>)" />
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
		}
		?>
		</td>
		</tr>
		<?php
		if($doc_count > 0){
			?>
			<tr><td style="height:10px;">&nbsp;</td></tr>
			<tr><td><img src="images/approved.png" />&nbsp;Approved Deal Document</td></tr>
			<tr><td><img src="images/icon_red_flag.gif" />&nbsp;Flagged for Review</td></tr>
			<?php
		}
		?>	
		<!--Documents-->
		<!--verification-->
		<?php
		/*************
		sng:6/mar/2012
		Here we show whether the deal has any verification or not.
		First we check whether admin verified the deal or not
		
		Then we check if any member has verified or not.
		
		It will be like
		Verified by 2 Bankers at Morgan Stanley
		Verified by 1 Lawyer at Freshfields
		We do not show the individuals
		
		If none, then the deal has no verification
		*****************/
		$g_veiw['any_verification'] = false;
		?>
		<tr><td class="deal_page_h2">Verification:</td></tr>
		<?php
		if($g_view['deal_data']['admin_verified']=='y'){
			?><tr><td>Verified by Admin of Data-CX</td></tr><?php
			$g_veiw['any_verification'] = true;
		}
		for($v=0;$v<$g_view['verify_count'];$v++){
			?><tr><td>Verified by <?php echo $g_view['verify_data'][$v]['cnt'];?> <?php echo $g_view['verify_data'][$v]['member_type'];?><?php echo ($g_view['verify_data'][$v]['cnt']>1)?'s':'';?> at <?php echo $g_view['verify_data'][$v]['name'];?></td></tr><?php
			$g_veiw['any_verification'] = true;
		}
		?>
		<?php
		if(!$g_veiw['any_verification']){
			?><tr><td>Unverified</td></tr><?php
		}
		?>
		<!--verification-->
	</table>
</td>
</tr>
<?php
/************************
sng:6/mar/2012
We want the members to corfirm the details as ok or to send correction.
This is where it is most visible
Clicking 'edit' takes them to the edit tab
Clicking 'confirm' store their confirmation. If they are not logged in
then they are taken to login page. After login, their confirmation is stored

We do not use ajax. We use page redirect to come to this page and use flash message

Note: even if admin has 'verified the deal' these buttons appear and they appear for everyone
***************************/
?>
<tr>
<td style="text-align:right;" colspan="3">
<input type="button" value="Confirm Details" class="btn_auto" onclick="goto_confirm_deal('<?php echo $g_view['deal_data']['deal_id'];?>');" />&nbsp;&nbsp;&nbsp;&nbsp;<input type="button" class="btn_auto" value="Edit Details" onclick="goto_edit_tab();" />
</td>
</tr>
<tr>
<td colspan="3" style="text-align:right;"><span class="msg_txt"><?php echo display_flash("confirm_deal_msg");?></span></td>
</tr>
</table>
<?php
/**************************************************************
sng:22/feb/2012
support for flag deal document popup
**/
?>
<style type="text/css">
#background_flag_deal_doc_popup{
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
#popup_flag_deal_doc{
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
#popup_flag_deal_doc_close{
	font-size:14px;
	line-height:14px;
	right:6px;
	top:4px;
	position:absolute;
	color:#6fa5fd;
	font-weight:700;
	display:block;
}
#popup_flag_deal_doc_result{
	font-size:12px;
	color:#3366FF;
}
</style>
<div id="popup_flag_deal_doc">
	<a id="popup_flag_deal_doc_close">x</a>
	<h1>Flag the Deal Document</h1>
	<div id="flag_deal_doc_popup_content">
	<form id="frm_flag_deal_doc" method="post" action="" enctype="multipart/form-data">
	<input type="hidden" name="deal_doc_id" id="flag_deal_doc_id" value="" />
	
	<table width="100%" border="0">
	<tr>
	<td>Reason</td>
	</tr>
	<tr>
	<td><textarea name="flag_reason" id="flag_deal_doc_reason" style="width:400px; height:100px;"></textarea></td>
	</tr>
	
	<tr><td><input type="Submit" value="Send" class="btn_auto" /></td></tr>
	</table>
	</form>
	</div>
	<div id="popup_flag_deal_doc_result"></div>
</div>
<div id="background_flag_deal_doc_popup"></div>
<script>
var flag_deal_doc_popup_status = 0;
//centering popup
function center_flag_deal_doc_Popup(){
	//request data for centering
	var windowWidth = $(window).width();
	var windowHeight = $(window).height();
	var deal_doc_popupHeight = jQuery("#popup_deal_doc").height();
	var deal_doc_popupWidth = jQuery("#popup_deal_doc").width();
	//centering
   jQuery("#popup_flag_deal_doc").css({
       "position": "absolute",
       "top": (windowHeight/2-deal_doc_popupHeight/2 + $(window).scrollTop()),
       "left": windowWidth/2-deal_doc_popupWidth/2
   });
   //only need force for IE6

   jQuery("#background_flag_deal_doc_popup").css({
       "height": windowHeight
   });

}
//loading popup with jQuery magic!
function load_flag_deal_doc_Popup(){
	//loads popup only if it is disabled
	if(flag_deal_doc_popup_status==0){
		jQuery("#background_flag_deal_doc_popup").css({
		"opacity": "0.7"
		});
		jQuery("#background_flag_deal_doc_popup").fadeIn("slow");
		jQuery("#popup_flag_deal_doc").fadeIn("slow");
		flag_deal_doc_popup_status = 1;
	}
}
//disabling popup with jQuery magic!
function disable_flag_deal_doc_Popup(){
	//disables popup only if it is enabled
	if(flag_deal_doc_popup_status==1){
		jQuery("#background_flag_deal_doc_popup").fadeOut("slow");
		jQuery("#popup_flag_deal_doc").fadeOut("slow");
		flag_deal_doc_popup_status = 0;
		update_deal_doc();
	}
}
</script>
<script>
//LOADING POPUP
function open_flag_deal_popup(deal_doc_id){
	//centering with css
	center_flag_deal_doc_Popup();
	
	//load popup
	load_flag_deal_doc_Popup();
	//set the hidden field
	jQuery('#flag_deal_doc_id').val(deal_doc_id);
	return false;
}
//CLOSING POPUP
//Click the x event!
jQuery("#popup_flag_deal_doc_close").click(function(){
	disable_flag_deal_doc_Popup();
});
//Click out event!
jQuery("#background_flag_deal_doc_popup").click(function(){
	disable_flag_deal_doc_Popup();
});
jQuery(function() {
	var flag_options = {
		beforeSubmit:  validate_flag_deal_doc,
		success:       flag_deal_doc_submitted,
		url:       'ajax/flag_deal_doc.php',  // your upload script
		dataType:  'json'
	};
	jQuery('#frm_flag_deal_doc').submit(function() {
		//alert("upload");
		jQuery("#popup_flag_deal_doc_result").html("submitting...");
		jQuery(this).ajaxSubmit(flag_options);
		return false;
	});
	
	
	///////////////////
});
</script>
<script>
function validate_flag_deal_doc(formData, jqForm, options) {
	if(jQuery("#flag_deal_doc_reason").val()==""){
		jQuery("#popup_flag_deal_doc_result").html("Please specify a reason.");
		return false;
	}
	return true;
}
function flag_deal_doc_submitted(data, statusText)  {
	if (statusText == 'success') {
		if (data.submitted == 'y') {
			jQuery("#popup_flag_deal_doc_result").html("Deal document flagged");
			//close
			disable_flag_deal_doc_Popup();
		} else {
			jQuery("#popup_flag_deal_doc_result").html(data.error);
		}
	} else {
		jQuery("#popup_flag_deal_doc_result").html('Unknown error!');
	}
}
</script>
<?php
/***********************************************************
sng:6/mar/2012
support for deal confirmation by member
************/
?>
<script>
function goto_confirm_deal(deal_id){
	window.location.replace('confirm_deal_by_member.php?deal_id='+deal_id);
}
</script>
<?php
/***************************************************************/
?>