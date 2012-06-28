<?php
/*****************************
1/oct/2011
we put these in container view
<script src="js/jquery-ui-1.8.11.custom.min.js" type="text/javascript"></script>  
<script src="js/jquery.ui.selectmenu.js" type="text/javascript"></script>
<link rel="stylesheet" href="css/custom-theme/jquery-ui-1.8.9.custom.css" />
<link rel="stylesheet" href="css/custom-theme/jquery.ui.selectmenu.css" />
*********************************/
?>
<script>
jQuery(function(){
	jQuery('.btn_auto').button();
});
</script>
<script type="text/javascript">
function goto_suggest_deal(){
	window.location="suggest_a_deal.php";
}
</script>
<table width="100%" cellpadding="0" cellspacing="0" border="0">
<tr>
<td style="text-align:left"><h1><?php echo $g_view['company_data']['name'];?></h1></td>
</tr>
<tr>
<td style="text-align:right;"><input type="button" class="btn_auto" value="suggest a deal" onclick="goto_suggest_deal();" /></td>
</tr>
<tr><td colspan="2" style="height:10px;">&nbsp;</td></tr>
</table>

<table width="100%" cellpadding="0" cellspacing="0" border="0">
	<tr>
		<td><h1>Recent Deals</h1></td>
	</tr>
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
					for($j=0;$j<$g_view['deal_count'];$j++){
						?>
						<tr>
						<td><a href="company.php?show_company_id=<?php echo $g_view['deal_data'][$j]['company_id'];?>"><?php echo $g_view['deal_data'][$j]['name'];?></a></td>
						<td><?php echo $g_view['deal_data'][$j]['date_of_deal'];?></td>
						<td>
						<?php
							show_deal_type_for_listing($g_view['deal_data'][$j]['deal_cat_name'],$g_view['deal_data'][$j]['deal_subcat1_name'],$g_view['deal_data'][$j]['deal_subcat2_name']);
							if(($g_view['deal_data'][$j]['deal_cat_name']=="M&A")&&($g_view['deal_data'][$j]['target_company_name']!="")){
								/************************************************
								sng:28/july/2010
								check if the subtype is Completed or not
								**********/
								if(strtolower($g_view['deal_data'][$j]['deal_subcat1_name'])=="completed"){
									echo ". Acquisition of ".$g_view['deal_data'][$j]['target_company_name'];
								}else{
									echo ". Proposed acquisition of ".$g_view['deal_data'][$j]['target_company_name'];
								}
								/******************************************/
							}
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
						?>
						 
						
						</td>
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
			</table>
		</td>
	</tr>
</table>