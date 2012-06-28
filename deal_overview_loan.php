<?php
/**************
remember that not all deals will have extra data, and even then, the date field may contain the default 0 value

This is Loan deal. If BOTH date_announced AND date_closed are not given BUT date_of_deal is there then:
Completed: is date_of_deal
******************/
$date_announced = "";
$date_completed = "";

if(($g_view['deal_data']['date_announced']=="0000-00-00"||$g_view['deal_data']['date_announced']=="")&&($g_view['deal_data']['date_closed']=="0000-00-00"||$g_view['deal_data']['date_closed']=="")&&$g_view['deal_data']['date_of_deal']!="0000-00-00"){
	$date_completed = ymd_to_dmy($g_view['deal_data']['date_of_deal']);
}
?>
<div style="width:300px; float:left;">
<h1>Key Dates:</h1>
<p>
Announced  / Filed: <?php 
	if($date_announced != ""){
		echo $date_announced;
	}elseif($g_view['deal_data']['date_announced']=="0000-00-00"||$g_view['deal_data']['date_announced']==""){
		echo "n/a";
	}else{
		echo ymd_to_dmy($g_view['deal_data']['date_announced']);
	}?><br />
Closed / Trading: <?php
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
Deal value: <?php echo convert_deal_value_for_display_round($g_view['deal_data']['value_in_billion'],$g_view['deal_data']['value_range_id'],$g_view['deal_data']['fuzzy_value']);?>
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

</div>

<div style="width:280px; float:left; margin-left:10px;">

<?php require_once("deal_overview_companies.php");?>
</div>