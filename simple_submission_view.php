<?php
/************
sng:17/jan/2012

simple submission view, embedded in home page
*******************/
?>
<style type="text/css">
    .std {
        width:370px;
        margin:5px;
        border: 1px solid #CCCCCC;      
    }
    hr.gray {
        color: #CCCCCC; background-color: #CCCCCC; height: 1px; margin-top: 15px; margin-bottom: 15px; display: block;
    }
    .special {
        color: #CCC;
        font-style: italic;
    }
    .black {
        color: #000;
        font-style: normal;
    }
    
    .list-item {
        width: 40%;
        float: left;
        margin-top: 10px;
        padding-left: 10px;
    }
    
    .invalidValueField {
        background: url("images/exclamation.png") center right no-repeat;
        
    } 
	.ui-datepicker-trigger {
        margin-left:5px;
        cursor:pointer;
    }   
</style>
<script>
<?php
/************************
do we need to alert when the user starts to enter deal data?
triggered when category or subcategory button is clicked

do we trigger search when cat or sub cat change?
**/
require_once("classes/class.account.php");
if(!$g_account->is_site_member_logged()){
?>
var _need_data_submission_alert = true;
var _trigger_category_change_notification = false;
<?php
}else{
?>
var _need_data_submission_alert = false;
var _trigger_category_change_notification = true;
<?php
}
/*****************************/
?>
function data_submission_alert(){
	if(_need_data_submission_alert){
		apprise("Please log-in before you enter / submit new data, many thanks.",{'textOk':'OK'});
		//we have alerted so no need to do this again
		_need_data_submission_alert = false;
	}
}
</script>
<script>
//we start with 2 url input box, so next one will be 3
var _current_url_num = 3;
var _url_markup = '';
var _default_url_input = 'http://';

var _currentBankNum  = 4;
var _currentLawFirmNum  = 4;
var _bankMarkup = ''
var _bankMarkup = ''

var _current_company_num = 4;
var _company_markup = '';

var _defaultInputs = new Array(); 
_defaultInputs['regulatory_links1'] = 'http://'; 
_defaultInputs['regulatory_links2'] = 'http://';
_defaultInputs['deal_value'] = 'e.g. 100 to indicate $100m';

var _lastClickedButton = 'Pending';

function reinitialize() {
	$(function() {
		for (index in _defaultInputs) {
			//console.log(index);
			if ($("#" + index).val() == '') {
                if (!$("#" + index).hasClass('special')) {
                    $("#" + index).addClass('special');    
                }
				$("#" + index).val(_defaultInputs[index])	
			}
		}
	});
		
    $('input.special').click(function(event) {
        if ($(this).val() == _defaultInputs[$(this).attr('id')]) {
            $(this).val('');
            $(this).addClass('black');
            
        }        
    })

    $('input.special').blur(function(event) {
        if ($(this).val() == '') {
            $(this).removeClass('black');
            $(this).val(_defaultInputs[$(this).attr('id')]);
        }        
    })
    
             
}

