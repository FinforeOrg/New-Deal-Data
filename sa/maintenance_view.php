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
<td>Site under maintenance </td>
<td>
<input name="site_in_maintenance" type="radio" value="Y" <?php if($g_view['data']['site_in_maintenance']=='Y'){?>checked="checked"<?php }?>>&nbsp;Yes&nbsp;&nbsp;&nbsp;<input name="site_in_maintenance" type="radio" value="N" <?php if($g_view['data']['site_in_maintenance']=='N'){?>checked="checked"<?php }?>>&nbsp;No
</td>
</tr>

<tr>
<td style="vertical-align:top;">Text to show</td>
<td><textarea name="site_in_maintenance_text" style="width:300px; height:80px;"><?php echo $g_view['data']['site_in_maintenance_text'];?></textarea>
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