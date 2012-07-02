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
<input name="action" type="hidden" value="edit" />
<table cellpadding="0" cellspacing="5" border="0" >
<tr>
<?php
/**************
sng:20/july/2011
These are top search options, not presets
************/
?>
<td style="width:150px;">Option Name</td>
<td style="width:300px;"><input type="text" name="name" value="<?php echo $g_view['option_data']['name'];?>" style="width:250px;" /><span class="err_txt"> *</span><br /><span class="err_txt"><?php echo $g_view['err']['name'];?></span></td>
</tr>
<?php
/*********************
sng:20/july/2011
support to group the items
****************************/
?>
<tr>
<td>Group Name</td>
<td><input type="text" name="group_name" value="<?php echo $g_view['option_data']['group_name'];?>" style="width:250px;" /></td>
</tr>
<tr>
<td>Display Order</td>
<td><input type="text" name="display_order" value="<?php echo $g_view['option_data']['display_order'];?>" style="width:40px;" /><span class="err_txt"> *</span><br /><span class="err_txt"><?php echo $g_view['err']['display_order'];?></span></td>
</tr>
<tr>
<td>
<td><input type="submit" name="submit" value="Update" /></td>
</tr>
</table>
</form>
</td>
</tr>
</table>