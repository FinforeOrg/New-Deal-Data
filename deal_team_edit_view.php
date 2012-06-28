<?php
/***********************
sng:29/sep/2011
we now include jquery in container view
<script type="text/javascript" src="js/jquery-1.2.1.pack.js"></script>
*****************************/
?>
<script type="text/javascript">
function lookup(inputString) {
	if(inputString.length == 0) {
		// Hide the suggestion box.
		$('#suggestions').hide();
	} else {
		// post data to our php processing page and if there is a return greater than zero
		// show the suggestions box
		//banker add banker, lawyer add lawyer
		//also a member of Barclays team can add a banker of Barclays
		//therefore we find member of same type from same company
		var this_mem_type = "<?php echo $_SESSION['member_type'];?>";
		var this_company_id = <?php echo $_SESSION['company_id'];?>;
		
		$('#team_member_searching').html("searching...");
		$.post("ajax/team_colleague_list.php", {name: ""+inputString+"",type: ""+this_mem_type+"",company_id: ""+this_company_id+""}, function(data){
			$('#team_member_searching').html("");
			if(data.length >0) {
				$('#suggestions').show();
				$('#autoSuggestionsList').html(data);
			}else{
				//no matches found, we hide the suggestion list
				setTimeout("$('#suggestions').hide();", 200);
			}
		});
	}
} //end

