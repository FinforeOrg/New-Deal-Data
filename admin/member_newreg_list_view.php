
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
<!--<td><strong>Company name</strong></td>-->
<td><strong>&nbsp;</strong></td>

</tr>
<?php
if($g_view['data_count']==0){
	?>
	<tr>
	  <td colspan="7">No new registration data found</td>
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
		</tr>
		<?php
	}
}
?>
</table>