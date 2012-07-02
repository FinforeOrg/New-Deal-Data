<script type="text/javascript" src="../js/jquery-1.2.1.pack.js"></script>
<script type="text/javascript">
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
<table cellspacing="0" cellpadding="0" border="0">
<tr>
<td>
<form method="post" action="">
<input type="hidden" name="action" value="search_company" />
<table width="100%" cellspacing="0" cellpadding="10" border="0">
<tr>
<td>Name</td>
<td><input type="text" name="company_name" value="<?php echo $g_mc->view_to_view($_POST['company_name']);?>" style="width:100px;" /></td>
</tr>
<tr>
<td>Region</td>
<td>
<select name="region">
<option value="">Select</option>
<?php
for($i=0;$i<$g_view['region_count'];$i++){
	?>
	<option value="<?php echo $g_view['region_list'][$i]['id'];?>" <?php if($_POST['region']==$g_view['region_list'][$i]['id']){?>selected="selected"<?php }?>><?php echo $g_view['region_list'][$i]['name'];?></option>
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
<select name="sector" id="sector" onchange="return sector_changed();">
<option value="">Select</option>
<?php
for($i=0;$i<$g_view['sector_count'];$i++){
	?>
	<option value="<?php echo $g_view['sector_list'][$i]['sector'];?>" <?php if($_POST['sector']==$g_view['sector_list'][$i]['sector']){?>selected="selected"<?php }?>><?php echo $g_view['sector_list'][$i]['sector'];?></option>
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
	<option value="<?php echo $g_view['industry_list'][$i]['industry'];?>" <?php if($_POST['industry']==$g_view['industry_list'][$i]['industry']){?>selected="selected"<?php }?>><?php echo $g_view['industry_list'][$i]['industry'];?></option>
	<?php
}
?>
</select>
</td>
</tr>
<?php
/***
sng:7/may/2010
We are using another menu option to search for banks / law firms. So here we only allow search for companies
***/
?>
<tr>
<tr>
<td></td>
<td><input type="submit" name="submit" value="search" /></td>
</tr>
</table>
</form>
</td>
</tr>
</table>
<table width="100%" cellpadding="5" cellspacing="0" border="1" style="border-collapse:collapse;">
<?php
if($g_view['msg']!=""){
?>
<tr>
<td colspan="8"><span class="msg_txt"><?php echo $g_view['msg'];?></span></td>
</tr>
<?php
}
?>
<tr bgcolor="#dec5b3" style="height:20px;">

<td><strong>Name</strong></td>
<td><strong>HQ</strong></td>
<td><strong>Sector</strong></td>
<td><strong>Industry</strong></td>
<td><strong>Logo</strong></td>

<td>&nbsp;</td>
</tr>
<?php
if($g_view['data_count']==0){
	?>
	<tr>
	  <td colspan="8">No company data found</td>
	</tr>
	<?php
}else{
	
	for($i=0;$i<$g_view['data_count'];$i++){
		?>
		<tr>
		
		<td><?php echo $g_view['data'][$i]['name'];?></td>
		<td><?php echo $g_view['data'][$i]['hq_country'];?></td>
		<td><?php echo $g_view['data'][$i]['sector'];?></td>
		<td><?php echo $g_view['data'][$i]['industry'];?></td>
		<td>
		<?php
		if($g_view['data'][$i]['logo']!=""){
			?>
			<img src="../uploaded_img/logo/thumbnails/<?php echo $g_view['data'][$i]['logo'];?>" border="0" />
			<?php
		}
		?>
		</td>
		
		<td>
		<form method="post" action="company_edit.php">
		<input type="hidden" name="company_id" value="<?php echo $g_view['data'][$i]['company_id'];?>" />
		<input type="submit" value="Edit" />
		</form>
		</td>
		</tr>
		<?php
	}
	
}
?>
</table>