$(function() {
	
	var _changes =  {
	}
	_changes['Preferred'] = _changes['Convertible'];
	reinitialize();
	 
	function updateFormFields() {
        //console.log(_lastClickedButton);
        if (undefined != _changes[_lastClickedButton]) {
            for (itemName in _changes[_lastClickedButton]['edit']) {
                $('#' + itemName).text(_changes[_lastClickedButton]['edit'][itemName]);
            }
            for (itemName in _changes[_lastClickedButton]['delete']) {
                //console.log('#' + _selectedSubCategory]['delete'][itemName]);
                $('#' + _changes[_lastClickedButton]['delete'][itemName]).hide();
            }
            for (itemName in _changes[_lastClickedButton]['load']) {
                $('#' + itemName).load(_changes[_lastClickedButton]['load'][itemName], function(){reinitialize();});
            }
            for (itemName in _changes[_lastClickedButton]['show']) {
                //console.log('#' + _selectedSubCategory]['delete'][itemName]);
                $('#' + _changes[_lastClickedButton]['show'][itemName]).show();
            }
                                    
        }
    }
	
	/***********************************************************************
	Do the deal type magick
	****/
    //$( ".radio" ).buttonset().click(function(idx){console.log('asdasd');$( ".radio" ).buttonset('refresh')});  ;    
    $( ".SS_radio_subcat" ).buttonset().click(function(){
        $( ".SS_radio_subsubcat :radio" ).each(function(idx){
            $(this).removeAttr('checked');
        })
        $( ".SS_radio_subsubcat" ).buttonset('refresh')        
    }).change(function(){
        //console.log($(this).find('input:checked').val())
        //_selectedSubCategory = $(this).find('input:checked').val();
        _lastClickedButton = $(this).find('input:checked').val();
        
        updateFormFields();
    }); 
       
    $( ".SS_radio_cat" ).buttonset().click(function(){
        $( ".SS_radio_subcat :radio" ).each(function(idx){
            $(this).removeAttr('checked');
        })
        $( ".SS_radio_subcat" ).buttonset('refresh')
    }).change(function(){
        _lastClickedButton = $(this).find('input:checked').val();
        updateFormFields();
    });  
    $( ".SS_radio_subsubcat" ).buttonset().change(function(){
        _lastClickedButton = $(this).find('input:checked').val();
        updateFormFields();
        //console.log($(this).find('input:checked').val())
    });;
    
    /** 
    * Handle the case when a hidden sub sub cat is checked 
    */
    $('#LT_cats :radio').click(function() {
        $('#LT_cats :checked').each(function(idx){
            if (!$(this).is(':visible')) {
                $(this).removeAttr('checked');
            }
        })         
    });
	/***********************************************************************/
	$('#add_url_btn').button().click(function(){
        $('#url_list').append(get_url_markup());
		set_url_inputs_defaults();
    });
	/************************************************************************/
	$('#add_banks_btn').button().click(function(){
        $('#banks').append(getBankMarkup());
        initAutocompleteForBanksAndLawFirms();
    });
	
	$('#add_law_firms_btn').button().click(function(){
        $('#law_firms').append(getLawFirmMarkup());
        initAutocompleteForBanksAndLawFirms();
    });
	
	initAutocompleteForBanksAndLawFirms();
	/************************************************************************/
	$('#deal_value_range').buttonset();
	/************************************************************************/
	$('#add_companies_btn').button().click(function(){
        $('#companies').append(get_company_markup());
		/***************
		sng:7/feb/2012
		For companies, we want to show the HQ, sector, industry in the suggestion dropdown
		Problem is, this is not working for dynamically added testbox
		initAutocompleteForCompanies();
		*****************/
        
    });
	initAutocompleteForCompanies();
	/************************************************************************/
	$('#deal_date_hint').buttonset();
	
	$( "input.date" ).datepicker({
		dateFormat: "dd/M/yy",
        showOtherMonths: true,
        selectOtherMonths: true,
        showOn: "button",
        buttonImage: "images/calendar.png",
        buttonImageOnly: true
    });
	/**************************************************************************/
});
</script>

<script>
/********
sng:25/jan/2012
support for sending notification to other part of the script that
category/sub category/sub sub category has changed

When cat/sub cat/sub sub cat change, we update the 3 vars and then call a function notify_category_change
This function checks if a listener function has been registered or not (_category_change_listener)
If so, call that function using the function var.

We have a function which can be used by another part of the code to register its own listener.

initially nothing is selected
************/
var _curr_cat = "";
var _curr_sub_cat = "";
var _curr_sub_sub_cat = "";
var _category_change_listener;

function set_category_change_listener(f){
	_category_change_listener = f;
}
function notify_category_change(){

	if(_category_change_listener != undefined){
		if(_trigger_category_change_notification){
			_category_change_listener(_curr_cat,_curr_sub_cat,_curr_sub_sub_cat);
		}
	}
	/************************
	sng:12/mar/2012
	Now let us update any code here
	**********************/
	update_note_box_label();
}

