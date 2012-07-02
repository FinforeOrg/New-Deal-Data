<table width="100%" cellpadding="5" cellspacing="0" border="0">
<tr>
<td><span class="msg_txt"><?php echo $g_view['msg'];?></span></td>
</tr>
<tr>
<td>
<form method="post" action="" enctype="multipart/form-data">
<input type="hidden" name="action" value="add"/>
<table width="100%" cellpadding="5" cellspacing="0" border="0">
<tr>
<td>Name</td>
<td>
<input name="name" type="text" style="width:200px;" value="<?php echo $g_view['input']['name'];?>" /><span class="err_txt"> *</span><br />
<span class="err_txt"><?php echo $g_view['err']['name'];?></span>
</td>
</tr>
<?php
/***
sng:9/jul/2010
although there is a code to generate abbreviated name for a bank/law firm, sometime admin may specify the exact code
*********/
?>
<tr>
<td>Abbreviated Name</td>
<td>
<input name="short_name" type="text" style="width:200px;" value="<?php echo $g_view['input']['short_name'];?>" /><br />
(Used in chart in place of the actual name.)
</td>
</tr>

<tr>
<td>Type</td>
<td>
<select name="type">
<option value="bank" <?php if(($g_view['input']['type']=="")||($g_view['input']['type']=="bank")){?> selected="selected"<?php }?>>Bank</option>
<option value="law firm" <?php if($g_view['input']['type']=="law firm"){?> selected="selected"<?php }?>>Law Firm</option>

</select><span class="err_txt"> *</span><br />
<span class="err_txt"><?php echo $g_view['err']['type'];?></span>
</td>
</tr>

<tr>
<td>Company Logo</td>
<td>
<input type="file" name="logo" style="width:200px;" />
</td>
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