<script type="text/javascript" src="nifty_utils.js"></script>
<table cellspacing="0" cellpadding="0" border="0">
<tr>
<td>
<form method="post" action="">
<input type="hidden" name="action" value="search_company" />
<table width="100%" cellspacing="0" cellpadding="10" border="0">
<tr>
<td>Name: </td>
<td><input type="text" name="company_name" value="<?php echo $g_mc->view_to_view($_POST['company_name']);?>" style="width:100px;" /></td>
<td>Type</td>
<td>
<select name="type">
<option value="bank" <?php if((!isset($_POST['type'])||($_POST['type']==""))||(isset($_POST['type'])&&($_POST['type']=="bank"))){?>selected="selected"<?php }?>>Bank</option>
<option value="law firm" <?php if(isset($_POST['type'])&&($_POST['type']=="law firm")){?>selected="selected"<?php }?>>Law Firm</option>

</select>
</td>
<td><input type="submit" name="submit" value="search" />
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
<td colspan="9"><span class="msg_txt"><?php echo $g_view['msg'];?></span></td>
</tr>
<?php
}
?>
<tr bgcolor="#dec5b3" style="height:20px;">

<td><strong>Name</strong></td>
<td><strong>Type</strong></td>
<td><strong>Logo</strong></td>
<td>&nbsp;</td>
<td>&nbsp;</td>
</tr>
<?php
if($g_view['data_count']==0){
	?>
	<tr>
	  <td colspan="9">None found</td>
	</tr>
	<?php
}else{
	
	for($i=0;$i<$g_view['data_count'];$i++){
		?>
		<tr>
		
		<td><?php echo $g_view['data'][$i]['name'];?></td>
		<td><?php echo $g_view['data'][$i]['type'];?></td>
		<td>
		<?php
		if($g_view['data'][$i]['logo']!=""){
			?>
			<img src="<?php echo LOGO_IMG_URL;?>/<?php echo $g_view['data'][$i]['logo'];?>" border="0" />
			<?php
		}
		?>
		</td>
		
		<td>
		<form method="post" action="blf_edit.php">
		<input type="hidden" name="company_id" value="<?php echo $g_view['data'][$i]['company_id'];?>" />
		<input type="submit" value="Edit" />
		</form>
		</td>
		<td>
		<?php
		/***
		sng:15/5/2010
		We might have to delete a spurious bank or law firm. We send the company_id of that bank/law firm
		However, since this is also a search form, we need to post the search params as well
		***/
		?>
		<form method="post" action="" onsubmit="return confirm_deletion_msg('Are you sure you want to delete the <?php echo $_POST['type'];?> <?php echo $g_view['data'][$i]['name'];?>?\nThis cannot be undone');">
		<input type="hidden" name="action" value="blf_delete" />
		<input type="hidden" name="company_id" value="<?php echo $g_view['data'][$i]['company_id'];?>" />
		<input type="hidden" name="company_name" value="<?php echo $g_mc->view_to_view($_POST['company_name']);?>" />
		<input type="hidden" name="type" value="<?php echo $_POST['type'];?>" />
		<input type="submit" value="Delete" />
		</form>
		</td>
		</tr>
		<?php
	}
	
}
?>
</table>