function SS_categoryChanged(category) {

    $('div.SS_radio_subcat:visible').hide();  
    $('#SS_subCatsForCat' + category).show();
    $('div.SS_radio_subsubcat:visible').hide();
	data_submission_alert();
	//category changed, we need to select sub and sub sub category
	_curr_cat = $('#SS_deal_cat_name'+category).val();
	_curr_sub_cat = "";
	_curr_sub_sub_cat = "";
	notify_category_change();
}
function SS_subCategoryChanged(subCategory) {

    $('div.SS_radio_subsubcat:visible').hide();
    $('#SS_subSubCatsForCat' + subCategory).show();  
	//sub cat changed, we need to select sub sub category
	_curr_sub_cat = $('#SS_deal_subCat_name'+subCategory).val();
	_curr_sub_sub_cat = "";
	notify_category_change();
}
function SS_subSubCategoryChanged(subSubCategory){

	_curr_sub_sub_cat = $('#SS_deal_subSubCat_name'+subSubCategory).val();
	notify_category_change();
}
/****************
sng:12/mar/2012
The label of the note textarea change when the deal type/subtype/subtype change
**********************/
function update_note_box_label(){
	var curr_cat = _curr_cat.toLowerCase();
	var curr_sub_cat = _curr_sub_cat.toLowerCase();
	var curr_sub_sub_cat = _curr_sub_sub_cat.toLowerCase();
	var box_label = "Enter additional description here:";
	if((curr_cat == "debt")&&(curr_sub_cat=="bond")){
		box_label = "Enter additional details here (e.g. maturity, coupon, rating, format, redemption, fees):";
	}else if((curr_cat == "debt")&&(curr_sub_cat=="loan")){
		box_label = "Enter additional details here (e.g. tenor, margin, fees):";
	}else if((curr_cat == "equity")&&(curr_sub_cat=="preferred")){
		box_label = "Enter additional details here (e.g. maturity, coupon, format, pricing, fees):";
	}else if((curr_cat == "equity")&&(curr_sub_cat=="convertible")){
		box_label = "Enter additional details here (e.g. maturity, coupon, format, pricing, fees, underlying, dividend protection mechanism):";
	}else if((curr_cat == "equity")&&(curr_sub_cat=="equity")&&(curr_sub_sub_cat=="additional")){
		box_label = "Enter additional details here (e.g. pricing, discount, primary/ secondary, fees, seller):";
	}else if((curr_cat == "equity")&&(curr_sub_cat=="equity")&&(curr_sub_sub_cat=="ipo")){
		box_label = "Enter additional details here (e.g. pricing, primary/ secondary, greenshoe, fees, seller):";
	}else if((curr_cat == "equity")&&(curr_sub_cat=="equity")&&(curr_sub_sub_cat=="rights issue")){
		box_label = "Enter additional details here (e.g. terms, TERP, key dates, fees, rump, underwriting:";
	}else if((curr_cat == "m&a")&&(curr_sub_cat=="pending")){
		box_label = "Enter additional details here (e.g. target, seller, buyer, termination fees, conditions, premium, valuation):";
	}else if((curr_cat == "m&a")&&(curr_sub_cat=="completed")){
		box_label = "Enter additional details here (e.g. target, seller, buyer, termination fees, conditions, premium, valuation):";
	}
	$('#label_additional_details').html(box_label);
}

