<?php
/***************
sng:10/oct/2012

We fetch the original submissions and suggestions/alterations
****************/
require_once("../../../include/global.php");
require_once("classes/class.account.php");
if(!$g_account->is_admin_logged()){
	echo "You need to login first";
	exit;
}
require_once("classes/class.transaction_support.php");
require_once("classes/class.transaction_suggestion.php");
require_once("classes/class.deal_support.php");
require_once("classes/class.transaction_partner.php");

$trans_support = new transaction_support();
$trans_suggestion = new transaction_suggestion();
$deal_support = new deal_support();
$trans_partner = new transaction_partner();

$g_view['deal_id'] = $_GET['deal_id'];
$g_view['partner_type'] = $_GET['partner_type'];
?>
<table cellpadding="5" cellspacing="0">
<tr>
<td style="vertical-align:top;">
<?php
/***********************************************
advisor original suggestion
**********/
$original_data_arr = NULL;
$original_data_count = 0;
$ok = $trans_suggestion->fetch_partners_with_grouping($g_view['deal_id'],$g_view['partner_type'],true,$original_data_arr,$original_data_count);
if(!$ok){
	?>Error fetching the original submission<?php
	exit;
}
/***********************************************/
?>
<div><strong>Original Submission</strong></div>
<div class="hr_div"></div>
<?php
if(0==$original_data_count){
	?>
	<div>N/A</div>
	<div class="hr_div"></div>
	<?php
}else{
	for($i=0;$i<$original_data_count;$i++){
		?>
		<div>
		<?php
		$cnt = $original_data_arr[$i]['suggested_firms_count'];
		for($j=0;$j<$cnt;$j++){
			$partner_name = $original_data_arr[$i]['suggested_firms'][$j]['partner_name'];
			$partner_role = $original_data_arr[$i]['suggested_firms'][$j]['role_name'];
			/******************
			sng:6/oct/2012
			We need the status note. When admin delete a record, that is also added as suggestion with status note [deleted by admin]
			Without showing the status note, members won't understand whether the entry was added or deleted
			*******************/
			$status_note = $original_data_arr[$i]['suggested_firms'][$j]['status_note'];
			?><div style="padding:5px 0px 5px 0px;">
			<?php echo $partner_name;?><br />
			<?php if($partner_role != ""){echo $partner_role;}else{echo "N/A";}?>
			<?php if($status_note!=""){echo "<br />".$status_note;}?>
			</div>
			<?php
		}
		?>
		</div>
		<div style="text-align:right;margin-top:10px;"><?php echo $original_data_arr[$i]['suggested_by'];?> on <?php echo $original_data_arr[$i]['suggested_on'];?></div>
		<div class="hr_div"></div>
		<?php
	}
}
/***********************************************
advisor alterations/suggestions
**********/
$suggestion_data_arr = NULL;
$suggestion_data_count = 0;
$ok = $trans_suggestion->fetch_partners_with_grouping($g_view['deal_id'],$g_view['partner_type'],false,$suggestion_data_arr,$suggestion_data_count);
if(!$ok){
	?>Error fetching the suggestions<?php
	exit;
}
/***********************************************/
?>
<div><strong>Additions/Suggestions</strong></div>
<div class="hr_div"></div>
<?php
if(0==$suggestion_data_count){
	?>
	<div>N/A</div>
	<div class="hr_div"></div>
	<?php
}else{
	for($i=0;$i<$suggestion_data_count;$i++){
		?>
		<div>
		<?php
		$cnt = $suggestion_data_arr[$i]['suggested_firms_count'];
		for($j=0;$j<$cnt;$j++){
			$partner_name = $suggestion_data_arr[$i]['suggested_firms'][$j]['partner_name'];
			$partner_role = $suggestion_data_arr[$i]['suggested_firms'][$j]['role_name'];
			/******************
			sng:6/oct/2012
			We need the status note. When admin delete a record, that is also added as suggestion with status note [deleted by admin]
			Without showing the status note, members won't understand whether the entry was added or deleted
			*******************/
			$status_note = $suggestion_data_arr[$i]['suggested_firms'][$j]['status_note'];
			?><div style="padding:5px 0px 5px 0px;">
			<?php echo $partner_name;?><br />
			<?php if($partner_role != ""){echo $partner_role;}else{echo "N/A";}?>
			<?php if($status_note!=""){echo "<br />".$status_note;}?>
			</div>
			<?php
		}
		?>
		</div>
		<div style="text-align:right;margin-top:10px;"><?php echo $suggestion_data_arr[$i]['suggested_by'];?> on <?php echo $suggestion_data_arr[$i]['suggested_on'];?></div>
		<div class="hr_div"></div>
		<?php
	}
}
?>
</td>
<td style="width:50px;">&nbsp;</td>
<td style="vertical-align:top;">
<?php
/****************************
Fetch the current records. For now, we are not showing the suggestions specific to a bank / law firm. We hope that the left
column will suffice

We need to fetch the role list if we are to allow admin to edit the roles. For that, we need the type of the deal
**************/
$g_view['deal_type_data'] = NULL;
$ok = $trans_support->get_deal_type($g_view['deal_id'],$g_view['deal_type_data']);
if(!$ok){
	?>Error fetching the deal type<?php
	exit;
}
$g_view['deal_type'] = $g_view['deal_type_data']['deal_cat_name'];

