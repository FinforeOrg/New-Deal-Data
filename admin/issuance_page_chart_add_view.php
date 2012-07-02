<script type="text/javascript" src="../js/jquery-1.2.1.pack.js"></script>
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
function month_division_changed(){
	var month_div_obj = document.getElementById('month_division');
	var offset_selected = month_div_obj.selectedIndex;
	if(offset_selected != 0){
		var month_div_selected = month_div_obj.options[offset_selected].value;
		jQuery.post("../ajax/month_division_list.php", {month_div: ""+month_div_selected+""}, function(data){
				if(data.length >0) {
					//alert(data);
					jQuery('#month_division_list').html(data);
				}
		});
	}
}
</script>
<table width="100%" cellpadding="5" cellspacing="0" border="0">
<tr>
<td><span class="msg_txt"><?php echo $g_view['msg'];?></span></td>
</tr>
<tr>
<td>
<form method="post" action="">
<input type="hidden" name="action" value="create"/>
<table width="100%" cellpadding="5" cellspacing="0" border="0">
<tr>
<td>Chart Caption</td>
<td>
<input name="name" type="text" style="width:200px;" value="<?php echo $g_view['input']['name'];?>" /><span class="err_txt"> *</span><br />
<span class="err_txt"><?php echo $g_view['err']['name'];?></span>
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
	<option value="<?php echo $g_view['cat_list'][$i]['type'];?>" <?php if($g_view['input']['deal_cat_name']==$g_view['cat_list'][$i]['type']){?>selected="selected"<?php }?>><?php echo $g_view['cat_list'][$i]['type'];?></option>
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
	<option value="<?php echo $g_view['subcat_list'][$i]['subtype1'];?>" <?php if($g_view['input']['deal_subcat1_name']==$g_view['subcat_list'][$i]['subtype1']){?>selected="selected"<?php }?>><?php echo $g_view['subcat_list'][$i]['subtype1'];?></option>
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
	<option value="<?php echo $g_view['sub_subcat_list'][$i]['subtype2'];?>" <?php if($g_view['input']['deal_subcat2_name']==$g_view['sub_subcat_list'][$i]['subtype2']){?>selected="selected"<?php }?>><?php echo $g_view['sub_subcat_list'][$i]['subtype2'];?></option>
	<?php
}
?>
</select>
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
	<option value="<?php echo $g_view['region_list'][$i]['name'];?>" <?php if($g_view['input']['region']==$g_view['region_list'][$i]['name']){?>selected="selected"<?php }?>><?php echo $g_view['region_list'][$i]['name'];?></option>
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
	<option value="<?php echo $g_view['country_list'][$i]['name'];?>" <?php if($g_view['input']['country']==$g_view['country_list'][$i]['name']){?>selected="selected"<?php }?>><?php echo $g_view['country_list'][$i]['name'];?></option>
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
	<option value="<?php echo $g_view['sector_list'][$i]['sector'];?>" <?php if($g_view['input']['sector']==$g_view['sector_list'][$i]['sector']){?>selected="selected"<?php }?>><?php echo $g_view['sector_list'][$i]['sector'];?></option>
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
	<option value="<?php echo $g_view['industry_list'][$i]['industry'];?>" <?php if($g_view['input']['industry']==$g_view['industry_list'][$i]['industry']){?>selected="selected"<?php }?>><?php echo $g_view['industry_list'][$i]['industry'];?></option>
	<?php
}
?>
</select>
</td>
</tr>


<tr>
<td>Deal size
</td>
<td>
<select name="deal_size" id="deal_size">
<option value="">All deal sizes</option>
<?php
for($i=0;$i<$g_view['deal_size_filter_list_count'];$i++){
?>
<option value="<?php echo $g_view['deal_size_filter_list'][$i]['condition'];?>" <?php if($g_view['input']['deal_size']==$g_view['deal_size_filter_list'][$i]['condition']){?>selected="selected"<?php }?> ><?php echo $g_view['deal_size_filter_list'][$i]['caption'];?></option>
<?php
}
?>
</select>
</td>
</tr>

<?php
/********************************************************************************
sng:10/jan/2011
We added 2 fields - groupings and start from. Now, the year/month division is no longer just quarterly but
can be yearly or half yearly also. The user can select the starting point
**********/
?>
<tr>
<td>Grouping</td>
<td>
<select name="month_division" id="month_division" onchange="return month_division_changed();">
<option value="" <?php if($g_view['input']['month_division']==""){?>selected="selected"<?php }?>>Select</option>
<option value="q" <?php if($g_view['input']['month_division']=="q"){?>selected="selected"<?php }?>>Quarterly</option>
<option value="h" <?php if($g_view['input']['month_division']=="h"){?>selected="selected"<?php }?>>Semi-Annual</option>
<option value="y" <?php if($g_view['input']['month_division']=="y"){?>selected="selected"<?php }?>>Annual</option>
</select><span class="err_txt">*</span><br />
<span id="err_month_division" class="err_txt"></span>
</td>
</tr>

<tr>
<td>Start From</td>
<td>
<select name="month_division_list" id="month_division_list" >
<option value="" selected="selected">Select</option>
<?php
for($j=0;$j<$g_view['month_div_cnt'];$j++){
	?>
	<option value="<?php echo $g_view['month_div']['value_arr'][$j];?>" <?php if($g_view['input']['month_division_list']==$g_view['month_div']['value_arr'][$j]){?>selected="selected"<?php }?>><?php echo $g_view['month_div']['label_arr'][$j];?></option>
	<?php
}
?>
</select><span class="err_txt">*</span><br />
<span id="err_month_division_list" class="err_txt"></span>
</td>
</tr>
<?php
/*******************************************************************/
?>

<tr>
<td></td>
<td><input type="submit" name="submit" value="Create" /></td>


</tr>

</table>
</form>
</td>
</tr>
</table>