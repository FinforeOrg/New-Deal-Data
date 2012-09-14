<script type="text/javascript" src="nifty_utils.js"></script>
<script type="text/javascript" src="../js/jquery-1.2.1.pack.js"></script>
<script type="text/javascript">
function deal_cat_changed(){
	
	var type_obj = document.getElementById('deal_cat_name');
	var offset_selected = type_obj.selectedIndex;
	if(offset_selected != 0){
		var deal_cat_name_selected = type_obj.options[offset_selected].value;
		//fetch the list of deal sub categories
		$.post("ajax/deal_subtype1_list.php", {deal_cat_name: ""+deal_cat_name_selected+""}, function(data){
			
				if(data.length >0) {
					$('#deal_subcat1_name').html(data);
				}
		});
	}
}

function deal_subcat_changed(){
	
	var type_obj = document.getElementById('deal_cat_name');
	var offset_selected = type_obj.selectedIndex;
	var type1_obj = document.getElementById('deal_subcat1_name');
	var offset1_selected = type1_obj.selectedIndex;
	
	if((offset_selected != 0)&&(offset1_selected!=0)){
		
		var deal_cat_name_selected = type_obj.options[offset_selected].value;
		var deal_subcat_name_selected = type1_obj.options[offset1_selected].value;
		//fetch the list of deal sub categories
		$.post("ajax/deal_subtype2_list.php", {deal_cat_name: ""+deal_cat_name_selected+"",deal_subcat_name: ""+deal_subcat_name_selected+""}, function(data){
			//alert(data);
				if(data.length >0) {
					$('#deal_subcat2_name').html(data);
				}
		});
	}
}

function sector_changed(){
	var sector_obj = document.getElementById('sector');
	var offset_selected = sector_obj.selectedIndex;
	if(offset_selected != 0){
		var sector_selected = sector_obj.options[offset_selected].value;
		//fetch the list of industries
		$.post("ajax/industry_list_for_sector.php", {sector: ""+sector_selected+""}, function(data){
				if(data.length >0) {
					$('#industry').html(data);
				}
		});
	}
}
</script>
<table cellspacing="0" cellpadding="0" border="0">
<tr>
<td>
<form method="post" action="">
<input type="hidden" name="action" value="search_deal" />
<table width="100%" cellspacing="0" cellpadding="5" border="0">
<tr>
<td>
	<table cellspacing="0" cellpadding="5">
	<tr>
	<td>
	Company: <input type="text" name="company_name" value="<?php echo $g_mc->view_to_view($_POST['company_name']);?>" style="width:300px;" />
	</td>
	<td>
	<select name="sector" id="sector" onchange="return sector_changed();">
	<option value="">Any Sector</option>
	<?php
	for($i=0;$i<$g_view['sector_count'];$i++){
	?>
	<option value="<?php echo $g_view['sector_list'][$i]['sector'];?>" <?php if($_POST['sector']== $g_view['sector_list'][$i]['sector']){?>selected="selected"<?php }?>><?php echo $g_view['sector_list'][$i]['sector'];?></option>
	<?php
	}
	?>
	</select>
	</td>
	<td>
	<select name="industry" id="industry">
	<option value="">Any Industry</option>
	<?php
	for($j=0;$j<$g_view['industry_count'];$j++){
	?>
	<option value="<?php echo $g_view['industry_list'][$j]['industry'];?>" <?php if($_POST['industry']==$g_view['industry_list'][$j]['industry']){?>selected="selected"<?php }?> ><?php echo $g_view['industry_list'][$j]['industry'];?></option>
	<?php
	}
	?>
	</select>
	</td>
	</tr>
	</table>
