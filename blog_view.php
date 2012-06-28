<div id="blog">
<table width="100%" cellpadding="0" cellspacing="0">
<?php
if($g_view['data_count']==0){
?>
<tr><td>No blog entries posted yet</td></tr>
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
		<h2><?php echo $g_view['data'][$i]['title'];?></h2>
		<h3>Posted on: <?php echo $g_view['data'][$i]['posted_on'];?></h3>
		<?php echo $g_view['data'][$i]['content'];?>
		</td>
		</tr>
		<?php
	}
	?>
	<tr>
	<td style="text-align:right">
	<?php
	if($g_view['start'] > 0){
		$prev_offset = $g_view['start'] - $g_view['num_to_show'];
		?>
		<a class="link_as_button" href="blog.php?start=<?php echo $prev_offset;?>">Prev</a>
		<?php
	}
	if($g_view['data_count'] > $g_view['num_to_show']){
		$next_offset = $g_view['start'] + $g_view['num_to_show'];
		?>
		&nbsp;&nbsp;&nbsp;<a class="link_as_button" href="blog.php?start=<?php echo $next_offset;?>">Next</a>
		<?php
	}
	?>
	</td>
	</tr>
	<?php
}
?>
</table>
</div>