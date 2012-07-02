<?php
/**************
sng:18/apr/2012
We now load everything here. This is called in ajax
***************/
require_once("../../include/global.php");
require_once("classes/class.transaction_suggestion.php");
require_once("classes/class.transaction_company.php");

$trans_suggestion = new transaction_suggestion();
$deal_comp = new transaction_company();

$g_view['deal_id'] = $_GET['deal_id'];

/**************************************************
first we need the minimal deal data
********************/
$g_view['deal_found'] = false;
$g_view['deal_data'] = array();
$success = $trans_suggestion->get_deal_detail($g_view['deal_id'],$g_view['deal_data'],$g_view['deal_found']);
if(!$success){
	echo "Cannot get the deal data";
	return;
}

if(!$g_view['deal_found']){
	echo "Deal data not found";
	return;
}
/***************
Next we need the roles for the companies for this deal type
*****************/
$g_view['company_roles'] = NULL;
$g_view['company_role_count'] = 0;
$ok = $deal_comp->get_all_deal_company_roles_for_deal_type($g_view['deal_data']['deal_cat_name'],$g_view['company_roles'],$g_view['company_role_count']);
if(!$ok){
	/****************
	let us not hang the script
	******************/
}
/**********************
Now we get the original submission.
Remember that now when we add a deal from front end
we store the participant company names for the deal in transaction_companies_suggestions along with
the id of the member who submitted the deal and the deal submission time.
Use get_original as true
**********************/
$g_view['original_participants_suggestion_arr'] = NULL;
$g_view['original_participants_suggestion_count'] = 0;

$ok = $trans_suggestion->fetch_participants_with_grouping($g_view['deal_id'],true,$g_view['original_participants_suggestion_arr'],$g_view['original_participants_suggestion_count']);

if(!$ok){
	echo "Cannot fetch the original submission";
	return;
}
/************
It may happen that there is no original submission, that is, no companies were suggested when the deal was added
and so $g_view['original_participants_suggestion_count'] may be 0
********************************************************************/

/********************************
get the suggestions
Use get_original as false
**********/
$g_view['participants_suggestion_arr'] = NULL;
$g_view['participants_suggestion_count'] = 0;

$ok = $trans_suggestion->fetch_participants_with_grouping($g_view['deal_id'],false,$g_view['participants_suggestion_arr'],$g_view['participants_suggestion_count']);

if(!$ok){
	echo "Cannot fetch the suggested participants";
	return;
}
/*******
It may happen that there is no suggestions. in that case $g_view['participants_suggestion_count'] is 0
***********************************************************/

/********************************
get the current participants
**********/
$g_view['curr_participants_grouped_arr'] = NULL;
$g_view['curr_participants_group_count'] = 0;

$ok = $trans_suggestion->get_current_participants_with_grouping($g_view['deal_id'],$g_view['curr_participants_grouped_arr'],$g_view['curr_participants_group_count']);

if(!$ok){
	echo "Cannot fetch the participants associated with the deal";
	return;
}
/*******
It may happen that there is no current participants. in that case $g_view['curr_participants_group_count'] is 0
***********************************************************/

/**************************
we now create the array of unique companies that we have in the suggestions
and we store the row offset where it should be shown in the data grid
***************************/
$g_view['unique_companies_arr'] = array();
$g_view['unique_companies_arr_head'] = -1;


for($i=0;$i<$g_view['original_participants_suggestion_count'];$i++){
	$temp_count = $g_view['original_participants_suggestion_arr'][$i]['suggested_companies_count'];
	for($j=0;$j<$temp_count;$j++){
		$temp_company = $g_view['original_participants_suggestion_arr'][$i]['suggested_companies'][$j]['company_name'];
		if(($temp_company!="")&&(!array_key_exists($temp_company,$g_view['unique_companies_arr']))){
			//enqueue
			$g_view['unique_companies_arr_head']++;
			$g_view['unique_companies_arr'][$temp_company] = $g_view['unique_companies_arr_head'];
		}
	}
}


