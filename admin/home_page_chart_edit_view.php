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
		var sector_name_selected = sector_obj.options[offset_selected].value;
		//fetch the list of industries for this sector
		$.post("ajax/industry_list_for_sector.php", {sector: ""+sector_name_selected+""}, function(data){
				if(data.length >0) {
					$('#industry').html(data);
				}
		});
	}
}
</script>
<script type="text/javascript">
function back_to_list(){
	window.location = "home_page_chart_list.php?start=<?php echo $g_view['start'];?>";
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
		//get the type
		var firm_type = document.getElementById('assign_company_type').value;
		
		$('#assign_firm_searching').html("searching...");
		$.post("ajax/get_firm.php", {name: ""+inputString+"",type: ""+firm_type+""}, function(data){
			$('#assign_firm_searching').html("");
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
	$('#assign_firm_name').val(name);
	$('#assign_company_id').val(company_id);
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
<input type="hidden" name="action" value="edit"/>
<input name="id" type="hidden" value="<?php echo $g_view['data']['id'];?>">
<input name="start" type="hidden" value="<?php echo $g_view['start'];?>">
<table width="100%" cellpadding="5" cellspacing="0" border="0">
<tr>
<td>Chart Caption</td>
<td>
<input name="name" type="text" style="width:200px;" value="<?php echo $g_view['data']['name'];?>" /><span class="err_txt"> *</span><br />
<span class="err_txt"><?php echo $g_view['err']['name'];?></span>
</td>
</tr>

<tr>
<td>For</td>
<td>
<?php
/***
sng:26/May/2010
Once the partner type is set, we will not change it. Now we assign a chart to a bank or law firm. So we should not change
a bank chart to law firm chart
We will use the value of $hidden_partner_type while assigning the chart to a firm
*******/
if(($g_view['data']['partner_type']=="")||($g_view['data']['partner_type']=="law firm")){
	$hidden_partner_type="law firm";
}
if($g_view['data']['partner_type']=="bank"){
	$hidden_partner_type="bank";
}
?>
<input type="hidden" name="partner_type" value="<?php echo $hidden_partner_type;?>" />
<?php echo $hidden_partner_type;?>
<br />
<span class="err_txt"><?php echo $g_view['err']['partner_type'];?></span>
</td>
</tr>

<tr>
<td>Category</td>
<td>
<select name="deal_cat_name" id="deal_cat_name" onchange="return deal_cat_changed();">
<option value="">Select</option>
<?php
for($i=0;$i<$g_view['cat_count'];$i++){
	?>
	<option value="<?php echo $g_view['cat_list'][$i]['type'];?>" <?php if($g_view['data']['deal_cat_name']==$g_view['cat_list'][$i]['type']){?>selected="selected"<?php }?>><?php echo $g_view['cat_list'][$i]['type'];?></option>
	<?php
}
?>
</select><span class="err_txt"> *</span><br />
<span class="err_txt"><?php echo $g_view['err']['deal_cat_name'];?></span>
</td>
</tr>

<tr>
<td>Sub Category</td>
<td>
<select name="deal_subcat1_name" id="deal_subcat1_name" onchange="return deal_subcat_changed();">
<option value="">Select</option>
<?php
for($i=0;$i<$g_view['subcat_count'];$i++){
	?>
	<option value="<?php echo $g_view['subcat_list'][$i]['subtype1'];?>" <?php if($g_view['data']['deal_subcat1_name']==$g_view['subcat_list'][$i]['subtype1']){?>selected="selected"<?php }?>><?php echo $g_view['subcat_list'][$i]['subtype1'];?></option>
	<?php
}
?>
</select>
</td>
</tr>

<tr>
<td>Sub sub Category</td>
<td>
<select name="deal_subcat2_name" id="deal_subcat2_name">
<option value="">Select</option>
<?php
for($i=0;$i<$g_view['sub_subcat_count'];$i++){
	?>
	<option value="<?php echo $g_view['sub_subcat_list'][$i]['subtype2'];?>" <?php if($g_view['data']['deal_subcat2_name']==$g_view['sub_subcat_list'][$i]['subtype2']){?>selected="selected"<?php }?>><?php echo $g_view['sub_subcat_list'][$i]['subtype2'];?></option>
	<?php
}
?>
</select>
</td>
</tr>

<tr>
<td>Year</td>
<td>
<?php
/***************************
sng:29/sep/2010
we now have a master list of date range and so we only send the id
in the chart creation logic, we run another query to get the date range
**************************/
?>
<select name="year">
<option value="">Select</option>
<?php
for($i=0;$i<$g_view['date_count'];$i++){
	?>
	<option value="<?php echo $g_view['date_list'][$i]['id'];?>" <?php if($g_view['data']['year']==$g_view['date_list'][$i]['id']){?>selected="selected"<?php }?>><?php echo $g_view['date_list'][$i]['name'];?></option>
	<?php
}
?>
</select><span class="err_txt"> *</span><br />
<span class="err_txt"><?php echo $g_view['err']['year'];?></span>
</td>
</tr>

<tr>
<td>Region</td>
<td>
<select name="region">
<option value="">Select</option>
<?php
for($i=0;$i<$g_view['region_count'];$i++){
	?>
	<option value="<?php echo $g_view['region_list'][$i]['name'];?>" <?php if($g_view['data']['region']==$g_view['region_list'][$i]['name']){?>selected="selected"<?php }?>><?php echo $g_view['region_list'][$i]['name'];?></option>
	<?php
}
?>
</select><br />
<span class="err_txt"><?php echo $g_view['err']['region'];?></span>
</td>
</tr>

<tr>
<td>Country</td>
<td>
<select name="country">
<option value="">Select</option>
<?php
for($i=0;$i<$g_view['country_count'];$i++){
	?>
	<option value="<?php echo $g_view['country_list'][$i]['name'];?>" <?php if($g_view['data']['country']==$g_view['country_list'][$i]['name']){?>selected="selected"<?php }?>><?php echo $g_view['country_list'][$i]['name'];?></option>
	<?php
}
?>
</select><br />
<span class="err_txt"><?php echo $g_view['err']['country'];?></span>
</td>
</tr>

<tr>
<td>Sector</td>
<td>
<select name="sector" id="sector" onchange="return sector_changed();">
<option value="">Select</option>
<?php
for($i=0;$i<$g_view['sector_count'];$i++){
	?>
	<option value="<?php echo $g_view['sector_list'][$i]['sector'];?>" <?php if($g_view['data']['sector']==$g_view['sector_list'][$i]['sector']){?>selected="selected"<?php }?>><?php echo $g_view['sector_list'][$i]['sector'];?></option>
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
<option value="">Select</option>
<?php
for($i=0;$i<$g_view['industry_count'];$i++){
	?>
	<option value="<?php echo $g_view['industry_list'][$i]['industry'];?>" <?php if($g_view['data']['industry']==$g_view['industry_list'][$i]['industry']){?>selected="selected"<?php }?>><?php echo $g_view['industry_list'][$i]['industry'];?></option>
	<?php
}
?>
</select>
</td>
</tr>

<tr>
<td>
Ranking based on 
</td>
<td>
<select name="ranking_criteria">
<option value="num_deals" <?php if(!isset($g_view['data']['ranking_criteria'])||($g_view['data']['ranking_criteria']=="num_deals")){?>selected="selected"<?php }?>>Total number of deals</option>
<option value="total_deal_value" <?php if($g_view['data']['ranking_criteria']=="total_deal_value"){?>selected="selected"<?php }?>>Total deal value</option>
<option value="total_adjusted_deal_value" <?php if($g_view['data']['ranking_criteria']=="total_adjusted_deal_value"){?>selected="selected"<?php }?>>Total adjusted deal value</option>
</select><span class="err_txt"> *</span><br />
<span class="err_txt"><?php echo $g_view['err']['ranking_criteria'];?></span>
</td>
</tr>

<tr>
<td></td>
<td><input type="submit" name="submit" value="Update" />&nbsp;&nbsp;&nbsp;&nbsp;<input type="button" value="Back" onclick="back_to_list()" /></td>
</tr>

<tr>
<td colspan="2">
    <div id="<?php echo $g_view['data']['containerId'];?>" class="chart">
    </div>
    <?php echo base64_decode($g_view['data']['img']);?>
</td>
</tr>

</table>
</form>
</td>
</tr>
<tr>
<td>
<?php
/***
sng:26/May/2010
We might want to assign this chart to a bank or law firm so that when we show
the bank page in the front, we may show the chart.
Note: Same chart can be assigned to multiple banks so we do not show here the banks where this chart
is assigned. For that, there is another menu option
Note: we also blocked admin from changing the firm type. So we use the value here without any fear.
The ajax code get the banks or law firm based on firm type
*******/
?>
<form method="post" action="">
<input name="id" type="hidden" value="<?php echo $g_view['id'];?>">
<input name="start" type="hidden" value="<?php echo $g_view['start'];?>">
<input type="hidden" name="action" value="assign_to_firm"/>
<input type="hidden" name="company_type" id="assign_company_type" value="<?php echo $hidden_partner_type;?>" />
<table width="100%" cellpadding="0" cellspacing="0" border="0">
<tr>
<td>
<input type="hidden" name="assign_company_id" id="assign_company_id" value="<?php echo $_POST['assign_company_id'];?>" />
Type the first few letters. If the firm is found, it will be shown in the list. Please select the firm.<br />
<input type="text" name="assign_firm_name" id="assign_firm_name" class="txtbox" style="width:200px;" value="<?php echo $g_mc->view_to_view($_POST['assign_firm_name']);?>" onkeyup="lookup(this.value);" onblur="hide_suggestion();" /><br />
		<span id="assign_firm_searching"></span><br />
		<span class="err_txt"><?php echo $g_view['err']['assign_company_id'];?></span>
		<div class="suggestionsBox" id="suggestions" style="display: none;">
		<img src="images/upArrow.png" style="position: relative; top: -18px; left: 30px;" alt="upArrow"  />
		<div class="suggestionList" id="autoSuggestionsList"></div>
		</div>
</td>
</tr>
<tr>
<td>
<input type="submit" name="submit" value="Assign" />
</td>
</tr>
</table>
</form>
</td>
</tr>
<?php
/************************************************************
sng:22/sep/2011
show the firms associated with this chart

26/sep/2011
admin will be able to remove an associated firm
********/
?>
<tr><td><strong>Firms which shows this chart</strong></td></tr>
<tr>
<td>
<table border="1" style="border-collapse:collapse" cellpadding="5" cellspacing="0">
<tr>
<td><strong>Firm</strong></td>
<td></td>
</tr>
<?php
if(0 == $g_view['firm_count']){
?>
<tr><td>No firms associated with this chart</td></tr>
<?php
}else{
	for($j=0;$j<$g_view['firm_count'];$j++){
		?>
		<tr>
		<td><?php echo $g_view['firm_list'][$j]['name'];?></td>
		<td>
		<form method="post" action="">
		<input name="id" type="hidden" value="<?php echo $g_view['id'];?>">
		<input name="start" type="hidden" value="<?php echo $g_view['start'];?>">
		<input type="hidden" name="firm_assoc_id" value="<?php echo $g_view['firm_list'][$j]['id'];?>" />
		<input type="hidden" name="action" value="remove_firm"/>
		<input type="submit" name="submit" value="remove" />
		</form>
		</td>
		</tr>
		<?php
	}
}
?>
</table>
</td>
</tr>
</table>