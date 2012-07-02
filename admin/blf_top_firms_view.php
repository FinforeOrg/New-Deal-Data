<table width="100%" cellpadding="5" cellspacing="0" border="1" style="border-collapse:collapse;">
<?php
if($g_view['msg']!=""){
?>
<tr>
<td colspan="9"><span class="msg_txt"><?php echo $g_view['msg'];?></span></td>
</tr>
<?php
}
?>
<tr bgcolor="#dec5b3" style="height:20px;">

<td><strong>Name</strong></td>
<td><strong>Type</strong></td>
<td><strong>Logo</strong></td>
<td>&nbsp;</td>
</tr>
<?php
if($g_view['data_count']==0){
	?>
	<tr>
	  <td colspan="9">None found</td>
	</tr>
	<?php
}else{
	
	for($i=0;$i<$g_view['data_count'];$i++){
		?>
		<tr>
		
		<td><?php echo $g_view['data'][$i]['name'];?></td>
		<td><?php echo $g_view['data'][$i]['type'];?></td>
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
		<form method="post" action="blf_edit.php">
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