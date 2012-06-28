<!--list delegates-->
<table width="100%" cellpadding="0" cellspacing="0" class="company" style="width:400px;">
<tr><th colspan="4">Delegating For</th></tr>
<tr>
<td>Name</td>
<td>Designation</td>
<td>Work Email</td>
<td>&nbsp;</td>
</tr>
<?php
if($g_view['delegate_for_count']==0){
?>
<tr><td colspan="4">None</td></tr>
<?php
}else{
	for($j=0;$j<$g_view['delegate_for_count'];$j++){
		?>
		<tr>
		<td>
		<?php echo $g_view['delegate_for_data'][$j]['f_name'];?> <?php echo $g_view['delegate_for_data'][$j]['l_name'];?>
		</td>
		<td>
		<?php echo $g_view['delegate_for_data'][$j]['designation'];?>
		</td>
		<td>
		<?php echo $g_view['delegate_for_data'][$j]['work_email'];?>
		</td>
		<td>
		<form method="post" action="">
		<input type="hidden" name="action" value="switch_identity" />
		<input type="hidden" name="switch_to_mem_id" value="<?php echo $g_view['delegate_for_data'][$j]['delegate_for_id'];?>" />
		<input type="submit" value="Switch" class="btn_auto" />
		</form>
		</td>
		</tr>
		<?php
	}
}
?>
<tr>
<td colspan="4">
<form method="post" action="">
<input type="hidden" name="action" value="switch_to_self" />
<input type="submit" value="Switch To Self" class="btn_auto" />
</form>
</td>
</tr>
</table>
<!--list delegates-->

