<?php
/**********************
sng:29/sep/2011
we now include jquery in the container view
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
		var this_company_id = <?php echo $_SESSION['company_id'];?>;
		var this_member_type = "<?php echo $_SESSION['member_type'];?>";
		
		$('#colleague_name_searching').html("searching...");
		$.post("ajax/colleague_list.php", {name: ""+inputString+"",type: ""+this_member_type+"",company_id: ""+this_company_id+""}, function(data){
			$('#colleague_name_searching').html("");
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
	
	$('#colleague_name').val(f_name+" "+l_name);
	$('#colleague_id').val(colleague_id);
	setTimeout("$('#suggestions').hide();", 200);
}
function hide_suggestion(){
	setTimeout("$('#suggestions').hide();", 200);
}
</script>
<table cellpadding="0" cellspacing="0" style="width:400px;">
<tr>
<td>
<!--search and add delegate-->
<form method="post" action="">
<input type="hidden" name="action" value="add_delegate" />
<input type="hidden" name="colleague_id" id="colleague_id" value="<?php echo $g_view['input']['colleague_id'];?>" />
<table width="100%" cellpadding="0" cellspacing="0" class="company">
<tr><th colspan="2">Add Delegate</th></tr>
<tr><td colspan="2"><span class="msg_txt"><?php echo $g_view['msg'];?></span></td></tr>
<tr><td colspan="2">Type the first few letters. If the colleague is found, it will be shown in the list. Please select the colleague you wish to appoint as delegate.</td></tr>
<tr>
<td>Colleague</td>
<td>
<input name="colleague_name" id="colleague_name" onkeyup="lookup(this.value);" onblur="hide_suggestion();" type="text" class="txtbox" value="<?php echo $g_view['input']['colleague_name'];?>"/><br /><span id="colleague_name_searching"></span><br /><span class="err_txt"><?php echo $g_view['err']['colleague_name'];?></span><br />
<div class="suggestionsBox" id="suggestions" style="display: none;">
<img src="images/upArrow.png" style="position: relative; top: -18px; left: 30px;" alt="upArrow"  />
<div class="suggestionList" id="autoSuggestionsList"></div>
</div>
</td>
</tr>

<tr>
<td>&nbsp;</td>
<td><input type="submit" value="Add" class="btn_auto" /></td>
</tr>
</table>
</form>
<!--search and add delegate-->
</td>
</tr>
<tr><td style="height:20px;">&nbsp;</td></tr>
<tr>
<td>
<!--list delegates-->
<table width="100%" cellpadding="0" cellspacing="0" class="company">
<tr><th colspan="4">My Delegates</th></tr>
<tr>
<td>Name</td>
<td>Designation</td>
<td>Work Email</td>
<td>&nbsp;</td>
</tr>
<?php
if($g_view['delegate_count']==0){
?>
<tr><td colspan="4">None</td></tr>
<?php
}else{
	for($j=0;$j<$g_view['delegate_count'];$j++){
		?>
		<tr>
		<td>
		<?php echo $g_view['delegate_data'][$j]['f_name'];?> <?php echo $g_view['delegate_data'][$j]['l_name'];?>
		</td>
		<td>
		<?php echo $g_view['delegate_data'][$j]['designation'];?>
		</td>
		<td>
		<?php echo $g_view['delegate_data'][$j]['work_email'];?>
		</td>
		<td>
		<form method="post" action="">
		<input type="hidden" name="action" value="delete_delegate" />
		<input type="hidden" name="colleague_id" value="<?php echo $g_view['delegate_data'][$j]['mem_id'];?>" />
		<input type="submit" value="Delete" class="btn_auto" />
		</form>
		</td>
		</tr>
		<?php
	}
}
?>
</table>
<!--list delegates-->
</td>
</tr>
</table>

