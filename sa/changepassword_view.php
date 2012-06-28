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
<td>Current Password</td>
<td><input name="password" type="password" style="width:200px;" value="" /><span class="err_txt"> *</span><br />
<span class="err_txt"><?php echo $g_view['err']['password'];?></span></td>
</tr>
<tr>
<td>New Password</td>
<td><input name="newpassword" type="password" style="width:200px;" value="" /><span class="err_txt"> *</span><br />
<span class="err_txt"><?php echo $g_view['err']['newpassword'];?></span></td>
</tr>
<tr>
<td>Retype New Password</td>
<td><input name="renewpassword" type="password" style="width:200px;" value="" /><span class="err_txt"> *</span><br />
<span class="err_txt"><?php echo $g_view['err']['renewpassword'];?></span></td>
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