for($i=0;$i<$g_view['participants_suggestion_count'];$i++){
	$temp_count = $g_view['participants_suggestion_arr'][$i]['suggested_companies_count'];
	for($j=0;$j<$temp_count;$j++){
		$temp_company = $g_view['participants_suggestion_arr'][$i]['suggested_companies'][$j]['company_name'];
		if(($temp_company!="")&&(!array_key_exists($temp_company,$g_view['unique_companies_arr']))){
			//enqueue
			$g_view['unique_companies_arr_head']++;
			$g_view['unique_companies_arr'][$temp_company] = $g_view['unique_companies_arr_head'];
		}
	}
}


for($i=0;$i<$g_view['curr_participants_group_count'];$i++){
	$temp_count = $g_view['curr_participants_grouped_arr'][$i]['suggested_companies_count'];
	for($j=0;$j<$temp_count;$j++){
		$temp_company = $g_view['curr_participants_grouped_arr'][$i]['suggested_companies'][$j]['company_name'];
		if(($temp_company!="")&&(!array_key_exists($temp_company,$g_view['unique_companies_arr']))){
			//enqueue
			$g_view['unique_companies_arr_head']++;
			$g_view['unique_companies_arr'][$temp_company] = $g_view['unique_companies_arr_head'];
		}
	}
}

/****************************************
Now we create the datagrid to hold the columns and rows for original submission, suggestions and current.
Different company data will go to different row, same company will go to same row n column. That is the reason
why we maintained the array $g_view['unique_companies_arr'].

So, how may columns and how many rows?
rows are number of unique companies or 1 if no companies
cols are (1 or num of orginal submission) + (1 or num of suggestions) + (1 or current)
***********************/
$g_view['datagrid'] = array();
$g_view['datagrid_num_rows'] = max(1,count($g_view['unique_companies_arr']));
$g_view['datagrid_num_cols'] = max(1,$g_view['original_participants_suggestion_count']) + max(1,$g_view['participants_suggestion_count']) + max(1,$g_view['curr_participants_group_count']);

/********************
Now we need to populate the grid. Todo that, first we initialize the cells with NULL
*********************/
for($cols=0;$cols<$g_view['datagrid_num_cols'];$cols++){
	$g_view['datagrid'][$cols] = array();
	for($rows=0;$rows<$g_view['datagrid_num_rows'];$rows++){
		$g_view['datagrid'][$cols][$rows] = NULL;
	}
}

/****************
Now we put proper data in proper cell
***************/
$datagrid_col = 0;
if(0 == $g_view['original_participants_suggestion_count']){
	//no original suggestion
	$datagrid_col++;
}else{
	for($i=0;$i<$g_view['original_participants_suggestion_count'];$i++){
		$temp_count = $g_view['original_participants_suggestion_arr'][$i]['suggested_companies_count'];
		for($j=0;$j<$temp_count;$j++){
			$temp_company = $g_view['original_participants_suggestion_arr'][$i]['suggested_companies'][$j];
			$datagrid_row = $g_view['unique_companies_arr'][$temp_company['company_name']];
			$g_view['datagrid'][$datagrid_col][$datagrid_row] = $temp_company;
		}
		$datagrid_col++;
	}
}

if(0 == $g_view['participants_suggestion_count']){
	//no participant suggestion
	$datagrid_col++;
}else{
	for($i=0;$i<$g_view['participants_suggestion_count'];$i++){
		$temp_count = $g_view['participants_suggestion_arr'][$i]['suggested_companies_count'];
		for($j=0;$j<$temp_count;$j++){
			$temp_company = $g_view['participants_suggestion_arr'][$i]['suggested_companies'][$j];
			$datagrid_row = $g_view['unique_companies_arr'][$temp_company['company_name']];
			$g_view['datagrid'][$datagrid_col][$datagrid_row] = $temp_company;
		}
		$datagrid_col++;
	}
}

if(0 == $g_view['curr_participants_group_count']){
	//no current
	$datagrid_col++;
}else{
	for($i=0;$i<$g_view['curr_participants_group_count'];$i++){
		$temp_count = $g_view['curr_participants_grouped_arr'][$i]['suggested_companies_count'];
		for($j=0;$j<$temp_count;$j++){
			$temp_company = $g_view['curr_participants_grouped_arr'][$i]['suggested_companies'][$j];
			$datagrid_row = $g_view['unique_companies_arr'][$temp_company['company_name']];
			$g_view['datagrid'][$datagrid_col][$datagrid_row] = $temp_company;
		}
		$datagrid_col++;
	}
}

