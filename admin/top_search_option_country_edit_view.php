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
<td>Option Name</td>
<td><input type="text" name="name" value="<?php echo $g_view['option_data']['name'];?>" style="width:100px;" /><span class="err_txt"> *</span></td>
<td>Display Order</td>
<td><input type="text" name="display_order" value="<?php echo $g_view['option_data']['display_order'];?>" style="width:40px;" /><span class="err_txt"> *</span></td>
<td><input type="submit" name="submit" value="Update" />
</tr>
<tr>
<td colspan="2"><span class="err_txt"><?php echo $g_view['err']['name'];?></span></td>
<td colspan="2"><span class="err_txt"><?php echo $g_view['err']['display_order'];?></span></td>
<td>&nbsp;</td>
</tr>
</table>
</form>
</td>
</tr>
</table>