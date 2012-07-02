<?php
/*****************
sng:23/may/2012
*************/
require_once("../../include/global.php");
$g_view['company_id'] = $_GET['company_id'];

require_once("classes/class.company_suggestion.php");
$comp_suggestion = new company_suggestion();

require_once("classes/class.company.php");
$comp = new company();

require_once("classes/class.country.php");
require_once("classes/class.account.php");
/*******************************
fetch countries
****/
$g_view['country_list'] = array();
$g_view['country_count'] = 0;
$success = $g_country->get_all_country_list($g_view['country_list'],$g_view['country_count']);
if(!$success){
	die("Cannot get country list");
}
/****************************************************************
fetch sector names
**************/
$g_view['sector_list'] = array();
$g_view['sector_count'] = 0;
$success = $g_company->get_all_sector_list($g_view['sector_list'],$g_view['sector_count']);
if(!$success){
	die("Cannot get sector list");
}
/****************
get the original submission data
****************/
$g_view['original_data'] = NULL;
$g_view['original_data_count'] = 0;
$ok = $comp_suggestion->fetch_suggestions_for_company($g_view['company_id'],true,$g_view['original_data'],$g_view['original_data_count']);
if(!$ok){
	echo "Cannot get the original submission data";
	return;
}


/***************
get the corrective suggestions
****************/
$g_view['suggestions_data'] = NULL;
$g_view['suggestions_data_count'] = 0;
$ok = $comp_suggestion->fetch_suggestions_for_company($g_view['company_id'],false,$g_view['suggestions_data'],$g_view['suggestions_data_count']);
if(!$ok){
	echo "Cannot get the suggestion data";
	return;
}

/******************************
get the current data
****************************/
$g_view['company_data'] = NULL;
$ok = $comp->get_company($g_view['company_id'],$g_view['company_data']);
if(!$ok){
	echo "Cannot get the current data";
}

/******************
Calculate the total num of columns we need
We need 1 col to show the original submission, 1 col to show the edit field, 1 col to show current data.
Then we have suggestions. If no suggestions then take one else that many num of columns. Finally, one col to
show the labels
******************/
$suggestion_colspan = max(1,$g_view['suggestions_data_count']);
$num_cols = 3 + $suggestion_colspan;
$column_width = 220;
$label_column_width = 100;
$table_width = ($column_width*$num_cols)+$label_column_width;
?>

