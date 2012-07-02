<table cellspacing="0" cellpadding="0" border="0">
<tr>
<td>
<form method="post" action="">
<input type="hidden" name="myaction" value="search_deal" />
<table width="100%" cellspacing="0" cellpadding="10" border="0">
<tr>
<td>
<input name="missing_info_name" type="radio" value="target_country" <?php if(($_POST['missing_info_name']=="target_country")||($_POST['missing_info_name']=="")){?>checked<?php }?>>&nbsp;Country of target company (for M&amp;A deals);
</td>
<td>
<input name="missing_info_name" type="radio" value="target_sector" <?php if($_POST['missing_info_name']=="target_sector"){?>checked<?php }?>>&nbsp;Target company sector (for M&amp;A deals)
</td>
<td>
<input name="missing_info_name" type="radio" value="source" <?php if($_POST['missing_info_name']=="source"){?>checked<?php }?>>&nbsp;Deal source
</td>
</tr>
<?php
/*****************************
sng:14/sep/2011
Since the code now search for deals using the deal_country/deal_sector/deal_industry, we want admin to find any deals
that is missing this info
**********************************/
?>
<tr>
<td>
<input name="missing_info_name" type="radio" value="deal_country" <?php if($_POST['missing_info_name']=="deal_country"){?>checked<?php }?>>&nbsp;Deal countries
</td>
<td>
<input name="missing_info_name" type="radio" value="deal_sector" <?php if($_POST['missing_info_name']=="deal_sector"){?>checked<?php }?>>&nbsp;Deal sectors
</td>
<td>
<input name="missing_info_name" type="radio" value="deal_industry" <?php if($_POST['missing_info_name']=="deal_industry"){?>checked<?php }?>>&nbsp;Deal industries
</td>
</tr>
<tr>
<td colspan="3">
<input type="submit" name="submit" value="List" />
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
<td><strong>&nbsp;</strong></td>
</tr>
<?php
if($g_view['data_count']==0){
	?>
	<tr>
	  <td colspan="8">None found</td>
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
	<td colspan="8" style="text-align:right">
	<?php
	if($g_view['start'] > 0){
		$prev_offset = $g_view['start'] - $g_view['num_to_show'];
		?>
		<a href="#" onClick="return go_page(<?php echo $prev_offset;?>);">Prev</a>
		<?php
	}
	if($g_view['data_count'] > $g_view['num_to_show']){
		$next_offset = $g_view['start'] + $g_view['num_to_show'];
		?>
		&nbsp;&nbsp;&nbsp;<a href="#" onClick="return go_page(<?php echo $next_offset;?>);">Next</a>
		<?php
	}
	?>
	</td>
	</tr>
	<?php
}
?>
<form id="pagination_helper" method="post" action="list_deals_missing_info.php">
<input type="hidden" name="myaction" value="search_deal" />
<input type="hidden" name="type" value="company" />
<input type="hidden" name="missing_info_name" value="<?php echo $_POST['missing_info_name'];?>" />
<input type="hidden" name="start" id="pagination_helper_start" value="0" />
</form>
<script type="text/javascript">
function go_page(offset){
	document.getElementById('pagination_helper_start').value = offset;
	document.getElementById('pagination_helper').submit();
	return false;
}
</script>
</table>