/*************************
Now we create the display grid from the data grid
********************/
$original_suggestion_colspan = max(1,$g_view['original_participants_suggestion_count']);
$participant_suggestion_colspan = max(1,$g_view['participants_suggestion_count']);
$current_colspan = max(1,$g_view['curr_participants_group_count']);
$table_width = 200*$g_view['datagrid_num_cols'];
$original_suggestion_col_start_at = 0;
$participant_suggestion_col_start_at = $original_suggestion_col_start_at + max(1,$g_view['original_participants_suggestion_count']);
$current_participant_col_start_at = $participant_suggestion_col_start_at + max(1,$g_view['participants_suggestion_count']);
//just for the input fields
$frm_participant_company_i = 0;
?>
<form id="frm_participant_suggestion">

<input type="hidden" name="deal_id" value="<?php echo $g_view['deal_id'];?>"  />
<table style="width:<?php echo $table_width;?>px;" cellpadding="0" cellspacing="0" border="0">

<tr>
<td class="deal-edit-snippet-header" style="min-width:200px;" colspan="<?php echo $original_suggestion_colspan;?>">Original Submission:</td>
<td class="deal-edit-snippet-header" style="min-width:200px;" colspan="<?php echo $participant_suggestion_colspan;?>">Edits / Additions:</td>
<td class="deal-edit-snippet-header" style="min-width:200px;" colspan="<?php echo $current_colspan;?>">Edit Current Participants</td>
</tr>

<?php
for($row = 0;$row<$g_view['datagrid_num_rows'];$row++){
	?>
	<tr>
	<?php
		for($col = 0;$col<$g_view['datagrid_num_cols'];$col++){
			?>
			<td class="deal-edit-snippet-mid-col" style="min-width:200px;<?php if($col == 0){?>border:0;<?php }?>">
			<?php
			if($row == 0 && $col == $original_suggestion_col_start_at && $g_view['original_participants_suggestion_count'] == 0){
				echo "None specified";
			}
			if($row == 0 && $col == $participant_suggestion_col_start_at && $g_view['participants_suggestion_count'] == 0){
				echo "None submitted yet";
			}
			if($row == 0 && $col == $current_participant_col_start_at && $g_view['curr_participants_group_count'] == 0){
				echo "None available";
			}
			if($g_view['datagrid'][$col][$row] != NULL){
				/********
				If this is the current participant col section, we allow to change the role with a dropdown and we send the name as hidden form element
				*************/
				?><div><strong><?php echo $g_view['datagrid'][$col][$row]['company_name'];?></strong></div><?php
				
				if($col >= $current_participant_col_start_at){
					//this is the last column
					?>
					<input type="hidden" name="companies[]" value="<?php echo $g_view['datagrid'][$col][$row]['company_name'];?>" />
					<input type="hidden" name="new_entry_<?php echo $frm_participant_company_i;?>" value="n" />
					<?php
					/*******************
					we need a way to know whether this is existing entry where member may just change the role OR whether this is a new entry where
					member type the company name and the role and footnote
					If this is a new entry, it must not be one from the current entries (that is if JPMorgan is there, do not allow to add JPMorgan again
					***************************/
					?>
					<div>
						<select name="participant_role_<?php echo $frm_participant_company_i;?>" class="deal-edit-snippet-dropdown std">
						<option value="">select role</option>
						<?php
						/**************
						now we show all roles
						*************/
						for($role_i=0;$role_i<$g_view['company_role_count'];$role_i++){
							?>
							<option value="<?php echo $g_view['company_roles'][$role_i]['role_id'];?>" <?php if($g_view['company_roles'][$role_i]['role_id']==$g_view['datagrid'][$col][$row]['role_id']){?>selected="selected"<?php }?> ><?php echo $g_view['company_roles'][$role_i]['role_name'];?></option>
							<?php
						}
						?>
						</select>
					</div>
					<div>
						<input type="text" name="participant_note_<?php echo $frm_participant_company_i;?>" id="participant_note_<?php echo $participant_i;?>" class="deal-edit-snippet-textbox std special <?php if($g_view['datagrid'][$col][$row]['footnote']!=""){?>black<?php }?>" value="<?php if($g_view['datagrid'][$col][$row]['footnote']!="") echo $g_view['datagrid'][$col][$row]['footnote']; else echo "" ;?>" />
					</div>
					<?php
					$frm_participant_company_i++;
				}else{
					?>
					<div><?php if($g_view['datagrid'][$col][$row]['role_id'] == 0) echo "N/A"; else echo $g_view['datagrid'][$col][$row]['role_name'];?></div>
					<div><?php if($g_view['datagrid'][$col][$row]['footnote'] == "") echo "N/A"; else echo $g_view['datagrid'][$col][$row]['footnote'];?></div>
					<?php
					/****************
					sng:16/apr/2012
					this cell has data and this is not the current participant section. If this is the suggestion columns, we also show the change status_note
					***************/
					if($col >= $participant_suggestion_col_start_at){
						if($g_view['datagrid'][$col][$row]['status_note']!=""){
							?><div><?php echo $g_view['datagrid'][$col][$row]['status_note'];?></div><?php
						}
					}
				}
			}
			?>
			</td>
			<?php
		}
	?>
	</tr>
	<?php
}
?>
<tr>
</tr>


