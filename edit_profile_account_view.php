<table cellpadding="0" cellspacing="0" style="width:400px;">
<tr>
<td>
<form method="post" action="">
<input type="hidden" name="action" value="change_profile" />
<table width="100%" cellpadding="0" cellspacing="0" class="company">
<tr><td colspan="2"><span class="msg_txt"><?php echo $g_view['msg'];?></span></td></tr>
<tr>
<td>First Name </td>
<td>
<input type="text" name="f_name" value="<?php echo $g_view['data']['f_name'];?>" class="txtbox" /><span class="err_txt"> *</span><br /><span class="err_txt"><?php echo $g_view['err']['f_name'];?></span>	
</td>
</tr>

<tr>
<td>Last Name </td>
<td>
<input type="text" name="l_name" value="<?php echo $g_view['data']['l_name'];?>" class="txtbox" /><span class="err_txt"> *</span><br /><span class="err_txt"><?php echo $g_view['err']['l_name'];?></span>	
</td>
</tr>

<tr>
<td>Home Email </td>
<td>
<input type="text" name="home_email" value="<?php echo $g_view['data']['home_email'];?>" class="txtbox" /><span class="err_txt"> *</span><br /><span class="err_txt"><?php echo $g_view['err']['home_email'];?></span>	
</td>
</tr>
<?php
/*******************************************************************
sng:22/jan/2011
We now set work email when editing company, so we do not do it here
<tr>
<td>Work Email </td>
<td>
<input type="text" name="work_email" value="<?php echo $g_view['data']['work_email'];?>" class="txtbox" /><span class="err_txt"> *</span><br /><span class="err_txt"><?php echo $g_view['err']['work_email'];?></span>	
</td>
</tr>
*************************************************************/
?>
<tr>
<td>&nbsp;</td>
<td><input type="submit" name="submit" value="Update" class="btn_auto" />
</table>
</form>
</td>
</tr>
</table>