<form id="company_data_frm" method="post" action="" onsubmit="return submit_company_correction();">
<input type="hidden" name="company_id" value="<?php echo $g_view['company_id'];?>" />
<table style="width:<?php echo $table_width;?>px;" cellpadding="0" cellspacing="0" border="0">
<!--/////////////////////headings////////////////////////////////-->
<tr>
<td class="deal-edit-snippet-header" style="min-width:<?php echo $label_column_width;?>px;"></td>
<td class="deal-edit-snippet-header" style="min-width:<?php echo $column_width;?>px;">Original Submission:</td>
<td class="deal-edit-snippet-header" style="min-width:<?php echo $column_width;?>px;" colspan="<?php echo $suggestion_colspan;?>">Edits / Additions:</td>
<td class="deal-edit-snippet-header" style="min-width:<?php echo $column_width;?>px;">Current</td>
<td class="deal-edit-snippet-header" style="min-width:<?php echo $column_width;?>px;">Your Suggestion</td>
</tr>
<!--/////////////////////headings////////////////////////////////-->
<!--////////////////////name//////////////////////////////////-->
<tr>
<td class="deal-edit-snippet-mid-col" style="border:0">Name</td>
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
<strong><?php echo $g_view['company_data']['name'];?></strong>
</td>
<td class="deal-edit-snippet-mid-col">
<input type="text" name="name" class="deal-edit-snippet-textbox" /><br />
Suggest Revised Name
</td>
</tr>
<!--////////////////////name//////////////////////////////////-->
<!--////////////////////logo//////////////////////////////////-->
<tr>
<td class="deal-edit-snippet-mid-col" style="border:0">Logo</td>
<td class="deal-edit-snippet-mid-col" style="border:0">
<?php
if($g_view['original_data_count']!=0){
	if($g_view['original_data'][0]['logo']!=""){
		?><img src="<?php echo LOGO_IMG_URL;?>/<?php echo $g_view['original_data'][0]['logo'];?>" /><?php
	}else{
		?>Logo not uploaded<?php
	}
}else{
	?>None available<?php
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
if($g_view['company_data']['logo']!=""){
	?><img src="<?php echo LOGO_IMG_URL;?>/<?php echo $g_view['company_data']['logo'];?>" /><?php
}else{
	?>Logo not uploaded<?php
}
?>
</td>
<td class="deal-edit-snippet-mid-col">
<input type="file" name="logo" class="deal-edit-snippet-textbox" /><br />
Suggest a logo
</td>
</tr>
<!--////////////////////logo//////////////////////////////////-->
<!--////////////////////country of HQ//////////////////////////////////-->
<tr>
<td class="deal-edit-snippet-mid-col" style="border:0">HQ</td>
<td class="deal-edit-snippet-mid-col" style="border:0">
<?php
if(0 == $g_view['original_data_count']){
	?>None available<?php
}else{
	?><strong><?php echo $g_view['original_data'][0]['hq_country'];?></strong><?php
}
?>
</td>
<?php
if(0 == $g_view['suggestions_data_count']){
	?><td class="deal-edit-snippet-mid-col">None available</td><?php
}else{
	for($i=0;$i<$g_view['suggestions_data_count'];$i++){
		?><td class="deal-edit-snippet-mid-col"><strong><?php echo $g_view['suggestions_data'][$i]['hq_country'];?></strong></td><?php
	}
}
?>
<td class="deal-edit-snippet-mid-col">
<strong><?php echo $g_view['company_data']['hq_country'];?></strong>
</td>
<td class="deal-edit-snippet-mid-col">
<select name="hq_country" class="deal-edit-snippet-dropdown">
<option value="">Select</option>
<?php
for($i=0;$i<$g_view['country_count'];$i++){
	?>
	<option value="<?php echo $g_view['country_list'][$i]['name'];?>" ><?php echo $g_view['country_list'][$i]['name'];?></option>
	<?php
}
?>
</select><br />
Suggest Revised Country
</td>
</tr>
<!--////////////////////country of HQ//////////////////////////////////-->
<!--////////////////////sector//////////////////////////////////-->
<tr>
<td class="deal-edit-snippet-mid-col" style="border:0">Sector</td>
<td class="deal-edit-snippet-mid-col" style="border:0">
<?php
if(0 == $g_view['original_data_count']){
	?>None available<?php
}else{
	?><strong><?php echo $g_view['original_data'][0]['sector'];?></strong><?php
}
?>
</td>
<?php
if(0 == $g_view['suggestions_data_count']){
	?><td class="deal-edit-snippet-mid-col">None available</td><?php
}else{
	for($i=0;$i<$g_view['suggestions_data_count'];$i++){
		?><td class="deal-edit-snippet-mid-col"><strong><?php echo $g_view['suggestions_data'][$i]['sector'];?></strong></td><?php
	}
}
?>
<td class="deal-edit-snippet-mid-col">
<strong><?php echo $g_view['company_data']['sector'];?></strong>
</td>
<td class="deal-edit-snippet-mid-col">
<select name="sector" id="sector" onChange="return company_sector_changed();" class="deal-edit-snippet-dropdown">
<option value="">Select</option>
<?php
for($i=0;$i<$g_view['sector_count'];$i++){
	?>
	<option value="<?php echo $g_view['sector_list'][$i]['sector'];?>"><?php echo $g_view['sector_list'][$i]['sector'];?></option>
	<?php
}
?>
</select><br />
Suggest Revised Sector
</td>
</tr>
<!--////////////////////sector//////////////////////////////////-->
<!--////////////////////industry//////////////////////////////////-->
<tr>
<td class="deal-edit-snippet-mid-col" style="border:0">Industry</td>
<td class="deal-edit-snippet-mid-col" style="border:0">
<?php
if(0 == $g_view['original_data_count']){
	?>None available<?php
}else{
	?><strong><?php echo $g_view['original_data'][0]['industry'];?></strong><?php
}
?>
</td>
<?php
if(0 == $g_view['suggestions_data_count']){
	?><td class="deal-edit-snippet-mid-col">None available</td><?php
}else{
	for($i=0;$i<$g_view['suggestions_data_count'];$i++){
		?><td class="deal-edit-snippet-mid-col"><strong><?php echo $g_view['suggestions_data'][$i]['industry'];?></strong></td><?php
	}
}
?>
<td class="deal-edit-snippet-mid-col">
<strong><?php echo $g_view['company_data']['industry'];?></strong>
</td>
<td class="deal-edit-snippet-mid-col">
<select name="industry" id="industry" class="deal-edit-snippet-dropdown">
<option value=""> Select industry </option>
</select><br />
Suggest Revised Industry
</td>
</tr>
<!--////////////////////industry//////////////////////////////////-->
<!--////////////////////footer//////////////////////////////////-->
<tr>
<td class="deal-edit-snippet-mid-col" style="border:0"></td>
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
<div id="company_data_frm_msg" class="msg_txt"></div>
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