function autoCompleteWithCustomOpenCallBack(itemFor, source) {
    $(itemFor).autocomplete({
        source: source,
        minLength: 3,
        select: function( event, ui ) {
            //console.log(ui.item);
        },
        open: function(e,ui) {
            var acData = $(this).data('autocomplete');
            //console.log(acData);
            acData.menu.element.find('li a').each(
                function() {
                    var thisText = $(this).text();
                    //Maybe the text is ucfirst
                    ucFirstTerm = acData.term.charAt(0).toUpperCase() + acData.term.slice(1);
                    // or all up
                    uppercaseTerm =  acData.term.toUpperCase();
                    var replacementText =  thisText.replace(acData.term, '<b>' + acData.term + '</b>').replace(ucFirstTerm, '<b>' + ucFirstTerm + '</b>').replace(uppercaseTerm, '<b>' + uppercaseTerm + '</b>');
                    $(this).html(replacementText);
                }
            );         
        }        
    })	
}
/***************
sng:7/feb/2012
We use this for companies so that we can show the name,hq,sector,industry in the suggestion dropdown. This way
the user can see whether it is vodafone UK or Vodafone India

We need to change this a bit. dynamically added textboxes are having problem. For then we need to pass the jQuery object directly.
*****************/
function autoCompleteWithCustomOpenCallBack_v2(itemFor, source) {
    itemFor.autocomplete({
        source: source,
        minLength: 3,
        select: function( event, ui ) {
            //console.log(ui.item);
			//$('select').selectmenu();
        }         
    }).data( "autocomplete" )._renderItem = function( ul, item ) {
	
	/*******************
	sng:19/oct/2011
	UGLY HACK
	in jquery.ui.selectmenu.css, the z-index of .ui-selectmenu-menu is set to 1005
	result: if there is a select under the auto complete input text box, the dropdown list is getting
	behind the select box.
	Setting the z-index of the UL element to 1005 solve the issue
	***********************/
	ul.css({zIndex: '1005'});
	return $( "<li></li>" )
                .data( "item.autocomplete", item )
                .append( "<a>" + item.label + "<br><span style='font-size:0.8em'>" + item.hq_country+"; "+item.sector + "; " + item.industry + "</span></a>" )
                .appendTo( ul );
	};
}
</script>

<script>
function get_url_markup(){
	_url_markup = '<div><input type="text" name="regulatory_links[]" id="regulatory_links'+_current_url_num+'" class="std special" value="'+_default_url_input+'"></div>';
	_current_url_num++;
	return _url_markup;
}
function set_url_inputs_defaults(){
	$('[id^="regulatory_links"]').click(function(event) {
        if ($(this).val() == _default_url_input) {
            $(this).val('');
            $(this).addClass('black');
            
        }        
    })

    $('[id^="regulatory_links"]').blur(function(event) {
        if ($(this).val() == '') {
            $(this).removeClass('black');
            $(this).val(_default_url_input);
        }        
    })
}
</script>

<script type="text/javascript" src="js/fileuploader.js"></script>
<link href="css/fileuploader.css" rel="stylesheet" type="text/css">
<script type="text/javascript">
var g_uploader;
function create_suggest_file_uploader(){            
		g_uploader = new qq.FileUploader({
		element: document.getElementById('suggestion_file'),
		action: 'ajax/fileuploader.php',
		debug: true
	});  
	/**************
	need to align this to right
	and same size as the Add more URLs button
	************/
	$('#suggestion_file .qq-upload-button').css('float','right'); 
	$('#suggestion_file .qq-upload-button').css('width','79px');
	$('#suggestion_file .qq-upload-button').css('height','15px');        
}
$(function(){
	create_suggest_file_uploader();
});
</script>

<script>
function getBankMarkup() {
    _bankMarkup = '<div class="list-item" ><input type="text" name="banks[]" style="width: 100%;"></div>';
    _currentBankNum++;
    return  _bankMarkup;
}
function getLawFirmMarkup() {
    _bankMarkup = '<div class="list-item" ><input type="text" name="law_firms[]" style="width: 100%;">';
    _currentLawFirmNum++;
    return  _bankMarkup;
}

function get_company_markup(){
	var input = $('<input type="text" name="companies[]" style="width: 100%;">');
	autoCompleteWithCustomOpenCallBack_v2(input, "ajax/sugest_a_deal_search_firm.php");
	_company_markup = $('<div class="list-item" >').append(input).append($('</div>'));
    _current_company_num++;
    return  _company_markup;
}

function initAutocompleteForBanksAndLawFirms() {
	autoCompleteWithCustomOpenCallBack("#banks input[type=text]", "ajax/sugest_a_deal_search_firm.php?for=advisors&type=1");
	autoCompleteWithCustomOpenCallBack("#law_firms input[type=text]", "ajax/sugest_a_deal_search_firm.php?for=advisors&type=2");
}
/***********
sng:7/feb/2012
We want to show the name as well as HQ, sector, industry in the suggestion dropdown

Problem is, unless we add autocomplete to the input field directly, it does not work for dynamically added items.
To handle that, we updated the _v2 to accept the jQuery obj directly.

That breaks the code here. So we get the group of inputs and iterate through each.
***************/
function initAutocompleteForCompanies() {
	var inputs = $("#companies input[type=text]");
	inputs.each(function(index){
		autoCompleteWithCustomOpenCallBack_v2($(this), "ajax/sugest_a_deal_search_firm.php"); 
	});
	
}
</script>

