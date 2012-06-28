<?php
/**************************
sng:19/mar/2012
We will now change the way we show detail and submit correction.
We will now show more details, grouped by sections
We will show the current suggestions
We will give more edit options.

Since we do not want to wreck old code, we will use this instead of deal_page_detail
*****************************/
/**************
sng:24/mar/2012
It may happen that the added_on date is not there. In that case show N/A
****************/
if($g_view['deal_data']['added_on'] == '0000-00-00 00:00:00'){
	$g_view['submisson_date'] = 'N/A';
}else{
	$g_view['submisson_date'] = date('jS M Y',strtotime($g_view['deal_data']['added_on']));
}

if($g_view['deal_data']['added_by_mem_id']!=0){
	$submitter_work_email_tokens = explode('@',$g_view['deal_data']['work_email']);
	$submitter_work_email_suffix = $submitter_work_email_tokens[1];
	$g_view['deal_submitter'] = $g_view['deal_data']['member_type']." @".$submitter_work_email_suffix;
}else{
	$g_view['deal_submitter'] = "Admin";
}
?>
<link rel="stylesheet" type="text/css" href="css/accordion/accordion.core.css" />
<link rel="stylesheet" type="text/css" href="css/accordion/accordion.style.css" />
<script src="js/accordion/jquery.accordion.2.0.js"></script>

<style type="text/css">
.std
{
	margin:5px;     
}
.special
{
	color: #CCC;
	font-style: italic;
}
.black
{
	color: #000;
	font-style: normal;
}
.hr_div
{
	height:10px;
	margin-top:20px;
	border-top:1px solid #CCCCCC;
}
</style>
<script>
/*******************
global stuff that is used by all the panels
*********************/
var _defaultInputs = new Array();

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
		return true;
		<?php
	}
	?>
    
}
</script>
<?php
/*************************
sng:12/apr/2012
More common stuff.

For the advisor panels, we require roles so that when we add new inputs for firms, we can populate role dropdown from javascript

We follow a prefix naming convention
banks - bank_
law firms - law_firm_
This convention is used in ajax/suggest_deal_correction/fetch_submitted_firms.php
*******************/
$g_view['bank_roles'] = NULL;
$g_view['bank_roles_count'] = 0;

$ok = $deal_support->front_get_deal_partner_roles('bank',$g_view['deal_data']['deal_cat_name'],$g_view['bank_roles'],$g_view['bank_roles_count']);
if(!$ok){
	/****************
	Let us not hang the script
	******************/
}

$g_view['law_firm_roles'] = NULL;
$g_view['law_firm_roles_count'] = 0;

$ok = $deal_support->front_get_deal_partner_roles('law firm',$g_view['deal_data']['deal_cat_name'],$g_view['law_firm_roles'],$g_view['law_firm_roles_count']);
if(!$ok){
	/****************
	Let us not hang the script
	******************/
}
?>
<script>
var bank_role_count=<?php echo $g_view['bank_roles_count'];?>;
var bank_role_ids = new Array();
var bank_role_names = new Array();


<?php
for($role_i=0;$role_i<$g_view['bank_roles_count'];$role_i++){
	?>
	bank_role_ids[<?php echo $role_i;?>]=<?php echo $g_view['bank_roles'][$role_i]['role_id'];?>;
	bank_role_names[<?php echo $role_i;?>]='<?php echo $g_view['bank_roles'][$role_i]['role_name'];?>';
	<?php
}
?>

var law_firm_role_count=<?php echo $g_view['law_firm_roles_count'];?>;
var law_firm_role_ids = new Array();
var law_firm_role_names = new Array();


<?php
for($role_i=0;$role_i<$g_view['law_firm_roles_count'];$role_i++){
	?>
	law_firm_role_ids[<?php echo $role_i;?>]=<?php echo $g_view['law_firm_roles'][$role_i]['role_id'];?>;
	law_firm_role_names[<?php echo $role_i;?>]='<?php echo $g_view['law_firm_roles'][$role_i]['role_name'];?>';
	<?php
}
?>
//client side counterpart to $frm_partner_firm_i = 0;
var _current_bank_num = 0;
var _current_law_firm_num = 0;


function add_partner(partner_type,container_div){
	var snippet = "";
	if(partner_type == 'bank'){
		snippet = get_bank_markup();
	}
	if(partner_type == 'law firm'){
		snippet = get_law_firm_markup();
	}
	$('#'+container_div).append(snippet);
}

