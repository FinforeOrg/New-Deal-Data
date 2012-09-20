<?php
/*********************
sng:29/sep/2011
we now include jquery in container view
<script src="js/jquery-1.3.2.js" type="text/javascript"></script>
**************************/
?>
<script src="js/jquery.form.js" type="text/javascript"></script>
<script type="text/javascript">
jQuery.noConflict()
</script>
<script type="text/javascript" src="js/prototype.js"></script>
<script src="js/logo_preference.js"></script>
<script type="text/javascript">
 

function open_report_box(){
	document.getElementById('error_report').style.width="400px";
	document.getElementById('error_report').style.height="60px";
	document.getElementById('error_report').style.visibility="visible";
	document.getElementById('send_error').style.visibility="visible";
	document.getElementById('btn_report_error').style.visibility="hidden";
}
function send_error_report(){
	document.getElementById("error_report_msg").innerHTML = "Sending...";
	new Ajax.Request('ajax/mark_deal_as_error.php', {
		method: 'post',
		parameters: $('frm_deal_error').serialize(true),
		onSuccess: function(transport){
			
			document.getElementById("error_report_msg").innerHTML = transport.responseText;
		},
		onFailure: function(){
			document.getElementById("error_report_msg").innerHTML = "Error sending report";
		}
	});
}

function open_suggestion_box(){
	document.getElementById('deal_suggestion').style.width="400px";
	document.getElementById('deal_suggestion').style.height="auto";
	document.getElementById('deal_suggestion').style.visibility="visible";
	document.getElementById('deal_suggestion_box').style.width="400px";
	document.getElementById('deal_suggestion_box').style.height="60px";
	document.getElementById('deal_suggestion_box').style.visibility="visible";
	document.getElementById('send_deal_suggestion').style.visibility="visible";
	document.getElementById('btn_deal_suggestion').style.visibility="hidden";
}

function send_suggestion_on_deal(){
	
	document.getElementById("deal_suggestion_msg").innerHTML = "Sending...";
	new Ajax.Request('ajax/make_suggestion_on_deal.php', {
		method: 'post',
		parameters: $('frm_deal_suggestion').serialize(true),
		onSuccess: function(transport){
			
			document.getElementById("deal_suggestion_msg").innerHTML = transport.responseText;
		},
		onFailure: function(){
			document.getElementById("deal_suggestion_msg").innerHTML = "Error sending suggestion";
		}
	});
}
</script>
<style type="text/css">
div#deal_suggestion{
	font-size:12px;
	color:#999999;
}
</style>
<table width="100%" cellpadding="0" cellspacing="0">