<script>
function can_submit() {
	<?php
	/******************
	sng:12/nov/2011
	If the user is not logged in, we show a popup
	********************/
	
	if(!$g_account->is_site_member_logged()){
		?>
		apprise("Please login to submit deal data",{'textOk':'OK'});
		return false;
		<?php
	}else{
		?>
		submit_data();
		<?php
	}
	?>
    
}

function submit_data(){
	
	/*****************
	clear the default texts, then take the values. Unfortunately, it does not clear the dynamically added url boxes.
	I have written a method that scans through each and if the value is default, set it to blank
	*****************/
	for (index in _defaultInputs) {
		if ($('#' + index).val() == _defaultInputs[index] ) {
			$('#' + index).val('');    
		}
	}
	
	$('[id^="regulatory_links"]').each(function(i){
		if($(this).val()==_default_url_input){
			$(this).val('');
		}
	});
	var data = $('#suggest_deal_form').serialize();
	/**************************
	we block the UI to show that the data is being posted
	***************************/
	clear_error_msg();
	$.blockUI({ message: '<h1>submitting...</h1><img src="images/loader.gif" />' });
	$.post('ajax/suggest_deal/simple_request.php',data,function(result){
		$.unblockUI();
		apprise(result.msg);
		if(result.status == 1){
			//all ok
		}else{
			//error
			$('#err_deal_type').html(result.err.deal_type);
			$('#err_companies').html(result.err.companies);
			$('#err_deal_date').html(result.err.deal_date);
			$('#err_deal_value').html(result.err.deal_value);
			$('#err_banks').html(result.err.banks);
			
		}
		/************************
		We now upload files via ajax and the filenames appears in a <ul id="qq-upload-list"></ul> (we got this from the fileupload js code).
		Once our request completes we need to blank out that area
		****************/
		create_suggest_file_uploader();
	},"json");
}
</script>
<script>
function clear_error_msg(){
	$('#err_deal_type').html('');
	$('#err_companies').html('');
	$('#err_deal_date').html('');
	$('#err_deal_value').html('');
	$('#err_banks').html('');
}
</script>
<form id="suggest_deal_form">
<table width="100%" border="0" cellspacing="0" cellpadding="4">
<tr>
<td>Type of Transaction: <span class="err_txt">*</span></td>
</tr>

