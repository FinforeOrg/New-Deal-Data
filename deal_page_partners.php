<?php
$bank_count = count($g_view['deal_data']['banks']);
$law_count = count($g_view['deal_data']['law_firms']);
/************
sng:28/sep/2011
We show the footnote regarding minor players only if there is one, otherwise we do not show the footnote
We need a variable to track

sng:23/mar/2012
We are thinking about role based solution, and may not use this field
******************/
/********
$g_view['has_minor'] = false;
***********/
?>
<table width="100%" cellpadding="0" cellspacing="0">
<!--banks and lawfirms-->
	<tr>
		<td>
			<table width="100%" cellpadding="0" cellspacing="2" class="registercontent">
				<tr>
					<th>Bank(s)</th>
					<th>&nbsp;</th>
					<th>Law Firm(s)</th>
				</tr>
			
				<tr>
					<td style="font-weight:bold;">
					<?php
					if($bank_count == 0){
						?>
						No banks listed for this transaction
						<?php
					}else{
						?>
						<?php echo $bank_count;?> bank(s) involved. Deal credit of $<?php echo convert_billion_to_million_for_display($g_view['deal_data']['banks'][0]['adjusted_value_in_billion']);?>m each.
						<?php
					}
					?>
					</td>
					<td>&nbsp;</td>
					<td style="font-weight:bold;">
					<?php
					if($law_count == 0){
						?>
						No law firms listed for this transaction
						<?php
					}else{
						?>
						<?php echo $law_count;?> law firm(s) involved. Deal credit of $<?php echo convert_billion_to_million_for_display($g_view['deal_data']['law_firms'][0]['adjusted_value_in_billion']);?>m each.
						<?php
					}
					?>
				</td>
			</tr>
			<tr>
			<td style="width:49%;">
			<!--banks-->
			<?php
			/*********
			sng:17/jun/2011
			support for sellside adviser
			
			sng:26/sep/2011
			support for insignificant flag
			***/
			if($bank_count > 0){
				?>
				<table width="100%" cellpadding="0" cellspacing="0" class="company">
				<?php
				for($i=0;$i<$bank_count;$i++){
					?>
					<tr>
					<?php
					/*********************************
					sng:23/mar/2012
					We are thinking about role based solution so for now we are not using this
					<td colspan="3"><strong><?php echo $g_view['deal_data']['banks'][$i]['name'];?> <?php if($g_view['deal_data']['banks'][$i]['is_insignificant']=='y'){?>*<?php $g_view['has_minor']=true; }?>
					************************************/
					?>
					
					<td colspan="3"><strong><?php echo $g_view['deal_data']['banks'][$i]['name'];?>
					
					<?php
					/***************************
					sng:23/mar/2012
					We now have role like 'Advisor, Sellside'. We no longer use the is_sellside flag
					*****************************/
					?>
					</strong>
					<?php
					if(!empty($g_view['deal_data']['banks'][$i]['role_name'])){
						?>
						<br /><?php echo $g_view['deal_data']['banks'][$i]['role_name'];?>
						<?php
					}
					?>
					</td>
					</tr>
					
					<tr><td colspan="3" style="height:10px; border:0 0 0 0">&nbsp;</td></tr>
					<?php
				}
				?>
				</table>
				<?php
			}
			?>
			<!--banks-->
			</td>
			<td>&nbsp;</td>
			<td style="width:49%">
			<!--law firms-->
			<?php
			if($law_count > 0){
				?>
				<table width="100%" cellpadding="0" cellspacing="0" class="company">
				<?php
				for($i=0;$i<$law_count;$i++){
					?>
					<?php
					/************************
					sng:23/mar/2012
					We are thinking about role based solution so for now we are not using this
					<tr>
					<td colspan="3"><strong><?php echo $g_view['deal_data']['law_firms'][$i]['name'];?> <?php if($g_view['deal_data']['law_firms'][$i]['is_insignificant']=='y'){?>*<?php $g_view['has_minor']=true; }?>
					**************************/
					?>
					
					<td colspan="3"><strong><?php echo $g_view['deal_data']['law_firms'][$i]['name'];?> 
					
					<?php
					/***************************
					sng:23/mar/2012
					We now have role like 'Advisor, Sellside'. We no longer use the is_sellside flag
					*****************************/
					?>
					</strong>
					<?php
					if(!empty($g_view['deal_data']['law_firms'][$i]['role_name'])){
						?>
						<br /><?php echo $g_view['deal_data']['law_firms'][$i]['role_name'];?>
						<?php
					}
					?>
					</td>
					</tr>
					
					
					<tr><td colspan="3" style="height:10px; border:0 0 0 0">&nbsp;</td></tr>
					<?php
				}
				?>
				</table>
				<?php
			}
			?>
			<!--law firms-->
			</td>
			</tr>
			</table>
		</td>
	</tr>
<!--banks and law firms-->
<tr>
<td>
<?php
/*****************************
sng:23/mar/2012
We are thinking about role based solution so for now we are not using this
if($g_view['has_minor']){
?>
* Participated in the transaction, but not classified as a lead advisor with full league table credit
<?php
}
*******************************/
?>

</td>
</tr>
</table>