<tr>
<?php
/*******************
The footer portion, need to put proper name in proper col.
for the current participant col, we show blank entry form
*******************/
$datagrid_col = 0;
if(0 == $g_view['original_participants_suggestion_count']){
	//no original suggestion
	$datagrid_col++;
	?><td></td><?php
}else{
	for($i=0;$i<$g_view['original_participants_suggestion_count'];$i++){
		?>
		<td class="deal-edit-snippet-mid-col" style="min-width:200px;<?php if($i == 0){?>border:0;<?php }?>">
		<div class="hr_div"></div>
		<div class="deal-edit-snippet-footer">Submitted <?php echo $g_view['original_participants_suggestion_arr'][$i]['suggested_on'];?></div>
		<div class="deal-edit-snippet-footer"><?php echo $g_view['original_participants_suggestion_arr'][$i]['suggested_by'];?></div>
		</td>
		<?php
		$datagrid_col++;
	}
}

if(0 == $g_view['participants_suggestion_count']){
	//no participant suggestion
	$datagrid_col++;
	?><td class="deal-edit-snippet-mid-col"></td><?php
}else{
	for($i=0;$i<$g_view['participants_suggestion_count'];$i++){
		?>
		<td class="deal-edit-snippet-mid-col">
		<div class="hr_div"></div>
		<div class="deal-edit-snippet-footer">Submitted <?php echo $g_view['participants_suggestion_arr'][$i]['suggested_on'];?></div>
		<div class="deal-edit-snippet-footer"><?php echo $g_view['participants_suggestion_arr'][$i]['suggested_by'];?></div>
		</td>
		<?php
		$datagrid_col++;
	}
}

if(0 == $g_view['curr_participants_group_count']){
	//no current
	$datagrid_col++;
	?><td class="deal-edit-snippet-mid-col">
	<div class="hr_div"></div>
	<div id="new_participant"></div>
	<div id="frm_participant_suggestion_msg" class="msg_txt"></div>
	
	<div style="float:left;"><input type="button" value="Add More" class="btn_auto" onClick="add_participant('new_participant')" /></div>
	
	<div style="float:left;"><input type="button" class="btn_auto" value="Submit" onClick="submit_company_suggestion('frm_participant_suggestion','frm_participant_suggestion_msg');"  /></div>
	</td><?php
}else{
	for($i=0;$i<$g_view['curr_participants_group_count'];$i++){
		?><td class="deal-edit-snippet-mid-col">
		<div class="hr_div"></div>
		<div id="new_participant"></div>
		<div id="frm_participant_suggestion_msg" class="msg_txt"></div>
		
		<div style="float:left;"><input type="button" value="Add More" class="btn_auto" onClick="add_participant('new_participant')" /></div>
	
		<div style="float:left;"><input type="button" class="btn_auto" value="Submit" onClick="submit_company_suggestion('frm_participant_suggestion','frm_participant_suggestion_msg');"  /></div>
		</td><?php
		$datagrid_col++;
	}
}
?>
</tr>
</table>
</form>
<script>
_current_company_num = <?php echo $frm_participant_company_i;?>;
$('.btn_auto').button();
set_footnote_inputs_defaults();
</script>