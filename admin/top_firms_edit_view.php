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
function back_to_list(){
	window.location = "top_firms_list.php?start=<?php echo $g_view['start'];?>";
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
<td>Caption</td>
<td>
<input name="caption" type="text" style="width:200px;" value="<?php echo $g_view['data']['caption'];?>" /><span class="err_txt"> *</span><br />
<span class="err_txt"><?php echo $g_view['err']['caption'];?></span>
</td>
</tr>

<tr>
<td>For</td>
<td>
<?php
/***
sng:26/May/2010
Once the partner type is set, we will not change it.
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
<input name="year" type="text" style="width:80px;" value="<?php echo $g_view['data']['year'];?>" /><span class="err_txt"> *</span><br />
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
<!--the current items in the list-->
<table width="100%" cellpadding="5" cellspacing="0" border="1" style="border-collapse:collapse">
<?php
$firm_count = count($g_view['data']['firms']);
if(0==$firm_count){
?>
<tr><td>No Firms in this list</td>
<?php
}else{
	for($a=0;$a < $firm_count;$a++){
		?>
		<tr><td><?php echo $g_view['data']['firms'][$a][1];?></td></tr>
		<?php
	}
}
?>
</table>
<!--the current items in the list-->
</td>
</tr>

</table>
</form>
</td>
</tr>

</table>