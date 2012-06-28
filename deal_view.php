<script type="text/javascript">
function goto_suggest_deal(){
	window.location="suggest_a_deal.php";
}
/*******************
sng:8/apr/2011
in transaction class, there is a function to create a tombstone. The generated html contains a onclick function goto_deal_detail
so even though it is not used, we define it here
*********/
function goto_deal_detail(deal_id){
	window.location="deal_detail.php?deal_id="+deal_id;
}
</script>
<div id="explanation">
<p>Any visitor can run a search for deal information by selecting a "Type of Transaction" and using our drop-down menus to refine their search.</p>
<p>You can download the details to excel, or if you have registered with us, you can save any search.</p>
<p>Registered users are able to create email alerts, so they receive notifications daily, of any interesting transactions added to the database.</p>
</div>
<table width="100%" cellpadding="0" cellspacing="0">
<tr>
<td>
<?php
include("deal_search_filter_form_view.php");
?>
</td>
</tr>
<!--////////////////////
sng:11/nov/2011
client wants to remove the featured deal section
<tr><td><h2>Featured Transaction and Team</h2></td></tr>
////////////////////////////-->
<?php
/************************************************
sng:14/apr/2011
client does not want the suggest a deal button here
if($g_account->is_site_member_logged()){
?>
<tr>
<td style="text-align:right;">
<input type="button" class="btn_auto" value="suggest a deal" onclick="goto_suggest_deal();" />
</td>
</tr>
<tr><td style="height:5px;">&nbsp;</td></tr>
<?php
}
***********************************************/
?>
<?php
/**********************************************************
if($g_view['featured_deal_found']){
	?>
	<tr>
	<td>
		<!--deal company, value data-->
		<table width="100%" cellpadding="0" cellspacing="0" border="0">
		<tr>
		<td>
		<h4><?php echo $g_view['featured_deal_data']['company_name'];?> (<?php echo $g_view['featured_deal_data']['deal_cat_name'];?>)</h4>
		</td>
		<td>
		<h4>$<?php echo convert_billion_to_million_for_display($g_view['featured_deal_data']['value_in_billion']);?>m</h4>
		</td>
		<td>
		<h4><?php echo date("jS M Y",strtotime($g_view['featured_deal_data']['date_of_deal']));?></h4>
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
			$g_trans->get_tombstone_from_deal_id($g_view['featured_deal_data']['deal_id']);
			?>
			<!--tombstone-->
		</td>
		<td style="width:10px;">&nbsp;</td>
		<td>
			<!--deal data, bankers lawyers-->
			<table cellpadding="0" cellspacing="0">
			<tr><td><?php echo $g_view['featured_deal_data']['company_name'];?> (<?php echo $g_view['featured_deal_data']['hq_country'];?>, <?php echo $g_view['featured_deal_data']['industry'];?>)</td></tr>
			<tr><td><?php echo $g_view['featured_deal_data']['deal_cat_name'];?> deal</td></tr>
			<tr><td>
			<?php
			/**
			sng:10/jul/2010
			if deal value is 0, it is not disclosed
			***
			if($g_view['featured_deal_data']['value_in_billion']==0){
				?>
				value not disclosed
				<?php
			}else{
				?>
				$<?php echo convert_billion_to_million_for_display($g_view['featured_deal_data']['value_in_billion']);?>m
				<?php
			}
			?>
			<?php
			/****
			sng:08/oct/2010
			if M&A deal and is pending, then show announced on
			****
			$closing_txt = "closed on";
			if(($g_view['featured_deal_data']['deal_cat_name']=="M&A")&&($g_view['featured_deal_data']['deal_subcat1_name']!="Completed")){
				$closing_txt = "announced on";
			}
			?>
			, <?php echo $closing_txt;?> <?php echo date("jS M Y",strtotime($g_view['featured_deal_data']['date_of_deal']));?></td></tr>
			<tr>
			<td>
			<?php
			$bank_count = count($g_view['featured_deal_data']['banks']);
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
					<td><?php echo $g_view['featured_deal_data']['banks'][$i]['name'];?></td>
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
			$law_count = count($g_view['featured_deal_data']['law_firms']);
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
					<td><?php echo $g_view['featured_deal_data']['law_firms'][$i]['name'];?></td>
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
			<input type="hidden" name="deal_id" value="<?php echo $g_view['featured_deal_data']['deal_id'];?>" />
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
****************************************************/
?>
</table>