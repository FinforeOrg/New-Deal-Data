<?php
/************************
sng:29/sep/2011
we now include jquery in the container view
<script type="text/javascript" src="js/jquery-1.2.1.pack.js"></script>

sng:29/jan/2013
deal subtype Additional is Secondaries
deal subtype IPO is IPOs
deal subtype Equity is Common Equity
******************************/
?>
<script type="text/javascript" src="js/datepicker.js"></script>
<link href="css/datepicker.css" rel="stylesheet" type="text/css" />

<script type="text/javascript">

function lookup(company_type,inputString) {

	if(inputString.length == 0) {
		// Hide the suggestion box.
		$('#deal_company_suggestions').hide();
	} else {
		// post data to our php processing page and if there is a return greater than zero
		// show the suggestions box
		$('#deal_company_name_searching').html("searching...");
		$.post("ajax/firm_list.php", {search_string: ""+inputString+"",type: ""+company_type+""}, function(data){
			$('#deal_company_name_searching').html("");
			if(data.length >0) {
				
				$('#deal_company_suggestions').show();
				$('#deal_company_suggestions_list').html(data);
			}
		});
	}
} //end

function fill(thisValue) {
	$('#deal_company_name').val(thisValue);
	setTimeout("$('#deal_company_suggestions').hide();", 200);
}

function target_lookup(company_type,inputString) {

	if(inputString.length == 0) {
		// Hide the suggestion box.
		$('#target_company_suggestions').hide();
	} else {
		// post data to our php processing page and if there is a return greater than zero
		// show the suggestions box
		$('#target_company_name_searching').html("searching...");
		$.post("ajax/target_firm_list.php", {search_string: ""+inputString+"",type: ""+company_type+""}, function(data){
			$('#target_company_name_searching').html("");
			if(data.length >0) {
				
				$('#target_company_suggestions').show();
				$('#target_company_suggestions_list').html(data);
			}
		});
	}
} //end

// if user clicks a suggestion, fill the text box.
function target_fill(thisValue) {
	$('#target_company_name').val(thisValue);
	setTimeout("$('#target_company_suggestions').hide();", 200);
}

function assoc_lookup(company_type,the_suffix,inputString){
	if(inputString.length == 0) {
		// Hide the suggestion box.
		var elem_id = "#"+company_type+the_suffix+"_suggestions";
		$(elem_id).hide();
	} else {
		// post data to our php processing page and if there is a return greater than zero
		// show the suggestions box
		var elem_id = "#"+company_type+the_suffix+"_searching";
		
		$(elem_id).html("searching...");
		$.post("ajax/assoc_firm_list.php", {search_string: ""+inputString+"",type: ""+company_type+"",suffix: ""+the_suffix+""}, function(data){
			var elem2_id = "#"+company_type+the_suffix+"_searching";
			$(elem2_id).html("");
			if(data.length >0) {
				var elem3_id = "#"+company_type+the_suffix+"_suggestions";
				$(elem3_id).show();
				var elem4_id = "#"+company_type+the_suffix+"_suggestions_list";
				$(elem4_id).html(data);
			}
		});
	}
}

function assoc_fill(company_type,the_suffix,thisValue) {
	var elem_id = "#"+company_type+the_suffix;
	$(elem_id).val(thisValue);
	var elem2_id = "#"+company_type+the_suffix+"_suggestions";
	f = function(){
		$(elem2_id).hide();
	};
	
	setTimeout("f()", 200);
}

function seller_lookup(company_type,inputString) {

	if(inputString.length == 0) {
		// Hide the suggestion box.
		$('#seller_company_suggestions').hide();
	} else {
		// post data to our php processing page and if there is a return greater than zero
		// show the suggestions box
		$('#seller_company_name_searching').html("searching...");
		$.post("ajax/seller_firm_list.php", {search_string: ""+inputString+"",type: ""+company_type+""}, function(data){
			$('#seller_company_name_searching').html("");
			if(data.length >0) {
				
				$('#seller_company_suggestions').show();
				$('#seller_company_suggestions_list').html(data);
			}
		});
	}
} //end

