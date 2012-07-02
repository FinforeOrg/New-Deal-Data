<script>
function submit_frm_role(role_id){
	jQuery('#role_id').val(role_id);
	jQuery('#role_name').val(jQuery('#role_name'+role_id).val());
	jQuery('#partner_type').val(jQuery('#partner_type'+role_id).val());
	jQuery('#for_deal_type').val(jQuery('#for_deal_type'+role_id).val());
	jQuery('#frm_role').submit();
	return false;
}
</script>
<table width="100%" cellpadding="5" cellspacing="0" border="0">
<tr>
<td><span class="msg_txt"><?php echo $g_view['msg'];?></span></td>
</tr>
<tr>
<td>
<form method="post" action="">
<input type="hidden" name="action" value="add" />
<table width="100%" cellpadding="5" cellspacing="0" border="0">


<tr>
<td>Role Name</td>
<td><input name="role_name" type="text" style="width:200px;" value="<?php echo $g_view['input']['role_name'];?>" /><span class="err_txt"> *</span><br />
<span class="err_txt"><?php echo $g_view['err']['role_name'];?></span></td>
</tr>

<tr>
<td>Deal Type</td>
<td>
<select name="for_deal_type">
<option value="" <?php if($g_view['input']['for_deal_type']==""){?>selected="selected"<?php }?>>select</option>
<?php
for($j=0;$j<$g_view['deal_types_count'];$j++){
	?>
	<option value="<?php echo $g_view['deal_types'][$j]['type'];?>" <?php if($g_view['input']['for_deal_type']==$g_view['deal_types'][$j]['type']){?>selected="selected"<?php }?>><?php echo $g_view['deal_types'][$j]['type'];?></option>
	<?php
}
?>
</select><span class="err_txt"> *</span><br /><span class="err_txt"><?php echo $g_view['err']['for_deal_type'];?></span>
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
<form id="frm_role" method="post">
<input type="hidden" name="myaction" value="update" />
<input type="hidden" name="role_id" id="role_id" value="" />
<input type="hidden" name="role_name" id="role_name" value="" />
<input type="hidden" name="for_deal_type" id="for_deal_type" value="" />
</form>
<table cellpadding="10" cellspacing="0" border="1" style="width:600px; border-collapse:collapse;">
<tr bgcolor="#dec5b3">
<td>Role Name</td>

<td>Deal Type</td>
<td></td>
</tr>
<?php
if(0 == $g_view['role_count']){
	?>
	<tr><td>No roles defined yet</td></tr>
	<?php
}else{
	for($j=0;$j<$g_view['role_count'];$j++){
		?>
		<tr>
		<td><input type="text" id="role_name<?php echo $g_view['role'][$j]['role_id'];?>" style="width:200px;" value="<?php echo $g_view['role'][$j]['role_name'];?>" /></td>
		
		<td><?php echo $g_view['role'][$j]['for_deal_type'];?>
		<input type="hidden" id="for_deal_type<?php echo $g_view['role'][$j]['role_id'];?>" value="<?php echo $g_view['role'][$j]['for_deal_type'];?>" /></td>
		<td><input type="button" value="Update" onclick="submit_frm_role(<?php echo $g_view['role'][$j]['role_id'];?>)" /></td>
		</tr>
		<?php
	}
}
?>
</table>