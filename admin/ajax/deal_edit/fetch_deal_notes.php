<?php
/******************
sng:5/oct/2012
Here we fetch the original submission as well as the suggestions
*******/
require_once("../../../include/global.php");
require_once("classes/class.account.php");
if(!$g_account->is_admin_logged()){
	echo "You need to login first";
	exit;
}

$g_view['deal_id'] = $_GET['deal_id'];

require_once("classes/class.transaction_suggestion.php");
$trans_suggestion = new transaction_suggestion();

$data_arr = NULL;
$data_count = 0;

$ok = $trans_suggestion->fetch_notes($g_view['deal_id'],true,$data_arr,$data_count);
if(!$ok){
	?>Error fetching the original submission<?php
	return;
}
?>
<div><strong>Original Submission</strong></div>
<div class="hr_div"></div>
<?php
if(0==$data_count){
	?>
	<div>N/A</div>
	<div class="hr_div"></div>
	<?php
}else{
	for($i=0;$i<$data_count;$i++){
		
		
		?>
		<div><?php echo nl2br($data_arr[$i]['note']);?></div>
		<div style="text-align:right;margin-top:10px;">
		<?php
		if($data_arr[$i]['suggested_by']!=0){
			$work_email = $data_arr[$i]['work_email'];
			$tokens = explode('@',$work_email);
			$work_email_suffix = $tokens[1];
			?>
			<?php echo $data_arr[$i]['member_type'];?> @<?php echo $work_email_suffix;?> on <?php echo date('jS M Y',strtotime($data_arr[$i]['date_suggested']));?>
			<?php
		}else{
			?>
			admin on <?php echo date('jS M Y',strtotime($data_arr[$i]['date_suggested']));?>
			<?php
		}
		?>
		</div>
		<div class="hr_div"></div>
		<?php
	}
}
/********************************************************/
$data_arr = NULL;
$data_count = 0;

$ok = $trans_suggestion->fetch_notes($g_view['deal_id'],false,$data_arr,$data_count);
if(!$ok){
	?>Error fetching the additions<?php
	return;
}
?>
<div><strong>Additions</strong></div>
<div class="hr_div"></div>
<?php
if(0==$data_count){
	?>
	<div>N/A</div>
	<div class="hr_div"></div>
	<?php
}else{
	for($i=0;$i<$data_count;$i++){
		
		?>
		<div><?php echo nl2br($data_arr[$i]['note']);?></div>
		<div style="text-align:right;margin-top:10px;">
		<?php
		if($data_arr[$i]['suggested_by']!=0){
			$work_email = $data_arr[$i]['work_email'];
			$tokens = explode('@',$work_email);
			$work_email_suffix = $tokens[1];
			?>
			<?php echo $data_arr[$i]['member_type'];?> @<?php echo $work_email_suffix;?> on <?php echo date('jS M Y',strtotime($data_arr[$i]['date_suggested']));?>
			<?php
		}else{
			?>
			admin on <?php echo date('jS M Y',strtotime($data_arr[$i]['date_suggested']));?>
			<?php
		}
		?>
		</div>
		<div class="hr_div"></div>
		<?php
	}
}
/*********************************************/
?>