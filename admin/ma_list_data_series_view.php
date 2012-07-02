<script>
function confirm_series_deletion(){
	var confirmation = confirm("Are you sure you want to delete the series along with all the data points?");
	if(confirmation){
		return true;
	}else{
		return false;
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
<input type="hidden" name="action" value="add" />
<table width="100%" cellpadding="5" cellspacing="0" border="0">


<tr>
<td>Region / Country</td>
<td>
<select name="metrics_region_country_id">
<option value="">Select</option>
<?php for($i=0;$i<$g_view['region_country_count'];$i++){
?>
<option value="<?php echo $g_view['region_country'][$i]['id'];?>" <?php if($_POST['metrics_region_country_id']==$g_view['region_country'][$i]['id']){?>selected="selected"<?php }?>><?php echo $g_view['region_country'][$i]['name'];?></option>
<?php
}
?>
</select><span class="err_txt"> *</span><br />
<span class="err_txt">
<?php echo $g_view['err']['metrics_region_country_id'];?></span>
</td>
</tr>

<tr>
<td>Sector / Industry</td>
<td>
<select name="metrics_sector_industry_id">
<option value="">Select</option>
<?php for($i=0;$i<$g_view['sector_industry_count'];$i++){
?>
<option value="<?php echo $g_view['sector_industry'][$i]['id'];?>" <?php if($_POST['metrics_sector_industry_id']==$g_view['sector_industry'][$i]['id']){?>selected="selected"<?php }?>><?php echo $g_view['sector_industry'][$i]['name'];?></option>
<?php
}
?>
</select><br /> (leave blank to indicate 'average' series)
</td>
</tr>

<tr>
<td>Type</td>
<td>
<select name="metrics_type_id">
<option value="">Select</option>
<?php for($i=0;$i<$g_view['type_count'];$i++){
?>
<option value="<?php echo $g_view['type'][$i]['type_id'];?>" <?php if($_POST['metrics_type_id']==$g_view['type'][$i]['type_id']){?>selected="selected"<?php }?>><?php echo $g_view['type'][$i]['type_name'];?></option>
<?php
}
?>
</select><span class="err_txt"> *</span><br />
<span class="err_txt">
<?php echo $g_view['err']['metrics_type_id'];?></span>
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

<table width="100%" cellpadding="0" cellspacing="0" border="1" style="border-collapse:collapse;">
<tr bgcolor="#dec5b3" style="height:20px;">
<td>Region / Country</td>
<td>Sector / Industry</td>
<td>Metric Type</td>
<td colspan="2"></td>
</tr>
<?php
if($g_view['series_count']==0){
	?>
	<tr>
	  <td colspan="6">No data found</td>
	</tr>
	<?php
}else{
	for($i=0;$i<$g_view['series_count'];$i++){
		?>
		<tr>
		<td><?php echo $g_view['series'][$i]['region_country_name'];?></td>
		<td><?php if($g_view['series'][$i]['sector_industry_name']==NULL) echo "Average"; else echo $g_view['series'][$i]['sector_industry_name'];?></td>
		<td><?php echo $g_view['series'][$i]['type_name'];?></td>
		<td>
		<form method="post" action="ma_series_data_points.php">
		<input type="hidden" name="series_id" value="<?php echo $g_view['series'][$i]['series_id'];?>" />
		<input type="submit" name="submit" value="View" />
		</form>
		</td>
		<td>
		<form method="post" action="" onsubmit="return confirm_series_deletion();">
		<input name="action" type="hidden" value="delete" />
		<input type="hidden" name="series_id" value="<?php echo $g_view['series'][$i]['series_id'];?>" />
		<input type="submit" name="submit" value="Delete" />
		</form>
		</td>
		</tr>
		<?php
	}
}
?>
</table>