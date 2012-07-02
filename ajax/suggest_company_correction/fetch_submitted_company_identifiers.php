<?php
/**********************
sng:23/may/2012
The problem here is that a submission will create one or more rows and not all submission has
same identifiers. This is like deal participant
**********************/
require_once("../../include/global.php");
$g_view['company_id'] = $_GET['company_id'];

require_once("classes/class.company_suggestion.php");
$comp_suggestion = new company_suggestion();

require_once("classes/class.company.php");
$comp = new company();

require_once("classes/class.account.php");

/****************
get the original submission data
A member may suggest one or more identifier data during a single submission and there
can be one or more submission by the same user. We use a grouping
****************/
$g_view['original_suggestion_arr'] = NULL;
$g_view['original_suggestion_count'] = 0;

$ok = $comp_suggestion->fetch_suggestions_for_company_identifiers_with_grouping($g_view['company_id'],true,$g_view['original_suggestion_arr'],$g_view['original_suggestion_count']);

if(!$ok){
	echo "Cannot fetch the original submission";
	return;
}

/***************
get the corrective suggestions with grouping
****************/
$g_view['suggestion_arr'] = NULL;
$g_view['suggestion_count'] = 0;

$ok = $comp_suggestion->fetch_suggestions_for_company_identifiers_with_grouping($g_view['company_id'],false,$g_view['suggestion_arr'],$g_view['suggestion_count']);

if(!$ok){
	echo "Cannot fetch the suggested identifiers";
	return;
}
/***************************
get the current data.
We do not group because we do not show who suggested and when
***************************/
$g_view['current'] = NULL;
$g_view['current_count'] = 0;
$ok = $comp->front_get_company_identifiers($g_view['company_id'],$g_view['current'],$g_view['current_count']);
if(!$ok){
	echo "Cannot fetch the current identifiers";
	return;
}
/**************************
Unlike participant, here we have an advantage. We have a master list of identifiers.
All we have to do is get that list and create our lookup array with identifier name
as key and the row offset where it should be shown in the data grid
***************************/
$g_view['identifiers_arr'] = array();


$g_view['identifier_list'] = NULL;
$g_view['identifier_list_count'] = 0;

$ok = $comp->admin_get_identifier_options($g_view['identifier_list'],$g_view['identifier_list_count']);
if(!$ok){
	echo "Cannot fetch the master list of identifiers";
}

for($i=0;$i<$g_view['identifier_list_count'];$i++){
	$g_view['identifiers_arr'][$g_view['identifier_list'][$i]['name']] = $i;
}
/****************************************
Now we create the datagrid to hold the columns and rows for original submission, suggestions and current.
Different identifier data will go to different row, same identifier will go to same row n column.

So, how may columns and how many rows?
rows are number of entries in master list
cols are (1 for labels) + (1 for orginal submission) + (1 or num of suggestions) + (1 for current)
***********************/
$g_view['datagrid'] = array();
$g_view['datagrid_num_rows'] = $g_view['identifier_list_count'];
$g_view['datagrid_num_cols'] = 3 + max(1,$g_view['suggestion_count']);

/********************
Now we need to populate the grid. Todo that, first we initialize the cells with NULL
we use col, row
*********************/
for($cols=0;$cols<$g_view['datagrid_num_cols'];$cols++){
	$g_view['datagrid'][$cols] = array();
	for($rows=0;$rows<$g_view['datagrid_num_rows'];$rows++){
		$g_view['datagrid'][$cols][$rows] = NULL;
	}
}

/***********************
populate the label col
********/
$datagrid_col = 0;

for($j=0;$j<$g_view['identifier_list_count'];$j++){
	$g_view['datagrid'][$datagrid_col][$j] = $g_view['identifier_list'][$j];
}
$datagrid_col++;
/*******************
populate the original suggestion
****************/
if(0 == $g_view['original_suggestion_count']){
	//no original suggestion
	$datagrid_col++;
}else{
	for($i=0;$i<$g_view['original_suggestion_count'];$i++){
		$temp_count = $g_view['original_suggestion_arr'][$i]['suggested_identifiers_count'];
		for($j=0;$j<$temp_count;$j++){
			$temp_identifier_rec = $g_view['original_suggestion_arr'][$i]['suggested_identifiers'][$j];
			$datagrid_row = $g_view['identifiers_arr'][$temp_identifier_rec['identifier_name']];
			$g_view['datagrid'][$datagrid_col][$datagrid_row] = $temp_identifier_rec;
		}
		$datagrid_col++;
	}
}
/**********************
populate corrective suggestions
**********************/
if(0 == $g_view['suggestion_count']){
	//no corrective suggestion
	$datagrid_col++;
}else{
	for($i=0;$i<$g_view['suggestion_count'];$i++){
		$temp_count = $g_view['suggestion_arr'][$i]['suggested_identifiers_count'];
		for($j=0;$j<$temp_count;$j++){
			$temp_identifier_rec = $g_view['suggestion_arr'][$i]['suggested_identifiers'][$j];
			$datagrid_row = $g_view['identifiers_arr'][$temp_identifier_rec['identifier_name']];
			$g_view['datagrid'][$datagrid_col][$datagrid_row] = $temp_identifier_rec;
		}
		$datagrid_col++;
	}
}
/************************
populate current
this is a bit different
*************************/
for($j=0;$j<$g_view['current_count'];$j++){
	$datagrid_row = $g_view['identifiers_arr'][$g_view['current'][$j]['name']];
	$g_view['datagrid'][$datagrid_col][$datagrid_row] = $g_view['current'][$j];
}



