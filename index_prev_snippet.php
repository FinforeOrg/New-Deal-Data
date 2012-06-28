<!--<table cellpadding="0" cellspacing="0" class="company" style="width:auto">-->
<tr>
<th style="width:110px;">Company</th>
<th style="width:60px;">Date</th>
<th style="width:120px;">Type</th>
<th style="width:100px;">Value US$m</th>
<th style="width:170px;">Bank(s)</th>
<th style="width:170px;">Law Firm(s)</th>
<th style="width:60px;">&nbsp;</th>
<!--<th>&nbsp;</th>-->
</tr>
<?php
if(0==$g_view['data_count']){
	?>
	<tr>
	<td colspan="8">
	No deals found.
	</td>
	</tr>
	<?php
}else{
	
	////////////////////////////////////////////////////////////////////
	for($j=0;$j<$g_view['data_count'];$j++){
		
		/*******************************************/
		?>
		<tr>
		<td><a href="company.php?show_company_id=<?php echo $g_view['data'][$j]['company_id'];?>"><?php echo $g_view['data'][$j]['company_name'];?></a>
        <?php 
        
                if (isset($_REQUEST['alert']) && isset($_REQUEST['token'])) {
                    if ((int) $g_view['data'][$j]['deal_id'] > (int) $lastAlertId && $lastAlertId !== false)
                    echo "<img src='images/new.png' style='float:right;width: 24px;' />"; 
                }

        ?>
        </td>
		<td><?php echo $g_view['data'][$j]['date_of_deal'];?></td>
		<td>
		<?php
		echo Util::get_deal_type_for_home_listing($g_view['data'][$j]);
		?>
		
		</td>
		<td><?php
		/****
		sng:10/jul/2010
		if the deal value is 0, then deal value is not disclosed 
		********/
		if($g_view['data'][$j]['value_in_billion']==0){
			?>
			not disclosed
			<?php
		}else{
			echo convert_billion_to_million_for_display_round($g_view['data'][$j]['value_in_billion']);
		}
		?></td>
		<td>
		<?php
		$banks_csv = "";
		$bank_cnt = count($g_view['data'][$j]['banks']);
		for($banks_i=0;$banks_i<$bank_cnt;$banks_i++){
			$banks_csv.=", ".$g_view['data'][$j]['banks'][$banks_i]['name'];
		}
		$banks_csv = substr($banks_csv,1);
		echo $banks_csv;
		?>
		</td>
		<td>
		<?php
		$law_csv = "";
		$law_cnt = count($g_view['data'][$j]['law_firms']);
		for($law_i=0;$law_i<$law_cnt;$law_i++){
			$law_csv.=", ".$g_view['data'][$j]['law_firms'][$law_i]['name'];
		}
		$law_csv = substr($law_csv,1);
		echo $law_csv;
		?>
		</td>
		<td>
		<form method="get" action="deal_detail.php">
		<input type="hidden" name="deal_id" value="<?php echo $g_view['data'][$j]['deal_id'];?>" />
		<input name="submit" type="submit" class="btn_auto" id="button" value="Detail" />
		</form>
		</td>
		
		</tr>
		<?php
	}
	
	
}
?>
<!--</table>-->