// if user clicks a suggestion, fill the text box.
function seller_fill(thisValue) {
	$('#seller_company_name').val(thisValue);
	setTimeout("$('#seller_company_suggestions').hide();", 200);
}
</script>
<script type="text/javascript">
function deal_cat_changed(){
	var type_obj = document.getElementById('deal_cat_name');
	var offset_selected = type_obj.selectedIndex;
	if(offset_selected != 0){
		var deal_cat_name_selected = type_obj.options[offset_selected].value;
		//fetch the list of deal sub categories
		$.post("admin/ajax/deal_subtype1_list.php", {deal_cat_name: ""+deal_cat_name_selected+""}, function(data){
				if(data.length >0) {
					$('#deal_subcat1_name').html(data);
					/***
					sng:9/aug/2010
					We change the subtype optons but what about the sub sub type? They can be selected only when subtype is selected.
					So we blank out sub sub type
					***/
					$('#deal_subcat2_name').html("<option value=\"\">Select</option>");
				}
		});
		show_hide_deal_type_specific_block();
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
		$.post("admin/ajax/deal_subtype2_list.php", {deal_cat_name: ""+deal_cat_name_selected+"",deal_subcat_name: ""+deal_subcat_name_selected+""}, function(data){
			//alert(data);
				if(data.length >0) {
					$('#deal_subcat2_name').html(data);
				}
		});
		show_hide_deal_subtype_specific_block();
	}
}

function deal_subsubcat_changed(){
	var type_obj = document.getElementById('deal_cat_name');
	var offset_selected = type_obj.selectedIndex;
	var type1_obj = document.getElementById('deal_subcat1_name');
	var offset1_selected = type1_obj.selectedIndex;
	var type2_obj = document.getElementById('deal_subcat2_name');
	var offset2_selected = type2_obj.selectedIndex;
	if((offset_selected != 0)&&(offset1_selected!=0)&&(offset2_selected!=0)){
		show_hide_deal_subsubtype_specific_block()
	}
}

function hide_all_deal_specific_blocks(){
	document.getElementById("debt_specific").className = "opt_hide";
	$("#debt_specific a.date-picker-control").css("visibility","hidden");
	document.getElementById("ma_specific").className = "opt_hide";
	document.getElementById("equity_ipo_specific").className = "opt_hide";
	document.getElementById("equity_additional_specific").className = "opt_hide";
	document.getElementById("equity_rights_issue_specific").className = "opt_hide";
}
function show_hide_deal_type_specific_block(){
	//deal type changed
	var deal_type = $('#deal_cat_name').val();
	if(deal_type.toLowerCase() == "debt"){
		hide_all_deal_specific_blocks();
		document.getElementById("debt_specific").className = "opt_show";
		$("#debt_specific a.date-picker-control").css("visibility","visible");
		return;
	}
	if(deal_type.toLowerCase() == "equity"){
		hide_all_deal_specific_blocks();
		return;
	}
	if(deal_type.toLowerCase() == "m&a"){
		hide_all_deal_specific_blocks();
		document.getElementById("ma_specific").className = "opt_show";
		return;
	}
}