/******************
Calculate the total num of columns we need
We need 1 col to show the original submission, 1 col to show the edit field, 1 col to show current data.
Then we have suggestions. If no suggestions then take one else that many num of columns. Finally, one col to
show the labels
******************/
$label_column_width = 200;
$column_width = 180;
$suggestion_colspan = max(1,$g_view['suggestion_count']);
$num_cols = 4 + $suggestion_colspan;
$table_width = ($column_width*$num_cols)+$label_column_width;
?>
<form id="company_identifier_data_frm" method="post" action="" onSubmit="return submit_company_identifier_correction();">
<input type="hidden" name="company_id" value="<?php echo $g_view['company_id'];?>" />
<?php
/***************
we send the identifiers as identifier_<id>. We also send the identifier ids as array so the handler knows what are the POST data
*****************/
for($j=0;$j<$g_view['identifier_list_count'];$j++){
	?><input type="hidden" name="identifier_ids[]" value="<?php echo $g_view['identifier_list'][$j]['identifier_id'];?>" /><?php
}
?>
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
<?php
/****************
create the table grid in html, one row for each identifier in the master list
***************/
for($row = 0;$row<$g_view['datagrid_num_rows'];$row++){
	?>
	<tr>
		<?php
		$col = 0;
		?>
		<td class="deal-edit-snippet-mid-col" style="border:0"><?php echo $g_view['datagrid'][$col][$row]['name'];?></td>
		<?php
		$col++;
		//original suggestion
		?><td class="deal-edit-snippet-mid-col"><?php
		if(0 == $g_view['original_suggestion_count']){
			/*********
			no original suggestion. If this is the first row, show message
			************/
			if(0 == $row){
				?>None specified<?php
			}
			
		}else{
			/************
			it may happen that there may not be values for all identifiers and some cells are NULL
			**************/
			if($g_view['datagrid'][$col][$row]!=NULL) echo $g_view['datagrid'][$col][$row]['value'];
		}
		?>
		</td>
		<?php
		$col++;
		//corrective suggestions
		/************
		given the current $col, how many more offsets for suggestions?
		easy - $col + number of suggestion cols
		*************/
		$limit = $col + $suggestion_colspan;
		for(;$col<$limit;$col++){
			?><td class="deal-edit-snippet-mid-col">
			
			<?php
			if((0 == $g_view['suggestion_count'])&&(0 == $row)){
				/**********
				No worry, if there are no suggestions, there will be only one col
				************/
				?>None suggested<?php
			}else{
				if($g_view['datagrid'][$col][$row]!=NULL){
					echo $g_view['datagrid'][$col][$row]['value'];
					if($g_view['datagrid'][$col][$row]['status_note']!=""){
						echo " [".$g_view['datagrid'][$col][$row]['status_note']."]";
					}
				}
				
			}
			?>
			</td><?php
		}
		//current data
		if(0 == $g_view['current_count']){
			/*********
			no original suggestion. If this is the first row, show message
			************/
			if(0 == $row){
				?><td class="deal-edit-snippet-mid-col">None found</td><?php
			}
		}else{
			?><td class="deal-edit-snippet-mid-col"><?php if($g_view['datagrid'][$col][$row]!=NULL) echo $g_view['datagrid'][$col][$row]['value'];?></td><?php
		}
	?>
	<td class="deal-edit-snippet-mid-col">
	<?php
	/**********
	We use $g_view['identifier_list'] with $row to get the id. We know that for each record in $g_view['identifier_list'], there is a row
	in same order
	**************/
	$field_name = "identifier_id_".$g_view['identifier_list'][$row]['identifier_id'];
	?>
	<input type="text" name="<?php echo $field_name;?>" class="deal-edit-snippet-textbox" />
	</td>
	</tr>
	<?php
}
?>
<!--////////////////////footer//////////////////////////////////-->
<tr>
<?php
/*************
label col has no footer data
**************/
?>
<td class="deal-edit-snippet-mid-col" style="border:0"></td>
<?php
/************
original suggestion footer
************/
?>
<td class="deal-edit-snippet-mid-col">
<?php
if($g_view['original_suggestion_count'] != 0){
	?>
	<div class="hr_div"></div>
	<div class="deal-edit-snippet-footer">Submitted <?php echo $g_view['original_suggestion_arr'][0]['suggested_on'];?></div>
	<div class="deal-edit-snippet-footer"><?php echo $g_view['original_suggestion_arr'][0]['suggested_by'];?></div>
	<?php
}else{
	?><?php
}
?>
</td>
<?php
/*********
corrective suggestions
************/
if($g_view['suggestion_count'] > 0){
	for($i=0;$i<$g_view['suggestion_count'];$i++){
		?>
		<td class="deal-edit-snippet-mid-col">
		<div class="hr_div"></div>
		<div class="deal-edit-snippet-footer">Submitted <?php echo $g_view['suggestion_arr'][$i]['suggested_on'];?></div>
		<div class="deal-edit-snippet-footer"><?php echo $g_view['suggestion_arr'][$i]['suggested_by'];?></div>
		</td>
		<?php
	}
}else{
	?><td class="deal-edit-snippet-mid-col"></td><?php
}

/***********
current data has no footer data
************/
?>
<td class="deal-edit-snippet-mid-col"></td>
<?php
/*******************
edit column will have the button
****************/
?>
<td class="deal-edit-snippet-mid-col">
<div class="hr_div"></div>
<div id="company_identifier_data_frm_msg" class="msg_txt"></div>
<div class="deal-edit-snippet-footer">
<?php
if($g_account->is_site_member_logged()){
	?><input type="submit" class="btn_auto" value="Submit"  /><?php
}else{
	?><input type="button" class="btn_auto" value="Submit" onClick="show_non_login_alert();" /><?php
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