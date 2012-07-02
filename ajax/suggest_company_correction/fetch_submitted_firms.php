<?php
/***********************
sng:11/may/2012

fetch the original suggestion and the corrective suggestion for a particular bank or law firm
*********************/
require_once("../../include/global.php");
$g_view['firm_id'] = $_GET['firm_id'];

require_once("classes/class.company_suggestion.php");
require_once("classes/class.company.php");
require_once("classes/class.account.php");
$comp_suggestion = new company_suggestion();
$comp = new company();

/****************
get the original submission data
****************/
$g_view['original_data'] = NULL;
$g_view['original_data_count'] = 0;
$ok = $comp_suggestion->fetch_firms($g_view['firm_id'],true,$g_view['original_data'],$g_view['original_data_count']);
if(!$ok){
	echo "Cannot get the original submission data";
	return;
}

/***************
get the corrective suggestions
****************/
$g_view['suggestions_data'] = NULL;
$g_view['suggestions_data_count'] = 0;
$ok = $comp_suggestion->fetch_firms($g_view['firm_id'],false,$g_view['suggestions_data'],$g_view['suggestions_data_count']);
if(!$ok){
	echo "Cannot get the suggestion data";
	return;
}

/******************************
get the current data
****************************/
$g_view['firm_data'] = NULL;
$ok = $comp->get_company($g_view['firm_id'],$g_view['firm_data']);
if(!$ok){
	echo "Cannot get the current data";
}
/******************
Calculate the total num of columns we need
We need 1 col to show the original submission, 1 col to show the edit field, 1 col to show current data.
Then we have suggestions. If no suggestions then take one else that many num of columns.
******************/
$suggestion_colspan = max(1,$g_view['suggestions_data_count']);
$num_cols = 3 + $suggestion_colspan;
$column_width = 220;
$table_width = $column_width*$num_cols;
?>
<script src="js/jquery.form.js"></script>
<style type="text/css">
.hr_div
{
	height:10px;
	margin-top:20px;
	border-top:1px solid #CCCCCC;
}
</style>
<form id="firm_data_frm" method="post" action="" onsubmit="return submit_firm_correction();">
<input type="hidden" name="firm_id" value="<?php echo $g_view['firm_id'];?>" />
<input type="hidden" name="type" value="<?php echo $g_view['firm_data']['type'];?>" />
<table style="width:<?php echo $table_width;?>px;" cellpadding="0" cellspacing="0" border="0">
<!--/////////////////////headings////////////////////////////////-->
<tr>
<td class="deal-edit-snippet-header" style="min-width:<?php echo $column_width;?>px;">Original Submission:</td>
<td class="deal-edit-snippet-header" style="min-width:<?php echo $column_width;?>px;" colspan="<?php echo $suggestion_colspan;?>">Edits / Additions:</td>
<td class="deal-edit-snippet-header" style="min-width:<?php echo $column_width;?>px;">Current</td>
<td class="deal-edit-snippet-header" style="min-width:<?php echo $column_width;?>px;">Your Suggestion</td>
</tr>

<!--/////////////////////headings////////////////////////////////-->
<!--////////////////////name//////////////////////////////////-->
<tr>
<td class="deal-edit-snippet-mid-col" style="border:0">
<?php
if(0 == $g_view['original_data_count']){
	?>None available<?php
}else{
	?><strong><?php echo $g_view['original_data'][0]['name'];?></strong><?php
}
?>
</td>

<?php
if(0 == $g_view['suggestions_data_count']){
	?><td class="deal-edit-snippet-mid-col">None available</td><?php
}else{
	for($i=0;$i<$g_view['suggestions_data_count'];$i++){
		?><td class="deal-edit-snippet-mid-col"><strong><?php echo $g_view['suggestions_data'][$i]['name'];?></strong></td><?php
	}
}
?>
<td class="deal-edit-snippet-mid-col">
<strong><?php echo $g_view['firm_data']['name'];?></strong>
</td>

<td class="deal-edit-snippet-mid-col">
Suggest Revised Name<br />
<input type="text" name="name" class="deal-edit-snippet-textbox" />
</td>