</td>
</tr>
<tr>
<td>
	<table cellspacing="0" cellpadding="5">
	<tr>
	<td>
	<select name="region">
	<option value="">Any Region</option>
	<?php
	for($i=0;$i<$g_view['region_count'];$i++){
	?>
	<option value="<?php echo $g_view['region_list'][$i]['name'];?>" <?php if($_POST['region']==$g_view['region_list'][$i]['name']){?>selected="selected"<?php }?> ><?php echo $g_view['region_list'][$i]['name'];?></option>
	<?php
	}
	?>
	</select>
	</td>
	<td>
	<select name="country">
	<option value="">Any Country</option>
	<?php
	for($i=0;$i<$g_view['country_count'];$i++){
	?>
	<option value="<?php echo $g_view['country_list'][$i]['name'];?>" <?php if($_POST['country']==$g_view['country_list'][$i]['name']){?>selected="selected"<?php }?>><?php echo $g_view['country_list'][$i]['name'];?></option>
	<?php
	}
	?>
	</select>
	</td>
	<td>
	<select name="deal_cat_name" id="deal_cat_name" onchange="return deal_cat_changed();">
	<option value="">Any Type of Deal</option>
	<?php
	for($k=0;$k<$g_view['cat_count'];$k++){
	?>
	<option value="<?php echo $g_view['cat_list'][$k]['type'];?>" <?php if($_POST['deal_cat_name']==$g_view['cat_list'][$k]['type']){?>selected="selected"<?php }?>><?php echo $g_view['cat_list'][$k]['type'];?></option>
	<?php
	}
	?>
	</select>
	</td>
	<td>
	<select name="deal_subcat1_name" id="deal_subcat1_name" onchange="return deal_subcat_changed();">
	<option value="">Select subtype</option>
	<?php
	for($i=0;$i<$g_view['subcat_count'];$i++){
	?>
	<option value="<?php echo $g_view['subcat_list'][$i]['subtype1'];?>" <?php if($_POST['deal_subcat1_name']==$g_view['subcat_list'][$i]['subtype1']){?>selected="selected"<?php }?>><?php echo $g_view['subcat_list'][$i]['subtype1'];?></option>
	<?php
	}
	?>
	</select>
	</td>
	<td>
	<select name="deal_subcat2_name" id="deal_subcat2_name">
	<option value="">Select sub subtype</option>
	<?php
	for($i=0;$i<$g_view['sub_subcat_count'];$i++){
	?>
	<option value="<?php echo $g_view['sub_subcat_list'][$i]['subtype2'];?>" <?php if($_POST['deal_subcat2_name']==$g_view['sub_subcat_list'][$i]['subtype2']){?>selected="selected"<?php }?>><?php echo $g_view['sub_subcat_list'][$i]['subtype2'];?></option>
	<?php
	}
	?>
	</select>
	</td>
	</tr>
	</table>
</td>
</tr>
<tr>
<td>
	<table cellspacing="0" cellpadding="5">
	<tr>
	<td>
	Year: <input name="year" type="text" style="width:80px;" value="<?php echo $_POST['year'];?>" />
	</td>
	<td>
	Value from: <input name="value_from" type="text" style="width:80px;" value="<?php echo $_POST['value_from'];?>" /> ($ bn)
	</td>
	<td>
	to: <input name="value_to" type="text" style="width:80px;" value="<?php echo $_POST['value_to'];?>" /> ($ bn)
	</td>
	<td style="text-align:right;"><input type="submit" name="submit" value="search" /></td>
	</tr>
	</table>
</td>
</tr>
</table>
</form>
</td>
</tr>

<?php
/*************
sng:20/jun/2011
support for jumping directly to deal record using the deal id. Basically this is search but gives only one record
****************/
?>
<tr>
<td>
<form method="post" action="">
<input type="hidden" name="action" value="search_deal_by_id" />
Do you know the deal id? Then type it here and jump to the record: <input type="text" name="deal_id" value="" />
<input type="submit" value="Search" />
</form>
</td>
</tr>
</table>
<script type="text/javascript" src="util.js"></script>
<table width="100%" cellpadding="5" cellspacing="0" border="1" style="border-collapse:collapse;">
<?php
if($g_view['msg']!=""){
?>
<tr>
<td colspan="9"><span class="msg_txt"><?php echo $g_view['msg'];?></span></td>
</tr>
<?php
}
?>
<tr bgcolor="#dec5b3" style="height:20px;">

