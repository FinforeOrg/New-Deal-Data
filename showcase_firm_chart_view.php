<table width="100%" cellpadding="0" cellspacing="0" border="0">
<tr>
<td style="text-align:left"><h1>Charts Showcasing <?php echo $g_view['company_data']['name'];?></h1></td>
</tr>
</table>
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
		<!--charts-->
		<table width="100%" cellpadding="0" cellspacing="0" class="registercontent">
		<tr><th><?php echo $g_view['data'][$i]['name'];?></th></tr>
		<tr><td>
                        <div class="chart" id="<?php echo $g_view['data'][$i]['containerId']?>" >
                            <?php echo base64_decode($g_view['data'][$i]['img'])?>
                        </div>
                     </td>
                        </tr>
		</table>
		<!--charts-->
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