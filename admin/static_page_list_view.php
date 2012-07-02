<table width="100%" cellpadding="5" cellspacing="0" border="1" style="border-collapse:collapse;">
<tr bgcolor="#dec5b3" style="height:20px;">
<td><strong>Page name</strong></td>
<td><strong>Heading</strong></td>
<td colspan="2"><strong>Action</strong></td>
</tr>
<?php
if($g_view['num_pages']==0){
	?>
	<tr><td colspan="3">No pages found</td></tr>
	<?php
}else{
	for($i=0;$i<$g_view['num_pages'];$i++){
		?>
		<tr>
		<td><?php echo $g_view['page_data_arr'][$i]['page_name'];?></td>
		<td><?php echo $g_view['page_data_arr'][$i]['heading'];?></td>
		<td><a href="static_page_edit.php?name=<?php echo $g_view['page_data_arr'][$i]['page_name'];?>">Edit</a></td>
		</tr>
		<?php
	}
}
?>
</table>