$g_view['roles'] = NULL;
$g_view['role_count'] = 0;
$ok = $deal_support->front_get_deal_partner_roles($g_view['partner_type'],$g_view['deal_type'],$g_view['roles'],$g_view['role_count']);
if(!$ok){
	?>Error fetching the role list<?php
	exit;
}

$curr_data_arr = NULL;
$curr_data_count = 0;
$ok = $trans_partner->get_all_partners_data_by_type($g_view['deal_id'],$g_view['partner_type'],$curr_data_arr,$curr_data_count);
if(!$ok){
	?>Error fetching current parners<?php
	exit;
}
?>
<div><strong>Current</strong></div>
<div class="hr_div"></div>
<table cellpadding="0" cellspacing="0" class="grey_table" style="width:100%;">
<tr>
<th>Name</th>
<th>Role</th>
<th>Flags</th>
<th>&nbsp;</th>
</tr>
<?php
if(0==$curr_data_count){
	?><tr><td colspan="4">None</td></tr><?php
}else{
	for($j=0;$j<$curr_data_count;$j++){
		?>
		<tr>
		<td>
		<?php
		/************
		merely using idXXX is causing conflict.
		found out by using console.log() and passing a hardcoded value id1
		************/
		?>
		<input type="hidden" id="trans_partner_record_id<?php echo $j;?>" value="<?php echo $curr_data_arr[$j]['id'];?>" />
		<?php echo $curr_data_arr[$j]['company_name'];?>
		</td>
		<td>
		<select id="trans_partner_role_id<?php echo $j;?>">
		<option value="" <?php if(0==$curr_data_arr[$j]['role_id']){?>selected="selected"<?php }?>>select role</option>
		<?php
		for($k=0;$k<$g_view['role_count'];$k++){
			?>
			<option value="<?php echo $g_view['roles'][$k]['role_id'];?>" <?php if($g_view['roles'][$k]['role_id']==$curr_data_arr[$j]['role_id']){?>selected="selected"<?php }?>><?php echo $g_view['roles'][$k]['role_name'];?></option>
			<?php
		}
		?>
		</select>
		</td>
		<td>None</td>
		<td><input type="button" value="Update" onclick="update_partner(<?php echo $j;?>,'<?php echo $g_view['partner_type'];?>')" /></td>
		</tr>
		<?php
	}
}
?>
</table>

</td>
</tr>
</table>