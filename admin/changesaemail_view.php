<table width="100%" cellpadding="5" cellspacing="0" border="0">
<tr>
<td><span class="msg_txt"><?php echo $g_view['msg'];?></span></td>
</tr>
<tr>
<td>
<form method="post" action="">
<input type="hidden" name="action" value="change" />
<table width="100%" cellpadding="5" cellspacing="0" border="0">
<tr>
<td>Email</td>
<td><input name="email" type="text" style="width:200px;" value="<?php echo $g_view['data']['email'];?>" /><span class="err_txt"> *</span><br />
<span class="err_txt"><?php echo $g_view['err']['email'];?></span></td>
</tr>



<tr>
<td></td>
<td><input type="submit" name="submit" value="Change" /></td>
</tr>

</table>
</form>
</td>
</tr>
</table>