</tr>
<!--////////////////////name//////////////////////////////////-->
<!--////////////////////logo//////////////////////////////////-->
<tr>
<td class="deal-edit-snippet-mid-col" style="border:0">
<?php
if($g_view['original_data_count']!=0){
	if($g_view['original_data'][0]['logo']!=""){
		?><img src="<?php echo LOGO_IMG_URL;?>/<?php echo $g_view['original_data'][0]['logo'];?>" /><?php
	}else{
		?>Logo not uploaded<?php
	}
}
?>
</td>
<?php
if(0 == $g_view['suggestions_data_count']){
	?><td class="deal-edit-snippet-mid-col"></td><?php
}else{
	for($i=0;$i<$g_view['suggestions_data_count'];$i++){
		?>
		<td class="deal-edit-snippet-mid-col">
		<?php
		if($g_view['suggestions_data'][$i]['logo']!=""){
			?><img src="<?php echo LOGO_IMG_URL;?>/<?php echo $g_view['suggestions_data'][$i]['logo'];?>" /><?php
		}
		?>
		</td>
		<?php
	}
}
?>
<td class="deal-edit-snippet-mid-col">
<?php
if($g_view['firm_data']['logo']!=""){
	?><img src="<?php echo LOGO_IMG_URL;?>/<?php echo $g_view['firm_data']['logo'];?>" /><?php
}else{
	?>Logo not uploaded<?php
}
?>
</td>
<td class="deal-edit-snippet-mid-col">
Suggest a logo<br />
<input type="file" name="logo" class="deal-edit-snippet-textbox" />
</td>
</tr>
<!--////////////////////logo//////////////////////////////////-->
<!--////////////////////footer//////////////////////////////////-->
<tr>
<td class="deal-edit-snippet-mid-col" style="border:0">
<?php
if($g_view['original_data_count']!=0){
	$date_suggested = $g_view['original_data'][0]['date_suggested'];
	if($date_suggested == "0000-00-00 00:00:00"){
		$display_date_suggested = "N/A";
	}else{
		$display_date_suggested = date('jS M Y',strtotime($date_suggested));
	}
	
	if($g_view['original_data'][0]['suggested_by']!=0){
		$submitter_work_email_tokens = explode('@', $g_view['original_data'][0]['work_email']);
		$submitter_work_email_suffix = $submitter_work_email_tokens[1];
		$submitter =  $g_view['original_data'][0]['member_type']." @".$submitter_work_email_suffix;
	}else{
		$submitter = "Admin";
	}
	?>
	<div class="hr_div"></div>
	<div class="deal-edit-snippet-footer">Submitted <?php echo $display_date_suggested;?></div>
	<div class="deal-edit-snippet-footer"><?php echo $submitter;?></div>
	<?php
}
?>
</td>
<?php
if(0 == $g_view['suggestions_data_count']){
	?><td class="deal-edit-snippet-mid-col"></td><?php
}else{
	for($i=0;$i<$g_view['suggestions_data_count'];$i++){
		$date_suggested = $g_view['suggestions_data'][$i]['date_suggested'];
		if($date_suggested == "0000-00-00 00:00:00"){
			$display_date_suggested = "N/A";
		}else{
			$display_date_suggested = date('jS M Y',strtotime($date_suggested));
		}
		
		if($g_view['suggestions_data'][$i]['suggested_by']!=0){
			$submitter_work_email_tokens = explode('@', $g_view['suggestions_data'][$i]['work_email']);
			$submitter_work_email_suffix = $submitter_work_email_tokens[1];
			$submitter =  $g_view['suggestions_data'][$i]['member_type']." @".$submitter_work_email_suffix;
		}else{
			$submitter = "Admin";
		}
		?>
		<td class="deal-edit-snippet-mid-col">
		<div class="hr_div"></div>
		<div class="deal-edit-snippet-footer">Submitted <?php echo $display_date_suggested;?></div>
		<div class="deal-edit-snippet-footer"><?php echo $submitter;?></div>
		</td>
		<?php
	}
}
?>
<?php
/**************
No need to show any submitter for the current data because
other columns will show who submitted it
*******************/
?>
<td class="deal-edit-snippet-mid-col"></td>
<td class="deal-edit-snippet-mid-col">
<div class="hr_div"></div>
<div id="firm_data_frm_msg" class="msg_txt"></div>
<div class="deal-edit-snippet-footer">
<?php
if($g_account->is_site_member_logged()){
	?><input type="submit" class="btn_auto" value="Submit"  /><?php
}else{
	?><input type="button" class="btn_auto" value="Submit" onclick="show_non_login_alert();" /><?php
}
?>

</div>
</td>
</tr>
<!--////////////////////footer//////////////////////////////////-->
</table>
</form>
<script>
$('.btn_auto').button();

</script>
<script>

function validate_suggestion_posting(formData, jqForm, options) {
	jQuery("#firm_data_frm_msg").html("submitting...");
	return true;
}
function suggestion_posted(data, statusText)  {
	if (statusText == 'success') {
		if (data.success == 'y') {
			jQuery("#firm_data_frm_msg").html(data.msg);
		} else {
			jQuery("#firm_data_frm_msg").html(data.msg);
		}
	} else {
		jQuery("#firm_data_frm_msg").html('Unknown error!');
	}
}
</script>
<script>
function show_non_login_alert(){
	apprise("Please log-in before you submit correction, many thanks.",{'textOk':'OK'});
}

function submit_firm_correction(){
	var suggestion_options = {
		beforeSubmit:  validate_suggestion_posting,
		success:       suggestion_posted,
		url:       'ajax/suggest_company_correction/firm.php',  // your upload script
		dataType:  'json'
	};
	jQuery("#firm_data_frm").ajaxSubmit(suggestion_options);
	return false;
}
</script>