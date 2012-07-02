<script type="text/javascript" src="../js/jquery-1.2.1.pack.js"></script>
<script type="text/javascript">
function lookup(inputString) {
	if(inputString.length == 0) {
		// Hide the suggestion box.
		$('#suggestions').hide();
	} else {
		//we need the company type
		var company_type_obj = document.getElementById("company_type");
		var company_type_selected = company_type_obj.options[company_type_obj.selectedIndex].value;
		if(company_type_selected==""){
			alert("Please select company type first");
			return;
		}
		// post data to our php processing page and if there is a return greater than zero
		// show the suggestions box
		
		$('#firm_searching').html("searching...");
		$.post("ajax/get_firm.php", {name: ""+inputString+"",type: ""+company_type_selected+""}, function(data){
			
			$('#firm_searching').html("");
			
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
function fill(company_id,name) {
	$('#firm_name').val(name);
	$('#company_id').val(company_id);
	setTimeout("$('#suggestions').hide();", 200);
}
function hide_suggestion(){
	setTimeout("$('#suggestions').hide();", 200);
}
</script>
<table width="100%" cellpadding="5" cellspacing="0" border="0">
<tr>
<td><span class="msg_txt"><?php echo $g_view['msg'];?></span></td>
</tr>
<tr>
<td>
<form method="post" action="">
<input type="hidden" name="action" value="edit" />
<input type="hidden" name="id" value="<?php echo $_POST['id'];?>" />
<table width="100%" cellpadding="5" cellspacing="0" border="0">
<tr>
<td>Company Type</td>
<td>
<select name="company_type" id="company_type" class="txtbox">
<option value="">Select</option>
<option value="bank" <?php if($g_view['data']['company_type']=="bank"){ ?> selected="selected"<?php }?>>Bank</option>
<option value="law firm" <?php if($g_view['data']['company_type']=="law firm"){ ?> selected="selected"<?php }?>>Law Firm</option>
<option value="company" <?php if($g_view['data']['company_type']=="company"){ ?> selected="selected"<?php }?>>Company</option>
</select><br />
<span class="err_txt"><?php echo $g_view['err']['company_type'];?></span>
</td>
</tr>

<tr>
<td>Company Name</td>
<td>
<input type="hidden" name="company_id" id="company_id" value="<?php echo $g_view['data']['company_id'];?>" />
Type the first few letters. If the firm is found, it will be shown in the list. Please select the firm.<br />
<input type="text" name="firm_name" id="firm_name" class="txtbox" style="width:200px;" value="<?php echo $g_view['data']['name'];?>" onkeyup="lookup(this.value);" onblur="hide_suggestion();" /><br />
		<span id="firm_searching"></span><br />
		<span class="err_txt"><?php echo $g_view['err']['company_id'];?></span>
		<div class="suggestionsBox" id="suggestions" style="display: none;">
		<img src="images/upArrow.png" style="position: relative; top: -18px; left: 30px;" alt="upArrow"  />
		<div class="suggestionList" id="autoSuggestionsList"></div>
		</div>
</td>
</tr>

<tr>
<td>Email suffix</td>
<td>
<input name="email_suffix" type="text" style="width:200px;" value="<?php echo $g_view['data']['email_suffix'];?>" /><span class="err_txt"> *</span><br />(Ex: @credit-suisse.com)<br />
<span class="err_txt"><?php echo $g_view['err']['email_suffix'];?></span>
</td>
</tr>



<tr>
<td></td>
<td><input type="submit" name="submit" value="Update" /></td>
</tr>

</table>
</form>
</td>
</tr>
</table>