<td><strong>Company Name</strong></td>
<td><strong>Date</strong></td>
<td><strong>Type</strong></td>
<td><strong>Deal Value<br />
(in billion) </strong></td>
<td><strong>Banks</strong></td>
<td><strong>Law firm</strong></td>
<td><strong>&nbsp;</strong></td>
<td>&nbsp;</td>
<td>&nbsp;</td>
</tr>
<?php
if($g_view['data_count']==0){
	?>
	<tr>
	  <td colspan="10">None found</td>
	</tr>
	<?php
}else{
	for($i=0;$i<$g_view['data_count'];$i++){
		?>
		<tr>
		<?php
		/************
		sng:14/feb/2012
		We now have multiple participants
		***************/
		?>
		<td><?php echo Util::deal_participants_to_csv($g_view['data'][$i]['participants']);?></td>
		<td><?php echo $g_view['data'][$i]['date_of_deal'];?></td>
		<td><?php echo $g_view['data'][$i]['deal_cat_name'];?> <?php echo $g_view['data'][$i]['deal_subcat1_name'];?> <?php echo $g_view['data'][$i]['deal_subcat2_name'];?></td>
		<?php
		/*************
		sng:14/feb/2012
		now we can have deal range instead of exact value
		**************/
		?>
		<td><?php if($g_view['data'][$i]['value_in_billion']!=0.0) echo $g_view['data'][$i]['value_in_billion'];else echo $g_view['data'][$i]['fuzzy_value'];?></td>
		<td>
		<?php
		$bank_cnt = count($g_view['data'][$i]['banks']);
		$banks_csv = "";
		if($bank_cnt > 0){
			for($banks_i=0;$banks_i<$bank_cnt;$banks_i++){
				$banks_csv.=", ".$g_view['data'][$i]['banks'][$banks_i]['name'];
			}
			$banks_csv = substr($banks_csv,1);
			echo $banks_csv;
		}else{
			?>
			None
			<?php
		}
		?><br />
		<a href="" onclick="return deal_bank_popup('<?php echo $g_view['data'][$i]['id'];?>');">Manage</a>
		<?php
		/****
		sng:25/may/2010
		if there is at least one bank, show add banker
		
		sng:05/dec/2011
		since we do not have members associated with deals in data-cx, we do not use it
		if($bank_cnt > 0){
			
			<br /><a href="" onclick="return deal_add_banker_popup('<?php echo $g_view['data'][$i]['id'];?>');">Add Banker</a>
			
		}
		***/
		?>
		</td>
		<td>
		<?php
		$law_csv = "";
		$law_cnt = count($g_view['data'][$i]['law_firms']);
		if($law_cnt > 0){
			for($law_i=0;$law_i<$law_cnt;$law_i++){
				$law_csv.=", ".$g_view['data'][$i]['law_firms'][$law_i]['name'];
			}
			$law_csv = substr($law_csv,1);
			echo $law_csv;
		}else{
			?>
			None
			<?php
		}
		
		?>
		<br />
		<a href="" onclick="return deal_lawfirm_popup('<?php echo $g_view['data'][$i]['id'];?>');">Manage</a>
		<?php
		/****
		sng:25/may/2010
		if there is at least one law firm, show add lawyer
		
		sng:05/dec/2011
		since we do not have members associated with deals in data-cx, we do not use it
		
		if($law_cnt > 0){
			
			<br /><a href="" onclick="return deal_add_lawyer_popup('<?php echo $g_view['data'][$i]['id'];?>');">Add Lawyer</a>
			
		}
		***/
		?>
		</td>
		<?php
		/************************************************************
		sng:25/feb/2011
		link to case studies for this deal
		
		sng:6/sep/2011
		link to documents for this deal
		*****************/
		?>
		<td>
		<form method="post" action="deal_case_study.php">
		<input type="hidden" name="transaction_id" value="<?php echo $g_view['data'][$i]['id'];?>" />
		<input type="submit" value="Case Studies" />
		</form>
		<br />
		<form method="post" action="deal_documents.php">
		<input type="hidden" name="transaction_id" value="<?php echo $g_view['data'][$i]['id'];?>" />
		<input type="submit" value="Documents" />
		</form>
		</td>
		<?php
		/********************************************************/
		?>
		<td>
		<form method="post" action="deal_edit.php">
		<input type="hidden" name="deal_id" value="<?php echo $g_view['data'][$i]['id'];?>" />
		<input type="submit" value="Edit" />
		</form>
		</td>
		<td>
		<form method="post" action="" onsubmit="return confirm_deletion();">
		<input type="hidden" name="action" value="del" />
		<input type="hidden" name="deal_id" value="<?php echo $g_view['data'][$i]['id'];?>" />
		
		<input type="hidden" name="company_name" value="<?php echo $g_mc->view_to_view($_POST['company_name']);?>" />
		<input type="hidden" name="sector" value="<?php echo $_POST['sector'];?>" />
		<input type="hidden" name="industry" value="<?php echo $_POST['industry'];?>" />
		<input type="hidden" name="region" value="<?php echo $_POST['region'];?>" />
		<input type="hidden" name="country" value="<?php echo $_POST['country'];?>" />
		<input type="hidden" name="deal_cat_name" value="<?php echo $_POST['deal_cat_name'];?>" />
		<input type="hidden" name="deal_subcat1_name" value="<?php echo $_POST['deal_subcat1_name'];?>" />
		<input type="hidden" name="deal_subcat2_name" value="<?php echo $_POST['deal_subcat2_name'];?>" />
		<input type="hidden" name="year" value="<?php echo $_POST['year'];?>" />
		<?php
		/****
		sng:13/nov/2010
		added 2 fields
		***/
		?>
		<input type="hidden" name="value_from" value="<?php echo $_POST['value_from'];?>" />
		<input type="hidden" name="value_to" value="<?php echo $_POST['value_to'];?>" />
		<input type="submit" value="delete" />
		</form>
		</td>
		</tr>
		<?php
	}
}
?>
</table>