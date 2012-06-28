<?php
/*********************
sng:5/mar/2012
We now show admin verified deals with an icon
The variable $g_view['has_verified_deal'] is defined in index_new_view.php
*********************/
?>
<!--<table cellpadding="0" cellspacing="0" class="company" style="width:auto">-->
<tr>
<th style="width:110px;">Companies</th>
<th style="width:100px;">Size</th>
<th style="width:200px;">Bank(s)</th>
<th></th>
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
		
		<td>
		<?php
		/***************************
		sng:1/feb/2012
		We now have multiple companies per deal
		<a href="company.php?show_company_id=<?php echo $g_view['data'][$j]['company_id'];?>"><?php echo $g_view['data'][$j]['company_name'];?></a>
		*******************/
		echo Util::deal_participants_to_csv_with_links($g_view['data'][$j]['participants']);
		?>
		
        <?php 
        
                if (isset($_REQUEST['alert']) && isset($_REQUEST['token'])) {
                    if ((int) $g_view['data'][$j]['deal_id'] > (int) $lastAlertId && $lastAlertId !== false)
                    echo "<img src='images/new.png' style='float:right;width: 24px;' />"; 
                }

        ?>
        </td>
		
		
		<td>
		<a href="deal_detail.php?deal_id=<?php echo $g_view['data'][$j]['deal_id'];?>">
		<?php
		echo $g_view['data'][$j]['fuzzy_value_short_caption'];
		?>
		</a>
		</td>
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
		if($g_view['data'][$j]['admin_verified']=='y'){
			?><img src="images/tick_ok.gif" /><?php
			$g_view['has_verified_deal'] = true;
		}
		?>
		</td>
		
		
		</tr>
		<?php
	}
	
	
}
?>
<!--</table>-->