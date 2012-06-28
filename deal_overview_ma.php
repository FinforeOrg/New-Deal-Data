<?php
/**************
remember that not all deals will have extra data, and even then, the date field may contain the default 0 value

This is M&A deal. If BOTH date_announced AND date_closed are not given BUT date_of_deal is there then:
if Completed, Completed: is date_of_deal
If Pending, Announced is date_of_deal
******************/
$date_announced = "";
$date_completed = "";

if(($g_view['deal_data']['date_announced']=="0000-00-00"||$g_view['deal_data']['date_announced']=="")&&($g_view['deal_data']['date_closed']=="0000-00-00"||$g_view['deal_data']['date_closed']=="")&&$g_view['deal_data']['date_of_deal']!="0000-00-00"){
	if(strtolower($g_view['deal_data']['deal_subcat1_name'])=="pending"){
		$date_announced = ymd_to_dmy($g_view['deal_data']['date_of_deal']);
	}
	if(strtolower($g_view['deal_data']['deal_subcat1_name'])=="completed"){
		$date_completed = ymd_to_dmy($g_view['deal_data']['date_of_deal']);
	}
}
?>
<?php
/******************************************
sng:25/oct/2011
300px is causing the right box to slide down
*****************************************/
?>
<div style="width:240px; float:left;">
<h1>Key Dates:</h1>
<p>
Rumoured: <?php if($g_view['deal_data']['date_rumour']=="0000-00-00"||$g_view['deal_data']['date_rumour']=="") echo "n/a"; else echo ymd_to_dmy($g_view['deal_data']['date_rumour']);?><br />
Announced: <?php 
	if($date_announced != ""){
		echo $date_announced;
	}elseif($g_view['deal_data']['date_announced']=="0000-00-00"||$g_view['deal_data']['date_announced']==""){
		echo "n/a";
	}else{
		echo ymd_to_dmy($g_view['deal_data']['date_announced']);
	}?><br />
Completed: <?php
	if($date_completed!=""){
		echo $date_completed;
	}elseif($g_view['deal_data']['date_closed']=="0000-00-00"||$g_view['deal_data']['date_closed']==""){
		echo "n/a";
	}else{
		echo ymd_to_dmy($g_view['deal_data']['date_closed']);
	}?>
</p>
<h1>Transaction Highlights:</h1>
<p>
Percentage acquired: <?php if($g_view['deal_data']['acquisition_percentage']==0) echo "n/a"; else echo $g_view['deal_data']['acquisition_percentage']." %";?><br />
Deal value: <?php echo convert_deal_value_for_display_round($g_view['deal_data']['value_in_billion'],$g_view['deal_data']['value_range_id'],$g_view['deal_data']['fuzzy_value']);?><br />
</p>
<?php
/***********
if the deal is in usd, we do not need this
if currency is empty, the deal is in usd
if currency is USD, the deal is in usd
*******/
if(($g_view['deal_data']['currency']!="")&&(strtoupper($g_view['deal_data']['currency'])!="USD")){
	?>
<p>
Local currency: <?php echo $g_view['deal_data']['currency'];?><br />
FX rate: <?php if($g_view['deal_data']['exchange_rate']!=0) echo $g_view['deal_data']['exchange_rate'];else echo "n/a";?><br />
Deal value: <?php if($g_view['deal_data']['value_in_billion_local_currency']!=0) echo convert_billion_to_million_for_display($g_view['deal_data']['value_in_billion_local_currency'])."m"; else echo "n/a";?>
</p>
	<?php
}
?>
<p>
<?php
if($g_view['deal_data']['takeover_name']!=""){
	echo $g_view['deal_data']['takeover_name'];?> transaction<br />
	<?php
}
?>
<?php
if($g_view['deal_data']['payment_type']!=""){
	/*********************************************
	sng:25/oct/2011
	***************************************/
	if($g_view['deal_data']['payment_type']=="cash") echo "All cash transaction";
	elseif($g_view['deal_data']['payment_type']=="equity") echo "All equity transaction";
	elseif($g_view['deal_data']['payment_type']=="part_cash_part_quity") echo "Part cash / part equity transaction";
	/********************
	sng:25/oct/2011
	***********************/
	if(strtolower($g_view['deal_data']['payment_type'])=="part_cash_part_quity"){
		if($g_view['deal_data']['equity_payment_percent']!=""&&$g_view['deal_data']['equity_payment_percent']!=0){
			echo "<br />Equity: ".$g_view['deal_data']['equity_payment_percent']."%";
		}
	}
	?><br /><?php
}
?>
<?php
if($g_view['deal_data']['target_listed_in_stock_exchange']=='y'){
	?>
	Target is publicly listed <?php if($g_view['deal_data']['target_stock_exchange_name']!=""){?>in <?php echo $g_view['deal_data']['target_stock_exchange_name'];?><?php }?>
	<?php
}else{
	//no need to show anything
}
?>
</p>
</div>

<div style="width:280px; float:right; margin-left:10px;">

<?php require_once("deal_overview_companies.php");?>
</div>