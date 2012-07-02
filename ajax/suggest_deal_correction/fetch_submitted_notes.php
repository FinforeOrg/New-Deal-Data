<?php
/*************************
sng:20/mar/2012
We use ajax to fetch the existing notes suggested for a deal
****************************/
require_once("../../include/global.php");
require_once("classes/class.transaction_suggestion.php");
$trans_suggestion = new transaction_suggestion();

$deal_id = $_GET['deal_id'];

$data_arr = NULL;
$data_count = 0;

/**********
sng:30/apr/2012
We get the corrections, not originals
***********/
$ok = $trans_suggestion->fetch_notes($deal_id,false,$data_arr,$data_count);
if(!$ok){
	?>Error<?php
	return;
}
if(0==$data_count){
	?>None yet<?php
	return;
}
for($i=0;$i<$data_count;$i++){
	$work_email = $data_arr[$i]['work_email'];
	$tokens = explode('@',$work_email);
	$work_email_suffix = $tokens[1];
?>
<div><?php echo nl2br($data_arr[$i]['note']);?></div>
<div style="text-align:right;margin-top:10px;"><?php echo $data_arr[$i]['member_type'];?> @<?php echo $work_email_suffix;?> on <?php echo date('jS M Y',strtotime($data_arr[$i]['date_suggested']));?></div>
<div class="hr_div"></div>
<?php
}
?>