function get_bank_markup(){
	var input = $('<input type="text" name="firms[]"  class="deal-edit-snippet-textbox std"  />');
	autoCompleteWithCustomOpenCallBack_v2_firm(input, "ajax/sugest_a_deal_search_firm.php?for=advisors&type=1");
	
	/**********
	we need a way to know whether this is existing entry where member may just change the role OR whether this is a new entry where
	member type the firm name and the role.
	If this is a new entry, it must not be one from the current entries (that is if JPMorgan is there, do not allow to add JPMorgan again
	******************/
	var bank_markup = $('<div>').append(input).append('<input type="hidden" name="new_entry_'+_current_bank_num+'" value="y" />').append($('</div><div>'));
	var _select_markup = '<select name="partner_role_'+ _current_bank_num+'" class="deal-edit-snippet-dropdown std">';
	_select_markup+='<option value="">select role</option>';
	for(var role_i=0;role_i<bank_role_count;role_i++){
		_select_markup+='<option value="'+bank_role_ids[role_i]+'">'+bank_role_names[role_i]+'</option>';
	}
	_select_markup+='</select>';
	
	
	bank_markup.append($(_select_markup));
	bank_markup.append($('</div>'));
    _current_bank_num++;
    return  bank_markup;
}

function get_law_firm_markup(){
	var input = $('<input type="text" name="firms[]"  class="deal-edit-snippet-textbox std"  />');
	autoCompleteWithCustomOpenCallBack_v2_firm(input, "ajax/sugest_a_deal_search_firm.php?for=advisors&type=2");
	
	/**********
	we need a way to know whether this is existing entry where member may just change the role OR whether this is a new entry where
	member type the firm name and the role.
	If this is a new entry, it must not be one from the current entries (that is if Freshfield is there, do not allow to add Freshfield again
	******************/
	var law_firm_markup = $('<div>').append(input).append('<input type="hidden" name="new_entry_'+_current_law_firm_num+'" value="y" />').append($('</div><div>'));
	var _select_markup = '<select name="partner_role_'+ _current_law_firm_num+'" class="deal-edit-snippet-dropdown std">';
	_select_markup+='<option value="">select role</option>';
	for(var role_i=0;role_i<law_firm_role_count;role_i++){
		_select_markup+='<option value="'+law_firm_role_ids[role_i]+'">'+law_firm_role_names[role_i]+'</option>';
	}
	_select_markup+='</select>';
	
	
	law_firm_markup.append($(_select_markup));
	law_firm_markup.append($('</div>'));
    _current_law_firm_num++;
    return  law_firm_markup;
}