// if user clicks a suggestion, fill the text box.
function fill(colleague_id,f_name,l_name) {
	
	$('#team_member_name').val(f_name+" "+l_name);
	$('#team_mem_id').val(colleague_id);
	setTimeout("$('#suggestions').hide();", 200);
}
function hide_suggestion(){
	setTimeout("$('#suggestions').hide();", 200);
}
</script>
<script type="text/javascript">
function flag_members(this_deal_id,this_partner_id){
	/****
	We use checkbox here with unique id of flag_1, or flag_2 etc whose value is member id.
	We get the checked status of checkboxes, and if checked, get the mem id and create a csv
	**********/
	var mem_count = <?php echo $g_view['deal_partner_team_data_count'];?>;
	var flag_csv = "";
	var flag_elem_id = "";
	var flag_elem = null;
	if(mem_count == 0){
		return;
	}
	///////////////////////////////////////////////////
	for(i=0;i<mem_count;i++){
		elem_id = "flag_"+i;
		flag_elem = document.getElementById(elem_id);
		if(flag_elem != null){
			if(flag_elem.checked){
				flag_csv+=","+flag_elem.value;
			}
		}
	}
	///////////////////////////
	if(flag_csv == ""){
		return;
	}
	////////////////////
	flag_csv = flag_csv.substr(1);
	/////////////////////////
	//now, fire ajax
	$('#flag_response').html('sending request');
	$.post("ajax/flag_deal_team_members.php", {deal_id: ""+this_deal_id+"",partner_id: ""+this_partner_id+"",flag_members: ""+flag_csv+""}, function(data){
		if(data.length >0) {
			$('#flag_response').html(data);
		}
	});
}
</script>
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
		<h4><?php echo ymd_to_dmy($g_view['deal_data']['date_of_deal']);?></h4>
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
		<td style="width:100px;">
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
	<th><?php echo $g_view['deal_partner_data']['name'];?>'s Team</th>
	<td>&nbsp;</td>
	<th>Add Collegues</th>
	</tr>
	<tr>
	<td>
		<table width="50%" cellpadding="0" cellspacing="0" class="company">
		<tr>
		<td colspan="3">
		<span class="msg_txt"><?php echo $g_view['msg'];?></span>
		</td>
		</tr>
		<tr>
		<td>Name</td>
		<td>Designation</td>
		<td>Flag as not part of this deal</td>
		</tr>
		<?php
		if($g_view['deal_partner_team_data_count'] == 0){
			?>
			<tr>
			<td colspan="3">None found</td>
			</tr>
			<?php
		}else{
			for($team_i=0;$team_i<$g_view['deal_partner_team_data_count'];$team_i++){
				?>
				<tr>
				<td><?php echo $g_view['deal_partner_team_data'][$team_i]['f_name']." ".$g_view['deal_partner_team_data'][$team_i]['l_name'];?></td>
				<td><?php echo $g_view['deal_partner_team_data'][$team_i]['designation'];?></td>
				<td>
				<?php
				/****
				sng:20/04/2010
				If I am in this then I should be able to remove myself
				sng:21/apr/2010
				For other members, I can flag the member as not part of the deal team. 
				***/
				if($g_view['deal_partner_team_data'][$team_i]['member_id']==$_SESSION['mem_id']){
					?>
					<form method="get" action="">
					<input type="hidden" name="action" value="remove_self" />
					<input type="hidden" name="deal_id" value="<?php echo $g_view['deal_id'];?>" />
					<input type="hidden" name="partner_id" value="<?php echo $g_view['deal_partner_id'];?>" />
					<input name="submit" type="submit" class="btn_auto" id="button" value="Remove Myself"/>
					</form>
					<?php
				}else{
					//mark as not part of the team
					?>
					<input type="checkbox" name="" id="flag_<?php echo $team_i;?>" value="<?php echo $g_view['deal_partner_team_data'][$team_i]['member_id'];?>" />
					<?php
				}
				?>
				</td>
				</tr>
				<?php
			}
		}
		?>
		<tr>
		<td>
		<form method="get" action="">
		<input type="hidden" name="action" value="add_self" />
		<input type="hidden" name="deal_id" value="<?php echo $g_view['deal_id'];?>" />
		<input type="hidden" name="partner_id" value="<?php echo $g_view['deal_partner_id'];?>" />
		<input name="submit" type="submit" class="btn_auto" id="button" value="Add Myself"/>
		</form>
		
		</td>
		<td>
		<?php
		/*********************************************
		sng:22/sep/2011
		For now, we do not show the deal team page. Therefore, we jump to the deal detail page
		
		<form method="get" action="deal_team.php">
		<input type="hidden" name="deal_id" value="<?php echo $g_view['deal_id'];?>" />
		<input type="hidden" name="partner_id" value="<?php echo $g_view['deal_partner_id'];?>" />
		<input name="submit" type="submit" class="btn_auto" id="button" value="Back" />
		</form>
		************************************************/
		?>
		<input type="button" class="btn_auto" value="Back" onclick="window.location.replace('deal_detail.php?deal_id=<?php echo $g_view['deal_id'];?>');" />
		</td>
		<td>
		<input name="submit" type="submit" class="btn_auto" id="button" value="Flag" onclick="flag_members(<?php echo $g_view['deal_id'];?>,<?php echo $g_view['deal_partner_id'];?>)"/><span id="flag_response"></span>
		</td>
		</tr>
		</table>
	</td>
	<td style="width:20px;">&nbsp;</td>
	<td>
		<table cellpadding="0" cellspacing="0" width="100%">
		<tr><td>Add a colleague to the team</td></tr>
		<tr>
		<td>
		<?php
		/***
		sng:5/may/2010
		We use post since we cannot risk using GET and blank action. The code to add self use get and the url has all sort
		of data which triggers addition of the user.
		So for colleague, we do not put anything extra on url, just the deal id and partner id, rest hidden
		*********/
		?>
		<form method="post" action="deal_team_edit.php?deal_id=<?php echo $g_view['deal_id'];?>&partner_id=<?php echo $g_view['deal_partner_id'];?>">
		<input type="hidden" name="action" value="add_team_member" />
		<input type="hidden" name="team_mem_id" id="team_mem_id" value="<?php echo $g_view['input']['team_mem_id'];?>" />
		<table width="100%" cellpadding="0" cellspacing="5">
		<tr><td colspan="2">Type the first few letters. If the member is found, it will be shown in the list. Please select the member you wish to add to the team.</td></tr>
		<tr>
		<td>Name:</td>
		<td><input type="text" name="team_member_name" id="team_member_name" class="txtbox" value="<?php echo $g_view['input']['team_member_name'];?>" onkeyup="lookup(this.value);" onblur="hide_suggestion();" /><br />
		<span id="team_member_searching"></span><br />
		<span class="err_txt"><?php echo $g_view['err']['team_member_name'];?></span>
		<div class="suggestionsBox" id="suggestions" style="display: none;">
		<img src="images/upArrow.png" style="position: relative; top: -18px; left: 30px;" alt="upArrow"  />
		<div class="suggestionList" id="autoSuggestionsList"></div>
		</div>
		</td>
		</tr>
		<tr>
		<td>&nbsp;</td>
		<td><input type="submit" name="submit" value="Add" class="btn_auto" /></td>
		</tr>
		</table>
		
		</form>
		</td>
		</tr>
		<tr><td>Colleague not listed above? Add them to the team, anyway.</td></tr>
		<tr>
		<td>
		<form method="post" action="deal_team_edit.php?deal_id=<?php echo $g_view['deal_id'];?>&partner_id=<?php echo $g_view['deal_partner_id'];?>">
		<input type="hidden" name="action" value="create_and_add_colleague" />
		<input type="hidden" name="company_id" value="<?php echo $_SESSION['company_id'];?>" />
		<input type="hidden" name="member_type" value="<?php echo $_SESSION['member_type'];?>" />
		<?php
		/***
		The company id and member type of this new member will be same as this member since the new
		member is my colleague as hence of same type and in same company
		***/
		?>
		<table width="100%" cellpadding="0" cellspacing="5">
		<tr>
		<td>First Name</td>
		<td>
		<input type="text" name="f_name" class="txtbox" value="<?php echo $g_view['input']['f_name'];?>" /><br />
		<span class="err_txt"><?php echo $g_view['err']['f_name'];?></span>
		</td>
		</tr>
		
		<tr>
		<td>Last Name</td>
		<td>
		<input type="text" name="l_name" class="txtbox" value="<?php echo $g_view['input']['l_name'];?>" /><br />
		<span class="err_txt"><?php echo $g_view['err']['l_name'];?></span>
		</td>
		</tr>
		<!--
		sng:7/jul/2010
		We accept the work email of the colleague also
		-->
		<tr>
		<td>Work Email</td>
		<td>
		<input type="text" name="work_email" class="txtbox" value="<?php echo $g_view['input']['work_email'];?>" /><br />
		<span class="err_txt"><?php echo $g_view['err']['work_email'];?></span><br />
		Your entry of this colleague will remain confidential, but we shall email them in case they wish to remove themselves
		</td>
		</tr>
		
		<tr>
		<td>Designation</td>
		<td>
		<select name="designation" class="txtbox">
		<option value="">Select</option>
		
		<?php
		
		for($j=0;$j<$g_view['designation_count'];$j++){
		?>
		<option value="<?php echo $g_view['designation_list'][$j]['designation'];?>" <?php if($g_view['input']['designation']==$g_view['designation_list'][$j]['designation']){?>selected="selected"<?php }?>><?php echo $g_view['designation_list'][$j]['designation'];?></option>
		<?php
		}
		?>   
		</select>
		<br />
		<span class="err_txt"><?php echo $g_view['err']['designation'];?></span>
		</td>
		</tr>
		<tr>
		<td>&nbsp;</td>
		<td><input type="submit" name="submit" value="Add" class="btn_auto" /></td>
		</tr>
		</table>
		</form>
		</td>
		</tr>
		</table>
	</td>
	</tr>
	</table>
	</td>
	</tr>
	
	
	<?php
}else{
	?>
	<tr><td>Deal data not found</td></tr>
	<?php
}
?>
</table>