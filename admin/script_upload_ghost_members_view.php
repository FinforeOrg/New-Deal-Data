<table width="100%" cellpadding="5" cellspacing="0" border="0">
<tr>
<td>
Enter the filename where the ghost list is present.<br />
<strong>Note:</strong> Please upload the file in the admin/data folder first
</td>
</tr>
<tr>
<td>
<form method="post" action="">
<input type="hidden" name="action" value="extract_ghosts" />
<table width="100%" cellpadding="5" cellspacing="0">
<tr>
<td>Filename</td>
<td><input type="text" name="data_file" value="" size="30"  /></td>
<tr>

</table>

<input type="submit" name="submit" value="Process" />
</form>
</td>
</tr>
<tr>
<td>
<p>Rows scanned <?php echo $rows_scanned;?></p>
<p>Members entered <?php echo $mem_count;?></p>
<p>Compnany created <?php echo $company_count;?></p>
</td>
</tr>
<tr>
<td>
<?php echo $msg_block;?>
</td>
</tr>
</table>