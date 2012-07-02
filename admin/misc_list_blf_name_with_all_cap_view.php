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
<tr>
<td colspan="5">
<form method="post" action="">
<input type="hidden" name="myaction" value="search" />
<table cellspacing="10">
<tr>
<td>For</td>
<td>
<select name="type">
<option value="bank" <?php if((!isset($_POST['type'])||($_POST['type']==""))||(isset($_POST['type'])&&($_POST['type']=="bank"))){?>selected="selected"<?php }?>>Bank</option>
<option value="law firm" <?php if(isset($_POST['type'])&&($_POST['type']=="law firm")){?>selected="selected"<?php }?>>Law Firm</option>
</select>
<td><input type="submit" value="Search" />
</tr>
</table>
</form>
</td>
</tr>
<tr bgcolor="#dec5b3" style="height:20px;">
<td><strong>Name</strong></td>
<td><strong>Type</strong></td>
<td>&nbsp;</td>
</tr>
<?php
if($g_view['data_count']==0){
	?>
	<tr>
	  <td colspan="5">None found</td>
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
		<td><?php echo $g_view['data'][$i]['name'];?></td>
		<td><?php echo $g_view['data'][$i]['type'];?></td>
		<td>
		<form method="post" action="blf_edit.php">
		<input type="hidden" name="company_id" value="<?php echo $g_view['data'][$i]['company_id'];?>" />
		<input type="submit" value="Edit" />
		</form>
		</td>
		</tr>
		<?php
	}
	?>
	<form id="pagination_support" method="post" action="">
	<input type="hidden" name="myaction" value="search" />
	<input type="hidden" name="type" value="<?php echo $_POST['type'];?>" />
	<input id="pagination_start" type="hidden" name="start" value="0" />
	</form>
	<script type="text/javascript">
	function goto_page(start){
		document.getElementById("pagination_start").value = start;
		document.getElementById("pagination_support").submit();
		return false;
	}
	</script>
	<tr>
	<td colspan="5" style="text-align:right">
	<?php
	if($g_view['start'] > 0){
		$prev_offset = $g_view['start'] - $g_view['num_to_show'];
		?>
		<a href="#" onClick="return goto_page(<?php echo $prev_offset;?>);">Prev</a>
		<?php
	}
	if($g_view['data_count'] > $g_view['num_to_show']){
		$next_offset = $g_view['start'] + $g_view['num_to_show'];
		?>
		&nbsp;&nbsp;&nbsp;<a href="#" onClick="return goto_page(<?php echo $next_offset;?>);">Next</a>
		<?php
	}
	?>
	</td>
	</tr>
	<?php
}
?>
</table>