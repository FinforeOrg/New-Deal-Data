<?php
/*************************
sng:20/mar/2012
We use ajax to fetch the existing sources suggested for a deal

sng: 5/apr/2012
We now load everything here.
****************************/
require_once("../../include/global.php");
require_once("classes/class.transaction_suggestion.php");
require_once("classes/class.deal_support.php");

$trans_suggestion = new transaction_suggestion();
$deal_support = new deal_support();

$g_view['deal_id'] = $_GET['deal_id'];
$g_view['partner_type'] = $_GET['type'];

/***********
a little hack required for javascript, since we have two panels with same functionality
******************/
if($g_view['partner_type'] == "bank"){
	$g_view['js_prefix'] = "bank";
}else if($g_view['partner_type'] == "law firm"){
	$g_view['js_prefix'] = "law_firm";
}else{
	$g_view['js_prefix'] = "";
}

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
/*************************************************
now we get the role names based on deal type and partner type
**********/

$g_view['firm_roles'] = NULL;
$g_view['firm_roles_count'] = 0;

$ok = $deal_support->front_get_deal_partner_roles($g_view['partner_type'],$g_view['deal_data']['deal_cat_name'],$g_view['firm_roles'],$g_view['firm_roles_count']);
if(!$ok){
	/****************
	Let us not hang the script
	******************/
}

/*************
get the original submission. Remember that now when we add a deal from front end
we store the partner names for the deal in transaction_partners_suggestions along with
the id of the member who submitted the deal and the deal submission time.
Use get_original as true
***************/
$g_view['original_partners_suggestion_arr'] = NULL;
$g_view['original_partners_suggestion_count'] = 0;

$ok = $trans_suggestion->fetch_partners_with_grouping($g_view['deal_id'],$g_view['partner_type'],true,$g_view['original_partners_suggestion_arr'],$g_view['original_partners_suggestion_count']);

if(!$ok){
	echo "Cannot fetch the original submission";
	return;
}
/************
It may happen that there is no original submission, that is, no banks/law firms were suggested when the deal was added
and so $g_view['original_partners_suggestion_count'] may be 0
********************************************************************/

/********************************
get the suggestions
Use get_original as false
**********/
$g_view['partners_suggestion_arr'] = NULL;
$g_view['partners_suggestion_count'] = 0;

$ok = $trans_suggestion->fetch_partners_with_grouping($g_view['deal_id'],$g_view['partner_type'],false,$g_view['partners_suggestion_arr'],$g_view['partners_suggestion_count']);

if(!$ok){
	echo "Cannot fetch the suggested partners";
	return;
}
/*******
It may happen that there is no suggestions. in that case $g_view['partners_suggestion_count'] is 0
***********************************************************/

/********************************
get the current partners
**********/
$g_view['curr_partners_grouped_arr'] = NULL;
$g_view['curr_partners_group_count'] = 0;

$ok = $trans_suggestion->get_current_partners_with_grouping($g_view['deal_id'],$g_view['partner_type'],$g_view['curr_partners_grouped_arr'],$g_view['curr_partners_group_count']);

if(!$ok){
	echo "Cannot fetch the partners associated with the deal";
	return;
}
/*******
It may happen that there is no current partners. in that case $g_view['curr_partners_group_count'] is 0
***********************************************************/

/**************************
we now create the array of unique firms that we have in the suggestions
and we store the row offset where it should be shown in the data grid
***************************/
$g_view['unique_firms_arr'] = array();
$g_view['unique_firms_arr_head'] = -1;


for($i=0;$i<$g_view['original_partners_suggestion_count'];$i++){
	$temp_count = $g_view['original_partners_suggestion_arr'][$i]['suggested_firms_count'];
	for($j=0;$j<$temp_count;$j++){
		$temp_firm = $g_view['original_partners_suggestion_arr'][$i]['suggested_firms'][$j]['partner_name'];
		if(($temp_firm!="")&&(!array_key_exists($temp_firm,$g_view['unique_firms_arr']))){
			//enqueue
			$g_view['unique_firms_arr_head']++;
			$g_view['unique_firms_arr'][$temp_firm] = $g_view['unique_firms_arr_head'];
		}
	}
}