<tr>
	<td>
		<div class="SS_radio_cat" style="font-size:10px;">
		<?php
		/*******************
		sng:25/jan/2012
		We will not preselect any options. That way, the user has to click on option.
		This will trigger warning if not logged in.
		This also triggers a deal search using the deal type / subtype / sub subtype
		as parameter
		*************************/
		if (!isset($_POST['deal_cat_name'])) {
			//$_POST['deal_cat_name'] = 'M&A';
			$_POST['deal_cat_name'] = "";
		}
		if (!isset($_POST['deal_subcat1_name'])) {
			//$_POST['deal_subcat1_name'] = 'Pending';
			$_POST['deal_subcat1_name'] = "";
		}
		if(!isset($_POST['deal_subcat2_name'])){
			$_POST['deal_subcat2_name'] = "";
		}
		$i = 1;
		foreach($categories as $categoryName=>$subCats) :?>   
			<input type="radio" id="SS_deal_cat_name<?php echo $i?>" name="deal_cat_name" value="<?php echo $categoryName?>" onClick="SS_categoryChanged(<?php echo $i?>)" <?php if($_POST['deal_cat_name']==$categoryName){?>checked<?php }?>/><label for="SS_deal_cat_name<?php echo $i?>"><?php echo $categoryName?></label>
		<?php $i++;endforeach?>
		</div>
		<?php 
		$i = 1; $j = 1;
		foreach($categories as $subCategoryName=>$subCats) :?>
			<div class="SS_radio_subcat" style="font-size:10px;margin-top:5px;display: <?php if($_POST['deal_cat_name']==$subCategoryName){?>block<?php } else {?> none <?php }?>;" id="SS_subCatsForCat<?php echo $i?>">   
			<?php foreach  ($subCats as $subCatName => $subSubCats) : ?>
				<input type="radio" id="SS_deal_subCat_name<?php echo $j?>" name="deal_subcat1_name" value="<?php echo ($subCatName == 'All') ? ''  : $subCatName?>" onClick="SS_subCategoryChanged(<?php echo $j?>)" <?php if($_POST['deal_subcat1_name']==$subCatName){?>checked<?php }?>/><label for="SS_deal_subCat_name<?php echo $j?>"><?php echo $subCatName?></label>
			<?php $j++;endforeach;?> 
			</div>
		<?php $i++; endforeach;?>

		<?php 
		$i = 1;$j = 1;$k = 1;
		foreach($categories as $subCategoryName=>$subCats) :?>
			<?php foreach  ($subCats as $subCatName => $subSubCats) : ?>
				<div class="SS_radio_subsubcat <?php echo "parent_$k"?>" style="font-size:10px;margin-top:5px; display:<?php if($_POST['deal_cat_name']==$subCategoryName && $_POST['deal_subcat1_name']==$subCatName){?>block<?php } else {?> none <?php }?>;" id="SS_subSubCatsForCat<?php echo $j?>">   
				<?php foreach ($subSubCats as $key=>$name) : ?>
					<?php if ($name == 'n/a') continue ?>
					<input type="radio" id="SS_deal_subSubCat_name<?php echo $i?>" name="deal_subcat2_name" value="<?php echo $name?>" onClick="SS_subSubCategoryChanged(<?php echo $i?>)" <?php if($_POST['deal_subcat2_name']==$name){?>checked<?php }?>/><label for="SS_deal_subSubCat_name<?php echo $i?>"><?php echo $name?></label>
				<?php $i++;endforeach; ?> 
				</div>
			<?php $j++;endforeach;?> 

		<?php $k++; endforeach;?>                    
	</td>
</tr>
<tr><td><span id="err_deal_type" class="err_txt"></span></td></tr>
<tr><td><hr class='gray'/></td></tr>

<tr>
<td>Companies / Entities involved in the transaction: <span class="err_txt">*</span></td>
</tr>

<tr>
<td>
<div id="companies">
<div class="list-item" ><input type="text" name="companies[]" style="width: 100%;"></div>
<div class="list-item" ><input type="text" name="companies[]" style="width: 100%;"></div>
<div class="list-item" ><input type="text" name="companies[]" style="width: 100%;"></div>
</div>
<div style="display: block; float: right; margin:10px 10px 10px 10px;"> 
<input type="button" id='add_companies_btn' value="Add more" />
</div>
</td>
</tr>
<tr><td><span id="err_companies" class="err_txt"></span></td></tr>
<tr><td><hr class='gray'/></td></tr>
<tr><td>Date of Transaction: <span class="err_txt">*</span></td></tr>
<tr>
<td>
<div id="deal_date_hint">
<input type="text" name="deal_date" id="deal_date" class="date" readonly="readonly" value="<?php echo date('d/M/Y');?>">&nbsp;&nbsp;&nbsp;&nbsp;
<input type="radio" name="deal_date_type" value="date_announced" id="deal_date_type_date_announced" checked="checked" /><label for="deal_date_type_date_announced">Announced</label>
<input type="radio" name="deal_date_type" value="date_completed" id="deal_date_type_date_completed" /><label for="deal_date_type_date_completed">Completed</label>
</div>
</td>
</tr>
<tr><td><span id="err_deal_date" class="err_txt"></span></td></tr>
<tr><td><hr class='gray'/></td></tr>
<tr>
<td>Please list any links to press releases, regulatory fillings, financial news sites, etc.</td>
</tr>

