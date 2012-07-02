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

<td><strong>Designation</strong></td>
<td><strong>Type</strong></td>
<td><strong>Deal share weight</strong></td>
<td>&nbsp;</td>


</tr>
<?php
if($g_view['data_count']==0){
	?>
	<tr>
	  <td colspan="6">No transaction data found</td>
	</tr>
	<?php
}else{
	for($i=0;$i<$g_view['data_count'];$i++){
		?>
		<tr>
		<td><?php echo $g_view['data'][$i]['designation'];?></td>
		<td><?php echo $g_view['data'][$i]['member_type'];?></td>
		<td><?php echo $g_view['data'][$i]['deal_share_weight'];?></td>
		<td>
		<form method="post" action="">
		<input type="hidden" name="action" value="delete" />
		<input type="hidden" name="id" value="<?php echo $g_view['data'][$i]['id'];?>" />
		<input type="submit" name="submit" value="delete" />
		</form>
		</td>
		</tr>
		<?php
	}
}
?>
</table>