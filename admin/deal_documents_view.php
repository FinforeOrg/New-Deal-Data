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
<form method="post" action=""  enctype="multipart/form-data" >
<input name="action" type="hidden" value="add" />

<input type="hidden" name="transaction_id" value="<?php echo $g_view['deal_id'];?>" />
<table cellpadding="0" cellspacing="5" border="0" >
<tr><td colspan="7">Upload a document</td></tr>
<tr>
<td><input type="file" name="qqfile" style="width:200px;" /></td>
<td><input type="submit" name="submit" value="Add" />
</tr>
<tr>
<td colspan="2"><span class="err_txt"><?php echo $g_view['err']['filename'];?></span></td>
</tr>
</table>
</form>
</td>
</tr>

<tr><td colspan="6" style="height:10px;">&nbsp;</td></tr>
<tr>
<td>
	<table width="100%" cellpadding="5" cellspacing="0" border="0">
	<tr>
	
	<td>Document</td>
	<td>Uploaded by</td>
	<td>&nbsp;</td>
	<td colspan="2">&nbsp;</td>
	</tr>
	<?php
	
	if($g_view['docs_cnt'] == 0){
		?>
		<tr><td colspan="6">None uploaded</td></tr>
		<?php
	}else{
		for($i=0;$i<$g_view['docs_cnt'];$i++){
			?>
			<tr>
			
			<td><?php echo $g_view['docs'][$i]['caption'];?></td>
			<td>
			<?php
			if($g_view['docs'][$i]['mem_id']!=0){
				echo $g_view['docs'][$i]['f_name']." ".$g_view['docs'][$i]['l_name'];
			}else{
				echo "admin";
			}
			?>
			</td>
			<td><?php if($g_view['docs'][$i]['is_approved']=='y'){?><img src="images/featured.png" /> <?php }?></td>
			<td>
			<form method="post" action="download_deal_doc.php" target="_blank">
			<input type="hidden" name="doc_id" value="<?php echo $g_view['docs'][$i]['file_id'];?>" />
			<input type="submit" name="submit" class="btn_auto" value="Download" />
			</form>
			</td>
			<td>
			<form method="post" action="">
			<input type="hidden" name="action" value="delete" />
			<input type="hidden" name="transaction_id" value="<?php echo $g_view['deal_id'];?>" />
			<input type="hidden" name="doc_id" value="<?php echo $g_view['docs'][$i]['file_id'];?>" />
			<input type="submit" name="submit" value="Delete" />
			</form>
			</td>
			</tr>
			<?php
		}
	}
	?>
	</table>
</td>
</tr>


</table>