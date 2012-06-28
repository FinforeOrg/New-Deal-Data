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
<td>Name</td>
<td><input name="name" type="text" style="width:200px;" value="<?php echo $g_view['input']['name'];?>" /><span class="err_txt"> *</span><br />
<span class="err_txt"><?php echo $g_view['err']['name'];?></span></td>
</tr>
<tr>
<td>Login Name</td>
<td><input name="login_name" type="text" style="width:200px;" value="<?php echo $g_view['input']['login_name'];?>" /><span class="err_txt"> *</span><br />
<span class="err_txt"><?php echo $g_view['err']['login_name'];?></span></td>
</tr>
<tr>
<td>Password</td>
<td><input name="password" type="password" style="width:200px;" value="" /><span class="err_txt"> *</span><br />
<span class="err_txt"><?php echo $g_view['err']['password'];?></span></td>
</tr>
<tr>
<td>Retype Password</td>
<td><input name="repassword" type="password" style="width:200px;" value="" /><span class="err_txt"> *</span><br />
<span class="err_txt"><?php echo $g_view['err']['repassword'];?></span></td>
</tr>
<tr>
<td>Email</td>
<td><input name="email" type="text" style="width:200px;" value="<?php echo $g_view['input']['email'];?>" /><span class="err_txt"> *</span><br />
<span class="err_txt"><?php echo $g_view['err']['email'];?></span></td>
</tr>



<tr>
<td></td>
<td><input type="submit" name="submit" value="Add" /></td>
</tr>

</table>
</form>
</td>
</tr>
</table>