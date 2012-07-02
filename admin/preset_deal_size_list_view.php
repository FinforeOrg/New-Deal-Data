<?php
/*****
sng:17/nov/2010
Now deal size preset options can be primary or non primary. This is considered when creating the permutations
for mmt
******/
?>
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
<td colspan="7">
To specify a range like deals < 200, fill only the To field.<br />
To specify a range like deals > 250, fill only the From field.<br />
To specify a range like deals between 200 and 500, fill both.<br />
The values should be specified in <strong>billion</strong>
</td>
</tr>
<tr>
<td>Preset Name</td>
<td><input type="text" name="name" value="" style="width:100px;" /><span class="err_txt"> *</span></td>
<td>Deal value from</td>
<td><input type="text" name="from_billion" value="" style="width:100px;" /><span class="err_txt"> *</span></td>
<td>To</td>
<td><input type="text" name="to_billion" value="" style="width:100px;" /><span class="err_txt"> *</span></td>
<td><input type="submit" name="submit" value="Add" />
</tr>
<tr>
<td colspan="2"><span class="err_txt"><?php echo $g_view['err']['name'];?></span></td>
<td colspan="2"><span class="err_txt"><?php echo $g_view['err']['from_billion'];?></span></td>
<td colspan="2"><span class="err_txt"><?php echo $g_view['err']['to_billion'];?></span></td>
<td>&nbsp;</td>
</tr>
</table>
</form>
</td>
</tr>

<tr bgcolor="#dec5b3" style="height:20px;">

<td><strong>Preset Name</strong></td>
<td><strong>Value from (in billion $)</strong></td>
<td><strong>To (in billion $)</strong></td>
<td><strong>Primary</strong></td>
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
		<td><?php echo $g_view['data'][$i]['from_billion'];?></td>
		<td><?php echo $g_view['data'][$i]['to_billion'];?></td>
		<td><?php echo $g_view['data'][$i]['is_primary'];?></td>
		<td>
		<a href="preset_deal_size_edit.php?preset_id=<?php echo $g_view['data'][$i]['preset_id'];?>">Edit</a>
		</td>
		<td>
		<form action="" method="post">
		<input type="hidden" name="action" value="delete" />
		<input type="hidden" name="preset_id" value="<?php echo $g_view['data'][$i]['preset_id'];?>" />
		<input type="submit" name="submit" value="Delete" />
		</form>
		</td>
		</tr>
		<?php
	}
}
?>
</table>