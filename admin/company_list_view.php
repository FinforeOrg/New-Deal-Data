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
<td style="width:20px;">&nbsp;</td>
<td><strong>Name</strong></td>
<td><strong>Type</strong></td>
<td><strong>Sector</strong></td>
<td><strong>Industry</strong></td>
<td><strong>Logo</strong></td>
<td>&nbsp;</td>
<td>&nbsp;</td>
</tr>
<?php
if($g_view['data_count']==0){
	?>
	<tr>
	  <td colspan="8">No company data found</td>
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
		if($g_view['data'][$i]['is_featured']=='Y'){
			?>
			<img src="images/featured.png" border="0"  />
			<?php
		}
		?>
		</td>
		<td><?php echo $g_view['data'][$i]['name'];?></td>
		<td><?php echo $g_view['data'][$i]['type'];?></td>
		<td><?php echo $g_view['data'][$i]['sector'];?></td>
		<td><?php echo $g_view['data'][$i]['industry'];?></td>
		<td>
		<?php
		if($g_view['data'][$i]['logo']!=""){
			?>
			<img src="../uploaded_img/logo/thumbnails/<?php echo $g_view['data'][$i]['logo'];?>" border="0" />
			<?php
		}
		?>
		</td>
		<td>
		<!--
		sng:19/apr/2010
		as per chat on 16/apr/2010, we show a random company in front instead of burdening admin
		to mark a company as featured
		-->
		<?php
		//if(($g_view['data'][$i]['is_featured']=='N')&&($g_view['data'][$i]['type']=="company")){
			//only a company of type 'company' can be set as featured in front end, and not bank or law firm
		?>
		<!--<form method="post" action="company_list.php?start=<?php echo $g_view['start'];?>">
		<input type="hidden" name="action" value="featured" />
		<input type="hidden" name="company_id" value="<?php echo $g_view['data'][$i]['company_id'];?>" />
		<input type="submit" value="Set as featured" />-->
		</form>
		<?php
		//}else{
			?>
			<!--&nbsp;-->
			<?php
		//}
		?>
		</td>
		<td>
		<form method="post" action="company_edit.php">
		<input type="hidden" name="company_id" value="<?php echo $g_view['data'][$i]['company_id'];?>" />
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
		<a href="company_list.php?start=<?php echo $prev_offset;?>">Prev</a>
		<?php
	}
	if($g_view['data_count'] > $g_view['num_to_show']){
		$next_offset = $g_view['start'] + $g_view['num_to_show'];
		?>
		&nbsp;&nbsp;&nbsp;<a href="company_list.php?start=<?php echo $next_offset;?>">Next</a>
		<?php
	}
	?>
	</td>
	</tr>
	<?php
}
?>
</table>