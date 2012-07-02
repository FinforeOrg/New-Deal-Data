<script type="text/javascript">
function show_note(note_id){
	var div_id = "note"+note_id;
	document.getElementById(div_id).style.display="table-row";
}
</script>
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

<td><strong>Name</strong></td>
<td><strong>Type</strong></td>
<td>&nbsp;</td>
<td>&nbsp;</td>
<td>&nbsp;</td>

</tr>
<?php
if($g_view['data_count']==0){
	?>
	<tr>
	  <td colspan="6">No duplicates</td>
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
		
		<td><?php echo $g_view['data'][$i]['name'];?></td>
		
		<td><?php echo $g_view['data'][$i]['type'];?></td>
		<td>
		<?php
		if($g_view['data'][$i]['private_note']!=""){
			?>
			<span onclick="show_note('<?php echo $g_view['data'][$i]['company_id'];?>');"><img src="images/icon_note_blu.gif" /></span>
			<?php
		}
		?>
		</td>
		<td>
		<?php
		/********************
		sng:6/feb/2011
		admin may want to edit the company or bank/law firm to put note in case of duplicate. since
		company edit and bank/law firm edit has different page, we check the type
		**********/
		if(($g_view['data'][$i]['type']=="bank")||($g_view['data'][$i]['type']=="law firm")){
			?>
			<form method="post" action="blf_edit.php">
			<input type="hidden" name="company_id" value="<?php echo $g_view['data'][$i]['company_id'];?>" />
			<input type="submit" value="Edit" />
			</form>
			<?php
		}
		if($g_view['data'][$i]['type']=="company"){
			?>
			<form method="post" action="company_edit.php">
			<input type="hidden" name="company_id" value="<?php echo $g_view['data'][$i]['company_id'];?>" />
			<input type="submit" value="Edit" />
			</form>
			<?php
		}
		?>
		
		</td>
		<td>
		<form method="post" action="">
		<input name="action" type="hidden" value="del_company" />
		<input name="company_id" type="hidden" value="<?php echo $g_view['data'][$i]['company_id'];?>" />
		<input type="submit" name="submit" value="Delete" />
		</form>
		</td>
		</tr>
		<?php
		/**************
		sng:6/feb/2011
		support for admin private note
		**************/
		?>
		<tr id="note<?php echo $g_view['data'][$i]['company_id'];?>" style="display:none">
		<td colspan="8" valign="top" style="border-top:1px solid #CCCCCC; border-bottom:1px solid #000000; padding-bottom:20px;">
		<?php echo $g_view['data'][$i]['private_note'];?></td>
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
		<a href="misc_probable_duplicate_firms.php?start=<?php echo $prev_offset;?>">Prev</a>
		<?php
	}
	if($g_view['data_count'] > $g_view['num_to_show']){
		$next_offset = $g_view['start'] + $g_view['num_to_show'];
		?>
		&nbsp;&nbsp;&nbsp;<a href="misc_probable_duplicate_firms.php?start=<?php echo $next_offset;?>">Next</a>
		<?php
	}
	?>
	</td>
	</tr>
	<?php
}
?>
</table>