for($i=0;$i<$g_view['partners_suggestion_count'];$i++){
	$temp_count = $g_view['partners_suggestion_arr'][$i]['suggested_firms_count'];
	for($j=0;$j<$temp_count;$j++){
		$temp_firm = $g_view['partners_suggestion_arr'][$i]['suggested_firms'][$j]['partner_name'];
		if(($temp_firm!="")&&(!array_key_exists($temp_firm,$g_view['unique_firms_arr']))){
			//enqueue
			$g_view['unique_firms_arr_head']++;
			$g_view['unique_firms_arr'][$temp_firm] = $g_view['unique_firms_arr_head'];
		}
	}
}


for($i=0;$i<$g_view['curr_partners_group_count'];$i++){
	$temp_count = $g_view['curr_partners_grouped_arr'][$i]['suggested_firms_count'];
	for($j=0;$j<$temp_count;$j++){
		$temp_firm = $g_view['curr_partners_grouped_arr'][$i]['suggested_firms'][$j]['partner_name'];
		if(($temp_firm!="")&&(!array_key_exists($temp_firm,$g_view['unique_firms_arr']))){
			//enqueue
			$g_view['unique_firms_arr_head']++;
			$g_view['unique_firms_arr'][$temp_firm] = $g_view['unique_firms_arr_head'];
		}
	}
}

/****************************************
Now we create the datagrid to hold the columns and rows for original submission, suggestions and current.
Different firm data will go to different row, same firm will go to same row n column. That is the reason
why we maintained the array $g_view['unique_firms_arr'].

So, how may columns and how many rows?
rows are number of unique firms or 1 if no firms
cols are (1 or num of orginal submission) + (1 or num of suggestions) + (1 or current)
***********************/
$g_view['datagrid'] = array();
$g_view['datagrid_num_rows'] = max(1,count($g_view['unique_firms_arr']));
$g_view['datagrid_num_cols'] = max(1,$g_view['original_partners_suggestion_count']) + max(1,$g_view['partners_suggestion_count']) + max(1,$g_view['curr_partners_group_count']);

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
if(0 == $g_view['original_partners_suggestion_count']){
	//no original suggestion
	$datagrid_col++;
}else{
	for($i=0;$i<$g_view['original_partners_suggestion_count'];$i++){
		$temp_count = $g_view['original_partners_suggestion_arr'][$i]['suggested_firms_count'];
		for($j=0;$j<$temp_count;$j++){
			$temp_firm = $g_view['original_partners_suggestion_arr'][$i]['suggested_firms'][$j];
			$datagrid_row = $g_view['unique_firms_arr'][$temp_firm['partner_name']];
			$g_view['datagrid'][$datagrid_col][$datagrid_row] = $temp_firm;
		}
		$datagrid_col++;
	}
}

if(0 == $g_view['partners_suggestion_count']){
	//no partner suggestion
	$datagrid_col++;
}else{
	for($i=0;$i<$g_view['partners_suggestion_count'];$i++){
		$temp_count = $g_view['partners_suggestion_arr'][$i]['suggested_firms_count'];
		for($j=0;$j<$temp_count;$j++){
			$temp_firm = $g_view['partners_suggestion_arr'][$i]['suggested_firms'][$j];
			$datagrid_row = $g_view['unique_firms_arr'][$temp_firm['partner_name']];
			$g_view['datagrid'][$datagrid_col][$datagrid_row] = $temp_firm;
		}
		$datagrid_col++;
	}
}

if(0 == $g_view['curr_partners_group_count']){
	//no current
	$datagrid_col++;
}else{
	for($i=0;$i<$g_view['curr_partners_group_count'];$i++){
		$temp_count = $g_view['curr_partners_grouped_arr'][$i]['suggested_firms_count'];
		for($j=0;$j<$temp_count;$j++){
			$temp_firm = $g_view['curr_partners_grouped_arr'][$i]['suggested_firms'][$j];
			$datagrid_row = $g_view['unique_firms_arr'][$temp_firm['partner_name']];
			$g_view['datagrid'][$datagrid_col][$datagrid_row] = $temp_firm;
		}
		$datagrid_col++;
	}
}

