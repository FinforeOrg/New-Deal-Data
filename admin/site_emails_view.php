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
<td>Contact Email</td>
<td><input name="contact_email" type="text" style="width:200px;" value="<?php echo $g_view['data']['contact_email'];?>" /><span class="err_txt"> *</span><br />
<span class="err_txt"><?php echo $g_view['err']['contact_email'];?></span></td>
</tr>
<tr>
<td>Registration Email</td>
<td><input name="registration_email" type="text" style="width:200px;" value="<?php echo $g_view['data']['registration_email'];?>" /><span class="err_txt"> *</span><br />
<span class="err_txt"><?php echo $g_view['err']['registration_email'];?></span></td>
</tr>

<tr>
<td>Registration Notification Email</td>
<td><input name="registration_notification_email" type="text" style="width:200px;" value="<?php echo $g_view['data']['registration_notification_email'];?>" /><span class="err_txt"> *</span><br />
<span class="err_txt"><?php echo $g_view['err']['registration_notification_email'];?></span></td>
</tr>

<tr>
<td>Suggestion Email</td>
<td><input name="suggestion_email" type="text" style="width:200px;" value="<?php echo $g_view['data']['suggestion_email'];?>" /><span class="err_txt"> *</span><br />
<span class="err_txt"><?php echo $g_view['err']['suggestion_email'];?></span></td>
</tr>

<tr>
<td>Member Related Email</td>
<td><input name="mem_related_email" type="text" style="width:200px;" value="<?php echo $g_view['data']['mem_related_email'];?>" /><span class="err_txt"> *</span><br />
<span class="err_txt"><?php echo $g_view['err']['mem_related_email'];?></span></td>
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