<table width="100%" cellpadding="0" cellspacing="5" border="0">
<?php
if($g_view['data_count'] == 0){
	?>
	<tr><td>None found</td></tr>
	<?php
}else{
	?>
	<tr>
	<?php
	$col_count = 0;
	for($i=0;$i<$g_view['data_count'];$i++){
		?>
		<td width="33%">
		<!--firm data-->
		<table width="100%" cellpadding="0" cellspacing="0" class="registercontent company">
		<tr><th><?php echo $g_view['data'][$i]['caption'];?></th></tr>
		<?php
		if($g_view['data'][$i]['firm_count'] == 0){
			?>
			<tr><td>None found</td></tr>
			<?php
		}else{
			for($j = 0;$j<$g_view['data'][$i]['firm_count'];$j++){
				$firm_data = $g_view['data'][$i]['firm_data_arr'][$j];
				//this is in the form id|firm name
				$firm_data_token = explode("|",$firm_data);
				?>
				<tr><td style="padding:5px 5px 5px 5px"><a href="showcase_firm.php?id=<?php echo $firm_data_token[0];?>"><?php echo $firm_data_token[1];?></a></td></tr>
				<?php
			}
		}
		?>
		</table>
		<!--firm data-->
		</td>
		<?php
		$col_count++;
		if($col_count == 3){
			$col_count = 0;
			?>
			</tr>
			<tr>
			<?php
		}
	}
	?>
	</tr>
	<?php
}
?>
</table>