function show_hide_deal_subtype_specific_block(){
	hide_all_deal_specific_blocks();
	var deal_type = $('#deal_cat_name').val();
	if(deal_type.toLowerCase() == "debt"){
		hide_all_deal_specific_blocks();
		document.getElementById("debt_specific").className = "opt_show";
		$("#debt_specific a.date-picker-control").css("visibility","visible");
		return;
	}
	if(deal_type.toLowerCase() == "m&a"){
		hide_all_deal_specific_blocks();
		document.getElementById("ma_specific").className = "opt_show";
		return;
	}
}
function show_hide_deal_subsubtype_specific_block(){
	
	hide_all_deal_specific_blocks();
	var deal_type = $('#deal_cat_name').val();
	deal_type = deal_type.toLowerCase();
	var deal_subtype = $('#deal_subcat1_name').val();
	deal_subtype = deal_subtype.toLowerCase();
	var deal_subsubtype = $('#deal_subcat2_name').val();
	deal_subsubtype = deal_subsubtype.toLowerCase();
	
	if(deal_type=="equity"){
		if(deal_subtype=="common equity"){
			if(deal_subsubtype=="ipos"){
				document.getElementById("equity_ipo_specific").className = "opt_show";
				return;
			}
			if(deal_subsubtype=="secondaries"){
				document.getElementById("equity_additional_specific").className = "opt_show";
				return;
			}
			if(deal_subsubtype=="rights issue"){
				document.getElementById("equity_rights_issue_specific").className = "opt_show";
				return;
			}
			return;
		}
		//for other equity subtypes, no need to show
		return;
	}
	if(deal_type=="debt"){
		document.getElementById("debt_specific").className = "opt_show";
		$("#debt_specific a.date-picker-control").css("visibility","visible");
		return;
	}
	if(deal_type=="m&a"){
		document.getElementById("ma_specific").className = "opt_show";
		return;
	}
}
function show_hide_all_deal_specific_blocks(){
	/***
	sng:9/aug/2010
	if there is validation error, we also need to show these depending on
	deal type/subtype/sub sub type selected
	***/
	hide_all_deal_specific_blocks();
	var deal_type = "<?php echo $g_view['input']['deal_cat_name'];?>";
	deal_type = deal_type.toLowerCase();
	var deal_subtype = "<?php echo $g_view['input']['deal_subcat1_name'];?>";
	deal_subtype = deal_subtype.toLowerCase();
	var deal_subsubtype = "<?php echo $g_view['input']['deal_subcat2_name'];?>";
	deal_subsubtype = deal_subsubtype.toLowerCase();
	
	if(deal_type=="debt"){
		document.getElementById("debt_specific").className = "opt_show";
		$("#debt_specific a.date-picker-control").css("visibility","visible");
		return;
	}
	if(deal_type=="m&a"){
		document.getElementById("ma_specific").className = "opt_show";
		return;
	}
	if(deal_type=="equity"){
		if(deal_subtype=="common equity"){
			if(deal_subsubtype=="ipos"){
				document.getElementById("equity_ipo_specific").className = "opt_show";
				return;
			}
			if(deal_subsubtype=="secondaries"){
				document.getElementById("equity_additional_specific").className = "opt_show";
				return;
			}
			if(deal_subsubtype=="rights issue"){
				document.getElementById("equity_rights_issue_specific").className = "opt_show";
				return;
			}
			return;
		}
		//for other equity subtypes, no need to show
		return;
	}
	
}
</script>
<table width="100%" border="0" cellpadding="0" cellspacing="0" class="register" style="width: 700px; margin: 0 auto;">
<tr>
<td>
	<table width="100%" border="0" cellpadding="0" cellspacing="0" class="registerinner">
	<tr>
	<td>
		<table width="100%" border="0" cellpadding="0" cellspacing="0" class="registercontent">
			<tr>
				<th>Deal Data</th>
			</tr>
			<tr>
				<td><span class="msg_txt"><?php echo $g_view['msg'];?></span></td>
			</tr>
			<tr>
				<td>
				<form method="post" action="">
				<input type="hidden" name="action" value="suggest"/>
				<table width="100%" border="0" cellspacing="0" cellpadding="0" style="width: auto;">
				<tr>
				<td colspan="3"><span class="err_txt">* </span> denotes mandatory information</td>
				<tr>
				<?php
				/***
				sng:16/aug/2010
				apart from the specific error messages below the fields, client wants a general error message
				***/
				?>
				<tr>
				<td colspan="3"><span class="err_txt"><?php echo $g_view['err_msg'];?></span></td>
				<tr>
				<tr><td colspan="3" style="height:7">&nbsp;</td></tr>
				
				<td>Company :</td>
				<td style="width:15px;">&nbsp;</td>
				<td><input name="deal_company_name" id="deal_company_name" onkeyup="lookup('company',this.value);" onblur="fill();" type="text" class="txtbox" value="<?php echo $g_view['input']['deal_company_name'];?>" /><span class="err_txt"> *</span><br /><span id="deal_company_name_searching"></span><br />
				<span class="err_txt"><?php echo $g_view['err']['deal_company_name'];?></span>
				<div class="suggestionsBox" id="deal_company_suggestions" style="display: none;">
				<img src="images/upArrow.png" style="position: relative; top: -18px; left: 30px;" alt="upArrow"  />
				<div class="suggestionList" id="deal_company_suggestions_list"></div>
				</div>
				</td>
				</tr>
				
				<tr><td colspan="3" style="height:7">&nbsp;</td></tr>
				
				<tr>
				<td>Deal Value :</td>
				<td style="width:15px;">&nbsp;</td>
				<td><input name="value_in_billion" type="text" style="width:200px;" value="<?php echo $g_view['input']['value_in_billion'];?>" /><span class="err_txt"> *</span>(in billion USD)<br />
				(Type 0 if the deal value is unknown or not disclosed)<br />