<?php
if($g_view['deal_found']){
	?>
	<tr>
	<td>
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
		</table>
		<!--deal company, value data-->
	</td>
	</tr>
	<tr>
	<td>
		<!--deal data-->
		<table cellpadding="0" cellspacing="0">
		<tr>
		<td style="width:200px;">
			<!--tombstone-->
			<?php
			$g_trans->get_tombstone_from_deal_id($g_view['deal_data']['deal_id']);
			?>
			<!--tombstone-->
		</td>
		<td style="width:10px;">&nbsp;</td>
		<td>
			<!--deal data, bankers lawyers-->
			<table cellpadding="0" cellspacing="0">
			<?php
			include("deal_data_banker_lawyer_view.php");
			?>
			<!--
			sng:6/jul/2010
			need space between deal data and note on deal detail page
			-->
			<tr><td style="height:10px;">&nbsp;</td></tr>
			<tr>
			<td>
			<?php echo nl2br($g_view['deal_data']['note']);?>
			</td>
			</tr>
			<tr>
				<td>
					<table width="100%">
						<tr>
							<td>
							<!--///////////report error/////////////////-->
							<?php
							/***
							sng:27/may/2010
							The report error should be visible only to logged in member
							***/
							if($g_account->is_site_member_logged()){
							?>
							<form id="frm_deal_error">
							<input type="hidden" name="deal_id" value="<?php echo $g_view['deal_data']['deal_id'];?>" />
							<input type="hidden" name="mem_id" value="<?php echo $_SESSION['mem_id'];?>" />
							<table>
							<tr><td><div><textarea name="error_report" id="error_report" style="width:0px;height:0px; visibility:hidden; overflow:auto;"></textarea></div></td></tr>
							<tr><td><span id="error_report_msg"></span></td></tr>
							<tr><td><input type="button" id="btn_report_error" value="Report error" class="btn_auto" onclick="open_report_box()" />&nbsp;&nbsp;<input type="button" id="send_error" value="Send" class="btn_auto" style="visibility:hidden;" onclick="send_error_report()" /></td></tr>
							</table>
							</form>
							<?php
							}
							?>
							<!--///////////report error/////////////////-->
							</td>
							<td>
							<!--///////////suggestion/////////////////-->
							<?php
							/***
							sng:27/may/2010
							The more suggestion should be visible only to logged in member
							***/
							if($g_account->is_site_member_logged()){
							?>
							<form id="frm_deal_suggestion">
							<input type="hidden" name="deal_id" value="<?php echo $g_view['deal_data']['deal_id'];?>" />
							<input type="hidden" name="mem_id" value="<?php echo $_SESSION['mem_id'];?>" />
							<table>
							<tr><td>
							<div id="deal_suggestion" style="visibility:hidden;width:0px;height:0px;">
							<?php
							/***
							sng:9/oct/2010
							If pending M&A deal then show prompt message
							****/
							if(($g_view['deal_data']['deal_cat_name']=="M&A")&&($g_view['deal_data']['deal_subcat1_name']!="Completed")){
								?>
								Please let us know the Status: Completed/Cancelled/Outbid. The date. And any URL reference you would like added to this deal.
								<?php
							}
							?>
							<textarea name="deal_suggestion" id="deal_suggestion_box" style="width:0px;height:0px;visibility:hidden;  overflow:auto;"></textarea>
							</div>
							</td></tr>
							<tr><td><span id="deal_suggestion_msg"></span></td></tr>
							<tr><td>
							<?php
							/****
							sng:9/oct/2010
							If the deal is M&A and is not complete, we show a dirrerent text on the button
							****/
							$suggestion_btn_txt = "Suggest more detail";
							if(($g_view['deal_data']['deal_cat_name']=="M&A")&&($g_view['deal_data']['deal_subcat1_name']!="Completed")){
								$suggestion_btn_txt = "Suggest More Detail / Mark Completed";
							}
							?>
							<input type="button" id="btn_deal_suggestion" value="<?php echo $suggestion_btn_txt;?>" class="btn_auto" onclick="open_suggestion_box()" />&nbsp;&nbsp;<input type="button" id="send_deal_suggestion" value="Send" class="btn_auto" style="visibility:hidden;" onclick="send_suggestion_on_deal()" />
							</td></tr>
							</table>
							</form>
							<?php
							}
							?>
							<!--///////////suggestion/////////////////-->
							</td>
						<tr>
					</table>
				</td>
			</tr>
			<?php
			/*********************************
			sng:8/apr/2011
			discussion link
			***/
			if($g_view['show_discussion']){
			?>
			<tr>
				<td>
					<form method="post" action="deal_discussion.php?deal_id=<?php echo $g_view['deal_data']['deal_id'];?>">
					<input type="submit" class="btn_auto" value="Go to Discussion Page" />
					</form>
				</td>
			</tr>
			<tr><td style="height:10px;">&nbsp;</td></tr>
			<?php
			}
			?>
			<?php
			/***********************************************************************
			sng:20/july/2010
			If the logo is not present, we show the link to send logo suggestion.
			This is only for logged in member
			***/
			if(($g_view['deal_data']['logo']=="")&&($g_account->is_site_member_logged())){
				?>
				<tr>
				  <td>
				  <!--
				  Dear member, if you have any suggestion about the logo, or have the logo image, please email us. You can attach the logo image to the email. This will help us to update our database.<br /><br />
				  -->
				  <form action="mailto:<?php echo $g_view['logo_email'];?>">
				<input type="submit" class="btn_auto" name="submit" value="Email us a logo" />
				</form>
				  <!--
				Dear member, if you have any suggestion about the logo, or have the logo image, please email us <a href="mailto:<?php echo $g_view['logo_email'];?>">here</a>. You can attach the logo image to the email. This will help us to update our database.
				-->
				</td>
				</tr>
				<?php
			}
			?>
			<?php
			/***************************************************
			sng:8/mar/2011
			case study section
			********/
			if($g_view['case_study_count'] > 0){
				?>
				<tr><td style="height:10px;">&nbsp;</td></tr>
				<tr>
				<td>
					<table cellpadding="0" cellspacing="0" class="registercontent">
						<tr>
							<th>Case Studies</th>
						</tr>
						<tr>
						<td>
						<table cellpadding="5" cellspacing="5" class="company">
						<?php
						for($cs = 0;$cs < $g_view['case_study_count'];$cs++){
							?>
							<tr>
							<td><?php echo $g_view['case_study'][$cs]['caption'];?></td>
							<td>
							<form method="post" action="download_case_study.php" target="_blank">
							<input type="hidden" name="case_study_id" value="<?php echo $g_view['case_study'][$cs]['case_study_id'];?>" />
							<input type="submit" name="submit" class="btn_auto" value="Download" />
							</form>
							</td>
							</tr>
							<?php
						}
						?>
						</table>
						</td>
						</tr>
					</table>
				</td>
				</tr>
				<?php
			}
			?>
			<?php
			if($g_view['can_upload_case_study']){
				?>
				<tr><td><input type="button" class="btn_auto" id="btn_case_study" value="Submit case study" /></td></tr>
				<?php
			}
			/*********************************************************************************/
			?>
			</table>
			<!--deal data, bankers lawyers-->
		</td>
		</tr>
		</table>
		<!--deal data-->
	</td>
	</tr>
	
	<tr>
	<td>
	<table width="100%" cellpadding="0" cellspacing="2" class="registercontent">
	<tr>
	<th>
	Bank(s)
	</th>
	<th>&nbsp;</th>
	<th>
	Law Firm(s)
	</th>
	</tr>
	<tr>
	<td style="width:49%;">
	<!--banks-->
	<?php
	if($bank_count > 0){
		?>
		<table width="100%" cellpadding="0" cellspacing="0" class="company">
		<?php
		for($i=0;$i<$bank_count;$i++){
			?>
			<tr>
			<td colspan="2"><?php echo $g_view['deal_data']['banks'][$i]['name'];?></td>
			</tr>
			<?php
			/***
			sng:27/may/2010
			client wants to show the team members for a deal
			only if the user is logged in. If so, then the view full team button and edit team button
			should also be hidden if the user is not logged in
			*****/
			if($g_account->is_site_member_logged()){
			?>
			<tr>
				<td colspan="2">
				<!--top 3 members-->
				<?php
				/****
				sng:5/May/2010
				An ugly hack but best in this case without upsetting all codes.
				For each bank, get the team members
				***********/
				?>
				<?php
				$g_view['member_data'] = NULL;
				$g_view['member_count'] = 0;
				$partner_id = $g_view['deal_data']['banks'][$i]['partner_id'];
				$success = $g_trans->get_deal_partner_members($g_view['deal_id'],$partner_id,3,$g_view['member_data'],$g_view['member_count']);
				if(!$success){
					die("Cannot get deal team data");
				}
				?>
				<table width="100%" cellpadding="0" cellspacing="0">
				<?php
				if($g_view['member_count'] == 0){
					?>
					<tr><td colspan="2">None added yet</td></tr>
					<?php
				}else{
					for($mem=0;$mem<$g_view['member_count'];$mem++){
						?>
						<tr>
						<td>
						<a href="profile.php?mem_id=<?php echo $g_view['member_data'][$mem]['member_id'];?>"><?php echo $g_view['member_data'][$mem]['f_name'];?> <?php echo $g_view['member_data'][$mem]['l_name'];?></a>						</td>
						<td><?php echo $g_view['member_data'][$mem]['designation'];?></td>
						</tr>
						<?php
					}
				}
				?>
				</table>
				<!--top 3 members-->				</td>
			</tr>
			<tr>
				<td>
				<?php
				if($g_view['member_count'] == 0){
					//no need to show the full detail button since there is no member here
					?>
					&nbsp;
					<?php
				}else{
					?>
					<form method="get" action="deal_team.php">
					<input type="hidden" name="deal_id" value="<?php echo $g_view['deal_id'];?>"  />
					<input type="hidden" name="partner_id" value="<?php echo $g_view['deal_data']['banks'][$i]['partner_id'];?>"  />
					<input name="submit" type="submit" class="btn_auto" id="button" value="Full Detail" />
					</form>
					<?php
				}
				?>				</td>
				<td>
				<form method="get" action="deal_team_edit.php">
				<input type="hidden" name="deal_id" value="<?php echo $g_view['deal_id'];?>" />
				<input type="hidden" name="partner_id" value="<?php echo $g_view['deal_data']['banks'][$i]['partner_id'];?>" />
				<input name="submit2" type="submit" class="btn_auto" id="submit" value="Edit Team" />
				</form>				</td>
			</tr>
			<?php
			}
			?>
			<tr><td colspan="2" style="height:10px; border:0 0 0 0">&nbsp;</td></tr>
			<?php
		}
		?>
		</table>
		<?php
	}
	?>
	<!--banks-->
	</td>
	<td>&nbsp;</td>
	<td style="width:49%">
	<!--law firms-->
	<?php
	if($law_count > 0){
		?>
		<table width="100%" cellpadding="0" cellspacing="0" class="company">
		<?php
		for($i=0;$i<$law_count;$i++){
			?>
			<tr>
			<td colspan="2"><?php echo $g_view['deal_data']['law_firms'][$i]['name'];?></td>
			</tr>
			<?php
			/****
			sng:5/May/2010
			An ugly hack but best in this case without upsetting all codes.
			For each bank, get the team members
			***********/
			?>
			<?php
			if($g_account->is_site_member_logged()){
			?>
			<tr>
				<td colspan="2">
				<!--top 3 members-->
				<?php
				/****
				sng:5/May/2010
				An ugly hack but best in this case without upsetting all codes.
				For each law firm, get the team members
				***********/
				$g_view['member_data'] = NULL;
				$g_view['member_count'] = 0;
				$partner_id = $g_view['deal_data']['law_firms'][$i]['partner_id'];
				$success = $g_trans->get_deal_partner_members($g_view['deal_id'],$partner_id,3,$g_view['member_data'],$g_view['member_count']);
				if(!$success){
					die("Cannot get deal team data");
				}
				?>
				<table width="100%" cellpadding="0" cellspacing="0">
				<?php
				if($g_view['member_count'] == 0){
					?>
					<tr><td colspan="2">None added yet</td></tr>
					<?php
				}else{
					for($mem=0;$mem<$g_view['member_count'];$mem++){
						?>
						<tr>
						<td>
						<a href="profile.php?mem_id=<?php echo $g_view['member_data'][$mem]['member_id'];?>"><?php echo $g_view['member_data'][$mem]['f_name'];?> <?php echo $g_view['member_data'][$mem]['l_name'];?></a>
						</td>
						<td><?php echo $g_view['member_data'][$mem]['designation'];?></td>
						</tr>
						<?php
					}
				}
				?>
				</table>
				<!--top 3 members-->
				</td>
			</tr>
			<tr>
				<td>
				<?php
				if($g_view['member_count'] == 0){
					//no need to show the full detail button since there is no member here
					?>
					&nbsp;
					<?php
				}else{
					?>
					<form method="get" action="deal_team.php">
					<input type="hidden" name="deal_id" value="<?php echo $g_view['deal_id'];?>"  />
					<input type="hidden" name="partner_id" value="<?php echo $g_view['deal_data']['law_firms'][$i]['partner_id'];?>"  />
					<input name="submit" type="submit" class="btn_auto" id="button" value="Full Detail" />
					</form>
					<?php
				}
				?>
				
				</td>
				<td>
				<form method="get" action="deal_team_edit.php">
				<input type="hidden" name="deal_id" value="<?php echo $g_view['deal_id'];?>" />
				<input type="hidden" name="partner_id" value="<?php echo $g_view['deal_data']['law_firms'][$i]['partner_id'];?>" />
				<input name="submit" type="submit" class="btn_auto" id="button" value="Edit Team" />
				</form>
				</td>
			</tr>
			<?php
			}
			?>
			<tr><td colspan="2" style="height:10px; border:0 0 0 0">&nbsp;</td></tr>
			<?php
		}
		?>
		</table>
		<?php
	}
	?>
	<!--law firms-->
	</td>
	</tr>
	</table>
	</td>
	</tr>
	<?php
	/***
	sng:8/jul/2010
	If sources are present, we show the sources section.
	sources are just urls separated by comma, so, we split and show in a list as hyperlinked item.
	The page will open in new window
	*********/
	if($g_view['deal_data']['sources']!=""){
		?>
		<tr><td>Sources</td></tr>
		<?php
		$source_urls = explode(",",$g_view['deal_data']['sources']);
		?>
		<tr><td>
		<ol>
		<?php
		foreach($source_urls as $source){
			$source = trim($source);
			?>
			<li><a href="<?php echo $source;?>" target="_blank"><?php echo $source;?></a></li>
			<?php
		}
		?>
		</ol>
		</td></tr>
		<?php
	}
	/*******************************/
	?>
	<?php
}else{
	?>
	<tr><td>Deal data not found</td></tr>
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
	height:192px;
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
		"top": (windowHeight/2-case_study_popupHeight/2)+200,
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
	<td><input type="file" name="case_study_file" id="case_study_file" style="width: 400px;" /></td>
	</tr>
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