<table cellspacing="0" cellpadding="0" border="0">
<tr>
<td>
<form method="post" action="">
<input type="hidden" name="action" value="search_deal" />
<table width="100%" cellspacing="0" cellpadding="5" border="0">
<tr>
<td>
	<table cellspacing="0" cellpadding="5">
	<tr>
	<td>
	Sector&nbsp;<input type="text" name="deal_sector" value="<?php echo $_POST['deal_sector'];?>" style="width:200px;" />
	</td>
	<td>
	Industry&nbsp;<input type="text" name="deal_industry" value="<?php echo $_POST['deal_industry'];?>" style="width:200px;" />
	</td>
	<td>
	<input type="submit" value="submit" />
	</td>
	</tr>
	</table>
</td>
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
<td colspan="8"><span class="msg_txt"><?php echo $g_view['msg'];?></span></td>
</tr>
<?php
}
?>
<tr bgcolor="#dec5b3" style="height:20px;">

<td><strong>Company Name</strong></td>
<td><strong>Date</strong></td>
<td><strong>Type</strong></td>
<td><strong>Deal Value<br />
(in billion) </strong></td>
<td><strong>Sector</strong></td>
<td><strong>Industry</strong></td>
<td><strong>&nbsp;</strong></td>

</tr>
<?php
if($g_view['data_count']==0){
	?>
	<tr>
	  <td colspan="10">None found</td>
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
		
		<td><?php echo $g_view['data'][$i]['company_name'];?></td>
		<td><?php echo $g_view['data'][$i]['date_of_deal'];?></td>
		<td><?php echo $g_view['data'][$i]['deal_cat_name'];?> <?php echo $g_view['data'][$i]['deal_subcat1_name'];?> <?php echo $g_view['data'][$i]['deal_subcat2_name'];?></td>
		<td><?php echo $g_view['data'][$i]['value_in_billion'];?></td>
		<td>
		<?php echo $g_view['data'][$i]['deal_sector'];?>
		
		</td>
		<td>
		<?php echo $g_view['data'][$i]['deal_industry'];?>
		
		
		</td>
		<td>
		<form method="post" action="deal_edit.php">
		<input type="hidden" name="deal_id" value="<?php echo $g_view['data'][$i]['id'];?>" />
		<input type="submit" value="Edit" />
		</form>
		</td>
		
		</tr>
		<?php
	}
	?>
	<tr>
	<td colspan="7">
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
	<script type="text/javascript">
	function goto_page(num){
		document.getElementById("start").value=num;
		document.getElementById("page_frm").submit();
		return false;
	}
	</script>
	<form method="post" id="page_frm" action="">
	<input type="hidden" name="action" value="search_deal" />
	<input type="hidden" name="start" id="start" value="0" />
	<input type="hidden" name="deal_sector" value="<?php echo $_POST['deal_sector'];?>" />
	<input type="hidden" name="deal_industry" value="<?php echo $_POST['deal_industry'];?>" />
	</form>
	<?php
}
?>
</table>