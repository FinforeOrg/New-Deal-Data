<script type="text/javascript" src="../js/jquery-1.2.1.pack.js"></script>
<script type="text/javascript">
function resend_activation_email(uid){
	var divid = "#resend_result_"+uid;
	$(divid).html("sending...");
	$.post(
        'ajax/resend_activation_email.php',
        {uid:""+uid+""},
        function(response) {
            $(divid).html(response);
        }
    )
	return false;
}
</script>
<table width="100%" cellpadding="5" cellspacing="0" border="1" style="border-collapse:collapse;">
<?php
if($g_view['msg']!=""){
?>
<tr>
<td colspan="7"><span class="msg_txt"><?php echo $g_view['msg'];?></span></td>
</tr>
<?php
}
?>
<tr bgcolor="#dec5b3" style="height:20px;">

<td><strong>Member Name</strong></td>
<td><strong>Company</strong></td>
<td><strong>Work Email</strong></td>
<td><strong>&nbsp;</strong></td>
<td>&nbsp;</td>
<td>&nbsp;</td>
</tr>
<?php
if($g_view['data_count']==0){
	?>
	<tr>
	  <td colspan="9">None found</td>
	</tr>
	<?php
}else{
	for($i=0;$i<$g_view['data_count'];$i++){
		?>
		<tr>
		
		<td><?php echo $g_view['data'][$i]['f_name']."&nbsp;".$g_view['data'][$i]['l_name']; ?></td>
		<td><?php echo $g_view['data'][$i]['company_name'];?></td>
		<td><?php echo $g_view['data'][$i]['work_email'];?></td>
		
		<td>
		
		<form method="post" action="new_registration_detail.php">
		<input type="hidden" name="uid" value="<?php echo $g_view['data'][$i]['uid'];?>" />
		<input type="submit" value="View" />
		</form>
		</td>
		<td>
		<input type="button" value="resend activation email" onclick="return resend_activation_email('<?php echo $g_view['data'][$i]['uid'];?>');" />
		<div id="resend_result_<?php echo $g_view['data'][$i]['uid'];?>"></div>
		</td>
		<?php
		/*********
		sng:6/oct/2010
		in case admin wish to activate directly
		***/
		?>
		<td>
		<form method="post" action="">
		<input type="hidden" name="myaction" value="activate" />
		<input type="hidden" name="uid" value="<?php echo $g_view['data'][$i]['uid'];?>" />
		<input type="submit" value="Activate" />
		</form>
		</td>
		</tr>
		<?php
	}
}
?>
</table>