<span class="err_txt"><?php echo $g_view['err']['value_in_billion'];?></span></td>
				</tr>
				<tr><td colspan="3" style="height:7">&nbsp;</td></tr>
				
				<tr>
				<td>Date of deal :</td>
				<td style="width:15px;">&nbsp;</td>
				<td>
				<input name="date_of_deal" id="date_of_deal" type="text" style="width:200px;" value="<?php echo $g_view['input']['date_of_deal'];?>" /><span class="err_txt"> *</span><br />
				<span class="err_txt"><?php echo $g_view['err']['date_of_deal'];?></span>
				<script type="text/javascript">
				// <![CDATA[       
				var opts = {                            
				formElements:{"date_of_deal":"Y-ds-m-ds-d"},
				showWeeks:false                    
				};      
				datePickerController.createDatePicker(opts);
				// ]]>
				</script>
				</td>
				</tr>
				<tr><td colspan="3" style="height:7">&nbsp;</td></tr>
				
				<tr><td colspan="3"><span class="err_txt"> (Only for deals in non USD)</span></td></tr>
				<tr><td colspan="3" style="height:7">&nbsp;</td></tr>
				<tr>
				<td>Deal Currency :</td>
				<td style="width:15px;">&nbsp;</td>
				<td><input name="currency" type="text" style="width:200px;" value="<?php echo $g_view['input']['currency'];?>" /><br />
				(ex: USD, EUR, JPY, GBP)</td>
				</tr>
				<tr><td colspan="3" style="height:7">&nbsp;</td></tr>
				
				<tr>
				<td>Exchange Rate :</td>
				<td style="width:15px;">&nbsp;</td>
				<td><input name="exchange_rate" type="text" style="width:200px;" value="<?php echo $g_view['input']['exchange_rate'];?>" /> (used to convert to USD)</td>
				</tr>
				<tr><td colspan="3" style="height:7">&nbsp;</td></tr>
				
				<tr>
				<td>Deal Value <br />
				  in specified currency :</td>
				<td style="width:15px;">&nbsp;</td>
				<td><input name="value_in_billion_local_currency" type="text" style="width:200px;" value="<?php echo $g_view['input']['value_in_billion_local_currency'];?>" /> (in billion)</td>
				</tr>
				<tr><td colspan="3" style="height:7">&nbsp;</td></tr>

				<tr><td colspan="3"><hr noshade="noshade" /></td></tr>
				<tr><td colspan="3" style="height:7">&nbsp;</td></tr>
				
				<?php
				/***
				sng:6/aug/2010
				extra field
				**/
				?>
				<tr>
				<td>Base Fee :</td>
				<td style="width:15px;">&nbsp;</td>
				<td><input name="base_fee" type="text" style="width:200px;" value="<?php echo $g_view['input']['base_fee'];?>" /> %</td>
				</tr>
				<tr><td colspan="3" style="height:7">&nbsp;</td></tr>
				
				<tr>
				<td>Incentive Fee :</td>
				<td style="width:15px;">&nbsp;</td>
				<td><input name="incentive_fee" type="text" style="width:200px;" value="<?php echo $g_view['input']['incentive_fee'];?>" /> %</td>
				</tr>
				<tr><td colspan="3" style="height:7">&nbsp;</td></tr>
				
				
				<tr>
				<td style="vertical-align:text-top">Note :</td>
				<td style="width:15px;">&nbsp;</td>
				<td>
				<textarea name="deal_note" style="width:300px; height:100px;"><?php echo $g_view['input']['deal_note'];?></textarea>
				</td>
				</tr>
				<tr><td colspan="3" style="height:7">&nbsp;</td></tr>
				
				<tr>
				<td style="vertical-align:text-top">Sources :</td>
				<td style="width:15px;">&nbsp;</td>
				<td>
				<textarea name="deal_sources" style="width:300px; height:100px;"><?php echo $g_view['input']['deal_sources'];?></textarea><br />
				(Type one or more urls separated by comma (where you have found info about this deal)
				</td>
				</tr>
				<tr><td colspan="3" style="height:7">&nbsp;</td></tr>
				<tr><td colspan="3"><hr noshade="noshade" /></td></tr>
				<tr><td colspan="3" style="height:7">&nbsp;</td></tr>
				<!--/////////////////////////////////////////////////////////////-->
				
				<tr>
				<td>Category of deal :</td>
				<td style="width:15px;">&nbsp;</td>
				<td>
				<select name="deal_cat_name" id="deal_cat_name" onchange="return deal_cat_changed();">
				<option value="">Select</option>
				<?php
				for($i=0;$i<$g_view['cat_count'];$i++){
				?>
				<option value="<?php echo $g_view['cat_list'][$i]['type'];?>" <?php if($g_view['input']['deal_cat_name']==$g_view['cat_list'][$i]['type']){?>selected="selected"<?php }?>><?php echo $g_view['cat_list'][$i]['type'];?></option>
				<?php
				}
				?>
				</select><span class="err_txt"> *</span><br />
				<span class="err_txt"><?php echo $g_view['err']['deal_cat_name'];?></span>
				</td>
				</tr>
				<tr><td colspan="3" style="height:7">&nbsp;</td></tr>
				
				<tr>
				<td>Sub category of deal :</td>
				<td style="width:15px;">&nbsp;</td>
				<td>
				<select name="deal_subcat1_name" id="deal_subcat1_name" onchange="return deal_subcat_changed();">
				<option value="">Select</option>
				<?php
				for($i=0;$i<$g_view['subcat1_count'];$i++){
				?>
				<option value="<?php echo $g_view['subcat1_list'][$i]['subtype1'];?>" <?php if($g_view['input']['deal_subcat1_name']==$g_view['subcat1_list'][$i]['subtype1']){?>selected="selected"<?php }?>><?php echo $g_view['subcat1_list'][$i]['subtype1'];?></option>
				<?php
				}
				?>
				</select><span class="err_txt"> *</span><br />
				<span class="err_txt"><?php echo $g_view['err']['deal_subcat1_name'];?></span>
				</td>
				</tr>
				<tr><td colspan="3" style="height:7">&nbsp;</td></tr>
				
				<tr>
				<td>Sub sub category of deal :</td>
				<td style="width:15px;">&nbsp;</td>
				<td>
				<select name="deal_subcat2_name" id="deal_subcat2_name" onchange="return deal_subsubcat_changed();">
				<option value="">Select</option>
				<?php
				for($i=0;$i<$g_view['subcat2_count'];$i++){
				?>
				<option value="<?php echo $g_view['subcat2_list'][$i]['subtype2'];?>" <?php if($g_view['input']['deal_subcat2_name']==$g_view['subcat2_list'][$i]['subtype2']){?>selected="selected"<?php }?>><?php echo $g_view['subcat2_list'][$i]['subtype2'];?></option>
				<?php
				}
				?>
				</select><span class="err_txt"> *</span><br />
				<span class="err_txt"><?php echo $g_view['err']['deal_subcat2_name'];?></span>
				</td>
				</tr>
				<tr><td colspan="3" style="height:7">&nbsp;</td></tr>
				
				<!--/////////////////////////////////////////////////////////////
				sng:7/aug/2010
				some fields are optional and are relevant only with certain deal type
				So, we hide them initially and make them visible when selection change
				
				//////////////////////////////////////////////////////////////////-->
				<tr>
				<td colspan="3">
					<div id="debt_specific" class="opt_hide">
					<table>
						<tr>
						<td>Coupon :</td>
						<td style="width:15px;">&nbsp;</td>
						<td>
						<input name="coupon" type="text" style="width:200px;" value="<?php if($g_view['input']['coupon']==""){?>n/a<?php }else{echo $g_view['input']['coupon'];}?>" />
						</td>
						</tr>
						<tr><td colspan="3" style="height:7">&nbsp;</td></tr>
						
						<tr>
						<td>Maturity Date :</td>
						<td style="width:15px;">&nbsp;</td>
						<td>
						<input name="maturity_date" id="maturity_date" type="text" style="width:200px;" value="<?php echo $g_view['input']['maturity_date'];?>" /> (select if applicable)
						<script type="text/javascript">
						// <![CDATA[       
						var opts = {                            
						formElements:{"maturity_date":"Y-ds-m-ds-d"},
						showWeeks:false                    
						};      
						datePickerController.createDatePicker(opts);
						// ]]>
						</script>
						</td>
						</tr>
						<tr><td colspan="3" style="height:7">&nbsp;</td></tr>
						
						<tr>
						<td>Current Rating :</td>
						<td style="width:15px;">&nbsp;</td>
						<td>
						<input name="current_rating" type="text" style="width:200px;" value="<?php echo $g_view['input']['current_rating'];?>" /><br />
						(ex: Moodys AAA, Fitch A3)
						</td>
						</tr>
						<tr><td colspan="3" style="height:7">&nbsp;</td></tr>
						
					</table>
					</div>
					<div id="ma_specific" class="opt_hide">
					<table>
						<tr>
						<td>Target Company :</td>
						<td style="width:15px;">&nbsp;</td>
						<td>
						<input name="target_company_name" id="target_company_name" onkeyup="target_lookup('company',this.value);" onblur="target_fill();" type="text" class="txtbox" value="<?php echo $g_view['input']['target_company_name'];?>" /><br /><span id="target_company_name_searching"></span><br />
						<span class="err_txt"><?php echo $g_view['err']['target_company_name'];?></span>
						<div class="suggestionsBox" id="target_company_suggestions" style="display: none;">
						<img src="images/upArrow.png" style="position: relative; top: -18px; left: 30px;" alt="upArrow"  />
						<div class="suggestionList" id="target_company_suggestions_list"></div>
						</div>
						</td>
						</tr>
						<tr><td colspan="3" style="height:7">&nbsp;</td></tr>
						
						<tr>
						<td>Country :</td>
						<td style="width:15px;">&nbsp;</td>
						<td>
						<select name="target_country">
						<option value="">Select</option>
						<?php
						for($i=0;$i<$g_view['country_count'];$i++){
						?>
						<option value="<?php echo $g_view['country_list'][$i]['name'];?>" <?php if($g_view['input']['target_country']==$g_view['country_list'][$i]['name']){?>selected="selected"<?php }?>><?php echo $g_view['country_list'][$i]['name'];?></option>
						<?php
						}
						?>
						</select><br />
						<span class="err_txt"><?php echo $g_view['err']['target_country'];?></span>
						</td>
						</tr>
						<tr><td colspan="3" style="height:7">&nbsp;</td></tr>
						
						<tr>
						<td>Sector :</td>
						<td style="width:15px;">&nbsp;</td>
						<td>
						<select name="target_sector">
						<option value="">Select</option>
						<?php
						for($i=0;$i<$g_view['sector_count'];$i++){
						?>
						<option value="<?php echo $g_view['sector_list'][$i]['sector'];?>" <?php if($g_view['input']['target_sector']==$g_view['sector_list'][$i]['sector']){?>selected="selected"<?php }?>><?php echo $g_view['sector_list'][$i]['sector'];?></option>
						<?php
						}
						?>
						</select><br />
						<span class="err_txt"><?php echo $g_view['err']['target_sector'];?></span>
						</td>
						</tr>
						<tr><td colspan="3" style="height:7">&nbsp;</td></tr>
						
						<tr>
						<td colspan="3">In case the target is a part or division of a larger company</td>
						</tr>
						<tr>
						<td>Seller Company :</td>
						<td style="width:15px;">&nbsp;</td>
						<td>
						<input name="seller_company_name" id="seller_company_name" onkeyup="seller_lookup('company',this.value);" onblur="seller_fill();" type="text" class="txtbox" value="<?php echo $g_view['input']['seller_company_name'];?>" /><br /><span id="seller_company_name_searching"></span><br />
						<span class="err_txt"><?php echo $g_view['err']['seller_company_name'];?></span>
						<div class="suggestionsBox" id="seller_company_suggestions" style="display: none;">
						<img src="images/upArrow.png" style="position: relative; top: -18px; left: 30px;" alt="upArrow"  />
						<div class="suggestionList" id="seller_company_suggestions_list"></div>
						</div>
						</td>
						</tr>
						<tr><td colspan="3" style="height:7">&nbsp;</td></tr>
						
						<tr>
						<td>Seller Country :</td>
						<td style="width:15px;">&nbsp;</td>
						<td>
						<select name="seller_country">
						<option value="">Select</option>
						<?php
						for($i=0;$i<$g_view['country_count'];$i++){
						?>
						<option value="<?php echo $g_view['country_list'][$i]['name'];?>" <?php if($g_view['input']['seller_country']==$g_view['country_list'][$i]['name']){?>selected="selected"<?php }?>><?php echo $g_view['country_list'][$i]['name'];?></option>
						<?php
						}
						?>
						</select><br />
						<span class="err_txt"><?php echo $g_view['err']['seller_country'];?></span>
						</td>
						</tr>
						<tr><td colspan="3" style="height:7">&nbsp;</td></tr>
						
						<tr>
						<td>Seller Sector :</td>
						<td style="width:15px;">&nbsp;</td>
						<td>
						<select name="seller_sector">
						<option value="">Select</option>
						<?php
						for($i=0;$i<$g_view['sector_count'];$i++){
						?>
						<option value="<?php echo $g_view['sector_list'][$i]['sector'];?>" <?php if($g_view['input']['seller_sector']==$g_view['sector_list'][$i]['sector']){?>selected="selected"<?php }?>><?php echo $g_view['sector_list'][$i]['sector'];?></option>
						<?php
						}
						?>
						</select><br />
						<span class="err_txt"><?php echo $g_view['err']['seller_sector'];?></span>
						</td>
						</tr>
						<tr><td colspan="3" style="height:7">&nbsp;</td></tr>
						
						
						<tr>
						<td>EV/EBITDA LTM</td>
						<td style="width:15px;">&nbsp;</td>
						<td>
						<input name="ev_ebitda_ltm" type="text" style="width:200px;" value="<?php echo $g_view['input']['ev_ebitda_ltm'];?>" />
						</td>
						</tr>
						<tr><td colspan="3" style="height:7">&nbsp;</td></tr>
						
						<tr>
						<td>EV/EBITDA +1yr</td>
						<td style="width:15px;">&nbsp;</td>
						<td>
						<input name="ev_ebitda_1yr" type="text" style="width:200px;" value="<?php echo $g_view['input']['ev_ebitda_1yr'];?>" />
						</td>
						</tr>
						<tr><td colspan="3" style="height:7">&nbsp;</td></tr>
						
						<tr>
						<td>Premia (30 days)</td>
						<td style="width:15px;">&nbsp;</td>
						<td>
						<input name="30_days_premia" type="text" style="width:200px;" value="<?php echo $g_view['input']['30_days_premia'];?>" /> %
						</td>
						</tr>
						<tr><td colspan="3" style="height:7">&nbsp;</td></tr>
					</table>
					</div>
					<div id="equity_ipo_specific" class="opt_hide">
					<table>
						<tr>
						<td>1 day price change :</td>
						<td style="width:15px;">&nbsp;</td>
						<td>
						<input name="1_day_price_change" type="text" style="width:200px;" value="<?php echo $g_view['input']['1_day_price_change'];?>" /> (%)
						</td>
						</tr>
						<tr><td colspan="3" style="height:7">&nbsp;</td></tr>
					</table>
					</div>
					<div id="equity_additional_specific" class="opt_hide">
					<table>
						<tr>
						<td>Discount to last :</td>
						<td style="width:15px;">&nbsp;</td>
						<td>
						<input name="discount_to_last" type="text" style="width:200px;" value="<?php echo $g_view['input']['discount_to_last'];?>" /> (%)
						</td>
						</tr>
						<tr><td colspan="3" style="height:7">&nbsp;</td></tr>
					</table>
					</div>
					<div id="equity_rights_issue_specific" class="opt_hide">
					<table>
						<tr>
						<td>Discount to TERP :</td>
						<td style="width:15px;">&nbsp;</td>
						<td>
						<input name="discount_to_terp" type="text" style="width:200px;" value="<?php echo $g_view['input']['discount_to_terp'];?>" /> (%)
						</td>
						</tr>
						<tr><td colspan="3" style="height:7">&nbsp;</td></tr>
					</table>
					</div>
				</td>
				</tr>
				<!--////////////////////////////////////////////////////////////////////-->
				
				
				<!--////////////////////////////////////////////////////////////////////-->
				
				<!--////////////////////////////////////////////////////////////////////-->
				
				<!--////////////////////////////////////////////////////////////////////-->
				
				
				
				
				<!--//////////////////////////////banks///////////////////////////////////////////////-->
				<tr>
				<th colspan="3">Banks involved in the deal</th>
				</tr>
				<tr><td colspan="3" style="height:7">&nbsp;</td></tr>
				<tr>
				<td colspan="3">
					<table width="100%" cellpadding="0" cellspacing="10">
						<tr>
						<?php
						$col_count = 0;
						$suffix = 1;
						$max_input = 9;
						while($suffix <= $max_input){
							?>
							<td>Bank <?php echo $suffix;?></td>
							<td>
							<?php
							$input_name = "bank".$suffix;
							?>
							<input name="<?php echo $input_name;?>" id="<?php echo $input_name;?>" type="text" style="width:150px;" value="<?php echo $g_view['input'][$input_name];?>" onkeyup="assoc_lookup('bank',<?php echo $suffix;?>,this.value);" onblur="assoc_fill('bank',<?php echo $suffix;?>);" /><br />
							<span id="<?php echo $input_name;?>_searching"></span><br />
							<div class="suggestionsBox" id="<?php echo $input_name;?>_suggestions" style="display: none;">
							<img src="images/upArrow.png" style="position: relative; top: -18px; left: 30px;" alt="upArrow"  />
							<div class="suggestionList" id="<?php echo $input_name;?>_suggestions_list"></div>
							</div>
							</td>
							<?php
							$col_count++;
							if($col_count == 3){
								?>
								</tr>
								<tr>
								<?php
								$col_count = 0;
							}
							$suffix++;
						}
						?>
						</tr>
					</table>
				</td>
				</tr>
				<tr><td colspan="3" style="height:7">&nbsp;</td></tr>
				<!--//////////////////////////////banks///////////////////////////////////////////////-->
				<!--//////////////////////////////law firms///////////////////////////////////////////////-->
				<tr>
				<th colspan="3">Law Firms involved in the deal</th>
				</tr>
				<tr><td colspan="3" style="height:7">&nbsp;</td></tr>
				<tr>
				<td colspan="3">
					<table width="100%" cellpadding="0" cellspacing="10">
						<tr>
						<?php
						$col_count = 0;
						$suffix = 1;
						$max_input = 9;
						while($suffix <= $max_input){
							?>
							<td>Law Firm <?php echo $suffix;?></td>
							<td>
							<?php
							$input_name = "law_firm".$suffix;
							?>
							<input name="<?php echo $input_name;?>" id="<?php echo $input_name;?>" type="text" style="width:120px;" value="<?php echo $g_view['input'][$input_name];?>" onkeyup="assoc_lookup('law_firm',<?php echo $suffix;?>,this.value);" onblur="assoc_fill('law_firm',<?php echo $suffix;?>);" /><br />
							<span id="<?php echo $input_name;?>_searching"></span><br />
							<div class="suggestionsBox" id="<?php echo $input_name;?>_suggestions" style="display: none;">
							<img src="images/upArrow.png" style="position: relative; top: -18px; left: 30px;" alt="upArrow"  />
							<div class="suggestionList" id="<?php echo $input_name;?>_suggestions_list"></div>
							</div>
							</td>
							<?php
							$col_count++;
							if($col_count == 3){
								?>
								</tr>
								<tr>
								<?php
								$col_count = 0;
							}
							$suffix++;
						}
						?>
						</tr>
					</table>
				</td>
				</tr>
				<tr><td colspan="3" style="height:7">&nbsp;</td></tr>
				<!--//////////////////////////////law firms///////////////////////////////////////////////-->
				<tr>
				<td colspan="2">&nbsp;</td>
				<td>
				<input type="submit" name="submit" class="btn_register" value="Submit" />
				</td>
				</tr>
				<tr><td colspan="3" style="height:7">&nbsp;</td></tr>
				
				
				</table>
				</form>
				</td>
			</tr>
		</table>
	</td>
	</tr>
	</table>
</td>
</tr>
</table>
<script type="text/javascript">
show_hide_all_deal_specific_blocks();
</script>