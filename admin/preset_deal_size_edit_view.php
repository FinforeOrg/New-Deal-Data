<?php
/****
sng:17/nov/2010
Now this could also be marked as primary or not (for mmt option permutation builder)
***/
?>
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
<td>Preset Name</td>
<td><input type="text" name="name" value="<?php echo $g_view['preset_data']['name'];?>" style="width:100px;" /><span class="err_txt"> *</span></td>
<td>
<input name="is_primary" type="checkbox" value="Y" <?php if($g_view['preset_data']['is_primary']=='Y'){?>checked="checked"<?php }?> />&nbsp;primary
</td>
<td><input type="submit" name="submit" value="Update" />
</tr>
<tr>
<td colspan="2"><span class="err_txt"><?php echo $g_view['err']['name'];?></span></td>
<td>&nbsp;</td>
<td>&nbsp;</td>
</tr>
</table>
</form>
</td>
</tr>
</table>