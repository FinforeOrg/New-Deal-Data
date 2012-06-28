<?php
/*********************
sng:5/mar/2012
We now show admin verified deals with an icon
*********************/
$g_view['has_verified_deal'] = false;
?>
<table width="100%" border="0" cellpadding="0" cellspacing="0" class="company">
	<tr>
		
		<th>Date</th>
		<th>Type</th>
		
		<th>Size<!-- (in million USD)--></th>
		<th style="width:200px;">Bankers</th>
		<th style="width:200px;">Lawyers</th>
		<th></th>
		<th>&nbsp;</th>
	</tr>
	<?php
	if(0==$g_view['deal_count']){
		?>
		<tr>
		<td colspan="7">
		No transactions found
		</td>
		</tr>
		<?php
	}else{
		for($j=0;$j<$g_view['deal_count'];$j++){
			?>
			<tr>
			
			<td><?php echo $g_view['deal_data'][$j]['date_of_deal'];?></td>
			<td>
			<?php 
				echo $g_view['deal_data'][$j]['deal_cat_name'];
				
			?>
			</td>
			
			<td><?php
			/****
			sng:10/jul/2010
			if the deal value is 0, then deal value is not disclosed 
			
			sng:23/jan/2012
			We show the value if we have it else show range value
			********
			if($g_view['deal_data'][$j]['value_in_billion']==0){
				?>
				not disclosed
				<?php
			}else{
				//convert to million nad then correct to 2 decimal places
				echo convert_billion_to_million_for_display_round($g_view['deal_data'][$j]['value_in_billion']);
			} 
			*******************************************/ 
			
			echo convert_deal_value_for_display_round($g_view['deal_data'][$j]['value_in_billion'],$g_view['deal_data'][$j]['value_range_id'],$g_view['deal_data'][$j]['fuzzy_value'])
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
			<?php 
			if($g_view['deal_data'][$j]['admin_verified']=='y'){
				?><img src="images/tick_ok.gif" /><?php
				$g_view['has_verified_deal'] = true;
			}
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
<?php
if($g_view['has_verified_deal']){
	?><p><img src="images/tick_ok.gif" /> Verified deal</p><?php
}
?>