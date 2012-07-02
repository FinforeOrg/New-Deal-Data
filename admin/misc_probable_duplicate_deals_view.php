<script type="text/javascript">
function goto_page(offset){
	var page = "misc_probable_duplicate_deals.php?start="+offset;
	var frm = document.getElementById("pagination_helper").action = page;
	document.getElementById("pagination_helper").submit();
	return false;
}
function show_note(note_id){
	var div_id = "note"+note_id;
	document.getElementById(div_id).style.display="table-row";
}
</script>
<table cellspacing="0" cellpadding="0" border="0">
<tr>
<td>
<form method="post" action="">
<input type="hidden" name="myaction" value="search_deal" />
<table cellspacing="0" cellpadding="5">
<tr>
<td>
<select name="deal_cat_name" id="deal_cat_name">
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
<td>Year: <input name="year" type="text" style="width:80px;" value="<?php echo $_POST['year'];?>" /></td>
<td style="text-align:right;"><input type="submit" name="submit" value="search" /></td>
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
<td colspan="6"><span class="msg_txt"><?php echo $g_view['msg'];?></span></td>
</tr>
<?php
}
?>
<tr bgcolor="#dec5b3" style="height:20px;">
<td><strong>Comapny</strong></td>
<td><strong>Date</strong></td>
<td><strong>Value</strong></td>
<td><strong>Type</strong></td>
<td>&nbsp;</td>
<td>&nbsp;</td>
</tr>
<?php
if($g_view['data_count']==0){
	?>
	<tr>
	  <td colspan="6">None found</td>
	</tr>
	<?php
}else{
	if($g_view['data_count'] > $g_view['num_to_show']){
		$total = $g_view['num_to_show'];
	}else{
		$total = $g_view['data_count'];
	}
	for($i=0;$i<$total;$i++){
		?>
		<tr>
		<td><?php echo $g_view['data'][$i]['company_name'];?></td>
		<td><?php echo $g_view['data'][$i]['date_of_deal'];?></td>
		<td><?php echo $g_view['data'][$i]['value_in_billion'];?></td>
		<td><?php echo $g_view['data'][$i]['deal_cat_name'];?> : <?php echo $g_view['data'][$i]['deal_subcat1_name'];?> : <?php echo $g_view['data'][$i]['deal_subcat2_name'];?></td>
		<td><?php
		if($g_view['data'][$i]['deal_private_note']!=""){
			?>
			<span onclick="show_note('<?php echo $g_view['data'][$i]['id'];?>');"><img src="images/icon_note_blu.gif" /></span>
			<?php
		}
		?>
		</td>
		<td>
		<form method="post" action="deal_edit.php">
		<input type="hidden" name="deal_id" value="<?php echo $g_view['data'][$i]['id'];?>" />
		<input type="submit" value="Edit" />
		</form>
		</td>
		
		
		</tr>
		<tr id="note<?php echo $g_view['data'][$i]['id'];?>" style="display:none">
		<td colspan="6" valign="top" style="border-top:1px solid #CCCCCC; border-bottom:1px solid #000000; padding-bottom:20px;">
		<?php echo $g_view['data'][$i]['deal_private_note'];?></td>
		</tr>
		<?php
	}
	?>
	<tr>
	<td colspan="6" style="text-align:right">
	<?php
	if($g_view['start'] > 0){
		$prev_offset = $g_view['start'] - $g_view['num_to_show'];
		?>
		<a href="#" onclick="return goto_page(<?php echo $prev_offset;?>);">Prev</a>
		<?php
	}
	if($g_view['data_count'] > $g_view['num_to_show']){
		$next_offset = $g_view['start'] + $g_view['num_to_show'];
		?>
		&nbsp;&nbsp;&nbsp;<a href="#" onclick="return goto_page(<?php echo $next_offset;?>);">Next</a>
		<?php
	}
	?>
	</td>
	</tr>
	<?php
}
?>
<form id="pagination_helper" method="post" action="">
<input type="hidden" name="myaction" value="search_deal" />
<input type="hidden" name="deal_cat_name" value="<?php echo $_POST['deal_cat_name'];?>" />
<input type="hidden" name="year" value="<?php echo $_POST['year'];?>" />
</form>
</table>