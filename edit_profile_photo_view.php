<table cellpadding="0" cellspacing="0" style="width:400px;">
<tr>
<td>
<form method="post" action="" enctype="multipart/form-data">
<input type="hidden" name="action" value="change_photo" />
<table width="100%" cellpadding="0" cellspacing="0" class="company">
<tr><td colspan="2"><span class="msg_txt"><?php echo $g_view['msg'];?></span></td></tr>

<tr>
<td colspan="2">
<?php
if($g_view['data']['profile_img']==""){
	?>
	<img src="images/no_profile_img.jpg" />
	<?php
}else{
	?>
	<img src="uploaded_img/profile/thumbnails/<?php echo $g_view['data']['profile_img'];?>" />
	<?php
}
?>
</td>
</tr>

<tr>
<td>Upload photo </td>
<td>
<input type="file" name="profile_img" class="txtbox" /><span class="err_txt"> *</span><br /><span class="err_txt"><?php echo $g_view['err']['profile_img'];?></span>	
</td>
</tr>



<tr>
<td>&nbsp;</td>
<td><input type="submit" name="submit" value="Update" class="btn_auto" />
</table>
</form>
</td>
</tr>
</table>