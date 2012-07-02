<script type="text/javascript">
function deal_cat_changed(){
	
	var type_obj = document.getElementById('deal_cat_name');
	var offset_selected = type_obj.selectedIndex;
	if(offset_selected != 0){
		var deal_cat_name_selected = type_obj.options[offset_selected].value;
		//fetch the list of deal sub categories
		$.post("ajax/deal_subtype1_list.php", {deal_cat_name: ""+deal_cat_name_selected+""}, function(data){
			
				if(data.length >0) {
					$('#deal_subcat1_name').html(data);
				}
		});
	}
}

function deal_subcat_changed(){
	
	var type_obj = document.getElementById('deal_cat_name');
	var offset_selected = type_obj.selectedIndex;
	var type1_obj = document.getElementById('deal_subcat1_name');
	var offset1_selected = type1_obj.selectedIndex;
	
	if((offset_selected != 0)&&(offset1_selected!=0)){
		
		var deal_cat_name_selected = type_obj.options[offset_selected].value;
		var deal_subcat_name_selected = type1_obj.options[offset1_selected].value;
		//fetch the list of deal sub categories
		$.post("ajax/deal_subtype2_list.php", {deal_cat_name: ""+deal_cat_name_selected+"",deal_subcat_name: ""+deal_subcat_name_selected+""}, function(data){
			//alert(data);
				if(data.length >0) {
					$('#deal_subcat2_name').html(data);
				}
		});
	}
}

