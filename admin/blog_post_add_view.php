<?php
include("admin/editor.inc.php");
?>
<table width="100%" cellpadding="5" cellspacing="0" border="0">
<tr>
<td><span class="msg_txt"><?php echo $g_view['msg'];?></span></td>
</tr>
<tr>
<td>
<form method="post" action="">
<input type="hidden" name="action" value="add" />
<table width="100%" cellpadding="5" cellspacing="0" border="0">
<tr>
<td>Title</td>
<td><input name="title" type="text" style="width:200px;" value="<?php echo $g_view['input']['title'];?>" /><span class="err_txt"> *</span><br />
<span class="err_txt"><?php echo $g_view['err']['title'];?></span></td>
</tr>

<tr>
<td colspan="2" valign="top">Content</td>
</tr>

<tr>
<td colspan="2">
<textarea name="content" style="width:200px; height:100px; overflow:auto;"><?php echo $g_view['input']['content'];?></textarea><span class="err_txt"> *</span><br />
<span class="err_txt"><?php echo $g_view['err']['content'];?></span>
</td>
</tr>

<tr>
<td></td>
<td><input type="submit" name="submit" value="Post" /></td>
</tr>

</table>
</form>
</td>
</tr>
</table>