/*************************
Now we create the display grid from the data grid
********************/
$original_suggestion_colspan = max(1,$g_view['original_partners_suggestion_count']);
$partner_suggestion_colspan = max(1,$g_view['partners_suggestion_count']);
$current_colspan = max(1,$g_view['curr_partners_group_count']);
$table_width = 200*$g_view['datagrid_num_cols'];
$original_suggestion_col_start_at = 0;
$partner_suggestion_col_start_at = $original_suggestion_col_start_at + max(1,$g_view['original_partners_suggestion_count']);
$current_partner_col_start_at = $partner_suggestion_col_start_at + max(1,$g_view['partners_suggestion_count']);
//just for the input fields
$frm_partner_firm_i = 0;
?>
<form id="frm_partner_suggestion_<?php echo $g_view['js_prefix'];?>">
<input type="hidden" name="partner_type" value="<?php echo $g_view['partner_type'];?>"  />
<input type="hidden" name="deal_id" value="<?php echo $g_view['deal_id'];?>"  />
<table style="width:<?php echo $table_width;?>px;" cellpadding="0" cellspacing="0" border="0">

<tr>
<td class="deal-edit-snippet-header" style="min-width:200px;" colspan="<?php echo $original_suggestion_colspan;?>">Original Submission:</td>
<td class="deal-edit-snippet-header" style="min-width:200px;" colspan="<?php echo $partner_suggestion_colspan;?>">Edits / Additions:</td>
<td class="deal-edit-snippet-header" style="min-width:200px;" colspan="<?php echo $current_colspan;?>">Edit Current Partners</td>
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
			if($row == 0 && $col == $original_suggestion_col_start_at && $g_view['original_partners_suggestion_count'] == 0){
				echo "None specified";
			}
			if($row == 0 && $col == $partner_suggestion_col_start_at && $g_view['partners_suggestion_count'] == 0){
				echo "None submitted yet";
			}
			if($row == 0 && $col == $current_partner_col_start_at && $g_view['curr_partners_group_count'] == 0){
				echo "None available";
			}
			if($g_view['datagrid'][$col][$row] != NULL){
				/********
				If this is the current partner col section, we allow to change the role with a dropdown and we send the name as hidden form element
				*************/
				?><div><strong><?php echo $g_view['datagrid'][$col][$row]['partner_name'];?></strong></div><?php
				
				if($col >= $current_partner_col_start_at){
					//this is the last column
					?>
					<input type="hidden" name="firms[]" value="<?php echo $g_view['datagrid'][$col][$row]['partner_name'];?>" />
					<input type="hidden" name="new_entry_<?php echo $frm_partner_firm_i;?>" value="n" />
					<?php
					/*******************
					we need a way to know whether this is existing entry where member may just change the role OR whether this is a new entry where
					member type the firm name and the role.
					If this is a new entry, it must not be one from the current entries (that is if JPMorgan is there, do not allow to add JPMorgan again
					***************************/
					?>
					<div>
						<select name="partner_role_<?php echo $frm_partner_firm_i;?>" class="deal-edit-snippet-dropdown std">
						<option value="">select role</option>
						<?php
						/**************
						now we show all roles
						*************/
						for($role_i=0;$role_i<$g_view['firm_roles_count'];$role_i++){
							?>
							<option value="<?php echo $g_view['firm_roles'][$role_i]['role_id'];?>" <?php if($g_view['firm_roles'][$role_i]['role_id']==$g_view['datagrid'][$col][$row]['role_id']){?>selected="selected"<?php }?> ><?php echo $g_view['firm_roles'][$role_i]['role_name'];?></option>
							<?php
						}
						?>
						</select>
					</div>
					<?php
					$frm_partner_firm_i++;
				}else{
					?>
					<div><?php if($g_view['datagrid'][$col][$row]['role_id'] == 0) echo "N/A"; else echo $g_view['datagrid'][$col][$row]['role_name'];?></div>
					<?php
					/****************
					sng:16/apr/2012
					this cell has data and this is not the current partner section. If this is the suggestion columns, we also show the change status_note
					***************/
					if($col >= $partner_suggestion_col_start_at){
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
for the current partner col, we show blank entry form
*******************/
$datagrid_col = 0;
if(0 == $g_view['original_partners_suggestion_count']){
	//no original suggestion
	$datagrid_col++;
	?><td></td><?php
}else{
	for($i=0;$i<$g_view['original_partners_suggestion_count'];$i++){
		?>
		<td class="deal-edit-snippet-mid-col" style="min-width:200px;<?php if($i == 0){?>border:0;<?php }?>">
		<div class="hr_div"></div>
		<div class="deal-edit-snippet-footer">Submitted <?php echo $g_view['original_partners_suggestion_arr'][$i]['suggested_on'];?></div>
		<div class="deal-edit-snippet-footer"><?php echo $g_view['original_partners_suggestion_arr'][$i]['suggested_by'];?></div>
		</td>
		<?php
		$datagrid_col++;
	}
}

if(0 == $g_view['partners_suggestion_count']){
	//no partner suggestion
	$datagrid_col++;
	?><td class="deal-edit-snippet-mid-col"></td><?php
}else{
	for($i=0;$i<$g_view['partners_suggestion_count'];$i++){
		?>
		<td class="deal-edit-snippet-mid-col">
		<div class="hr_div"></div>
		<div class="deal-edit-snippet-footer">Submitted <?php echo $g_view['partners_suggestion_arr'][$i]['suggested_on'];?></div>
		<div class="deal-edit-snippet-footer"><?php echo $g_view['partners_suggestion_arr'][$i]['suggested_by'];?></div>
		</td>
		<?php
		$datagrid_col++;
	}
}

if(0 == $g_view['curr_partners_group_count']){
	//no current
	$datagrid_col++;
	?><td class="deal-edit-snippet-mid-col">
	<div class="hr_div"></div>
	<div id="new_partner_<?php echo $g_view['js_prefix'];?>"></div>
	<div id="frm_partner_suggestion_msg_<?php echo $g_view['js_prefix'];?>" class="msg_txt"></div>
	
	<div style="float:left;"><input type="button" value="Add" class="btn_auto" onclick="add_partner('<?php echo $g_view['partner_type'];?>','new_partner_<?php echo $g_view['js_prefix'];?>')" /></div>
	
	<div style="float:left;"><input type="button" class="btn_auto" value="Submit" onclick="submit_partner_suggestion('<?php echo $g_view['partner_type'];?>','frm_partner_suggestion_<?php echo $g_view['js_prefix'];?>','frm_partner_suggestion_msg_<?php echo $g_view['js_prefix'];?>');"  /></div>
	</td><?php
}else{
	for($i=0;$i<$g_view['curr_partners_group_count'];$i++){
		?><td class="deal-edit-snippet-mid-col">
		<div class="hr_div"></div>
		<div id="new_partner_<?php echo $g_view['js_prefix'];?>"></div>
		<div id="frm_partner_suggestion_msg_<?php echo $g_view['js_prefix'];?>" class="msg_txt"></div>
		
		<div style="float:left;"><input type="button" value="Add" class="btn_auto" onclick="add_partner('<?php echo $g_view['partner_type'];?>','new_partner_<?php echo $g_view['js_prefix'];?>')" /></div>
	
		<div style="float:left;"><input type="button" class="btn_auto" value="Submit" onclick="submit_partner_suggestion('<?php echo $g_view['partner_type'];?>','frm_partner_suggestion_<?php echo $g_view['js_prefix'];?>','frm_partner_suggestion_msg_<?php echo $g_view['js_prefix'];?>');"  /></div>
		</td><?php
		$datagrid_col++;
	}
}
?>
</tr>
</table>
</form>
<script>
_current_<?php echo $g_view['js_prefix'];?>_num = <?php echo $frm_partner_firm_i;?>;
$('.btn_auto').button();
</script>