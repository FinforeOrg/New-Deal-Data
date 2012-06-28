<table width="100%" cellpadding="0" cellspacing="0" border="0">
<tr>
<td>
<span style="color:#E86200;">Hi <a href="my_profile.php"><?php echo $_SESSION['f_name']." ".substr($_SESSION['l_name'],0,1);?></a></span>
</td>
</tr>
<?php
if($g_view['competitor_found']){
	if($g_view['deal_found']){
		?>
		<tr>
		<td>
			<!--deal company, value data-->
			<table width="100%" cellpadding="0" cellspacing="0" border="0">
			<tr>
			<td>
			<h4><?php echo $g_view['deal_data']['company_name'];?> (<?php echo $g_view['deal_data']['deal_cat_name'];?>)</h4>
			</td>
			<td>
			<h4><?php if($g_view['deal_data']['value_in_billion']==0) echo "Value not disclosed"; else echo "$".convert_billion_to_million_for_display($g_view['deal_data']['value_in_billion'])."m";?></h4>
			</td>
			<td>
			<h4><?php echo date("jS M Y",strtotime($g_view['deal_data']['date_of_deal']));?></h4>
			</td>
			</tr>
			</table>
			<!--deal company, value data-->
		</td>
		</tr>
		<tr>
		<td>
			<!--deal data-->
			<table cellpadding="0" cellspacing="0">
			<tr>
			<td style="width:200px;">
				<!--tombstone-->
				<?php
				$g_trans->get_tombstone_from_deal_id($g_view['deal_data']['deal_id']);
				?>
				<!--tombstone-->
			</td>
			<td style="width:10px;">&nbsp;</td>
			<td>
				<!--deal data, bankers lawyers-->
				<table cellpadding="0" cellspacing="0">
				<tr><td><?php echo $g_view['deal_data']['company_name'];?> (<?php echo $g_view['deal_data']['hq_country'];?>, <?php echo $g_view['deal_data']['industry'];?>)</td></tr>
				<tr><td><?php echo $g_view['deal_data']['deal_cat_name'];?> deal</td></tr>
				<tr><td>
				<?php
				/**
				sng:10/jul/2010
				if deal value is 0, it is not disclosed
				***/
				if($g_view['deal_data']['value_in_billion']==0){
					?>
					value not disclosed
					<?php
				}else{
					?>
					$<?php echo convert_billion_to_million_for_display($g_view['deal_data']['value_in_billion']);?>m
					<?php
				}
				?>
				<?php
				/****
				sng:08/oct/2010
				if M&A deal and is pending, then show announced on
				****/
				$closing_txt = "closed on";
				if(($g_view['deal_data']['deal_cat_name']=="M&A")&&($g_view['deal_data']['deal_subcat1_name']!="Completed")){
					$closing_txt = "announced on";
				}
				?>
				, <?php echo $closing_txt;?> <?php echo date("jS M Y",strtotime($g_view['deal_data']['date_of_deal']));?></td></tr>
				<tr>
				<td>
				<?php
				$bank_count = count($g_view['deal_data']['banks']);
				if($bank_count == 0){
					?>
					No banks listed for this transaction
					<?php
				}else{
					?>
					<?php echo $bank_count;?> bank(s) involved
					<?php
				}
				?>
				</td>
				</tr>
				<?php
				if($bank_count > 0){
					?>
					<tr>
					<td>
					<!--the banks-->
					<table cellpadding="2" cellspacing="5" border="0" style="width:auto;">
					<tr>
					<?php
					$row_count = 0;
					for($i=0;$i<$bank_count;$i++){
						?>
						<td><?php echo $g_view['deal_data']['banks'][$i]['name'];?></td>
						<?php
						$row_count++;
						if($row_count == 3){
							$row_count = 0;
							?>
							</tr>
							<tr>
							<?php
						}
					}
					?>
					</tr>
					</table>
					<!--the banks-->
					</td>
					</tr>
					<?php
				}
				?>
				<?php ////////////////////////////////////////////////// ?>
				<tr>
				<td>
				<?php
				$law_count = count($g_view['deal_data']['law_firms']);
				if($law_count == 0){
					?>
					No law firms listed for this transaction
					<?php
				}else{
					?>
					<?php echo $law_count;?> law firm(s) involved
					<?php
				}
				?>
				</td>
				</tr>
				<?php
				if($law_count > 0){
					?>
					<tr>
					<td>
					<!--the law firms-->
					<table cellpadding="2" cellspacing="5" border="0" style="width:auto;">
					<tr>
					<?php
					$row_count = 0;
					for($i=0;$i<$law_count;$i++){
						?>
						<td><?php echo $g_view['deal_data']['law_firms'][$i]['name'];?></td>
						<?php
						$row_count++;
						if($row_count == 3){
							$row_count = 0;
							?>
							</tr>
							<tr>
							<?php
						}
					}
					?>
					</tr>
					</table>
					<!--the law firms-->
					</td>
					</tr>
					<?php
				}
				?>
				<tr>
				<td>
				<form method="get" action="deal_detail.php">
				<input type="hidden" name="deal_id" value="<?php echo $g_view['deal_data']['deal_id'];?>" />
				<input name="submit" type="submit" class="btn_auto" id="button" value="Detail" />
				</form>
				</td>
				</tr>
				</table>
				<!--deal data, bankers lawyers-->
			</td>
			</tr>
			</table>
			<!--deal data-->
		</td>
		</tr>
		<?php
	}else{
		?>
		<tr><td>Featured deal not found</td></tr>
		<?php
	}
}else{
	?>
	<tr><td>No company of similar industry found</td></tr>
	<?php
}
?>
</table>