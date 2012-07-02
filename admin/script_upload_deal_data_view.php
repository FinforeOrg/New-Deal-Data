<table width="100%" cellpadding="5" cellspacing="0" border="0">
<tr>
<td>
Enter the filename where the deal list is present.<br />
<strong>Note:</strong> Please upload the file in the admin/data folder first
</td>
</tr>
<tr>
<td>
<form method="post" action="">
<input type="hidden" name="action" value="extract_deals" />
<table width="100%" cellpadding="5" cellspacing="0">
<tr>
<td>Filename</td>
<td><input type="text" name="data_file" value="" size="30"  /></td>
<tr>
<tr>
<td>Number of bank columns</td>
<td><input type="text" name="num_bank_cols" value="<?php echo $num_bank_cols;?>" size="30"  /></td>
</tr>
<tr>
<td>Number of law firm columns</td>
<td><input type="text" name="num_law_firm_cols" value="<?php echo $num_law_firm_cols;?>" size="30"  /></td>
</tr>
</table>

<input type="submit" name="submit" value="Process" />
</form>
</td>
</tr>
<tr>
<td>
<p>Rows scanned <?php echo $rows_scanned;?></p>
<p>Companies entered <?php echo $company_count;?></p>
<p>Banks entered <?php echo $bank_count;?></p>
<p>Law firms entered <?php echo $law_count;?></p>
<p>Deals entered <?php echo $deals_count;?></p>
</td>
</tr>
<tr>
<td>
<?php echo $msg_block;?>
</td>
</tr>
</table>