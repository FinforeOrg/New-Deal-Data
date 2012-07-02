<?php
$g_view['new_transaction_id'] = $new_transaction_id;
?>
<table width="100%" cellpadding="5" cellspacing="0" border="0">
<tr>
<td><span class="msg_txt"><?php echo $g_view['msg'];?></span></td>
</tr>
<tr>
<td>
<form method="post" id="edit_deal_frm" action="deal_edit.php">
<input type="hidden" name="deal_id" value="<?php echo $g_view['new_transaction_id'];?>" />
</form>
</td>
</tr>
</table>
<script>
jQuery(function(){
	jQuery('#edit_deal_frm').submit();
});
</script>