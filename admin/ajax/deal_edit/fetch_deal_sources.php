<?php
/*****************
sng:3/oct/2012

sng:6/oct/2012
****************/
require_once("../../../include/global.php");
require_once("classes/class.account.php");
if(!$g_account->is_admin_logged()){
	echo "You need to login first";
	exit;
}

require_once("classes/class.transaction_source.php");
require_once("classes/class.transaction_suggestion.php");

$trans_source = new transaction_source();
$trans_suggestion = new transaction_suggestion();

$g_view['deal_id'] = $_GET['deal_id'];
?>
<table cellpadding="5" cellspacing="0">
<tr>
<td style="vertical-align:top;">
<?php
/*****************************************
source original submission
*************/
$group_data_arr = NULL;
$group_data_count = 0;
$ok = $trans_suggestion->fetch_sources_with_grouping($g_view['deal_id'],true,$group_data_arr,$group_data_count);
if(!$ok){
	?>Error fetching the original submission<?php
	return;
}
?>
<div><strong>Original Submission</strong></div>
<div class="hr_div"></div>
<?php
if(0==$group_data_count){
	?>
	<div>N/A</div>
	<div class="hr_div"></div>
	<?php
}else{
	for($i=0;$i<$group_data_count;$i++){
		?>
		<div>
		<?php
		$cnt = $group_data_arr[$i]['suggested_sources_count'];
		for($j=0;$j<$cnt;$j++){
			$source = $group_data_arr[$i]['suggested_sources'][$j]['source_url'];
			/******************
			sng:6/oct/2012
			We need the status note. When admin delete a record, that is also added as suggestion with status note [deleted by admin]
			Without showing the status note, members won't understand whether the entry was added or deleted
			*******************/
			$status_note = $group_data_arr[$i]['suggested_sources'][$j]['status_note'];
			?><div style="padding:5px 0px 5px 0px;"><a href="<?php echo $source;?>" target="_blank"><?php echo $source;?></a><?php if($status_note!=""){echo " [".$status_note."]";}?></div><?php
		}
		?>
		</div>
		<div style="text-align:right;margin-top:10px;"><?php echo $group_data_arr[$i]['suggested_by'];?> on <?php echo $group_data_arr[$i]['suggested_on'];?></div>
		<div class="hr_div"></div>
		<?php
	}
}
/*****************************************
source additional submissions
*************/
$group_data_arr = NULL;
$group_data_count = 0;
$ok = $trans_suggestion->fetch_sources_with_grouping($g_view['deal_id'],false,$group_data_arr,$group_data_count);
if(!$ok){
	?>Error fetching the original submission<?php
	return;
}
?>
<div><strong>Additions</strong></div>
<div class="hr_div"></div>
<?php
if(0==$group_data_count){
	?>
	<div>N/A</div>
	<div class="hr_div"></div>
	<?php
}else{
	for($i=0;$i<$group_data_count;$i++){
		?>
		<div>
		<?php
		$cnt = $group_data_arr[$i]['suggested_sources_count'];
		for($j=0;$j<$cnt;$j++){
			$source = $group_data_arr[$i]['suggested_sources'][$j]['source_url'];
			/******************
			sng:6/oct/2012
			We need the status note. When admin delete a record, that is also added as suggestion with status note [deleted by admin]
			Without showing the status note, members won't understand whether the entry was added or deleted
			*******************/
			$status_note = $group_data_arr[$i]['suggested_sources'][$j]['status_note'];
			?><div style="padding:5px 0px 5px 0px;"><a href="<?php echo $source;?>" target="_blank"><?php echo $source;?></a><?php if($status_note!=""){echo " [".$status_note."]";}?></div><?php
		}
		?>
		</div>
		<div style="text-align:right;margin-top:10px;"><?php echo $group_data_arr[$i]['suggested_by'];?> on <?php echo $group_data_arr[$i]['suggested_on'];?></div>
		<div class="hr_div"></div>
		<?php
	}
}
?>
</td>
<td style="width:50px;">&nbsp;</td>
<td style="vertical-align:top;">
<div><strong>Current</strong></div>
<div class="hr_div"></div>
<?php
/**********************
current entries
***********/
$g_view['sources'] = NULL;
$g_view['sources_count'] = 0;
$ok = $trans_source->get_deal_sources($g_view['deal_id'],$g_view['sources']);
if(!$ok){
	?><div class="err_txt">error fetching sources</div><?php
}else{
	?>
	<div>
	<?php
	/**************
	sng:6/oct/2012
	We want admin to select multiple entries and delete in one go.
	So we use checkbox and pass deal_id via hidden field and use a form to submit all data
	***************/
	?>
	<form id="frm_delete_source">
	<input type="hidden" name="deal_id" value="<?php echo $g_view['deal_id'];?>" />
	<table cellpadding="10" cellspacing="0" border="1" style="border-collapse:collapse;">
	<?php
	$g_view['sources_count'] = count($g_view['sources']);
	if(0 == $g_view['sources_count']){
		?>
		<tr><td colspan="2"><div class="msg_txt">None specified</div></td></tr>
		<?php
	}else{
		for($j=0;$j<$g_view['sources_count'];$j++){
			$source = $g_view['sources'][$j]['source_url'];
			?>
			<tr>
			<td><a href="<?php echo $source;?>" target="_blank"><?php echo $source;?></a></td>
			<td><input name="source_id[]" type="checkbox" value="<?php echo $g_view['sources'][$j]['id'];?>" /></td>
			</tr>
			<?php
		}
	}
	?>
	</table>
	</form>
	</div>
	<div style="float: left;"><input type="button" value="Delete" onClick="submit_frm_delete_source();" /></div>
	<div style="clear:both"></div>
	<?php
}
?>
<div class="hr_div"></div>
<?php
/********************
new addition by admin
************/
?>
<div><strong>Add Source URLs</strong></div>
<div class="hr_div"></div>

<form id="frm_edit_source">
<input type="hidden" name="deal_id" value="<?php echo $g_view['deal_id'];?>" />

<div id="source_url_list">
<div><input type="text" name="regulatory_links[]" style="width:200px;" /></div>
<div><input type="text" name="regulatory_links[]" style="width:200px;" /></div>
</div>

<div style="float: left;"><input type="button" onclick="add_source_url_markup()" value="Add more URLs" /></div>
<div style="float: left;"><input type="button" value="Submit" onClick="submit_frm_edit_source();" /></div>

</form>
</td>
</tr>
</table>