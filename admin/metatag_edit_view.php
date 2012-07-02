<table width="100%" cellpadding="5" cellspacing="0" border="0">
<tr>
<td><span class="msg_txt"><?php echo $g_view['msg'];?></span></td>
</tr>
<tr>
<td>
<form method="post" action="">
<input type="hidden" name="action" value="edit" />
<table width="100%" cellpadding="5" cellspacing="0" border="0">
<tr>
<td>Meta Title</td>
<td><input name="meta_title" type="text" style="width:200px;" value="<?php echo $g_view['data']['meta_title'];?>" /></td>
</tr>

<tr>
<td>Meta Keywords</td>
<td><textarea name="meta_keywords" style="width:200px; height:100px; overflow:auto;"><?php echo $g_view['data']['meta_keywords'];?></textarea></td>
</tr>

<tr>
<td>Meta Description</td>
<td><textarea name="meta_description" style="width:200px; height:100px; overflow:auto;"><?php echo $g_view['data']['meta_description'];?></textarea></td>
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