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
<td colspan="8"><span class="msg_txt"><?php echo $g_view['msg'];?></span></td>
</tr>
<?php
}
?>
<tr bgcolor="#dec5b3" style="height:20px;">

<td><strong>Name</strong></td>
<td><strong>Type</strong></td>
<td><strong>HQ</strong></td>
<td><strong>Sector</strong></td>
<td><strong>Industry</strong></td>
<td>&nbsp;</td>
<td colspan="2">&nbsp;</td>

</tr>
<?php
if($g_view['data_count']==0){
	?>
	<tr>
	  <td colspan="8">No duplicates</td>
	</tr>
	<?php
}else{
	for($i=0;$i<$g_view['data_count'];$i++){
		?>
		<tr>
		
		<td><?php echo $g_view['data'][$i]['name'];?></td>
		<td><?php echo $g_view['data'][$i]['type'];?></td>
		<td><?php echo $g_view['data'][$i]['hq_country'];?></td>
		<td><?php echo $g_view['data'][$i]['sector'];?></td>
		<td><?php echo $g_view['data'][$i]['industry'];?></td>
		<td>
		<?php
		/*********
		sng:6/feb/2011
		support for admin note
		*********/
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
}
?>
</table>