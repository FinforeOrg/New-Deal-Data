<table width="100%" cellpadding="5" cellspacing="0" border="0">
<tr>
<td><span class="msg_txt"><?php echo $g_view['msg'];?></span></td>
</tr>
<tr>
<td>

	<table width="100%" cellpadding="5" cellspacing="0" border="0">
	<tr>
	<td>Region / Country</td>
	<td><?php echo $g_view['series']['region_country_name'];?></td>
	</tr>
	
	<tr>
	<td>Sector / Industry</td>
	<td><?php if($g_view['series']['sector_industry_name']==NULL) echo "Average"; else echo $g_view['series']['sector_industry_name'];?></td>
	</tr>
	
	<tr>
	<td>Type</td>
	<td><?php echo $g_view['series']['type_name'];?></td>
	</tr>
	</table>

</td>
</tr>

<tr>
<td>
<form method="post" action="">
<input type="hidden" name="action" value="add" />
<input type="hidden" name="series_id" value="<?php echo $g_view['series_id'];?>" />
<table width="100%" cellpadding="5" cellspacing="0" border="0">
<tr>
<td>Year</td>
<td><input name="year" value="<?php echo $_POST['year'];?>" style="width:100px;" /><span class="err_txt"> *</span><br />
<span class="err_txt"><?php echo $g_view['err']['year'];?></span>
</td>
</tr>

<tr>
<td>Quarter</td>
<td><input name="quarter" value="<?php echo $_POST['quarter'];?>" style="width:100px;" /><span class="err_txt"> *</span><br />
<span class="err_txt"><?php echo $g_view['err']['quarter'];?></span>
</td>
</tr>

<tr>
<td>Value</td>
<td><input name="value" value="<?php echo $_POST['value'];?>" style="width:100px;" /><span class="err_txt"> *</span><br />
<span class="err_txt"><?php echo $g_view['err']['value'];?></span>
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


<table width="100%" cellpadding="10" cellspacing="0" border="1" style="border-collapse:collapse;">
<tr bgcolor="#dec5b3" style="height:20px;">
<td>Year</td>
<td>Quarter</td>
<td>Value</td>
</tr>
<?php
if($g_view['series_points_count']==0){
	?>
	<tr>
	  <td colspan="6">No data found</td>
	</tr>
	<?php
}else{
	for($i=0;$i<$g_view['series_points_count'];$i++){
		?>
		<tr>
		<td><?php echo $g_view['series_points'][$i]['year'];?></td>
		<td><?php echo $g_view['series_points'][$i]['quarter'];?></td>
		<td><?php echo $g_view['series_points'][$i]['value'];?></td>
		</tr>
		<?php
	}
}
?>
</table>