<table width="100%" cellpadding="5" cellspacing="0" border="1" style="border-collapse:collapse;">
<tr>
<td>
<form method="post" action="ghost_member_list.php">
<table cellpadding="5" cellspacing="0" border="0">
<tr>
<td>First Name</td>
<td><input type="text" name="f_name" value="<?php echo $g_mc->view_to_view($_POST['f_name']);?>" style="width:200px;" /></td>
<td>Last Name</td>
<td><input type="text" name="l_name" value="<?php echo $g_mc->view_to_view($_POST['l_name']);?>" style="width:200px;" /></td>
<td><input type="submit" name="submit" value="Search" />
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
<td colspan="7"><span class="msg_txt"><?php echo $g_view['msg'];?></span></td>
</tr>
<?php
}
?>
<tr bgcolor="#dec5b3" style="height:20px;">
<td><strong>Member Name</strong></td>
<td><strong>Type</strong></td>
<td><strong>Company name</strong></td>
<td><strong>Designation</strong></td>
<td colspan="3"><strong>Action</strong></td>
</tr>
<?php
if($g_view['data_count']==0){
	?>
	<tr>
	  <td colspan="7">No ghost member data found</td>
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
		
		<td><?php echo $g_view['data'][$i]['f_name']."&nbsp;".$g_view['data'][$i]['l_name']; ?></td>
		
		<td><?php echo $g_view['data'][$i]['member_type'];?></td>
		<td><?php echo $g_view['data'][$i]['company_name'];?></td>
		<td><?php echo $g_view['data'][$i]['designation'];?></td>
		
		<td>
		
		<form method="post" action="ghost_member_profile.php">
		<input type="hidden" name="mem_id" value="<?php echo $g_view['data'][$i]['mem_id'];?>" />
		<input type="submit" value="Edit" />
		</form>
		</td>
		<?php
		/**************
		sng:2/dec/2011
		in data-cx, we do not have concept of deal partner team member so we do not need
		to see the deals of ghost member
		****************/
		?>
		<td>
		<form method="post" action="">
		<input type="hidden" name="action" value="delete" />
		<input type="hidden" name="mem_id" value="<?php echo $g_view['data'][$i]['mem_id'];?>" />
		<input type="submit" value="Delete" />
		</form>
		</td>
		</tr>
		<?php
	}
	?>
	<form id="pagination_helper" method="post" action="ghost_member_list.php">
		
		<input type="hidden" name="f_name" value="<?php echo $g_mc->view_to_view($_POST['f_name']);?>" />
		<input type="hidden" name="l_name" value="<?php echo $g_mc->view_to_view($_POST['l_name']);?>" />
		
	</form>
	<script type="text/javascript">
	function go_page(offset){
		document.getElementById('pagination_helper').action = "ghost_member_list.php?start="+offset;
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