<table width="100%" cellpadding="5" cellspacing="0" border="0">
<tr>
<td><span class="msg_txt"><?php echo $g_view['msg'];?></span></td>
</tr>
<tr>
<td>
<form method="post" action="" enctype="multipart/form-data">
<input type="hidden" name="action" value="edit" />
<input type="hidden" name="deal_id" value="<?php echo $_POST['deal_id'];?>" />
<input type="hidden" name="company_type" value="<?php echo $_POST['company_type'];?>" />
<table width="100%" cellpadding="5" cellspacing="0" border="0">
<tr>
<td>Name</td>
<td>
<?php echo $g_view['data']['company_name'];?>
</td>
</tr>


<tr>
<td>Type</td>
<td>
<?php echo $_POST['company_type'];?>
</td>
</tr>

<tr>
<td>Company Logo</td>
<td>
<?php
if($g_view['data']['logo']==""){
	?>
	None uploaded
	<?php
}else{
	?>
	<img src="../uploaded_img/logo/thumbnails/<?php echo $g_view['data']['logo'];?>" border="0" />
	<?php
}
?>
<br />
<input type="file" name="logo" style="width:200px;" /><br />
<input type="hidden" name="current_logo" style="width:200px;" value="<?php echo $g_view['data']['logo'];?>"/>
</td>
</tr>


<tr>
<td></td>
<td><input type="submit" name="submit" value="Update" /></td>
</tr>

</table>
</form>
</td>
</tr>
</table>