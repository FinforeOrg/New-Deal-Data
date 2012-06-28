<table cellpadding="0" cellspacing="0" style="width:500px;">
<tr>
<td>
<form method="post" action="">
<input type="hidden" name="action" value="update_desc" />
<table width="100%" cellpadding="0" cellspacing="0" class="company">
<tr><td colspan="2"><span class="msg_txt"><?php echo $g_view['msg'];?></span></td></tr>
<tr>
<td>Company</td>
<td><?php echo $g_view['data']['company_name'];?></td>
</tr>
<tr>
<td style="vertical-align:top;">
Company Description
</td>
<td>
<textarea name="brief_desc" class="txtbox" style="width:400px; height:200px; overflow:auto;"><?php echo $g_view['data']['brief_desc'];?></textarea><span class="err_txt"> *</span><br /><span class="err_txt"><?php echo $g_view['err']['brief_desc'];?></span>	
</td>
</tr>

<tr>
<td>&nbsp;</td>
<td><input type="submit" name="submit" value="Update" class="btn_auto" />
</table>
</form>
</td>
</tr>
</table>