/***************
sng:7/feb/2012
We use this for companies so that we can show the name,hq,sector,industry in the suggestion dropdown. This way
the user can see whether it is vodafone UK or Vodafone India

We need to change this a bit. dynamically added textboxes are having problem. For then we need to pass the jQuery object directly.
*****************/
function autoCompleteWithCustomOpenCallBack_v2(itemFor, data_source) {
	
    itemFor.autocomplete({
		source: data_source,
		minLength: 3,
		select: function( event, ui ) {
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

function autoCompleteWithCustomOpenCallBack_v2_firm(itemFor, data_source) {
	
    itemFor.autocomplete({
		source: data_source,
		minLength: 3,
		select: function( event, ui ) {
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
		.append( "<a>" + item.label + "</a>" )
		.appendTo( ul );
	};
}

function submit_partner_suggestion(partner_type,form_id,msg_div_id){
	if(can_submit()){
		$('#'+msg_div_id).html('sending...');
		var data = $('#'+form_id).serialize();
		
		
		$.post('ajax/suggest_deal_correction/firm.php',data,function(result){
			$('#'+msg_div_id).html(result.msg);
			if(result.success == 'y'){
				if(partner_type == "bank"){
					load_financial_advisor();
				}else if(partner_type == "law firm"){
					load_legal_advisor();
				}else{
					return;
				}
			}
		},"json");
	}
}
</script>

<?php
/*******************************
sng:20/apr/2012
For participants
*******************/
require_once("classes/class.transaction_company.php");
$deal_comp = new transaction_company();

$g_view['company_roles'] = NULL;
$g_view['company_role_count'] = 0;
$ok = $deal_comp->get_all_deal_company_roles_for_deal_type($g_view['deal_data']['deal_cat_name'],$g_view['company_roles'],$g_view['company_role_count']);
if(!$ok){
	/****************
	let us not hang the script
	******************/
}
?>
<script>
var company_role_count=<?php echo $g_view['company_role_count'];?>;
var company_role_ids = new Array();
var company_role_names = new Array();
<?php
for($role_i=0;$role_i<$g_view['company_role_count'];$role_i++){
	?>
	company_role_ids[<?php echo $role_i;?>]=<?php echo $g_view['company_roles'][$role_i]['role_id'];?>;
	company_role_names[<?php echo $role_i;?>]='<?php echo $g_view['company_roles'][$role_i]['role_name'];?>';
	<?php
}
?>
//client side counterpart to $frm_participant_company_i = 0;
var _current_company_num = 0;

function add_participant(container_div){
	var snippet = "";
	snippet = get_company_markup();
	$('#'+container_div).append(snippet);
	set_footnote_inputs_defaults();
}

function get_company_markup(){
	var input = $('<input type="text" name="companies[]"  class="deal-edit-snippet-textbox std"  />');
	autoCompleteWithCustomOpenCallBack_v2(input, "ajax/sugest_a_deal_search_firm.php");
	
	/**********
	we need a way to know whether this is existing entry where member may just change the role OR whether this is a new entry where
	member type the firm name and the role.
	If this is a new entry, it must not be one from the current entries (that is if JPMorgan is there, do not allow to add JPMorgan again
	******************/
	var company_markup = $('<div>').append(input).append('<input type="hidden" name="new_entry_'+_current_company_num+'" value="y" />').append($('</div><div>'));
	var _select_markup = '<select name="participant_role_'+ _current_company_num+'" class="deal-edit-snippet-dropdown std">';
	_select_markup+='<option value="">select role</option>';
	for(var role_i=0;role_i<company_role_count;role_i++){
		_select_markup+='<option value="'+company_role_ids[role_i]+'">'+company_role_names[role_i]+'</option>';
	}
	_select_markup+='</select>';
	
	
	company_markup.append($(_select_markup));
	company_markup.append($('</div><div>'));
	
	var _footnote_markup = '<input type="text" name="participant_note_'+ _current_company_num+'" id="participant_note_'+ _current_company_num+'" class="deal-edit-snippet-textbox std special" />';
	
	company_markup.append($(_footnote_markup));
	company_markup.append($('</div>'));
	
    _current_company_num++;
    return  company_markup;
}

var _default_footnote_input = 'footnote';

function set_footnote_inputs_defaults(){
	var participant_footnotes = $('[id^="participant_note"]');
	participant_footnotes.each(function(index){
		if ($(this).val() == '') {
			$(this).val(_default_footnote_input);	
		}
	});
	
	$('[id^="participant_note"]').click(function(event) {
        if ($(this).val() == _default_footnote_input) {
            $(this).val('');
            $(this).addClass('black');
            
        }        
    })

    $('[id^="participant_note"]').blur(function(event) {
        if ($(this).val() == '') {
            $(this).removeClass('black');
            $(this).val(_default_footnote_input);
        }        
    })
}

function submit_company_suggestion(form_id,msg_div_id){
	if(can_submit()){
		/*****
		need to blank out the footnote boxes if the default text is set there
		******/
		$('[id^="participant_note"]').each(function(i){
			if($(this).val()==_default_footnote_input){
				$(this).val('');
			}
		});
		
		$('#'+msg_div_id).html('sending...');
		var data = $('#'+form_id).serialize();
		
		
		$.post('ajax/suggest_deal_correction/participant.php',data,function(result){
			$('#'+msg_div_id).html(result.msg);
			if(result.success == 'y'){
				load_participants();
			}
		},"json");
	}
}
</script>

<ul id="example1" class="accordion">
	<li>
	<h3>Deal Participants</h3>
	<div class="panel loading">
	<?php require("deal_edit_snippets/participants.php");?>
	</div>
	</li>
	
	<li>
	<h3>Deal Size</h3>
	<div class="panel loading">
	<?php require("deal_edit_snippets/deal_valuations.php");?>
	</div>
	</li>
	
	<li>
	<h3>Additional Details</h3>
	<div class="panel loading">
	<?php require("deal_edit_snippets/additional_details.php");?>
	</div>
	</li>
	
	<li>
	<h3>Notes</h3>
	<div class="panel loading">
	<?php require("deal_edit_snippets/notes.php");?>
	</div>
	</li>
	
	<li>
	<h3>Sources</h3>
	<div class="panel loading">
	<?php require("deal_edit_snippets/sources.php");?>
	</div>
	</li>
	
	<li>
	<h3>Financial Advisors</h3>
	<div class="panel loading">
	<?php require("deal_edit_snippets/financial_advisors.php");?>
	</div>
	</li>
	
	<li>
	<h3>Legal Advisors</h3>
	<div class="panel loading">
	<?php require("deal_edit_snippets/legal_advisors.php");?>
	</div>
	</li>
</ul>
<script>
$(function(){
	$('#example1').accordion();
});
</script>

<script>
function can_edit_alert(){
	<?php
	/******************
	sng:13/dec/2011
	If the user is not logged in, we show a popup
	********************/
	if(!$g_account->is_site_member_logged()){
		?>
		apprise("Please log-in before you enter / submit new data, many thanks.",{'textOk':'OK'});
		<?php
	}
	?>
}
</script>