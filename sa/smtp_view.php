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
<td>SMTP Host</td>
<td><input name="smtp_host" type="text" style="width:200px;" value="<?php echo $g_view['data']['smtp_host'];?>" /><span class="err_txt"> *</span><br />
<span class="err_txt"><?php echo $g_view['err']['smtp_host'];?></span></td>
</tr>
<tr>
<td>SMTP Port</td>
<td><input name="smtp_port" type="text" style="width:40px;" value="<?php echo $g_view['data']['smtp_port'];?>" /><span class="err_txt"> *</span><br />
<span class="err_txt"><?php echo $g_view['err']['smtp_port'];?></span></td>
</tr>

<tr>
<td>SMTP Username</td>
<td><input name="smtp_user" type="text" style="width:200px;" value="<?php echo $g_view['data']['smtp_user'];?>" /><span class="err_txt"> *</span><br />
<span class="err_txt"><?php echo $g_view['err']['smtp_user'];?></span></td>
</tr>

<tr>
<td>SMTP Password</td>
<td><input name="smtp_pass" type="text" style="width:200px;" value="<?php echo $g_view['data']['smtp_pass'];?>" /><span class="err_txt"> *</span><br />
<span class="err_txt"><?php echo $g_view['err']['smtp_pass'];?></span></td>
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