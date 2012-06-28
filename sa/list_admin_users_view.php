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
<td>&nbsp;</td>
<td><strong>Name</strong></td>
<td><strong>Login Name</strong></td>
<td><strong>Password</strong></td>
<td><strong>Email</strong></td>
<td><strong>Action</strong></td>
</tr>
<?php
if($g_view['data_count']==0){
	?>
	<tr><td colspan="6">No admin users found</td></tr>
	<?php
}else{
	for($i=0;$i<$g_view['data_count'];$i++){
		?>
		<tr>
		<td>
		<?php
		if($g_view['data'][$i]['is_active']=='Y'){
			$icon = "unlock_icon.gif";
		}else{
			$icon = "locked_icon.gif";
		}
		?>
		<img src="images/<?php echo $icon;?>" />
		</td>
		<td><?php echo $g_view['data'][$i]['name'];?></td>
		<td><?php echo $g_view['data'][$i]['login_name'];?></td>
		<td><?php echo $g_view['data'][$i]['password'];?></td>
		<td><?php echo $g_view['data'][$i]['email'];?></td>
		<td>
		<?php
		if($g_view['data'][$i]['is_active']=='Y'){
			?>
			<form method="post" action="">
			<input name="action" type="hidden" value="flip_active">
			<input name="active" type="hidden" value="N">
			<input name="admin_id" type="hidden" value="<?php echo $g_view['data'][$i]['id'];?>">
			<input type="submit" value="Deactivate">
			</form>
			<?php
		}else{
			?>
			<form method="post" action="">
			<input name="action" type="hidden" value="flip_active">
			<input name="active" type="hidden" value="Y">
			<input name="admin_id" type="hidden" value="<?php echo $g_view['data'][$i]['id'];?>">
			<input type="submit" value="Activate">
			</form>
			<?php
		}
		?>
		</td>
		</tr>
		<?php
	}
}
?>
</table>