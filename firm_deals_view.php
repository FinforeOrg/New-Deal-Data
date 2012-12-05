<script type="text/javascript">
function goto_suggest_deal(){
	window.location="suggest_a_deal.php";
}
</script>
<table width="100%" cellpadding="0" cellspacing="0" border="0">
<tr>
<td style="text-align:left"><h1><?php echo $g_view['company_data']['name'];?> Deals</h1></td>
</tr>
<tr>
<td style="text-align:right;"><input type="button" class="btn_auto" value="suggest a deal" onclick="goto_suggest_deal();" /></td>
</tr>
<tr><td colspan="2" style="height:10px;">&nbsp;</td></tr>
</table>

<table width="100%" cellpadding="0" cellspacing="0" border="0">
	
	<tr>
		<td><img src="images/spacer.gif" width="1" height="10" alt="" /></td>
	</tr>
	<tr>
		<td>
			<table width="100%" border="0" cellpadding="0" cellspacing="0" class="company">
				<tr>
					<th style="width:150px;">Company</th>
					<th style="width:60px;">Date</th>
					<th>Type</th>
					<th>Size (in million USD)</th>
					<th style="width:170px;">Bankers</th>
					<th style="width:170px;">Lawyers</th>
					<th>&nbsp;</th>
				</tr>
				<?php
				if(0==$g_view['deal_count']){
					?>
					<tr>
					<td colspan="6">
					None found
					</td>
					</tr>
					<?php
				}else{
					//we fetched one extra
					if($g_view['deal_count'] > $g_view['num_to_show']){
						$total = $g_view['num_to_show'];
					}else{
						$total = $g_view['deal_count'];
					}
					for($j=0;$j<$total;$j++){
						?>
						<tr>
						<td>
						<?php
						/**************
						sng:8/aug/2012
						We now have multiple companies per deal
						<a href="company.php?show_company_id=<?php echo $g_view['deal_data'][$j]['company_id'];?>"><?php echo $g_view['deal_data'][$j]['name'];?></a>
						***********/
						echo Util::deal_participants_to_csv_with_links($g_view['deal_data'][$j]['participants']);
						?>
						</td>
						<td><?php echo $g_view['deal_data'][$j]['date_of_deal'];?></td>
						<td>
						<?php 
							show_deal_type_for_listing($g_view['deal_data'][$j]['deal_cat_name'],$g_view['deal_data'][$j]['deal_subcat1_name'],$g_view['deal_data'][$j]['deal_subcat2_name']);
							/************
							sng:5/dec/2012
							Now we have concept of participants. We no longer use target company field so we have removed 'Acquisition of' for M&A deals
							*****************/
							?>
						</td>
						<td><?php
						/****
						sng:10/jul/2010
						if the deal value is 0, then deal value is not disclosed 
						********/
						if($g_view['deal_data'][$j]['value_in_billion']==0){
							?>
							not disclosed
							<?php
						}else{
							//convert to million nad then correct to 2 decimal places
							echo convert_billion_to_million_for_display_round($g_view['deal_data'][$j]['value_in_billion']);
						} 
						 
						
						?></td>
						<td>
						<?php
						$banks_csv = "";
						$bank_cnt = count($g_view['deal_data'][$j]['banks']);
						for($banks_i=0;$banks_i<$bank_cnt;$banks_i++){
							$banks_csv.=", ".$g_view['deal_data'][$j]['banks'][$banks_i]['name'];
						}
						$banks_csv = substr($banks_csv,1);
						echo $banks_csv;
						?>
						</td>
						<td>
						<?php
						$law_csv = "";
						$law_cnt = count($g_view['deal_data'][$j]['law_firms']);
						for($law_i=0;$law_i<$law_cnt;$law_i++){
							$law_csv.=", ".$g_view['deal_data'][$j]['law_firms'][$law_i]['name'];
						}
						$law_csv = substr($law_csv,1);
						echo $law_csv;
						?>
						</td>
						<td>
						<form method="get" action="deal_detail.php">
						<input type="hidden" name="deal_id" value="<?php echo $g_view['deal_data'][$j]['id'];?>" />
						<input name="submit" type="submit" class="btn_auto" id="button" value="Detail" />
						</form>
						</td>
						</tr>
						<?php
					}
				}
				?>
				<tr>
				<td colspan="7" style="text-align:right">
				<!--
				sng:10/jul/2010
				We has to send filter data, so simple href cannot be used
				-->
				<?php
				if($g_view['start_offset'] > 0){
					$prev_offset = $g_view['start_offset'] - $g_view['num_to_show'];
					?>
					<a class="link_as_button" href="#" onclick="return go_firms_deals(<?php echo $g_view['firm_id'];?>,<?php echo $prev_offset;?>);">Prev</a>
					<?php
				}
				if($g_view['deal_count'] > $g_view['num_to_show']){
					$next_offset = $g_view['start_offset'] + $g_view['num_to_show'];
					?>
					&nbsp;&nbsp;&nbsp;<a class="link_as_button" href="#" onclick="return go_firms_deals(<?php echo $g_view['firm_id'];?>,<?php echo $next_offset;?>);">Next</a>
					<?php
				}
				?>
				</td>
				<?php
				/***
				sng:10/jul/2010
				When seeing the list of deals by clicking on the firm name, we want to show only the deals that satisfy the
				filters. So we sent the filters via hidden field. So if we want to go to next page, we need to send those again
				
				sng:23/july/2010
				Now logged in user can filter via industry also. We put hidden field to help in pagination, protected by if clause
				
				support for field deal_size
				******/
				?>
				<form id="firm_deals_helper" method="post" action="dummy.php">
				<!--
				The firm id is sent via query string
				-->
				<input type="hidden" name="region" value="<?php echo $_POST['region'];?>" />
				<input type="hidden" name="country" value="<?php echo $_POST['country'];?>" />
				<input type="hidden" name="deal_cat_name" value="<?php echo $_POST['deal_cat_name'];?>" />
				<input type="hidden" name="deal_subcat1_name" value="<?php echo $_POST['deal_subcat1_name'];?>" />
				<input type="hidden" name="deal_subcat2_name" value="<?php echo $_POST['deal_subcat2_name'];?>" />
				<input type="hidden" name="sector" value="<?php echo $_POST['sector'];?>" />
				<?php
				if($g_account->is_site_member_logged()){
					?>
					<input type="hidden" name="industry" value="<?php echo $_POST['industry'];?>" />
					<?php
				}
				?>
				
				<input type="hidden" name="year" value="<?php echo $_POST['year'];?>" />
				<?php
				/************
				sng:23/jul/2012
				we cannot send condition like >=2 so we encode it
				***************/
				?>
				<input type="hidden" name="deal_size" value="<?php echo base64_encode($_POST['deal_size']);?>" />
				<!--
				pagination offset is also sent by query string
				-->
				</form>
				
				<script type="text/javascript">
				function go_firms_deals(firm_id,page_offset){
					document.getElementById('firm_deals_helper').action = "firm_deals.php?id="+firm_id+"&start="+page_offset;
					document.getElementById('firm_deals_helper').submit();
					return false;
				}
				</script>
				</tr>
			</table>
		</td>
	</tr>
</table>