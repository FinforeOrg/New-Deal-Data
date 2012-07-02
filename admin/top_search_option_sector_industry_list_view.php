<table width="100%" cellpadding="5" cellspacing="0" border="1" style="border-collapse:collapse;">
<?php
if($g_view['msg']!=""){
?>
<tr>
<td colspan="6"><span class="msg_txt"><?php echo $g_view['msg'];?></span></td>
</tr>
<?php
}
?>
<tr>
<td colspan="6">
<form method="post" action="" >
<input name="action" type="hidden" value="add" />
<table cellpadding="0" cellspacing="5" border="0" >
<tr>
<td>Option Name</td>
<td><input type="text" name="name" value="" style="width:100px;" /><span class="err_txt"> *</span></td>
<td><input type="submit" name="submit" value="Add" />
</tr>
<tr>
<td colspan="2"><span class="err_txt"><?php echo $g_view['err']['name'];?></span></td>
<td>&nbsp;</td>
</tr>
</table>
</form>
</td>
</tr>

<tr bgcolor="#dec5b3" style="height:20px;">

<td><strong>Option Name</strong></td>
<td><strong>Group</strong></td>
<td><strong>#</strong></td>
<td>&nbsp;</td>
<td>&nbsp;</td>
</tr>
<?php
if($g_view['data_count']==0){
	?>
	<tr>
	  <td colspan="7">None found</td>
	</tr>
	<?php
}else{
	for($i=0;$i<$g_view['data_count'];$i++){
		?>
		<tr>
		<td><?php echo $g_view['data'][$i]['name'];?></td>
		<?php
		/***************
		sng:20/july/2011
		support to show the group name
		****************/
		?>
		<td><?php echo $g_view['data'][$i]['group_name'];?></td>
		<td><?php echo $g_view['data'][$i]['display_order'];?></td>
		<td>
		<a href="top_search_option_sector_industry_edit.php?option_id=<?php echo $g_view['data'][$i]['option_id'];?>">Edit</a>
		</td>
		<td>
		<a href="top_search_option_sector_industry_preset_mapping.php?option_id=<?php echo $g_view['data'][$i]['option_id'];?>">Mapping</a>
		</td>
		</tr>
		<?php
	}
}
?>
</table>