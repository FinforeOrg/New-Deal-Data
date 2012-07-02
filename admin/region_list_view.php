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
<?php
/***********************************
sng:23/feb/2011
we now need to show whether the region is active/inactive
and a button to deactivate/activate

sng:24/feb/2011
Need display order
**********************************/
?>
<td>&nbsp;</td>
<td><strong>Name</strong></td>
<td>#</td>
<td>&nbsp;</td>
<td>&nbsp;</td>
<td>&nbsp;</td>
</tr>
<?php
if($g_view['data_count']==0){
	?>
	<tr>
	  <td colspan="6">No region data found</td>
	</tr>
	<?php
}else{
	for($i=0;$i<$g_view['data_count'];$i++){
		?>
		<tr>
		<?php
		/*******************
		sng:23/feb/2011
		show active/inactive link
		*********************/
		?>
		<td>
		<?php
		if($g_view['data'][$i]['is_active']=='y'){
			?>
			<img src="images/featured.png" />
			<?php
		}else{
			?>
			<img src="images/delete_icon.gif" />
			<?php
		}
		?>
		</td>
		<?php
		/**********************************************/
		?>
		
		<td><?php echo $g_view['data'][$i]['name'];?></td>
		<?php
		/*************************************************
		sng:24/feb/2011
		support for display order
		******/
		?>
		<td>
		<form method="post" action="">
		<input type="hidden" name="action" value="change_display_order" />
		<input type="hidden" name="region_id" value="<?php echo $g_view['data'][$i]['id'];?>" />
		<input type="text" name="display_order" value="<?php echo $g_view['data'][$i]['display_order'];?>" style="width:40px;" />
		<input type="submit" name="submit" value="Change" />
		</form>
		</td>
		<?php
		/******************************************************/
		?>
		<td><a href="region_country_list.php?region_id=<?php echo $g_view['data'][$i]['id'];?>">List countries</a></td>
		<?php
		/**************************
		sng:23/feb/2011
		button to deactivate if this is active and vice versa
		*****/
		if($g_view['data'][$i]['is_active']=='y'){
			$show_button = "Deactivate";
			$is_active = "n";
		}else{
			$show_button = "Activate";
			$is_active = "y";
		}
		?>
		<td>
		<form method="post" action="">
		<input type="hidden" name="action" value="toggle_active" />
		<input type="hidden" name="region_id" value="<?php echo $g_view['data'][$i]['id'];?>" />
		<input type="hidden" name="is_active" value="<?php echo $is_active;?>" />
		<input type="submit" name="submit" value="<?php echo $show_button;?>" />
		</form>
		</td>
		<?php
		/***************************************/
		?>
		<td>
		<form method="post" action="">
		<input type="hidden" name="action" value="delete_region" />
		<input type="hidden" name="region_id" value="<?php echo $g_view['data'][$i]['id'];?>" />
		<input type="submit" name="submit" value="Delete" />
		</form>
		</td>
		</tr>
		<?php
	}
}
?>
</table>