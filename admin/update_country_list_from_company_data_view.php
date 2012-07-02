<table width="100%" cellpadding="5" cellspacing="0" border="0">
<tr>
<td><span class="msg_txt"><?php echo $g_view['msg'];?></span></td>
</tr>
<tr>
<td>
<form method="post" action="">
<input type="hidden" name="action" value="update" />
<table width="100%" cellpadding="5" cellspacing="0" border="0">
<tr>
<td>Update country list</td>
<td>This operation scans the company table to find countries of HQs that are not in the country master table</td>
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