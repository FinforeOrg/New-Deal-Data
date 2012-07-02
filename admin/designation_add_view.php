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
<td>Designation</td>
<td>
<input name="designation" type="text" style="width:200px;" value="<?php echo $g_view['input']['designation'];?>" /><span class="err_txt"> *</span><br />
<span class="err_txt"><?php echo $g_view['err']['designation'];?></span>
</td>
</tr>

<tr>
<td>Type</td>
<td>
<select name="type">
<option value="">Select</option>
<option value="banker" <?php if($g_view['input']['type']=="banker"){?> selected="selected"<?php }?>>Banker</option>
<option value="lawyer" <?php if($g_view['input']['type']=="lawyer"){?> selected="selected"<?php }?>>Lawyer</option>
<option value="company rep" <?php if($g_view['input']['type']=="company rep"){?> selected="selected"<?php }?>>Company Rep</option>
<?php
/*********************
sng:5/apr/2011
we have added a new role: data partner, but they all belong to company
************************/
?>
<option value="data partner" <?php if($g_view['input']['type']=="data partner"){ ?> selected="selected"<?php }?>>Data Partner</option>
</select><span class="err_txt"> *</span><br />
<span class="err_txt"><?php echo $g_view['err']['type'];?></span>
</td>
</tr>

<tr>
<td>Deal share weight</td>
<td>
<input name="deal_share_weight" type="text" style="width:200px;" value="<?php echo $g_view['input']['deal_share_weight'];?>" /><span class="err_txt"> *</span><br />
<span class="err_txt"><?php echo $g_view['err']['deal_share_weight'];?></span>
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