<tr>
<td>
<!--/////////////////////////////////////////////////URLs//////////////////////////////////////-->
<div id="url_list">
<div><input type="text" name="regulatory_links[]" id="regulatory_links1" class="std special"></div>
<div><input type="text" name="regulatory_links[]" id="regulatory_links2" class="std special"></div>
</div>
<div style="display: block; float: right;"> 
<input type="button" id='add_url_btn' value="Add more URLs" />
</div>
<!--/////////////////////////////////////////////////URLs//////////////////////////////////////-->
</td>
</tr>


<tr>
<td>
<div>
<div style="float:left;padding-right:5px;">Upload any files related to the transaction:</div>
<div style="clear:both"></div>
<div id="suggestion_file"></div>
</div>

</td>
</tr>

<tr>
<td>Deal Size: <span class="err_txt">*</span></td>
</tr>

<tr>
<td>
<div id="deal_value_range">
<?php
	
for($j=0;$j<$g_view['value_range_items_count'];$j++){
	?>
	<input type="radio" name="value_range_id" value="<?php echo $g_view['value_range_items'][$j]['value_range_id'];?>" id="value_range_id<?php echo $j;?>"><label for="value_range_id<?php echo $j;?>"><?php echo $g_view['value_range_items'][$j]['display_text'];?></label>
	<?php
}
//the special case of undisclosed which is treated as 0
?>
<input type="radio" name="value_range_id" value="0" id="value_range_id<?php echo $j;?>"><label for="value_range_id<?php echo $j;?>" onclick="togleButton(this);">Undisclosed</label>

</div>
Note: Deals with no exact size are excluded from the league tables that use size and adjusted size
</td>
</tr>
<tr>
<td>
Or enter size here: <input type="text" name="deal_value" id="deal_value" class="std special" style="width:185px">
</td>
</tr>
<tr><td><span id="err_deal_value" class="err_txt"></span></td></tr>

<tr>
<td><span id="label_additional_details">Enter additional details here:</span></td>
</tr>

<tr>
<td>
<textarea name="additional_details" cols="" rows="" style="width:100%; height:75px;"></textarea>
</td>
</tr>

<tr><td><hr class='gray'/></td></tr>

<tr>
<td>Banks involved in the transaction: <span class="err_txt">*</span></td>
</tr>

<tr>
<td>
<div id="banks">
<div class="list-item" ><input type="text" name="banks[]" style="width: 100%;"></div>
<div class="list-item" ><input type="text" name="banks[]" style="width: 100%;"></div>
<div class="list-item" ><input type="text" name="banks[]" style="width: 100%;"></div>
</div>
<div style="display: block; float: right; margin:10px 10px 10px 10px;"> 
<input type="button" id='add_banks_btn' value="Add more" />
</div>
</td>
</tr>
<tr><td><span id="err_banks" class="err_txt"></span></td></tr>

<tr>
<td>Law Firms involved in the transaction:</td>
</tr>

<tr>
<td>
<div id="law_firms">
<div class="list-item" ><input type="text" name="law_firms[]" style="width: 100%;"></div>
<div class="list-item" ><input type="text" name="law_firms[]" style="width: 100%;"></div>
<div class="list-item" ><input type="text" name="law_firms[]" style="width: 100%;"></div>
</div>
<div style="display: block; float: right; margin:10px 10px 10px 10px;"> 
<input type="button" id='add_law_firms_btn' value="Add more" />
</div>
</td>
</tr>

<tr>
<td>Option to notify colleagues and partners that this transaction has been submitted (please separate emails with commas):</td>
</tr>
<tr>
<td>
<textarea name="notification_email_list" cols="" rows="" style="width:100%; height:40px;"></textarea>
</td>
</tr>

<tr>
<td>
<!--//////////////////////////////////
the member has to click this specifically if he/she wants to remain annonymous
///////////////////////////////////////-->
<input type="checkbox" name="not_mine" id="not_mine">
<label for="not_mine">Do not attribute email notification to my account</label>
</td>
</tr>

<tr>
<td style="text-align:center">
<input type="button" name="submit_data" id="submit_data" value="Submit Transaction to Clearing House" class="btn_auto" onclick="return can_submit();">
</td>
</tr>

</table>
</form>