function sector_changed(){
	var sector_obj = document.getElementById('sector');
	var offset_selected = sector_obj.selectedIndex;
	if(offset_selected != 0){
		var sector_selected = sector_obj.options[offset_selected].value;
		//fetch the list of industries
		$.post("ajax/industry_list_for_sector.php", {sector: ""+sector_selected+""}, function(data){
				if(data.length >0) {
					$('#industry').html(data);
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
		var firm_type = jQuery('input:radio[name=firm_type]:checked').val();

		
		$('#firm_searching').html("searching...");
		$.post("ajax/get_company_for_deal.php", {name: ""+inputString+"",type: firm_type}, function(data){
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
function fill(bank_id,name) {
	$('#firm_name').val(name);
	$('#partner_id').val(bank_id);
	setTimeout("$('#suggestions').hide();", 200);
}
function hide_suggestion(){
	setTimeout("$('#suggestions').hide();", 200);
}
</script>
<p><span class="msg_txt"><?php echo $g_view['msg'];?></span></p>
<p>
Add notification
</p>
<div>
<form method="post" action="manage_deal_participant_notification.php?start=<?php echo $g_view['start'];?>">
<input type="hidden" name="myaction" value="add" />
<input type="hidden" name="partner_id" id="partner_id" value="<?php echo $_POST['partner_id'];?>" />
<table width="100%" cellpadding="5" cellspacing="0" border="1" style="border-collapse:collapse;">

<tr>
<td>Firm</td>
<td>
Type the first few letters. If the bank is found, it will be shown in the list. Please select the bank.<br />
<input name="firm_type" type="radio" value="bank" <?php if(($_POST['firm_type']=="bank")||(!isset($_POST['firm_type']))||($_POST['firm_type']=="")){?>checked="checked"<?php }?>> Bank&nbsp;&nbsp;<input name="firm_type" type="radio" value="law firm" <?php if($_POST['firm_type']=="law firm"){?>checked="checked"<?php }?>> Law Firm
<input type="text" name="firm_name" id="firm_name" class="txtbox" style="width:200px;" value="<?php echo $_POST['firm_name'];?>" onkeyup="lookup(this.value);" onblur="hide_suggestion();" autocomplete="off" /><span class="err_txt">*</span><br />
		<span id="firm_searching"></span><br />
		<span class="err_txt"><?php echo $g_view['err']['firm_name'];?></span>
		<div class="suggestionsBox" id="suggestions" style="display: none;">
		<img src="images/upArrow.png" style="position: relative; top: -18px; left: 30px;" alt="upArrow"  />
		<div class="suggestionList" id="autoSuggestionsList"></div>
		</div>
</td>
</tr>

<tr>
<td>Email</td>
<td><input name="email" type="text" value="<?php echo $_POST['email'];?>" style="width:200px;"><span class="err_txt">*</span><br>
<span class="err_txt"><?php echo $g_view['err']['email'];?></span></td>
</tr>

<tr>
<td>Region</td>
<td>
<select name="region">
<option value="">Any Region</option>
<?php
for($i=0;$i<$g_view['region_count'];$i++){
?>
<option value="<?php echo $g_view['region_list'][$i]['name'];?>" <?php if($_POST['region']==$g_view['region_list'][$i]['name']){?>selected="selected"<?php }?> ><?php echo $g_view['region_list'][$i]['name'];?></option>
<?php
}
?>
</select>
</td>
</tr>

<tr>
<td>Country</td>
<td>
<select name="country">
<option value="">Any Country</option>
<?php
for($i=0;$i<$g_view['country_count'];$i++){
?>
<option value="<?php echo $g_view['country_list'][$i]['name'];?>" <?php if($_POST['country']==$g_view['country_list'][$i]['name']){?>selected="selected"<?php }?>><?php echo $g_view['country_list'][$i]['name'];?></option>
<?php
}
?>
</select>
</td>
</tr>

<tr>
<td>Deal Type </td>
<td>
<select name="deal_cat_name" id="deal_cat_name" onchange="return deal_cat_changed();">
<option value="">Any Type of Deal</option>
<?php
for($k=0;$k<$g_view['cat_count'];$k++){
?>
<option value="<?php echo $g_view['cat_list'][$k]['type'];?>" <?php if($_POST['deal_cat_name']==$g_view['cat_list'][$k]['type']){?>selected="selected"<?php }?>><?php echo $g_view['cat_list'][$k]['type'];?></option>
<?php
}
?>
</select>
</td>
</tr>

<tr>
<td>Deal Sub-type </td>
<td>
<select name="deal_subcat1_name" id="deal_subcat1_name" onchange="return deal_subcat_changed();">
<option value="">Select subtype</option>
<?php
for($i=0;$i<$g_view['subcat_count'];$i++){
?>
<option value="<?php echo $g_view['subcat_list'][$i]['subtype1'];?>" <?php if($_POST['deal_subcat1_name']==$g_view['subcat_list'][$i]['subtype1']){?>selected="selected"<?php }?>><?php echo $g_view['subcat_list'][$i]['subtype1'];?></option>
<?php
}
?>
</select>
</td>
</tr>

<tr>
<td>Deal Sub subtype</td>
<td>
<select name="deal_subcat2_name" id="deal_subcat2_name">
<option value="">Select sub subtype</option>
<?php
for($i=0;$i<$g_view['sub_subcat_count'];$i++){
?>
<option value="<?php echo $g_view['sub_subcat_list'][$i]['subtype2'];?>" <?php if($_POST['deal_subcat2_name']==$g_view['sub_subcat_list'][$i]['subtype2']){?>selected="selected"<?php }?>><?php echo $g_view['sub_subcat_list'][$i]['subtype2'];?></option>
<?php
}
?>
</select>
</td>
</tr>




<tr>
<td>Sector</td>
<td>
<select name="sector" id="sector" onchange="return sector_changed();">
<option value="">Any Sector</option>
<?php
for($i=0;$i<$g_view['sector_count'];$i++){
?>
<option value="<?php echo $g_view['sector_list'][$i]['sector'];?>" <?php if($_POST['sector']== $g_view['sector_list'][$i]['sector']){?>selected="selected"<?php }?>><?php echo $g_view['sector_list'][$i]['sector'];?></option>
<?php
}
?>
</select>
</td>
</tr>

<tr>
<td>Industry</td>
<td>
<select name="industry" id="industry">
<option value="">Any Industry</option>
<?php
for($j=0;$j<$g_view['industry_count'];$j++){
?>
<option value="<?php echo $g_view['industry_list'][$j]['industry'];?>" <?php if($_POST['industry']==$g_view['industry_list'][$j]['industry']){?>selected="selected"<?php }?> ><?php echo $g_view['industry_list'][$j]['industry'];?></option>
<?php
}
?>
</select>
</td>
</tr>

<tr>
<td></td>
<td><input type="submit" name="submit" value="add" /></td>
</tr>
</table>
</form>
</div>
<table width="100%" cellpadding="5" cellspacing="0" border="1" style="border-collapse:collapse;">
<tr bgcolor="#dec5b3" style="height:20px;">
<td><strong>Firm</strong></td>
<td><strong>Email</strong></td>
<td><strong>Deal Type</strong></td>
<td><strong>Region</strong></td>
<td><strong>Country</strong></td>
<td><strong>Sector</strong></td>
<td><strong>Industry</strong></td>
<td></td>
</tr>
<?php
if(0==$g_view['data_count']){
	?>
	<tr><td colspan="8">None found</td></tr>
	<?php
}else{
	if($g_view['data_count'] > $g_view['num_to_show']){
		$total = $g_view['num_to_show'];
	}else{
		$total = $g_view['data_count'];
	}
	for($j=0;$j<$total;$j++){
		?>
		<tr>
		<td><?php echo $g_view['data'][$j]['firm_name'];?></td>
		<td><?php echo $g_view['data'][$j]['email'];?></td>
		<td><?php echo $g_view['data'][$j]['deal_cat_name'];?>,<?php echo $g_view['data'][$j]['deal_subcat1_name'];?>,<?php echo $g_view['data'][$j]['deal_subcat2_name'];?></td>
		<td><?php echo $g_view['data'][$j]['region_name'];?></td>
		<td><?php echo $g_view['data'][$j]['country_name'];?></td>
		<td><?php echo $g_view['data'][$j]['deal_sector'];?></td>
		<td><?php echo $g_view['data'][$j]['deal_industry'];?></td>
		<td>
		<form method="post" action="manage_deal_participant_notification.php?start=<?php echo $g_view['start'];?>">
		<input type="hidden" name="myaction" value="delete" />
		<input type="hidden" name="notify_id" value="<?php echo $g_view['data'][$j]['notify_id'];?>" />
		<input type="submit" name="submit" value="delete" />
		</form>
		</td>
		</tr>
		<?php
	}
	?>
	<tr>
	<td colspan="8" style="text-align:right">
	<?php
	if($g_view['start'] > 0){
		$prev_offset = $g_view['start'] - $g_view['num_to_show'];
		?>
		<a href="deals_marked_as_error.php?start=<?php echo $prev_offset;?>">Prev</a>
		<?php
	}
	if($g_view['data_count'] > $g_view['num_to_show']){
		$next_offset = $g_view['start'] + $g_view['num_to_show'];
		?>
		&nbsp;&nbsp;&nbsp;<a href="manage_deal_participant_notification.php?start=<?php echo $next_offset;?>">Next</a>
		<?php
	}
	?>
	</td>
	</tr>
	<?php
}
?>
</table>