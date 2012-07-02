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
<td>Update transaction type/subtype list</td>
<td>This operation scans the transaction table to find transaction type/subtypes that are not in the transaction type/subtype master table</td>
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