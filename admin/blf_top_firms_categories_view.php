<script type="text/javascript" src="util.js"></script>
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
<form method="post" action="" >
<input name="action" type="hidden" value="add" />
<table cellpadding="0" cellspacing="5" border="0" >
<tr>
<td>Category Name</td>
<td><input type="text" name="name" value="<?php echo $g_view['input']['name'];?>" style="width:200px;" /><span class="err_txt"> *</span><br />
<span class="err_txt"><?php echo $g_view['err']['name'];?></span></td>
</tr>

<td>&nbsp;</td>
<td><input type="submit" name="submit" value="Add" />
</tr>
</table>
</form>
</td>
</tr>

<tr bgcolor="#dec5b3" style="height:20px;">
<td>&nbsp;</td>
<td><strong>Name</strong></td>
<td>&nbsp;</td>
<td>&nbsp;</td>
<td>&nbsp;</td>
</tr>
<?php
if($g_view['data_count']==0){
	?>
	<tr>
	  <td colspan="5">No top firms categories found</td>
	</tr>
	<?php
}else{
	for($i=0;$i<$g_view['data_count'];$i++){
		?>
		<tr>
		<td>
		<?php
		if($g_view['data'][$i]['is_default']=='Y'){
			?>
			<img src="images/featured.png" />
			<?php
		}
		?>
		</td>
		<td><?php echo $g_view['data'][$i]['name'];?></td>
		<td><a href="" onclick="return top_firm_popup('<?php echo $g_view['data'][$i]['id'];?>','bank');">Banks</a></td>
		<td><a href="" onclick="return top_firm_popup('<?php echo $g_view['data'][$i]['id'];?>','law firm');">Law Firms</a></td>
		<td>
		<?php
		if($g_view['data'][$i]['is_default']!='Y'){
			?>
			<form method="post" action="">
				<input type="hidden" name="action" value="make_default" />
				<input type="hidden" name="id" value="<?php echo $g_view['data'][$i]['id'];?>" />
 				<input type="submit" name="submit" value="Make default" />
			</form>
			<?php
		}
		?>
		</td>
		</tr>
		<?php
	}
}
?>
</table>