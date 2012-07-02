<table width="100%" cellpadding="5" cellspacing="0" border="1" style="border-collapse:collapse;">
<tr>
<td>
<form method="post" action="member_list.php">
<table cellpadding="5" cellspacing="0" border="0">
<tr>
<td>First Name</td>
<td><input type="text" name="f_name" value="<?php echo $g_mc->view_to_view($_POST['f_name']);?>" style="width:200px;" /></td>
<td>Last Name</td>
<td><input type="text" name="l_name" value="<?php echo $g_mc->view_to_view($_POST['l_name']);?>" style="width:200px;" /></td>
</tr>
<tr>
<td>Company</td>
<td><input type="text" name="company" value="<?php echo $g_mc->view_to_view($_POST['company']);?>" style="width:200px;" /></td>
</tr>
<tr>
<td colspan="4"><input type="submit" name="submit" value="Search" /></td>
</tr>
</table>
</form>
</td>
</tr>
</table>
<table width="100%" cellpadding="5" cellspacing="0" border="1" style="border-collapse:collapse;">
<?php
if($g_view['msg']!=""){
?>
<tr>
<td colspan="9"><span class="msg_txt"><?php echo $g_view['msg'];?></span></td>
</tr>
<?php
}
?>
<tr bgcolor="#dec5b3" style="height:20px;">
<td>&nbsp;</td>
<td><strong>Member Name</strong></td>
<!--<td><strong>Home Email</strong></td>-->
<td><strong>Work Email</strong></td>
<td><strong>Company name</strong></td>
<td><strong>Action</strong></td>
<td><strong>&nbsp;</strong></td>

</tr>
<?php
if($g_view['data_count']==0){
	?>
	<tr>
	  <td colspan="9">No member data found</td>
	</tr>
	<?php
}else{
	if($g_view['data_count'] > $g_view['num_to_show']){
		$total = $g_view['num_to_show'];
	}else{
		$total = $g_view['data_count'];
	}
	for($i=0;$i<$total;$i++){
		?>
		<tr>
		<td>
		<?php
		if($g_view['data'][$i]['blocked']=='N'){
			$icon = "unlock_icon.gif";
		}else{
			$icon = "locked_icon.gif";
		}
		?>
		<img src="images/<?php echo $icon;?>" />
		</td>
		<td><?php echo $g_view['data'][$i]['f_name']."&nbsp;".$g_view['data'][$i]['l_name']; ?></td>
		
		<td><?php echo $g_view['data'][$i]['work_email'];?></td>
		<td><?php echo $g_view['data'][$i]['company_name'];?></td>
		<td>
		<?php
		if($g_view['data'][$i]['blocked']=='N'){
			?>
			<form method="post" action="">
			<input name="action" type="hidden" value="blocked">
			<input name="active" type="hidden" value="Y">
			<input name="mem_id" type="hidden" value="<?php echo $g_view['data'][$i]['mem_id'];?>">
			<input type="hidden" name="f_name" value="<?php echo $g_mc->view_to_view($_POST['f_name']);?>" />
			<input type="hidden" name="l_name" value="<?php echo $g_mc->view_to_view($_POST['l_name']);?>" />
			<input type="hidden" name="company" value="<?php echo $g_mc->view_to_view($_POST['company']);?>" />
			<input type="submit" value="Block">
			</form>
			<?php
		}else{
			?>
			<form method="post" action="">
			<input name="action" type="hidden" value="blocked">
			<input name="active" type="hidden" value="N">
			<input name="mem_id" type="hidden" value="<?php echo $g_view['data'][$i]['mem_id'];?>">
			<input type="hidden" name="f_name" value="<?php echo $g_mc->view_to_view($_POST['f_name']);?>" />
			<input type="hidden" name="l_name" value="<?php echo $g_mc->view_to_view($_POST['l_name']);?>" />
			<input type="hidden" name="company" value="<?php echo $g_mc->view_to_view($_POST['company']);?>" />
			<input type="submit" value="Unblock">
			</form>
			<?php
		}
		?>
		</td>
		<td>
		
		<form method="post" action="member_profile.php">
		<input type="hidden" name="mem_id" value="<?php echo $g_view['data'][$i]['mem_id'];?>" />
		<input type="submit" value="View" />
		</form>
		</td>
		</tr>
		<?php
	}
	?>
	<form id="pagination_helper" method="post" action="ghost_member_list.php">
		
		<input type="hidden" name="f_name" value="<?php echo $g_mc->view_to_view($_POST['f_name']);?>" />
		<input type="hidden" name="l_name" value="<?php echo $g_mc->view_to_view($_POST['l_name']);?>" />
		<input type="hidden" name="company" value="<?php echo $g_mc->view_to_view($_POST['company']);?>" />
	</form>
	<script type="text/javascript">
	function go_page(offset){
		document.getElementById('pagination_helper').action = "member_list.php?start="+offset;
		document.getElementById('pagination_helper').submit();
		return false;
	}
	</script>
	<tr>
	<td colspan="7" style="text-align:right">
	<?php
	if($g_view['start'] > 0){
		$prev_offset = $g_view['start'] - $g_view['num_to_show'];
		?>
		<a href="#" onclick="return go_page(<?php echo $prev_offset;?>);">Prev</a>
		<?php
	}
	if($g_view['data_count'] > $g_view['num_to_show']){
		$next_offset = $g_view['start'] + $g_view['num_to_show'];
		?>
		&nbsp;&nbsp;&nbsp;<a href="#" onclick="return go_page(<?php echo $next_offset;?>);">Next</a>
		<?php
	}
	?>
	</td>
	</tr>
	<?php
}
?>
</table>