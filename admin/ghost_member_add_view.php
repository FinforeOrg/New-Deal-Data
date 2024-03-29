<script type="text/javascript" src="../js/jquery-1.2.1.pack.js"></script>
<script type="text/javascript">
function membership_type_changed(){
	var type_obj = document.getElementById('membership_type');
	var offset_selected = type_obj.selectedIndex;
	if(offset_selected != 0){
		var type_selected = type_obj.options[offset_selected].value;
		//fetch the list of designations
		$.post("../ajax/designation_list.php", {membership_type: ""+type_selected+""}, function(data){
				if(data.length >0) {
					$('#designation').html(data);
				}
		});
	}
}
</script>
<script type="text/javascript">
function lookup(inputString) {

	if(inputString.length == 0) {
		// Hide the suggestion box.
		$('#suggestions').hide();
	} else {
		// post data to our php processing page and if there is a return greater than zero
		// show the suggestions box
		
		
		$('#company_searching').html("searching...");
		//get the type of member
			var type_obj = document.getElementById('membership_type');
			var offset_selected = type_obj.selectedIndex;
			if(offset_selected == 0){
				alert("Please select membership type first");
				return;
			}
			
			var type_selected = type_obj.options[offset_selected].value;
			
		$.post("../ajax/company_list.php", {search_string: ""+inputString+"",type: ""+type_selected+""}, function(data){
			$('#company_searching').html("");
			
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
function fill(name) {
	$('#firm_name').val(name);
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
<input type="hidden" name="action" value="add"/>
<table width="100%" cellpadding="5" cellspacing="0" border="0">
<tr>
<td>First Name</td>
<td><input name="first_name" type="text" class="txtbox" value="<?php echo $g_view['input']['first_name'];?>"/><span class="err_txt"> *</span><br />
<span class="err_txt"><?php echo $g_view['err']['first_name'];?></span>								
</td>
</tr>

<tr>
<td>Last Name</td>
<td><input name="last_name" type="text" class="txtbox" value="<?php echo $g_view['input']['last_name'];?>"/><span class="err_txt"> *</span><br />
<span class="err_txt"><?php echo $g_view['err']['last_name'];?></span>
</td>
</tr>

<tr>
<td>Membership Type</td>
<td>
<select name="type" id="membership_type" class="txtbox" onchange="membership_type_changed();">
<option value="">Select</option>
<option value="banker" <?php if($g_view['input']['type']=="banker"){ ?> selected="selected"<?php }?>>Banker</option>
<option value="lawyer" <?php if($g_view['input']['type']=="lawyer"){ ?> selected="selected"<?php }?>>Lawyer</option>
<option value="company rep" <?php if($g_view['input']['type']=="company rep"){ ?> selected="selected"<?php }?>>Company Rep</option>
<?php
/*********************
sng:5/apr/2011
we have added a new role: data partner, but they all belong to company
************************/
?>
<option value="data partner" <?php if($g_view['input']['type']=="data partner"){ ?> selected="selected"<?php }?>>Financial Journalist / Data Provider</option>
</select><span class="err_txt"> *</span><br />
<span class="err_txt"><?php echo $g_view['err']['type'];?></span>
</td>
</tr>

<tr>
<td>Company</td>
<td>
Type the first few letters. If the company is found, it will be shown in the list. Please select the company.<br />
<input type="text" name="firm_name" id="firm_name" class="txtbox" style="width:200px;" value="<?php echo $g_view['input']['firm_name'];?>" onkeyup="lookup(this.value);" onblur="hide_suggestion();" /><span class="err_txt"> *</span><br />
		<span id="company_searching"></span><br />
		<span class="err_txt"><?php echo $g_view['err']['firm_name'];?></span>
		<div class="suggestionsBox" id="suggestions" style="display: none;">
		<img src="images/upArrow.png" style="position: relative; top: -18px; left: 30px;" alt="upArrow"  />
		<div class="suggestionList" id="autoSuggestionsList"></div>
		</div>
</td>
</tr>

<tr>
<td>Designation</td>
<td>
<select name="designation" id="designation" class="txtbox">
<option value="">Select</option>

<?php
/***
We now show a filtered designation list by default.
Initially, membeship type is not selected so this will be blank
*******/
for($j=0;$j<$g_view['designation_count'];$j++){
?>
<option value="<?php echo $g_view['designation_list'][$j]['designation'];?>" <?php if($g_view['input']['designation']==$g_view['designation_list'][$j]['designation']){?>selected="selected"<?php }?>><?php echo $g_view['designation_list'][$j]['designation'];?></option>
<?php
}
?>   
</select><span class="err_txt"> *</span><br />
<span class="err_txt"><?php echo $g_view['err']['designation'];?></span>
</td>
</tr>

<tr>
<td>Location</td>
<td>
<select name="location" class="txtbox">
<option value="">Select</option>
<?php
for($i=0;$i<$g_view['country_count'];$i++){
?>
<option value="<?php echo $g_view['country_list'][$i]['name'];?>" <?php if($g_view['input']['location']==$g_view['country_list'][$i]['name']){?>selected="selected"<?php }?>><?php echo $g_view['country_list'][$i]['name'];?></option>
<?php
}
?>
</select><span class="err_txt"> *</span><br />
<span class="err_txt"><?php echo $g_view['err']['location'];?></span>
</td>
</tr>

<tr>
<td></td>
<td><input type="submit" name="submit" value="Add" /></td>
</tr>

</table>
</form>
</td>
</tr>
</table>