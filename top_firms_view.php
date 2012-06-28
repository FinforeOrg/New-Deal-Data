<div id="explanation">
<p>The following pages display the leading firms, both banks and law firms for specific types of transactions. You can use the drop-down menu to refine your analysis.</p>
</div>
<table width="100%" cellpadding="0" cellspacing="5" class="registercontent">
<tr>
<td colspan="2">
<?php
/***
sng:19/jul/2010
Now, there are categories for top firms. If a category is selected, we need to show the firms
under those categories only.

sng:22/july/2010
Now we no longer need a default category. Admin can mark a category as default. So
if no category is selected, then select that one
*************/
if($g_view['top_firm_cat_data_count'] > 0){
	?>
	<form id="top_firms_categories_frm" method="post" action="top_firms.php">
	<select name="top_firm_cat_id" id="top_firm_cat_id" onchange="document.getElementById('top_firms_categories_frm').submit();">
	<?php
	for($i=0;$i<$g_view['top_firm_cat_data_count'];$i++){
		$select_this = false;
		if($g_view['top_firm_cat_id'] == ""){
			if($g_view['top_firm_cat_data'][$i]['is_default'] == 'Y'){
				$select_this = true;
			}
		}else{
			if($g_view['top_firm_cat_id']==$g_view['top_firm_cat_data'][$i]['id']){
				$select_this = true;
			}
		}
		?>
		<option value="<?php echo $g_view['top_firm_cat_data'][$i]['id'];?>" <?php if($select_this){?>selected="selected"<?php }?>><?php echo $g_view['top_firm_cat_data'][$i]['name'];?></option>
		<?php
	}
	?>
	</select>
	</form>
	<?php
}
?>
</td>
</tr>
<tr>
<th style="width:50%">Top Banks</th>
<th>Top Law Firms</th>
</tr>
<tr>
<td>
<?php
if($g_view['bank_data_count']==0){
	?>
	None found
	<?php
}else{
	?>
	<table width="100%" cellpadding="0" cellspacing="10">
	<tr>
	<?php
	$col_cnt = 0;
	for($i=0;$i<$g_view['bank_data_count'];$i++){
		?>
		<td>
		<a href="showcase_firm.php?id=<?php echo $g_view['bank_data'][$i]['company_id'];?>">
		<?php
		if($g_view['bank_data'][$i]['logo']!=""){
			?>
			<img src="uploaded_img/logo/<?php echo $g_view['bank_data'][$i]['logo'];?>" style="width:150px;" />
			<?php
		}else{
			echo $g_view['bank_data'][$i]['name'];
		}
		?>
		</a>
		</td>
		<?php
		$col_cnt++;
		if($col_cnt == 2){
			$col_cnt = 0;
			?>
			</tr><tr>
			<?php
		}
	}
	?>
	</tr>
	</table>
	<?php
}
?>
</td>
<td>
<?php
if($g_view['lawfirm_data_count']==0){
	?>
	None found
	<?php
}else{
	?>
	<table width="100%" cellpadding="0" cellspacing="10">
	<tr>
	<?php
	$col_cnt = 0;
	for($i=0;$i<$g_view['lawfirm_data_count'];$i++){
		?>
		<td>
		<a href="showcase_firm.php?id=<?php echo $g_view['lawfirm_data'][$i]['company_id'];?>">
		<?php
		if($g_view['lawfirm_data'][$i]['logo']!=""){
			?>
			<img src="uploaded_img/logo/<?php echo $g_view['lawfirm_data'][$i]['logo'];?>" style="width:150px;" />
			<?php
		}else{
			echo $g_view['lawfirm_data'][$i]['name'];
		}
		?>
		</a>
		</td>
		<?php
		$col_cnt++;
		if($col_cnt == 2){
			$col_cnt = 0;
			?>
			</tr><tr>
			<?php
		}
	}
	?>
	</tr>
	</table>
	<?php
}
?>
</td>
</tr>
</table>
<script>
/**************************
sng:24/oct/2011
Unless the dom is loaded, trying to do this results in error if IE is used
***************************/
$(function(){
	$('#top_firm_cat_id').selectmenu();
});
</script>