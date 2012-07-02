<script src="util.js"></script>
<script type="text/javascript">
function goto_add_deal(suggestion_id){
	window.location.replace("simple_deal_add.php");
	return false;
}
</script>
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
<?php
/******************
sng:30/aug/2011
I think a description here will make things easier
********************/
if($g_view['data_count']!=0){
?>
<p>
Click the 'View' button to see the detail in a popup window.
</p>
<p>
If the suggestion is useless or duplicate, close the popup window and click 'Reject' button to delete it.
</p>
<p>
If the detail is usefull, click the 'Create a deal' button to open the form and start adding the data.
</p>
<?
}
?>
<tr bgcolor="#dec5b3" style="height:20px;">
<td><strong>Company</strong></td>
<td><strong>Date</strong></td>
<td><strong>Value in $m</strong></td>
<td><strong>Category</strong></td>
<td><strong>Sub category 1</strong></td>
<td><strong>Sub category 2</strong></td>
<td><strong>By</strong></td>
<td>&nbsp;</td>
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
		<td><?php echo $g_view['data'][$i]['deal_company_name'];?></td>
		<td><?php
		/***********
		sng:5/july/2011
		For new deal suggestion, we do not have date of deal. We use date closed if it is there or date announced if it is there
		or blank
		***************/
		if($g_view['data'][$i]['date_closed']!="0000-00-00") echo date("M, Y",strtotime($g_view['data'][$i]['date_closed']));
		elseif($g_view['data'][$i]['date_announced']!="0000-00-00") echo date("M, Y",strtotime($g_view['data'][$i]['date_announced']));
		else echo "n/a";
		?></td>
		<td><?php echo $g_view['data'][$i]['value_in_million'];?></td>
		<td><?php echo $g_view['data'][$i]['deal_cat_name'];?></td>
		<td>
		<?php echo $g_view['data'][$i]['deal_subcat1_name'];?>
		</td>
		<td>
		<?php echo $g_view['data'][$i]['deal_subcat2_name'];?>
		</td>
		<td>
		<?php echo $g_view['data'][$i]['f_name'];?> <?php echo $g_view['data'][$i]['l_name'];?> [<?php echo $g_view['data'][$i]['designation'];?>] <?php echo $g_view['data'][$i]['work_company'];?><br /><br />on <?php echo date("Y-m-d",strtotime($g_view['data'][$i]['date_suggested']));?>
		</td>
		<td>
		<input type="button" onclick="return deal_suggestion_popup('<?php echo $g_view['data'][$i]['id'];?>');" name="view" value="View" />
		</td>
		</tr>
		
		<tr>
		<td colspan="6"></td>
		<td>
		<form method="post" action="">
		<input type="hidden" name="action" value="reject" />
		<input type="hidden" name="id" value="<?php echo $g_view['data'][$i]['id'];?>" />
		<input type="submit" name="submit" value="Reject" />
		</form>
		</td>
		<td>
		<input type="button" onclick="goto_add_deal('<?php echo $g_view['data'][$i]['id'];?>');return false;" value="Create a deal" />
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
		<a href="deal_suggestion_list.php?start=<?php echo $prev_offset;?>">Prev</a>
		<?php
	}
	if($g_view['data_count'] > $g_view['num_to_show']){
		$next_offset = $g_view['start'] + $g_view['num_to_show'];
		?>
		&nbsp;&nbsp;&nbsp;<a href="deal_suggestion_list.php?start=<?php echo $next_offset;?>">Next</a>
		<?php
	}
	?>
	</td>
	</tr>
	<?php
}
?>
</table>