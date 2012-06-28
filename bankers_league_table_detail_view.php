<?php
require_once("league_table_filter_support_js.php");
?>
<script type="text/javascript">
function goto_league_table(){
	//submit the filter data to league_table.php
	document.getElementById("league_table_filter").action="bankers_league_table.php";
	document.getElementById("league_table_filter").submit();
}
</script>
<table width="100%" cellpadding="0" cellspacing="0" border="0">
<tr>
<td>
<!--filter area-->
<form method="post" action="" id="league_table_filter">
	<table width="100%"  cellspacing="0" cellpadding="0" >
	<tr>
	<td>
	<input type="hidden" name="member_type" id="member_type" value="banker" />Banker
	</td>
	
	
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
	<td>&nbsp;</td>
	</tr>
	<tr>
	<td colspan="7" style="hight:40px;">&nbsp;</td>
	</tr>
	
	<tr>
	
	<td>
	<select name="sector">
	<option value="">Any Sector</option>
	<?php
	for($j=0;$j<$g_view['sector_count'];$j++){
	?>
	<option value="<?php echo $g_view['sector_list'][$j]['sector'];?>" <?php if($_POST['sector']==$g_view['sector_list'][$j]['sector']){?>selected="selected"<?php }?> ><?php echo $g_view['sector_list'][$j]['sector'];?></option>
	<?php
	}
	?>
	</select>		</td>
	
	<td>
	Year: <input name="year" type="text" style="width:80px;" value="<?php echo $_POST['year'];?>" />
	</td>
	
	<td>
	<select name="ranking_criteria" id="ranking_criteria">
<option value="num_deals" <?php if(!isset($_POST['ranking_criteria'])||($_POST['ranking_criteria']=="num_deals")){?>selected="selected"<?php }?>>Total number of deals</option>
<option value="total_deal_value" <?php if($_POST['ranking_criteria']=="total_deal_value"){?>selected="selected"<?php }?>>Total deal value</option>
<option value="total_adjusted_deal_value" <?php if($_POST['ranking_criteria']=="total_adjusted_deal_value"){?>selected="selected"<?php }?>>Total adjusted deal value</option>
</select><span class="err_txt"> *</span><br />
<span class="err_txt" id="err_ranking_criteria"></span>
	</td>
	
	<td colspan="1">&nbsp;</td>
	<td style="text-align: right;"><input name="chart" type="submit" class="btn_auto" value="Bankers" onclick=" return goto_league_table();" /></td>
	<td style="text-align: right;"><input name="submit" type="submit" class="btn_auto" value="Update" /></td>
	
	</tr>
	<tr>
	<td colspan="7" style="hight:40px;">&nbsp;</td>
	</tr>
	
	</table>
</form>
<!--filter area-->
</td>
</tr>
<tr>
<td>
<!--listing data-->
<table width="100%" cellpadding="0" cellspacing="0" class="company">
<tr>
<th>Rank</th>
<th>Name</th>
<th>Firm</th>
<th>Tombstone #</th>
<th>Tombstone $billion</th>
<th>Adjusted $billion</th>
</tr>
<?php
if(0==$g_view['data_count']){
	?>
	<tr>
	<td colspan="5">
	None found.
	</td>
	</tr>
	<?php
}else{
	//we fetched one extra
	if($g_view['data_count'] > $g_view['num_to_show']){
		$total = $g_view['num_to_show'];
	}else{
		$total = $g_view['data_count'];
	}
	////////////////////////////////////////////////////////////////////
	for($j=0;$j<$total;$j++){
		?>
		<tr>
		<td><?php echo $g_view['start_offset']+$j+1;?></td>
		<td><a href="profile.php?mem_id=<?php echo $g_view['data'][$j]['member_id'];?>"><?php echo $g_view['data'][$j]['f_name'];?> <?php echo $g_view['data'][$j]['l_name'];?></a></td>
		<td><?php echo $g_view['data'][$j]['firm_name'];?></td>
		<td><?php echo $g_view['data'][$j]['num_deals'];?></td>
		<td><?php echo number_format($g_view['data'][$j]['total_deal_value'],2);?></td>
		<td><?php echo number_format($g_view['data'][$j]['total_adjusted_deal_value'],2);?></td>
		</tr>
		<?php
	}
	//////////////////////////////////////
	?>
	<form id="pagination_helper" method="post" action="league_table_detail.php">
	<input type="hidden" name="member_type" id="member_type" value="banker" />
	<input type="hidden" name="region" value="<?php echo $_POST['region'];?>" />
	<input type="hidden" name="country" value="<?php echo $_POST['country'];?>" />
	<input type="hidden" name="deal_cat_name" value="<?php echo $_POST['deal_cat_name'];?>" />
	<input type="hidden" name="deal_subcat1_name" value="<?php echo $_POST['deal_subcat1_name'];?>" />
	<input type="hidden" name="deal_subcat2_name" value="<?php echo $_POST['deal_subcat2_name'];?>" />
	<input type="hidden" name="sector" value="<?php echo $_POST['sector'];?>" />
	<input type="hidden" name="year" value="<?php echo $_POST['year'];?>" />
	<input type="hidden" name="ranking_criteria" value="<?php echo $_POST['ranking_criteria'];?>" />
	<input type="hidden" name="start" id="pagination_helper_start" value="0" />
	</form>
	
	<script type="text/javascript">
	function go_page(offset){
		document.getElementById('pagination_helper_start').value = offset;
		document.getElementById('pagination_helper').submit();
		return false;
	}
	</script>
	<tr>
	<td colspan="5" style="text-align:right;">
	<?php
	if($g_view['start_offset'] > 0){
		?>
		<a class="link_as_button" href="#" onclick="return go_page(<?php echo $g_view['start_offset']-$g_view['num_to_show'];?>);">Prev</a>
		<?php
	}
	if($g_view['data_count'] > $g_view['num_to_show']){
		?>
		&nbsp;&nbsp;&nbsp;<a class="link_as_button" href="#" onclick="return go_page(<?php echo $g_view['start_offset']+$g_view['num_to_show'];?>);">Next</a>
		<?php
	}
	?>
	</td>
	</tr>
	<?php
}
?>
</table>
<!--listing data-->
</td>
</tr>
</table>