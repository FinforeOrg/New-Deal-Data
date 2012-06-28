<table cellpadding="0" cellspacing="0" class="company" style="width:400px;">
<tr>
<th colspan="3">Members who recommended me</th>
</tr>
<?php
if($g_view['recommended_by_count']==0){
?>
<tr><td colspan="3">None</td></tr>
<?php
}else{
	for($i=0;$i<$g_view['recommended_by_count'];$i++){
		?>
		<tr>
		<td><a href="profile.php?mem_id=<?php echo $g_view['recommended_by_data'][$i]['mem_id'];?>"><?php echo $g_view['recommended_by_data'][$i]['f_name'];?> <?php echo $g_view['recommended_by_data'][$recom]['l_name'];?></a></td>
		<td><?php echo $g_view['recommended_by_data'][$i]['designation'];?> at <?php echo $g_view['recommended_by_data'][$i]['company_name'];?></td>
		<td>
		<form method="post" action="">
		<input type="hidden" name="action" value="delete_recommend_me" />
		<input type="hidden" name="mem_id" value="<?php echo $g_view['recommended_by_data'][$i]['mem_id'];?>"  />
		<input type="submit" name="submit" value="Delete" class="btn_auto" />
		</form>
		</td>
		</tr>
		<?php
	}
}
?>
<tr>
<th colspan="3">Members who admire me</th>
</tr>
<?php
if($g_view['admired_by_count']==0){
?>
<tr><td colspan="3">None</td></tr>
<?php
}else{
	for($i=0;$i<$g_view['admired_by_count'];$i++){
		?>
		<tr>
		<td>
		<a href="profile.php?mem_id=<?php echo $g_view['admired_by_data'][$i]['mem_id'];?>"><?php echo $g_view['admired_by_data'][$i]['f_name'];?> <?php echo $g_view['admired_by_data'][$i]['l_name'];?></a>
		</td>
		<td><?php echo $g_view['admired_by_data'][$i]['designation'];?> at <?php echo $g_view['admired_by_data'][$i]['company_name'];?></td>
		<td>
		<form method="post" action="">
		<input type="hidden" name="action" value="delete_admire_me" />
		<input type="hidden" name="mem_id" value="<?php echo $g_view['admired_by_data'][$i]['mem_id'];?>"  />
		<input type="submit" name="submit" value="Delete" class="btn_auto" />
		</form>
		</td>
		</tr>
		<?php
	}
}
?>
</table>