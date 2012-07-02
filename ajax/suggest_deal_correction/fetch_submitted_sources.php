<?php
/*************************
sng:20/mar/2012
We use ajax to fetch the existing sources suggested for a deal

sng:2/may/2012
Since we are fetching the later suggestions and not the original submission. we send false.
also, we use a different function that provides grouping.
****************************/
require_once("../../include/global.php");
require_once("classes/class.transaction_suggestion.php");
$trans_suggestion = new transaction_suggestion();

$deal_id = $_GET['deal_id'];

$group_data_arr = NULL;
$group_data_count = 0;

$ok = $trans_suggestion->fetch_sources_with_grouping($deal_id,false,$group_data_arr,$group_data_count);
if(!$ok){
	?>Error<?php
	return;
}
if(0==$group_data_count){
	?>None yet<?php
	return;
}
for($i=0;$i<$group_data_count;$i++){
	?>
	<div>
	<?php 
	$cnt = $group_data_arr[$i]['suggested_sources_count'];
	for($j=0;$j<$cnt;$j++){
		$source = $group_data_arr[$i]['suggested_sources'][$j]['source_url'];
		?><div style="padding:5px 0px 5px 0px;"><a href="<?php echo $source;?>" target="_blank"><?php echo $source;?></a></div><?php
	}
	?>
	</div>
	<div style="text-align:right;margin-top:10px;"><?php echo $group_data_arr[$i]['suggested_by'];?> on <?php echo $group_data_arr[$i]['date_suggested'];?></div>
	<div class="hr_div"></div>
	<?php
}
?>