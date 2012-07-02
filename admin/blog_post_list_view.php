<table width="100%" cellpadding="5" cellspacing="0" border="1" style="border-collapse:collapse;">
<?php
if($g_view['msg']!=""){
?>
<tr>
<td colspan="6"><span class="msg_txt"><?php echo $g_view['msg'];?></span></td>
</tr>
<?php
}
?>
<tr bgcolor="#dec5b3" style="height:20px;">

<td><strong>Title</strong></td>
<td><strong>Posted On</strong></td>
<td colspan=2><strong>Action</strong></td>
</tr>
<?php
if($g_view['data_count']==0){
	?>
	<tr>
	  <td colspan="6">No blog posting found</td>
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
		
		<td><?php echo $g_view['data'][$i]['title'];?></td>
		<td><?php echo $g_view['data'][$i]['posted_on'];?></td>
		<?php
		/*********************
		sng:29/sep/2011
		support for edit
		***********************/
		?>
		<td>
		<form action="blog_post_edit.php" method="post">
		<input type="hidden" name="blog_id" value="<?php echo  $g_view['data'][$i]['blog_id'];?>"  />
		<input type="submit" value="Edit"  />
		</form>
		</td>
		<td>
		<form action="" method="post">
		<input type="hidden" name="action" value="del" />
		<input type="hidden" name="blog_id" value="<?php echo  $g_view['data'][$i]['blog_id'];?>"  />
		<input type="submit" value="Delete"  />
		</form>
		</td>
		</tr>
		<?php
	}
	?>
	<tr>
	<td colspan="6" style="text-align:right">
	<?php
	if($g_view['start'] > 0){
		$prev_offset = $g_view['start'] - $g_view['num_to_show'];
		?>
		<a href="blog_post_list.php?start=<?php echo $prev_offset;?>">Prev</a>
		<?php
	}
	if($g_view['data_count'] > $g_view['num_to_show']){
		$next_offset = $g_view['start'] + $g_view['num_to_show'];
		?>
		&nbsp;&nbsp;&nbsp;<a href="blog_post_list.php?start=<?php echo $next_offset;?>">Next</a>
		<?php
	}
	?>
	</td>
	</tr>
	<?php
}
?>
</table>