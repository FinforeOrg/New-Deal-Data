<?php
$g_view['new_transaction_id'] = $new_transaction_id;
?>
<script type="text/javascript" src="util.js"></script>
<table width="100%" cellpadding="5" cellspacing="0" border="0">
<tr>
<td><span class="msg_txt"><?php echo $g_view['msg'];?></span></td>
</tr>
<tr>
<td>
Please click the links to add the banks and law firms to this deal
</td>
</tr>
<tr>
<td>
<a href="" onclick="return deal_bank_popup('<?php echo $g_view['new_transaction_id'];?>');">Add Banks</a><br /><br />
<a href="" onclick="return deal_lawfirm_popup('<?php echo $g_view['new_transaction_id'];?>');">Add Law Firms</a>
</td>
</tr>
</table>