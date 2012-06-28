<form method="post" action="">
<input type="hidden" name="action" value="send_email" />
<table width="100%" cellpadding="5" cellspacing="5">
<tr>
<td>Your Email</td>
<td>
<input name="your_email" type="text" class="txtbox" value="<?php echo $g_view['input']['your_email'];?>"/><br />
<span class="err_txt"><?php echo $g_view['err']['your_email'];?></span>
</td>
</tr>
<tr>
<td style="vertical-align:top">Your Message</td>
<td>
<textarea name="your_message" style="width:300px; height:200px;"><?php echo $g_view['input']['your_message'];?></textarea><br />
<span class="err_txt"><?php echo $g_view['err']['your_message'];?></span>
</td>
</tr>
<tr>
<td>&nbsp;</td>
<td><input type="submit" value="Send" class="btn_auto" /></td>
</tr>
</table>
</form>