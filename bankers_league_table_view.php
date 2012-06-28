<script type="text/javascript" src="js/prototype.js"></script>
<?php
require_once("league_table_filter_support_js.php");
?>
<script type="text/javascript">
function post_filter_data(){
	document.getElementById("toppers").innerHTML = "<p>Generating</p>";
	new Ajax.Request('ajax/individual_league_table_creator.php', {
		method: 'post',
		parameters: $('league_table_filter').serialize(true),
		onSuccess: function(transport){
			
			document.getElementById("toppers").innerHTML = transport.responseText;
		},
		onFailure: function(){
			document.getElementById("toppers").innerHTML = "<p>Error</p>";
		}
	});
}
</script>
<script type="text/javascript">
function goto_login(){
	window.location.href = "index.php";
}
</script>
<table width="100%" cellpadding="0" cellspacing="0" border="0">
<tr>
<td style="text-align:left"><h1>League Table for Bankers</h1></td>
<?php
/***
1/jun/2010
Now the link is in league table page. That page is visible to everyone
****/
?>
<!--<td style="text-align:right;"><a href="top_banks_per_criteria.php" class="black_link">See details of the 5 best banks</a></td>-->
</tr>
</table>
<table width="100%" cellpadding="0" cellspacing="5" class="registercontent">
<tr>
<th>Customize your league table</th>
<th>Top 10 Bankers</th>
</tr>
<tr>
<td>
<!--the filters-->
<form method="post" id="league_table_filter" action="bankers_league_table_detail.php">
<table width="100%" cellpadding="0" cellspacing="0" class="company">
<tr>
<td>For</td>
<td>
<input type="hidden" name="member_type" id="member_type" value="banker" />Banker
</td>
</tr>

<tr>
<td>
Deal Category
</td>
<td>
<select name="deal_cat_name" id="deal_cat_name" onchange="return deal_cat_changed();">
<option value="">Select</option>
<?php
for($i=0;$i<$g_view['cat_count'];$i++){
	?>
	<option value="<?php echo $g_view['cat_list'][$i]['type'];?>" <?php if($_POST==$g_view['cat_list'][$i]['type']){?>selected="selected"<?php }?>><?php echo $g_view['cat_list'][$i]['type'];?></option>
	<?php
}
?>
</select>
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
	<option value="<?php echo $g_view['subcat_list'][$i]['subtype1'];?>" <?php if($_POST['deal_subcat1_name']==$g_view['subcat_list'][$i]['subtype1']){?>selected="selected"<?php }?>><?php echo $g_view['subcat_list'][$i]['subtype1'];?></option>
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
	<option value="<?php echo $g_view['sub_subcat_list'][$i]['subtype2'];?>" <?php if($_POST['deal_subcat2_name']==$g_view['sub_subcat_list'][$i]['subtype2']){?>selected="selected"<?php }?>><?php echo $g_view['sub_subcat_list'][$i]['subtype2'];?></option>
	<?php
}
?>
</select>
</td>
</tr>

<tr>
<td>Year</td>
<td>
<input name="year" id="year" type="text" style="width:80px;" value="<?php echo $_POST['year'];?>" />
</td>
</tr>

<tr>
<td>Region</td>
<td>
<select name="region" id="region">
<option value="">Select</option>
<?php
for($i=0;$i<$g_view['region_count'];$i++){
	?>
	<option value="<?php echo $g_view['region_list'][$i]['name'];?>" <?php if($_POST['region']==$g_view['region_list'][$i]['name']){?>selected="selected"<?php }?>><?php echo $g_view['region_list'][$i]['name'];?></option>
	<?php
}
?>
</select>
</td>
</tr>

<tr>
<td>Country</td>
<td>
<select name="country" id="country">
<option value="">Select</option>
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
<td>Sector</td>
<td>
<select name="sector" id="sector">
	<option value="">Any Sector</option>
	<?php
	for($j=0;$j<$g_view['sector_count'];$j++){
	?>
	<option value="<?php echo $g_view['sector_list'][$j]['sector'];?>" <?php if($_POST['sector']==$g_view['sector_list'][$j]['sector']){?>selected="selected"<?php }?> ><?php echo $g_view['sector_list'][$j]['sector'];?></option>
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
<select name="ranking_criteria" id="ranking_criteria">
<option value="num_deals" <?php if(!isset($_POST['ranking_criteria'])||($_POST['ranking_criteria']=="num_deals")){?>selected="selected"<?php }?>>Total number of deals</option>
<option value="total_deal_value" <?php if($_POST['ranking_criteria']=="total_deal_value"){?>selected="selected"<?php }?>>Total deal value</option>
<option value="total_adjusted_deal_value" <?php if($_POST['ranking_criteria']=="total_adjusted_deal_value"){?>selected="selected"<?php }?>>Total adjusted deal value</option>
</select><span class="err_txt"> *</span><br />
<span class="err_txt" id="err_ranking_criteria"></span>
</td>
</tr>

<tr>
<td>&nbsp;</td>
<td>
<input type="button" name="generate" value="Generate" class="btn_auto" onclick="post_filter_data();" />
</td>
</tr>

<tr><td colspan="2">&nbsp;</td></tr>
<?php
/***
sng:21/apr/2010
Only logged in members can see the actual stat numbers
********/
if(!$g_account->is_site_member_logged()){
?>
<tr>
<td>&nbsp;</td>
<td><input type="button" class="btn_auto" value="Login to view details" onclick="goto_login();" /></td>
</tr>
<?php
}else{
?>
<tr>
<td>&nbsp;</td>
<td><input type="submit" class="btn_auto" value="Details" /></td>
</tr>
<?php
}
?>


</table>
</form>
<!--the filters-->
</td>

<td style="text-align:center">
<!--the chart area-->
<div id="toppers" style="width:400px; height:auto;">
</div>
<!--the chart area-->
</td>
</tr>
</table>
<script type="text/javascript">
post_filter_data();
</script>