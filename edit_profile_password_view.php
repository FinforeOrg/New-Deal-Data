<table cellpadding="0" cellspacing="0" style="width:400px;">
<tr>
<td>
<form method="post" action="">
<input type="hidden" name="action" value="change_password" />
<table width="100%" cellpadding="0" cellspacing="0" class="company">
<tr><td colspan="2"><span class="msg_txt"><?php echo $g_view['msg'];?></span></td></tr>
<tr>
<td>
current Password
</td>
<td>
<input type="password" name="curr_password" class="txtbox" /><span class="err_txt"> *</span><br /><span class="err_txt"><?php echo $g_view['err']['curr_password'];?></span>	
</td>
</tr>
<tr>
<td>
New Password
</td>
<td>
<input type="password" name="new_password" class="txtbox" /><span class="err_txt"> *</span><br /><span class="err_txt"><?php echo $g_view['err']['new_password'];?></span>
</td>
</tr>
<tr>
<td>
Retype Password
</td>
<td>
<input type="password" name="re_password" class="txtbox" /><span class="err_txt"> *</span><br /><span class="err_txt"><?php echo $g_view['err']['re_password'];?></span>
</td>
</tr>
<tr>
<td>&nbsp;</td>
<td><input type="submit" name="submit" value="Change" class="btn_auto" />
</table>
</form>
</td>
</tr>
</table>