<?php
require_once("include/global.php");
require_once("classes/db.php");
$db = new db();
?>
<style type="text/css">
    .std {
        width:388px;
        margin:5px;
        border: 1px solid #CCCCCC;      
    }
    hr.orange {
        color: #E86200; background-color: #E86200; height: 1px; margin-top: 15px; margin-bottom: 15px; display: block;
    }
    .special {
        color: #CCC;
        font-style: italic;
    }
    .black {
        color: #000;
        font-style: normal;
    }
    .ui-datepicker-trigger {
        margin-left:5px;
        cursor:pointer;
    }
    .list-item {
        width: 30%;
        float: left;
        margin-top: 10px;
        padding-left: 10px;
    }
	
	.list-item2 {
        width: 23%;
        float: left;
        margin-top: 10px;
        padding-left: 10px;
		padding-bottom:5px;
    }
	.list-item2 input{
	margin-bottom:5px;
	margin-top:5px;
	}
    .participant_footnote{
	color:#CCCCCC;
	}
    .invalidValueField {
        background: url("/images/exclamation.png") center right no-repeat;
        
    }    
	
</style>


<script type="text/javascript">
var _currentBankNum  = 4;
var _currentLawFirmNum  = 4;
var _bankMarkup = '';
/*******************************
sng:16/mar/2012
We now have roles dropdown for banks
Problem is, the roles dropdown options change with deal types and then we need to consider
adding bank options dynamically.
We store the role names and ids in global var and use those when creating bank options
**********************************/
var bank_role_count = <?php echo $g_view['bank_roles_count'];?>;
var bank_role_ids = new Array();
var bank_role_names = new Array();
<?php
for($bank_role_i=0;$bank_role_i<$g_view['bank_roles_count'];$bank_role_i++){
	?>
	bank_role_ids[<?php echo $bank_role_i;?>]=<?php echo $g_view['bank_roles'][$bank_role_i]['role_id'];?>;
	bank_role_names[<?php echo $bank_role_i;?>]='<?php echo $g_view['bank_roles'][$bank_role_i]['role_name'];?>';
	<?php
}
?>

var _lawFirmMarkup = '';
/*******************************
sng:2/may/2012
We now have roles dropdown for law firms
**********************************/
var law_firm_role_count = <?php echo $g_view['law_firm_roles_count'];?>;
var law_firm_role_ids = new Array();
var law_firm_role_names = new Array();
<?php
for($law_firm_role_i=0;$law_firm_role_i<$g_view['law_firm_roles_count'];$law_firm_role_i++){
	?>
	law_firm_role_ids[<?php echo $law_firm_role_i;?>]=<?php echo $g_view['law_firm_roles'][$law_firm_role_i]['role_id'];?>;
	law_firm_role_names[<?php echo $law_firm_role_i;?>]='<?php echo $g_view['law_firm_roles'][$law_firm_role_i]['role_name'];?>';
	<?php
}
?>
/************
sng:8/feb/2012
We fetch companies snippet via ajax. We set this value from the snippet code
**************/
var _current_company_num = 0;
var _company_markup = '';
/********************
sng:9/feb/2012
When we create the role dropdown, we need the appropriate options also (depending on the dela type selected).
These are set in sjax/suggest_deal/snippets/participants_any.php
**********************/
var role_count=0;
var role_ids;
var role_names;

var _defaultInputs = new Array(); 
_defaultInputs['regulatory_links1'] = 'http://'; 
_defaultInputs['regulatory_links2'] = 'http://'; 
_defaultInputs['regulatory_links3'] = 'http://'; 
_defaultInputs['regulatory_links4'] = 'http://';
_defaultInputs['search_term'] = 'Enter name of a participant in the deal';
_defaultInputs['parent_company_name'] = 'enter name';
_defaultInputs['buyer_company_name'] = 'enter name';
_defaultInputs['target_company_name'] = 'enter name';
_defaultInputs['seller_company_name'] = 'enter name';
_defaultInputs['implied_equity_value'] = 'Enter amount';
_defaultInputs['aquisition_percentage'] = 'Enter amount, e.g. 51.0';
_defaultInputs['net_debt'] = 'Enter amount';
/*************
sng:3/may/2012
Introduced 3 new fields
***************/
_defaultInputs['total_debt_million_local_currency'] = 'Enter amount';
_defaultInputs['cash_million_local_currency'] = 'Enter amount';
_defaultInputs['adjustments_million_local_currency'] = 'Enter amount';

_defaultInputs['divident_payment'] = 'Enter amount (in Local Currency Millions)';
_defaultInputs['enterprise_value'] = 'Enter amount';
_defaultInputs['implied_deal_size'] = 'Enter amount';
_defaultInputs['implied_deal_size_local'] = 'Enter amount';
_defaultInputs['implied_enterprise_value'] = 'Enter amount';
_defaultInputs['local_currency_rate'] = 'e.g. 1.5';
_defaultInputs['equity_percentage'] = 'Equity payment %, e.g. 25.0';
_defaultInputs['local_currency'] = 'e.g. USD, EUR, JPY, RMB, CHF';
_defaultInputs['local_currency_of_share_price'] = 'e.g. USD, EUR, JPY, RMB, CHF';
_defaultInputs['year_to_maturity'] = 'e.g. 2';
_defaultInputs['current_rating'] = 'e.g. S&P, Moody`s';
_defaultInputs['bond_format'] = 'e.g. domestic, REG S, 144A, Shelf, EMTN';
_defaultInputs['collateral'] = 'e.g. secured/ unsecured';
_defaultInputs['seniority'] = 'e.g. senior/ subordinated';

_defaultInputs['year_to_call'] = 'e.g. 3';
_defaultInputs['deal_value'] = 'Enter amount';
_defaultInputs['deal_size'] = 'Enter amount';
_defaultInputs['local_currency'] = 'e.g. USD, EUR, JPY, RMB, CHF';
_defaultInputs['local_currency_rate'] = 'e.g. 1.5';
_defaultInputs['tenor'] = 'e.g. 2 years'
_defaultInputs['country_of_headquarters_subsidiary'] = 'start typing for suggestions'
_defaultInputs['country_of_headquarters_buyer'] = 'start typing for suggestions'
_defaultInputs['country_of_headquarters_seller'] = 'start typing for suggestions'
_defaultInputs['country_of_headquarters_target'] = 'start typing for suggestions'
_defaultInputs['target_stock_exchange_name'] = 'Ex: New York stock exchange';
_defaultInputs['deal_price_per_share'] = _defaultInputs['deal_value']
_defaultInputs['share_price_prior_to_announcement'] = _defaultInputs['deal_value']
_defaultInputs['total_shares_outstanding'] = _defaultInputs['deal_value']
_defaultInputs['implied_premium'] = "Enter percentage, e.g. +10.0"
_defaultInputs['performance_on_first_day'] = _defaultInputs['implied_premium']
_defaultInputs['fee_to_sellside'] = "Enter percentage, e.g. 1.25"
_defaultInputs['premium_discount'] = "Enter percentage, e.g. -2.50"
_defaultInputs['fee_gross'] = _defaultInputs['fee_to_sellside']
_defaultInputs['subscription_rate'] = _defaultInputs['fee_to_sellside']
_defaultInputs['margin'] = _defaultInputs['fee_to_sellside']
_defaultInputs['fee_commitment'] = _defaultInputs['fee_to_sellside']
_defaultInputs['fee_base'] = _defaultInputs['fee_to_sellside']
_defaultInputs['free_float_post_transaction'] = _defaultInputs['fee_to_sellside']
_defaultInputs['premium_discount_to_terp'] = _defaultInputs['fee_to_sellside']
_defaultInputs['fee_upfront'] = _defaultInputs['fee_to_sellside']
_defaultInputs['fee_utilisation'] = _defaultInputs['fee_to_sellside']
_defaultInputs['fee_arrangement'] = _defaultInputs['fee_to_sellside']
_defaultInputs['free_float_post_transaction'] = _defaultInputs['fee_to_sellside']
_defaultInputs['fee_to_buyside'] = _defaultInputs['fee_to_sellside']
_defaultInputs['coupon'] = _defaultInputs['fee_to_sellside']
_defaultInputs['redemption_price'] = 'Enter percentage, e.g. 105.00';
_defaultInputs['shares_vs_adtv'] = 'Enter as multiple, e.g. 10.0';
_defaultInputs['shares_sold_vs_adtv'] = 'Enter as multiple, e.g. 10.0';





/***********
sng:23/mar/2012
We no longer need Sellside checkbox. We now have role like 'Advisor, Sellside' to take care of it
*****************/ 
function getBankMarkup() {
    _bankMarkup = '<div class="list-item" ><table width="100%" border="0" cellspacing="0" cellpadding="4"><tr><td>Bank ' + _currentBankNum + '  :      </td><td><input type="text" name="banks[]" style="width: 100%;"></td></tr><tr><td>&nbsp;</td><td>';
	<?php
	/***************
	sng:16/mar/2012
	we now have role dropdown for banks
	and another checkbox, 'Not lead advisor'
	
	sng:23/mar/2012
	We no longer need this checkbox 'Not lead advisor' since we now have role like 'Junior Advisor'
	********************/
	?>
	var _select_markup = '<select name="bank_role_id_'+ _currentBankNum+'" id="bank_role_id_'+ _currentBankNum+'">';
	_select_markup+='<option value="0">Select role</option>';
	for(var role_i=0;role_i<bank_role_count;role_i++){
		_select_markup+='<option value="'+bank_role_ids[role_i]+'">'+bank_role_names[role_i]+'</option>';
	}
	_select_markup+='</select>';
	_bankMarkup+=_select_markup;
	
	_bankMarkup+='</td></tr></table></div>';
    _currentBankNum++;
    return  _bankMarkup;
}
function getLawFirmMarkup() {
    _lawFirmMarkup = '<div class="list-item" ><table width="100%" border="0" cellspacing="0" cellpadding="4"><tr><td>Law Firm ' + _currentLawFirmNum + '  :      </td><td><input type="text" name="law_firms[]" style="width: 100%;"></td></tr><tr><td>&nbsp;</td><td>';
	
	<?php
	/***************
	sng:2/may/2012
	we now have role dropdown for law firms
	********************/
	?>
	
	var _select_markup = '<select name="law_firm_role_id_'+ _currentLawFirmNum+'" id="law_firm_role_id_'+ _currentLawFirmNum+'">';
	_select_markup+='<option value="0">Select role</option>';
	for(var role_i=0;role_i<law_firm_role_count;role_i++){
		_select_markup+='<option value="'+law_firm_role_ids[role_i]+'">'+law_firm_role_names[role_i]+'</option>';
	}
	_select_markup+='</select>';
	_lawFirmMarkup+=_select_markup;
	_lawFirmMarkup+='</td></tr></table></div>';
    _currentLawFirmNum++;
    return  _lawFirmMarkup;
}
/*************************************
sng:16/mar/2012
******************/
function update_bank_roles(){
	var _option_markup = '<option value="0">Select role</option>';
	for(var role_i=0;role_i<bank_role_count;role_i++){
		_option_markup+='<option value="'+bank_role_ids[role_i]+'">'+bank_role_names[role_i]+'</option>';
	}
	
	//update all bank role dropdowns
	for(var i=1;i<_currentBankNum;i++){
		$('#bank_role_id_'+i).html(_option_markup);
	}
}
/*************************************
sng:2/may/2012
******************/
function update_law_firm_roles(){
	var _option_markup = '<option value="0">Select role</option>';
	for(var role_i=0;role_i<law_firm_role_count;role_i++){
		_option_markup+='<option value="'+law_firm_role_ids[role_i]+'">'+law_firm_role_names[role_i]+'</option>';
	}
	
	//update all law firm role dropdowns
	for(var i=1;i<_currentLawFirmNum;i++){
		$('#law_firm_role_id_'+i).html(_option_markup);
	}
}
/*******************
sng:8/feb/2012
*********************/
function get_company_markup(){
	var input = $('<input class="participant_company" type="text" name="companies[]" style="width: 100%;" />');
	autoCompleteWithCustomOpenCallBack_v2(input, "ajax/sugest_a_deal_search_firm.php");
	
	
	_company_markup = $('<div class="list-item2" >').append(input).append($('<br />'));
	var _select_markup = '<select name="company_participant_role_'+ _current_company_num+'">';
	_select_markup+='<option>select role</option>';
	for(var role_i=0;role_i<role_count;role_i++){
		_select_markup+='<option value="'+role_ids[role_i]+'">'+role_names[role_i]+'</option>';
	}
	_select_markup+='</select>';
	
	_select_markup+='<br /><input type="text" name="company_participant_note_'+ _current_company_num+'" class="participant_footnote" style="width: 100%;">';
	_company_markup.append($(_select_markup));
	_company_markup.append($('</div>'));
    _current_company_num++;
    return  _company_markup;
}

function detailed_deal_search(start){
		$('#results').addClass('loading');
		$.post('ajax/sugest_a_deal_search.php?start='+start,
			{'search_term' : $('#search_term').val()},
			function(data) {
				$('#resultsSeparator').show();
				$('#results').html(data).removeClass('loading');
			}
		);
	}

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
    
    $( "input.date" ).datepicker({
        showOtherMonths: true,
        selectOtherMonths: true,
        showOn: "button",
        buttonImage: "images/calendar.png",
        buttonImageOnly: true,
    });  
	
	/****************
	sng:8/feb/2012
	we now load the list of participants via ajax, so we need to call the handler after loading the snippet
	*******************/
	init_participants();       
}
/***********
sng:8/feb/2012
*************/
function init_participants(){
	initAutocompleteForCompanies();
	beautify_role_dropdown();
	init_participants_footnote();
}

function init_participants_footnote(){
	var participant_footnotes = $(".participant_footnote");
	participant_footnotes.each(function(index){
		if ($(this).val() == '') {
			$(this).val('footnote');	
		}
	});
	
	$('input.participant_footnote').click(function(event) {
        if ($(this).val() == 'footnote') {
            $(this).val('');
            $(this).addClass('black');
        }        
    });

    $('input.participant_footnote').blur(function(event) {
        if ($(this).val() == '') {
            $(this).removeClass('black');
            $(this).val('footnote');
        }        
    });
}


var _lastClickedButton = 'Pending';
var _requiredFields = {
    'Pending' : {
        'step1' : [
            'announced_date'
            
        ],
        'step2' : [
           'implied_deal_size' 
        ],
        
        'step3' : [
            'bank1'
        ]
    },
    'Completed' : 
    {
        'step1' :[
        ],
        'step2' :[
        ],
        'step3' :[
        ]
    },
	'Bond' : {
		'step1' : [
			'closed_date'
		],
		'step2' : [
			'deal_size'
		],
        'step3' :[ 
            'bank1',
            'end_date'
        ]
	},
    'Loan' : {
        'step1' : [
            'closed_date'
        ],
        'step2' : [
            'facility_size'
         ],
        'step3' :[ 
            'bank1',
            'end_date' ,
            'margin' ,
        ]
    },
    'Convertible' : {
        'step1' : [
            'closed_date'  
        ],
        'step2' : [
            'deal_size'
         ],
        'step3' :[ 
            'coupon',
            'end_date' ,
            'bank1' ,
        ]
    },
    'Additional' : {
        'step1' : [
            'closed_date'  
        ],
        'step2' : [
            'deal_size'
         ],
        'step3' :[ 
            'bank1',
        ]
    },    
    'IPO' : {
        'step1' : [
        ],
        'step2' : [
            'deal_size'
         ],
        'step3' :[ 
            'bank1',
        ]
    } ,
    'Rights Issue' : {
        'step1' : [
            'closed_date'   
        ],
        'step2' : [
            'deal_size'
         ],
        'step3' :[ 
            'bank1',
        ]
    }
}   
$(function() { 
    //console.log(_requiredFields);
    var _changes =  {
		'Debt':
		{
			'load':{
				'companies': 'ajax/suggest_deal/snippets/participants.php?deal_type=Debt'
			}
		},
		'Equity':
		{
			'load':{
				'companies': 'ajax/suggest_deal/snippets/participants.php?deal_type=Equity'
			}
		},
		'M&A':
		{
			'load':{
				'companies': 'ajax/suggest_deal/snippets/participants.php?deal_type=M%26A'
			}
		},
          'Bond' :
            {
                'edit' : 
                    {
                        'announced_date_label':'Announced/ Filed',
                        'closed_date_label':'Closed/ Trading',
                    },
                'delete' : [
                    'rumor_date_row',
                    'addition_buyer_text',
                    'exrights-date-row',
                ],
                
                'load' : {
                    'deal_valuation_tab' : 'ajax/suggest_deal/snippets.php?snippet=deal_valuation_tab',
                    'aditional_tab_details' : 'ajax/suggest_deal/snippets.php?snippet=bond_additional_information'
                },
                'show': [
                    'closed_date_row'
                ]
                
            },
           'Loan' :
            {
                'edit' : 
                    {
                        'announced_date_label':'Announced/ Filed',
                        'closed_date_label':'Closed/ Trading',
                    },
                'delete' : [
                    'rumor_date_row',
                    'addition_buyer_text',
                    'exrights-date-row',
                ],
                
                'load' : {
                    'deal_valuation_tab' : 'ajax/suggest_deal/snippets.php?snippet=deal_valuation_tab_loan',
                    'aditional_tab_details' : 'ajax/suggest_deal/snippets.php?snippet=loan_additional_information'
                },
                'show': [
                    'closed_date_row'
                ]
                
            },
          'Pending' :
            {
                'edit' : 
                    {
                        'announced_date_label':'Announced',
                        'closed_date_label':'Closed',
                    },
                'delete' : [
                    'exrights-date-row',
                    'closed_date_row'
                ],
                
                'load' : {
                    'deal_valuation_tab' : 'ajax/suggest_deal/snippets.php?snippet=deal_valuation_tab_pending',
                    'aditional_tab_details' : 'ajax/suggest_deal/snippets.php?snippet=pending_additional_information'
                },    
                'show': [
                    'rumor_date_row',
                    'addition_buyer_text', 
                ]
                
            },          
           'Completed' :
            {
                'edit' : 
                    {
                        'announced_date_label':'Announced',
                        'closed_date_label':'Closed',
                    },
                'delete' : [
                    'exrights-date-row',
                ],
                
                'load' : {
                    'deal_valuation_tab' : 'ajax/suggest_deal/snippets.php?snippet=deal_valuation_tab_pending',
                    'aditional_tab_details' : 'ajax/suggest_deal/snippets.php?snippet=pending_additional_information'
                },    
                'show': [
                    'rumor_date_row',
                    'closed_date_row',
                    'addition_buyer_text', 
                ]
                
            },
            'IPO' :
            {
                'edit' : 
                    {
                        'announced_date_label':'Announced/ Filed',
                        'closed_date_label':'Closed/ Trading',
                    },
                'delete' : [
                    'rumor_date_row',
                    'addition_buyer_text',
                    'exrights-date-row',
                ],
                
                'load' : {
                    'deal_valuation_tab' : 'ajax/suggest_deal/snippets.php?snippet=deal_valuation_tab_ipo',
                    'aditional_tab_details' : 'ajax/suggest_deal/snippets.php?snippet=ipo_additional_information'
                },
                'show': [
                    'closed_date_row',
                    
                ]
                
            }, 
          'Convertible' :
            {
                'edit' : 
                    {
                        'announced_date_label':'Announced/ Filed',
                        'closed_date_label':'Closed/ Trading',
                    },
                'delete' : [
                    'rumor_date_row',
                    'addition_buyer_text',
                    'exrights-date-row',
                ],
                
                'load' : {
                    'deal_valuation_tab' : 'ajax/suggest_deal/snippets.php?snippet=deal_valuation_tab',
                    'aditional_tab_details' : 'ajax/suggest_deal/snippets.php?snippet=convertible_additional_information'
                },
                'show': [
                    'closed_date_row'
                ]
                
            },
             'Rights Issue' :
            {
                'edit' : 
                    {
                        'announced_date_label':'Announced/ Filed',
                        'closed_date_label':'Closed/ Trading',
                    },
                'delete' : [
                    'rumor_date_row',
                    'addition_buyer_text',
                ],
                
                'load' : {
                    'deal_valuation_tab' : 'ajax/suggest_deal/snippets.php?snippet=deal_valuation_tab_ri',
                    'aditional_tab_details' : 'ajax/suggest_deal/snippets.php?snippet=ri_additional_information'
                },
                'show': [
                    'exrights-date-row',
                    'closed_date_row'
                ]
                
            },
            'Additional' :
            {
                'edit' : 
                    {
                        'announced_date_label':'Announced/ Filed',
                        'closed_date_label':'Closed/ Trading',
                    },
                'delete' : [
                    'rumor_date_row',
                    'addition_buyer_text',
                    'exrights-date-row',
                ],
                
                'load' : {
                    'deal_valuation_tab' : 'ajax/suggest_deal/snippets.php?snippet=deal_valuation_tab_additional',
                    'aditional_tab_details' : 'ajax/suggest_deal/snippets.php?snippet=additional_additional_information'
                },
                'show': [
                    'closed_date_row'
                ]
                
            },
          }
    //_changes['Completed'] = _changes['Pending'];
    _changes['Preferred'] = _changes['Convertible'];
    //console.log(_changes);
    reinitialize();
	<?php
	/******************************
	sng:23/jan/2012
	we now want to enter the company name and press ENTER to trigger the search.
	What we do is put a form element and trap onsubmit() to call detailed_deal_search.
	In that function we trigger the ajax call that we had here.
	********************************/
	?>
    $('#search').button();/*.click(function(event){
        //$('#tombstone_search_frm').submit();
        $('#results').addClass('loading');
        $.post(
            'ajax/sugest_a_deal_search.php',
            {'search_term' : $('#search_term').val()},
            function(data) {
                $('#resultsSeparator').show();
                $('#results').html(data).removeClass('loading');
            }
        )
    });*/
	
	
	
    
    
    /*********************
	sng:8/feb/2012
	We no longer have the company boxes with country/sector/industry dropdowns
	***********************/
	
	$( "#target_stock_exchange_name" ).autocomplete({
        source: "ajax/sugest_a_deal_search_stock_exchange.php",
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
	
	$( "#local_currency" ).autocomplete({
        source: "ajax/suggest_a_deal_search_currency.php",
        minLength: 1,
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
                .append( "<a>" + item.label + " - "+item.name+" </a>" )
                .appendTo( ul );
    };
	$( "#local_currency_of_share_price" ).autocomplete({
        source: "ajax/suggest_a_deal_search_currency.php",
        minLength: 1,
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
                .append( "<a>" + item.label + " - "+item.name+" </a>" )
                .appendTo( ul );
    };
    
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
    // Do the deal type magick
    //$( ".radio" ).buttonset().click(function(idx){console.log('asdasd');$( ".radio" ).buttonset('refresh')});  ;    
    $( ".radio_subcat" ).buttonset().click(function(){
        $( ".radio_subsubcat :radio" ).each(function(idx){
            $(this).removeAttr('checked');
        })
        $( ".radio_subsubcat" ).buttonset('refresh')        
    }).change(function(){
        //console.log($(this).find('input:checked').val())
        //_selectedSubCategory = $(this).find('input:checked').val();
        _lastClickedButton = $(this).find('input:checked').val();
        
        updateFormFields();
    }); 
       
    $( ".radio_cat" ).buttonset().click(function(){
        $( ".radio_subcat :radio" ).each(function(idx){
            $(this).removeAttr('checked');
        })
        $( ".radio_subcat" ).buttonset('refresh')
    }).change(function(){
        _lastClickedButton = $(this).find('input:checked').val();
		/********************************
		sng:16/mar/2012
		Now we have roles for banks. These roles change depending upon the deal type
		so we trigger a change here
		*********************************/
		fetch_partners_roles(_lastClickedButton);
        updateFormFields();
    });  
    $( ".radio_subsubcat" ).buttonset().change(function(){
        _lastClickedButton = $(this).find('input:checked').val();
        updateFormFields();
        //console.log($(this).find('input:checked').val())
    });;
    
    /** 
    * Hadle the case when a hidden sub sub cat is checked 
    */
    $('#cats :radio').click(function() {
        $('#cats :checked').each(function(idx){
            if (!$(this).is(':visible')) {
                $(this).removeAttr('checked');
            }
        })         
    });
 
    
    $('#transaction_type_check').buttonset();
	

    $('#hostile_or_friendly').buttonset();
    
    $('.button_checkbox').button({
          /*  icons: {
                primary: "ui-icon-check"
            },
            text: true  */
    })
    $('.next-step-button').button();
    $('#submit_data').button().click(function(event){event.preventDefault()});
    $( "#multi_step_form" ).accordion({autoHeight: false, changestart: function(event, ui) {}, event: 'passValidation'});
    
    $('.ui-accordion-header').attr("disabled", "disabled");
    
    $('#add_banks_btn').button().click(function(){
        $('#banks').append(getBankMarkup());
        initAutocompleteForBanksAndLawFirms();
    });
    $('#add_law_firms_btn').button().click(function(){
        $('#law_firms').append(getLawFirmMarkup());
        initAutocompleteForBanksAndLawFirms();
    }) 
    
    initAutocompleteForBanksAndLawFirms();
	
	
	/***********
	sng:8/feb/2012
	we no longer have buyers and target. We now have participants and roles
	***************/
	
	/***********************
	sng:8/feb/2012
	*********************/
	$('#add_companies_btn').button().click(function(){
        $('#companies').append(get_company_markup());
		/***************
		For companies, we want to show the HQ, sector, industry in the suggestion dropdown
		Problem is, this is not working for dynamically added textbox
		initAutocompleteForCompanies();
		*****************/
        /***********
		sng:8/feb/2012
		*************/
		beautify_role_dropdown();
		init_participants_footnote();
    });
	/***************************************************/       
});
/*****************
sng:8/feb/2012
*******************/
function beautify_role_dropdown(){
	$('.list-item2 select').selectmenu();
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
sng:8/feb/2012
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
function initAutocompleteForBanksAndLawFirms() {
	autoCompleteWithCustomOpenCallBack("#banks input[type=text]", "ajax/sugest_a_deal_search_firm.php?for=advisors&type=1");
	autoCompleteWithCustomOpenCallBack("#law_firms input[type=text]", "ajax/sugest_a_deal_search_firm.php?for=advisors&type=2");
}
/*******************
sng:8/feb/2012

We want to show the name as well as HQ, sector, industry in the suggestion dropdown

Problem is, unless we add autocomplete to the input field directly, it does not work for dynamically added items.
To handle that, we updated the _v2 to accept the jQuery obj directly.

That breaks the code here. So we get the group of inputs and iterate through each.
***************/
function initAutocompleteForCompanies() {
	/************
	sng:10/feb/2012
	Now we have 2 kinds of textboxes, one for company and the other for footnote.
	We use class based selector to attach autocomplete
	****************/
	var inputs = $("#companies .participant_company");
	inputs.each(function(index){
		autoCompleteWithCustomOpenCallBack_v2($(this), "ajax/sugest_a_deal_search_firm.php"); 
	});
	
}

/*****************
sng:12/mar/2012
At any moment, we need to keep track of what is the current deal type, sub type, sub sub type
so that we can show the proper label in the notes text area. You see, we are getting rid of
the multiple text areas for notes and placing only one text area
********************/
var _curr_cat = "M&A";
var _curr_sub_cat = "Pending";
var _curr_sub_sub_cat = "";
$(function(){
	update_note_box_label();
});

function categoryChanged(category) {
    $('div.radio_subcat:visible').hide();  
    $('#subCatsForCat' + category).show();
    $('div.radio_subsubcat:visible').hide();
	
	_curr_cat = $('#deal_cat_name'+category).val();
	_curr_sub_cat = "";
	_curr_sub_sub_cat = "";
	
	update_note_box_label();
}

function subCategoryChanged(subCategory) {
    $('div.radio_subsubcat:visible').hide();
    $('#subSubCatsForCat' + subCategory).show();
	
	_curr_sub_cat = $('#deal_subCat_name'+subCategory).val();
	_curr_sub_sub_cat = "";
	
	update_note_box_label(); 
} 
/*****************
sng:12/mar/2012
We need to track the subcategory change also
****************/
function subSubCategoryChanged(subSubCategory){

	_curr_sub_sub_cat = $('#deal_subSubCat_name'+subSubCategory).val();
	update_note_box_label();
}
/****************
sng:12/mar/2012
The label of the note textarea change when the deal type/subtype/subtype change
**********************/
function update_note_box_label(){
	var curr_cat = _curr_cat.toLowerCase();
	var curr_sub_cat = _curr_sub_cat.toLowerCase();
	var curr_sub_sub_cat = _curr_sub_sub_cat.toLowerCase();
	var note_box_label = "Enter additional details here:";
	if((curr_cat == "debt")&&(curr_sub_cat=="bond")){
		note_box_label = "Enter additional details here (e.g. terms, conditions, puts/ calls):";
	}else if((curr_cat == "debt")&&(curr_sub_cat=="loan")){
		note_box_label = "Enter additional details here (e.g. terms, conditions):";
	}else if((curr_cat == "equity")&&(curr_sub_cat=="preferred")){
		note_box_label = "Enter additional details here (e.g. terms, redemption, ratings, puts/ calls):";
	}else if((curr_cat == "equity")&&(curr_sub_cat=="convertible")){
		note_box_label = "Enter additional details here (e.g. terms, redemption, ratings, puts/ calls, dividend protection mechanism):";
	}else if((curr_cat == "equity")&&(curr_sub_cat=="equity")&&(curr_sub_sub_cat=="additional")){
		note_box_label = "Enter additional details here (e.g. selling shareholders):";
	}else if((curr_cat == "equity")&&(curr_sub_cat=="equity")&&(curr_sub_sub_cat=="ipo")){
		note_box_label = "Enter additional details here (e.g. selling shareholders, greenshoe):";
	}else if((curr_cat == "equity")&&(curr_sub_cat=="equity")&&(curr_sub_sub_cat=="rights issue")){
		note_box_label = "Enter additional details here (e.g. underwriting, existing shareholders, rump):";
	}else if((curr_cat == "m&a")&&(curr_sub_cat=="pending")){
		note_box_label = "Enter additional details here (e.g. termination fees, conditions):";
	}else if((curr_cat == "m&a")&&(curr_sub_cat=="completed")){
		note_box_label = "Enter additional details here  (e.g. termination fees, conditions):";
	}
	$('#label_additional_details').html(note_box_label);
}

function toggleEquityPercentage(buttonPressed, show) {
    //console.log(show);
    if (show == true) {
        $('#equity_percentage').show();    
    } else {
        $('#equity_percentage').hide(); 
    }
    $('#transaction_type_check span.ui-button-icon-primary').remove();
    addCheckSign(buttonPressed);
}

function toggleListed(button) {
	/****************************************
	sng:21/july/2011
	We no longer use this to toggle the section attached to "Target listed in stock exchange"
    $('#publicly_listed_details').toggle();
	togleButton(button);
	**********************************************/
}

function togleButton(button) {
	/*$(button).parent().find('span.ui-button-icon-primary').remove();
	$(button).parent().find('label.ui-button-text-icon-primary').removeClass('ui-button-text-icon-primary');*/
    
	/*if ($(button).hasClass('ui-button-text-icon-primary')) {
        $(button).removeClass('ui-button-text-icon-primary');
        $(button).find('span.ui-button-icon-primary').remove(); 
    } else {
        addCheckSign(button);
    } */
	$('#hostile_or_friendly span.ui-button-icon-primary').remove();
    addCheckSign(button);
}
function addCheckSign(button) {
  	$(button).addClass('ui-button-text-icon-primary').prepend('<span class="ui-button-icon-primary ui-icon ui-icon-check"></span>');
}

/**********************************************************************
sng:12/july/2011
function to handle toggling of single button, check / uncheck
***************/
function toggle_single_button(button,id){
	//the marker class, since we are adding the id also, this will get the correct span,
	//even if there are many buttons that can be toggled
	var thisclass = id+"_ui-icon-check";
	
	if($(button).hasClass('ui-button-text-icon-primary')){
		$(button).removeClass('ui-button-text-icon-primary');
		//remove the marked span
		$('span.'+thisclass).remove();
	}else{
		//be sure to put the marker class also
		$(button).addClass('ui-button-text-icon-primary').prepend('<span class="ui-button-icon-primary ui-icon ui-icon-check '+thisclass+'"></span>');
	}
}
/********************************************************************/
function nextStep(currentStep, nextStep) {
    
    if (!validateFields(currentStep)) {
        return false;
    }
    
    $("#" + nextStep).trigger('passValidation');
    window.scrollTo(0,0);
    return true;
}

function previousStep(step) {
	$('#' + step).trigger('passValidation');
	window.scrollTo(0,0);
}

function validateFields(currentStep) {
    var curentStepId = $(currentStep).attr('id');
    var valid = true;
	if(_requiredFields[_lastClickedButton] == undefined) {
		return true;
	}
    if (_requiredFields[_lastClickedButton][curentStepId] != undefined) {
        var currentRequiredFields =  _requiredFields[_lastClickedButton][curentStepId];    
    } else {
        return true;
    }
    
    for (currentField in currentRequiredFields) {
        var fieldId =  currentRequiredFields[currentField];
        var defaultValue = ''; 
        var fieldToAddClassTo = $("#" + fieldId);
        
        if (_defaultInputs[fieldId] != undefined) {
            defaultValue = _defaultInputs[fieldId];
        } 
        
        if ($("#" + fieldId).val() == defaultValue) {
            if ($("select#" + fieldId).length>0) {
                fieldToAddClassTo = $('#' + fieldId + "-button").children().first();
            } 
			//console.log(fieldId);
            $(fieldToAddClassTo).addClass('invalidValueField'); 
            valid = false;
        } else {
           $(fieldToAddClassTo).removeClass('invalidValueField');
        }
    }
    
    if (valid == false) {
      $('#mandatoryWarning-' + curentStepId).show();  
    } else {
      $('#mandatoryWarning-' + curentStepId).hide();  
    }
    
    return valid;
}

function validateAndSubmit() {
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
	}
	?>
    var valid = validateFields($('#step3'));
    if (valid) {
        console.log('Form is valid we should submit it');
        for (index in _defaultInputs) {
            if ($('#' + index).val() == _defaultInputs[index] ) {
                $('#' + index).val('');    
            }
        }
		/**************************************************************
		sng:5/july/2011
		let us check if the checkbox 'all details are public info' is checked or not
		***************/
		if(!jQuery("#public_details").attr('checked')){
			alert("You need to confirm that all the details are public information");
			return false;
		}
		/*************************************************************/
        var data = $('#sugest_deal_form').serialize();
		/**********
		sng:9/jun/2011
		we block the UI so show that the data is being submitted
		**********/
		$.blockUI({ message: '<h1>submitting...</h1><img src="/images/loader.gif" />' });
        $.post(
            'ajax/suggest_deal/request.php?action=submitData',
            data,
            function(result) {
				$.unblockUI();
                //console.log(status);
				
				if(result.status == 1){
					apprise("Your suggestion has been stored");
				}else{
					/**********************
					sng:16/mar/2012
					We now show the error msg from server
					**********************/
					apprise(result.msg);
				}
				/********************
				sng:31/aug/2011
				We now upload files via ajax and the filenames appears in a <ul id="qq-upload-list"></ul> (we got this from the fileupload js code). Once our request completes
				we need to blank out that area
				****************/
				create_suggest_file_uploader();
            },
			"json"
        );
    } else {
         return false; 
    }
    
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
}
jQuery(document).ready(function(){
	create_suggest_file_uploader();
});  
</script>
<script>
/******************
sng:16/mar/2012
********/
function fetch_partners_roles(deal_type){
	$.get('ajax/suggest_deal/fetch_partner_roles.php?deal_type='+deal_type,function(data){
		if(data.status == '1'){
			bank_role_count = data.bank_role_count;
			bank_role_ids = new Array();
			bank_role_names = new Array();
			for(var bank_role_i=0;bank_role_i<bank_role_count;bank_role_i++){
				bank_role_ids[bank_role_i] = data.bank_role_ids[bank_role_i];
				bank_role_names[bank_role_i] = data.bank_role_names[bank_role_i];
			}
			update_bank_roles();
			
			/****************
			sng:2/may/2012
			update the law firm role dropdowns
			and data
			******************/
			law_firm_role_count = data.law_firm_role_count;
			law_firm_role_ids = new Array();
			law_firm_role_names = new Array();
			for(var law_firm_role_i=0;law_firm_role_i<law_firm_role_count;law_firm_role_i++){
				law_firm_role_ids[law_firm_role_i] = data.law_firm_role_ids[law_firm_role_i];
				law_firm_role_names[law_firm_role_i] = data.law_firm_role_names[law_firm_role_i];
			}
			update_law_firm_roles();
			
		}	
	},"json");
}
</script>
<!--//////////////////////////////////////////
sng:14/nov/2011
removed the class="registercontent" here
////////////////////////////////-->
<table width="100%" cellspacing="0" cellpadding="0" border="0"  style="width: 100%; margin: 0pt auto;font-size: 12px;">
    <tbody>
        <tr>
            <td>
			<form method="post" action="dummy.php" onsubmit="detailed_deal_search(0);return false;">
                <table width="100%" border="0" cellspacing="0" cellpadding="0" style="">
                  <tr>
                    <td width="25%">Check if a deal is already in database</td>
                    <td>    
                        <input name="search_term" id="search_term" type="text" style="border:1px solid #CCC; width: 90%; height:20px; background:url(images/search-bk.png) top left no-repeat; padding-left:20px; line-height:20px;" value="Enter name of a participant in the deal" class="special">                
                    </td>
                    <td>
                       <input type="submit" name="search" id="search" value="Search" style="float:right">
                    </td>
                  </tr>
                </table> 
				</form>
                <hr style="color: #E86200; background-color: #E86200; height: 1px; margin-top: 15px; margin-bottom: 15px; display: none;" id="resultsSeparator"/>
                <div style="display: block; width:100%;" id="results" ></div>
                <hr style="color: #E86200; background-color: #E86200; height: 1px; margin-top: 15px; margin-bottom: 15px;" />                
                <form id="sugest_deal_form" action="#" method="post">
                    <div id="multi_step_form">
                    <h3 id="step1"><a href="#">Deal information</a></h3>
                    <div style="overflow:hidden" >
                        <table width="100%" cellspacing="0" cellpadding="0" border="0">
                        <tbody>
                          <tr>
                            <td width="49%" style="border-right: 1px solid rgb(204, 204, 204);padding:10px">
                                <table width="100%" border="0" cellspacing="0" cellpadding="4">
                                  <tr>
                                    <td>Type of Deal: </td>
                                  </tr>
                                  <tr>
                                    <td>
                                      <div class="radio_cat" style="font-size:10px;">
                                        <?php
                                        if (!isset($_POST['deal_cat_name'])) {
                                             $_POST['deal_cat_name'] = 'M&A';
                                        }
                                        if (!isset($_POST['deal_subcat1_name'])) {
                                            $_POST['deal_subcat1_name'] = 'Pending';
                                        }
                                            $i = 1;
                                            foreach($categories as $categoryName=>$subCats) :?>   
                                        <input type="radio" id="deal_cat_name<?php echo $i?>" name="deal_cat_name" value="<?php echo $categoryName?>" onClick="categoryChanged(<?php echo $i?>)" <?php if($_POST['deal_cat_name']==$categoryName){?>checked<?php }?>/><label for="deal_cat_name<?php echo $i?>"><?php echo $categoryName?></label>
                                        <?php $i++;endforeach?>
                                  </div>
                                      <?php 
                                            $i = 1; $j = 1;
                                            foreach($categories as $subCategoryName=>$subCats) :?>
                                      <div class="radio_subcat" style="font-size:10px;margin-top:5px;display: <?php if($_POST['deal_cat_name']==$subCategoryName){?>block<?php } else {?> none <?php }?>;" id="subCatsForCat<?php echo $i?>">   
                                        <?php foreach  ($subCats as $subCatName => $subSubCats) : ?>
                                        <input type="radio" id="deal_subCat_name<?php echo $j?>" name="deal_subcat1_name" value="<?php echo ($subCatName == 'All') ? ''  : $subCatName?>" onClick="subCategoryChanged(<?php echo $j?>)" <?php if($_POST['deal_subcat1_name']==$subCatName){?>checked<?php }?>/><label for="deal_subCat_name<?php echo $j?>"><?php echo $subCatName?></label>
                                        <?php $j++;endforeach;?> 
                                  </div>
                                      <?php $i++; endforeach;?>
                                      
                                      <?php 
                                            $i = 1;$j = 1;$k = 1;
                                            foreach($categories as $subCategoryName=>$subCats) :?>
                                      <?php foreach  ($subCats as $subCatName => $subSubCats) : ?>
                                      <div class="radio_subsubcat <?php echo "parent_$k"?>" style="font-size:10px;margin-top:5px; display:<?php if($_POST['deal_cat_name']==$subCategoryName && $_POST['deal_subcat1_name']==$subCatName){?>block<?php } else {?> none <?php }?>;" id="subSubCatsForCat<?php echo $j?>">   
                                        <?php foreach ($subSubCats as $key=>$name) : ?>
                                        <?php if ($name == 'n/a') continue ?>
                                        <input type="radio" id="deal_subSubCat_name<?php echo $i?>" name="deal_subcat2_name" value="<?php echo $name?>" onClick="subSubCategoryChanged(<?php echo $i?>)" <?php if($_POST['deal_subcat2_name']==$name){?>checked<?php }?>/><label for="deal_subSubCat_name<?php echo $i?>"><?php echo $name?></label>
                                        <?php $i++;endforeach; ?> 
                                      </div>
                                      <?php $j++;endforeach;?> 
                                      
                                      <?php $k++; endforeach;?>                    
                                    </td>
                                  </tr>
                                </table>                 
                            </td>
                              <td align="left" valign="top" style="padding:5px">
                              <table width="100%" border="0" cellspacing="0" cellpadding="4">
                                <tr>
                                  <td colspan="3">Date of Deal</td>
                                </tr>
                                <tr>
                                  <td colspan="3">Only enter relevant dates</td>
                                </tr>
                                <tr id='rumor_date_row'>
                                  <td style="width:50px">&nbsp;</td>
                                  <td style=""><span id='rumor_date_label'>Rumour:</span></td>
                                  <td><input type="text" name="rumour_date" id="rumour_date" class="date" readonly="readonly"></td>
                                </tr>
                                <tr>
                                  <td style="width:50px">&nbsp;</td>
                                  <td style=""><span id='announced_date_label'>Announced:</span></td>
                                  <td><input type="text" name="announced_date" id="announced_date" class="date" readonly="readonly"></td>
                                </tr>
                                <tr id="exrights-date-row" style="display: none;">
                                  <td style="width:50px">&nbsp;</td>
                                  <td style=""><span id='exrights_date_label'>Ex-Rights:</span></td>
                                  <td><input type="text" name="exrights_date" id="exrights_date" class="date" readonly="readonly"></td>
                                </tr>
                        
                                <tr id='closed_date_row' style="display:none">
                                  <td style="width:50px">&nbsp;</td>
                                  <td style=""><span id='closed_date_label'>Closed:</span></td>
                                  <td><input type="text" name="closed_date" id="closed_date" class="date" readonly="readonly"></td>
                                </tr>
                              </table>
                             </td>
                          </tr>
                        </tbody>
                    </table>
                    <hr class="orange" />
                    <?php
					/*****************
					sng:8/feb/2012
					We no longer use fixed boxes
					**********************/
					?>
					<table width="100%" border="0" cellspacing="0" cellpadding="0">
					<tr><td>Companies / Entities involved in the transaction:</td></tr>
					<tr>
					<td>
					<div id="companies">
					<?php include_once("ajax/suggest_deal/snippets/participants_default.php");?>
					</div>
					<div style="display: block; float: right; margin:10px 10px 10px 10px;"> 
					<input type="button" id='add_companies_btn' value="Add More Companies" />
					</div>
</td>
</tr>
					</table>
					<hr class="orange" />
                    <table width="100%" border="0" cellspacing="0" cellpadding="4">
                      <tr>
                        <td colspan="2">Please list any links to press releases, regulatory fillings, financial news sites, etc.</td>
                      </tr>
                      <tr>
                        <td><input type="text" name="regulatory_links[]" id="regulatory_links1" class="std special"></td>
                        <td><input type="text" name="regulatory_links[]" id="regulatory_links2" class="std special"></td>
                      </tr>
                      <tr>
                        <td><input type="text" name="regulatory_links[]" id="regulatory_links3" class="std special"></td>
                        <td><input type="text" name="regulatory_links[]" id="regulatory_links4" class="std special"></td>
                      </tr>
                      <tr>
                        <td colspan="2">Upload any Files related to the transaction: </td>
                      </tr>
                      <tr>
                        <td align="center" valign="middle">
                            <div id="suggestion_file"></div>
                        </td>
                        <td>&nbsp;</td>
                      </tr>
                    </table> 
                    <hr class="orange" id='pre_seller_info' />
                    <?php
					/******************
					sng:8/feb/2012
					we no longer use conpany box and sector / industry dropdown etc. Now we will have free text box with role dropdown
					*********************/
					?>
                    <div style="margin:10px; margin-left: 60px;margin-right: 60px;" id='addition_buyer_text'>
						<?php
						/******************
						sng:1/mar/2012
						We no longer need this since user will enter the details in footnote section for each participating company
						We keep the <div> because it is used in the javascript.
                          Enter additional text on Buyer, Target and/or Seller: <br />
                        <textarea name="additional_details" cols="" rows="" style="width:100%; height:150px;"></textarea>
						**********************/
						?>
                    </div> 
                    <table width="100%" border="0">
                      <tr>
                        <td width="100px"></td>
                        <td>
                            <div class="ui-widget" style="display: none;" id="mandatoryWarning-step1">
                                <div style="padding: 0 .3em;" class="ui-state-error ui-corner-all"> 
                                    <span style="float: left; margin-right: .3em;" class="ui-icon ui-icon-alert"></span> 
                                    Fields marked with a <span class="invalidValueField"> &nbsp;&nbsp;&nbsp;</span> are mandatory.
                                </div>
                            </div>                        
                        </td>
                        <td><span style="float:right">
                          <input type="button" onclick="nextStep(this, 'step2')" value="Next step" class="next-step-button" id="step1"/>
                        </span></td>
                      </tr>
                    </table>
                 </div>   
                    <h3 id="step2"> <a href="#"> Deal valuation </a></h3>
                    <div style="overflow:hidden">
                        <div id='deal_valuation_tab' >
                            <table width="100%" border="0" cellspacing="0" cellpadding="4">
                              <tr>
                                <td>
                                    <table width="100%" border="0" cellspacing="0" cellpadding="4">
                                      <tr>
                                        <td width="48%">Implied Equity Value (in Local Currency Millions):</td>
                                        <td>Acquisition of What Percentage (%):</td>
                                      </tr>
                                      <tr>
                                        <td width="48%"><input name="implied_equity_value" type="text" class="std special" id="implied_equity_value"></td>
                                        <td><input name="aquisition_percentage" type="text" class="std special" id="aquisition_percentage"></td>
                                      </tr>
									  
									  <tr>
                                        <td>Any dividend payment on top of equity purchase:</td>
                                      </tr>
                                      <tr>
                                        <td><input name="divident_payment" type="text" class="std special" id="divident_payment"></td>
                                      </tr>
									  <?php
									  /*******************
									  sng:3/may/2012
									  some new fields
									  *********************/
									  ?>
									  <tr>
                                        <td width="48%">Total Debt (in Local Currency Millions):</td>
                                        <td>Cash (in Local Currency Millions):</td>
                                      </tr>
                                      <tr>
                                        <td width="48%"><input name="total_debt_million_local_currency" type="text" class="std special" id="total_debt_million_local_currency"></td>
                                        <td><input name="cash_million_local_currency" type="text" class="std special" id="cash_million_local_currency"></td>
                                      </tr>
									  <tr>
									  	<td width="48%">Adjustments (in Local Currency Millions):</td>
										<td width="48%">Net Debt (in Local Currency Millions):</td>
									  </tr>
									  <tr>
									  	<td width="48%"><input name="adjustments_million_local_currency" type="text" class="std special" id="adjustments_million_local_currency"></td>
										<td width="48%"><input name="net_debt" type="text" class="std special" id="net_debt"></td>
									  </tr>
									  
                                      
									  
                                      <tr>
                                        <td width="48%">Enterprise Value  (in Local Currency Millions):</td>
                                        <td>Implied Deal Size  (in Local Currency Millions):</td>
                                      </tr>
                                      <tr>
                                        <td width="48%"><input name="enterprise_value" type="text" class="std special" id="enterprise_value"></td>
                                        <td><input name="implied_deal_size_local" type="text" class="std special" id="implied_deal_size_local"></td>
                                      </tr>
                                      <tr>
                                        <td width="48%">
                                            Transaction type: <br />
                                            <div id="transaction_type_check">
                                              <input type="radio" name="transaction_type" value="cash" id="transaction_type_0"><label for="transaction_type_0" onclick="toggleEquityPercentage(this,false)">Cash</label>
                                              <input type="radio" name="transaction_type" value="equity" id="transaction_type_1"><label for="transaction_type_1" onclick="toggleEquityPercentage(this, false)">Equity</label>
                                              <input type="radio" name="transaction_type" value="part_cash_part_quity" id="transaction_type_2"> <label for="transaction_type_2" onclick="toggleEquityPercentage(this, true)">Part Cash/ part Equity</label>                
                                            </div>
                                            <br />
                                             <div id="hostile_or_friendly">
											 <?php
											 /**********
											 sng:8/jun/2011
											 We need these options from takeover_type_master, will send the id
											 *************/
											 $takeover_q = "select * from ".TP."takeover_type_master where is_active='y'";
											 $success = $db->select_query($takeover_q);
											 if($success){
											 	$takeover_q_row_count = $db->row_count();
												if($takeover_q_row_count > 0){
													$takeover_q_result = $db->get_result_set_as_array();
													for($t = 0;$t<$takeover_q_row_count;$t++){
														?>
														 <input type="radio" name="friendly_or_hostile" value="<?php echo $takeover_q_result[$t]['takeover_id'];?>" id="hostile_or_friendly<?php echo $t+1;?>"><label for="hostile_or_friendly<?php echo $t+1;?>" onclick="togleButton(this);"><?php echo $takeover_q_result[$t]['takeover_name'];?></label>
														<?php
													}
												}
											 }
											 ?>
                                              <!--<input type="radio" name="friendly_or_hostile" value="Friendly" id="hostile_or_friendly1"><label for="hostile_or_friendly1" onclick="togleButton(this);">Friendly</label>
                                              <input type="radio" name="friendly_or_hostile" value="Hostily" id="hostile_or_friendly2"><label for="hostile_or_friendly2" onclick="togleButton(this);">Hostile</label>-->
              
                                            </div>
                                        </td>
                                        <td>
                                            &nbsp;<br />
                                            <input type="text" name="equity_percentage" class="std special" id="equity_percentage" value="" style="display: none;" /></td>
                                      </tr>
                                      <tr>
                                        <td>&nbsp;</td>
                                        <td></td>
                                      </tr>
                                    </table>
                                </td>
                              </tr>
                            </table>
                        <table width="100%" border="0" cellspacing="0" cellpadding="4">
                          <tr>
                            <td width="48%">Local Currency:</td>
                            <td>Implied Deal Size (in USD Millions):</td>
                          </tr>
                          <tr>
                            <td><input type="text" name="local_currency" id="local_currency" class="std special"></td>
                            <td><input type="text" name="implied_deal_size" id="implied_deal_size" class="std special"></td>
                          </tr>
                          <tr>
                            <td>Local Currency per 1 USD:</td>
                            <td>Implied Enterprise Value (in USD Millions)</td>
                          </tr>
                          <tr>
                            <td><input type="text" name="local_currency_rate" id="local_currency_rate" class="std special"></td>
                            <td><input type="text" name="implied_enterprise_value" id="implied_enterprise_value" class="std special"></td>
                          </tr>
                          <tr>
                            <td colspan="2">&nbsp;</td>
                          </tr>
						  <!--/////////////////////////////////////////////////
  sng:21/july/2011
  When this button is clicked, a check mark is placed. However, when it is clicked again, the check mark remains (but the
  server does not get the value, which is correct since I have unchecked it
  We have created a function to deal with this.
  Instead of togleButton(), we use toggle_single_button() and pass both the element and the id;
  /////////////////////////////////////////////////////////-->
                          <tr>
                            <td colspan="2">
                                <div style="line-height: 24px;">
                                    <input type="checkbox" class="button_checkbox" name="publicly_listed" id="publicly_listed" /> <label for="publicly_listed" style="height: 24px;" onclick="toggle_single_button(this,'publicly_listed')">Target is publicly listed on a stock exchange.</label> 
                                </div>    
                            </td>
                          </tr>
                        </table>
                        <table id="publicly_listed_details" style="display: none;">
							<tr>
                            <td>Name of the stock exchange:</td>
                            <td>&nbsp;</td>
                          </tr>
						  <tr>
                            <td><input type="text" name="target_stock_exchange_name" id="target_stock_exchange_name" class="std special" value=""></td>
                            <td>&nbsp;</td>
                          </tr>
                          <tr>
                            <td>Deal price per share:</td>
                            <td>Local Currency of Share Price (if different):</td>
                          </tr>
                          <tr>
                            <td><input type="text" name="deal_price_per_share" id="deal_price_per_share" class="std special" value=""></td>
                            <td><input type="text" name="local_currency_of_share_price" id="local_currency_of_share_price" class="std special"></td>
                          </tr>
                          <tr>
                            <td>Share price prior to announcement:</td>
                            <td>Date of share price, prior to announcement:</td>
                          </tr>
                          <tr>
                            <td><input type="text" name="share_price_prior_to_announcement" id="share_price_prior_to_announcement" class="std special"></td>
                            <td><input class="date" type="text" name="date_of_share_price_prior_to_announce" id="date_of_share_price_prior_to_announce"></td>
                          </tr>
                          <tr>
                            <td>Implied Premium:</td>
                            <td>Total shares outstanding (million)</td>
                          </tr>
                          <tr>
                            <td><input type="text" name="implied_premium" id="implied_premium" class="std special"></td>
                            <td><input type="text" name="total_shares_outstanding" id="total_shares_outstanding" class="std special"></td>
                          </tr>
                        </table>
						<!--/////////////////////////
						  sng:12/mar/2012
						  we will only use a single 'note' textarea
                        <table>
                           <tr>
                            <td colspan="2" style="padding:10px;">
                                <div style="margin: 10px 60px;">
                                  Enter additional text deal value: <br>
                                  <textarea name="addition_text_on_deal_value" style="width: 100%; height: 150px"></textarea>
                                </div>
                            </td>
                          </tr>
                        </table>
						///////////////////////////-->
                    </div>
                    <table width="100%" border="0">
                      <tr>
                        <td width="100px"><span style="float:right">
                          <input type="button" onclick="previousStep('step1')" value="Previous step" class="next-step-button" />
                        </span></td>
                        <td>
                            <div class="ui-widget" style="display: none;" id="mandatoryWarning-step2">
                                <div style="padding: 0 .3em;" class="ui-state-error ui-corner-all"> 
                                    <span style="float: left; margin-right: .3em;" class="ui-icon ui-icon-alert"></span> 
                                    Fields marked with a <span class="invalidValueField"> &nbsp;&nbsp;&nbsp;</span> are mandatory.
                                </div>
                            </div>                        
                        </td>
                        <td><span style="float:right">
                          <input type="button" onclick="nextStep(this, 'step3')" value="Next step" class="next-step-button" id="step2"/>
                        </span></td>
                      </tr>
                    </table>                    
                   </div> 
                    <h3 id="step3"> <a href="#"> Additional Information </a></h3> 
                    <div style="overflow: hidden;">
                        <div id='aditional_tab_details'>
                            <table width="100%" border="0" cellspacing="0" cellpadding="4">
                              <tr>
                                <td width="48%">Termination Fee (in Local Currency Millions):</td>
                                <td>End Date for Termination Fee:</td>
                              </tr>
                              <tr>
                                <td><input type="text" name="termination_fee" id="termination_fee" class="std"></td>
                                <td><input type="text" name="end_date_for_termination_fee" id="end_date_for_termination_fee" class="date"></td>
                              </tr>
							  <!--/////////////////////////
							  sng:12/mar/2012
							  we will only use a single 'note' textarea
                              <tr>
                                <td colspan="2">Enter additional text on termination fee:</td>
                              </tr>
                              <tr>
                                <td colspan="2">
                                    <div style="margin: 10px 60px;">
                                      <textarea name="addition_text_on_termination_fee" style="width: 100%; height: 150px"></textarea>
                                    </div>    
                                </td>
                              </tr>
                              <tr>
                                <td colspan="2">Enter text on Conditions: </td>
                              </tr>
                              <tr>
                                <td colspan="2">
                                    <div style="margin: 10px 60px;">
                                      <textarea name="addition_text_on_conditions" style="width: 100%; height: 150px"></textarea>
                                    </div>    
                                </td>
                              </tr>
							  ///////////////////////////-->
                              <tr>
                                <td>Fee (%) to Sellside Advisors:</td>
                                <td>Fee (%) to Buyside Advisors:</td>
                              </tr>
                              <tr>
                                <td><input type="text" name="fee_to_sellside" id="fee_to_sellside" class="std special"></td>
                                <td><input type="text" name="fee_to_buyside" id="fee_to_buyside" class="std special"></td>
                              </tr>
                            </table>
                            <hr class="orange" />
                            Financial Metrics (if publicly available):<br />
                            Local Currency, Millions<br />
                            <table width="100%" border="0" cellspacing="0" cellpadding="4">
                              <tr>
                                <td width="10%">&nbsp;</td>
                                <td width="24%">&nbsp;</td>
                                <td width="24%" align="center">Last 12 Months:</td>
                                <td width="24%" align="center">Most Recent Year:</td>
                                <td width="24%" align="center">Next Year:</td>
                              </tr>
                              <tr>
                                <td>&nbsp;</td>
                                <td>Revenues:</td>
                                <td align="center"><input type="text" name="revenues_last_12_months" id="revenues_last_12_months"></td>
                                <td align="center"><input type="text" name="revenues_most_recent_year" id="revenues_most_recent_year"></td>
                                <td align="center"><input type="text" name="revenues_next_year" id="revenues_next_year"></td>
                              </tr>
                              <tr>
                                <td>&nbsp;</td>
                                <td>EBITDA:</td>
                                <td align="center"><input type="text" name="ebitda_last_12_months" id="ebitda_last_12_months"></td>
                                <td align="center"><input type="text" name="ebitda_most_recent_year" id="ebitda_most_recent_year"></td>
                                <td align="center"><input type="text" name="ebitda_next_year" id="ebitda_next_year"></td>
                              </tr>
                              <tr>
                                <td>&nbsp;</td>
                                <td>Net Income:</td>
                                <td align="center"><input type="text" name="net_income_last_12_months" id="net_income_last_12_months"></td>
                                <td align="center"><input type="text" name="net_income_most_recent_year" id="net_income_most_recent_year"></td>
                                <td align="center"><input type="text" name="net_income_net_year" id="net_income_net_year"></td>
                              </tr>
                            </table>
                            Year-End Date of Most Recent Financial Year <span style="width:30px;"> &nbsp;</span><input class="date" name="year_end_of_most_recent_financial_year" />                        </div>         
                        <hr class="orange">
						<?php
						/*************************
						sng:12/mar/2012
						We will now have a single 'note' box
						***************************/
						?>
						<div style="margin: 10px 60px;">
							 <span id="label_additional_details">Enter additional details here:</span><br>
							<textarea style="width: 100%; height: 150px;" rows="" cols="" name="additional_deal_details_note"></textarea>
						</div>
						<hr class="orange">
                        <table width="100%" cellspacing="5" cellpadding="5" border="0" class="registercontent">
                            <tbody>
                                <tr>
                                    <th width="49%">Banks involved in the deal</th>
                                </tr>
                                <tr>
                                    <td>
                                    <div id="banks">
                                          <div class="list-item" >
                                              <table width="100%" border="0" cellspacing="0" cellpadding="4">
                                                <tr>
                                                  <td>Bank 1:      </td>
                                                  <td><input type="text" name="banks[]" style="width: 100%;" id='bank1'></td>
                                                </tr>
                                                
												<?php
												/****************************
												sng:16/mar/2012
												The roles dropdowns for partner banks
												*******************************/
												?>
												<tr>
													<td>&nbsp;</td>
													<td>
													<select name="bank_role_id_1" id="bank_role_id_1">
													<option value="0">Select role</option>
													<?php
													for($bank_role_i=0;$bank_role_i<$g_view['bank_roles_count'];$bank_role_i++){
														?>
														<option value="<?php echo $g_view['bank_roles'][$bank_role_i]['role_id'];?>"><?php echo $g_view['bank_roles'][$bank_role_i]['role_name'];?></option>
														<?php
													}
													?>
													</select>
													</td>
												</tr>
												<?php
												/***********************
												sng:23/mar/2012
												We no longer need this checkbox 'Not lead advisor' since we now have role like 'Junior Advisor'
												**************************/
												?>
                                              </table>
                                          </div>     
                                          <div class="list-item" >
                                              <table width="100%" border="0" cellspacing="0" cellpadding="4">
                                                <tr>
                                                  <td>Bank 2:      </td>
                                                  <td><input type="text" name="banks[]" style="width: 100%;"></td>
                                                </tr>
                                                
												<tr>
													<td>&nbsp;</td>
													<td>
													<select name="bank_role_id_2" id="bank_role_id_2">
													<option value="0">Select role</option>
													<?php
													for($bank_role_i=0;$bank_role_i<$g_view['bank_roles_count'];$bank_role_i++){
														?>
														<option value="<?php echo $g_view['bank_roles'][$bank_role_i]['role_id'];?>"><?php echo $g_view['bank_roles'][$bank_role_i]['role_name'];?></option>
														<?php
													}
													?>
													</select>
													</td>
												</tr>
												
                                              </table>
                                          </div>
                                          <div class="list-item" >
                                              <table width="100%" border="0" cellspacing="0" cellpadding="4">
                                                <tr>
                                                  <td>Bank 3:      </td>
                                                  <td><input type="text" name="banks[]" style="width: 100%;"></td>
                                                </tr>
                                                
												<tr>
													<td>&nbsp;</td>
													<td>
													<select name="bank_role_id_3" id="bank_role_id_3">
													<option value="0">Select role</option>
													<?php
													for($bank_role_i=0;$bank_role_i<$g_view['bank_roles_count'];$bank_role_i++){
														?>
														<option value="<?php echo $g_view['bank_roles'][$bank_role_i]['role_id'];?>"><?php echo $g_view['bank_roles'][$bank_role_i]['role_name'];?></option>
														<?php
													}
													?>
													</select>
													</td>
												</tr>
												
                                              </table>
                                          </div>
                                    </div>
                                    <div style="display: block; float: right;"> 
                                        <input type="button" id='add_banks_btn' value="Add more" />
                                    </div>                                      
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <table width="100%" cellspacing="5" cellpadding="5" border="0" class="registercontent">
                            <tbody>
                                <tr>
                                    <th width="49%">Law Firms involved in the deal</th>
                                </tr>
                                <tr>
                                    <td>
                                    <div id="law_firms">
                                          <div class="list-item" >
                                              <table width="100%" border="0" cellspacing="0" cellpadding="4">
                                                <tr>
                                                  <td>Law Firm 1:      </td>
                                                  <td><input type="text" name="law_firms[]" style="width: 100%;" id='law_firm1'></td>
                                                </tr>
												<?php
												/****************************
												sng:2/may/2012
												The roles dropdowns for partner law firms
												*******************************/
												?>
												<tr>
													<td>&nbsp;</td>
													<td>
													<select name="law_firm_role_id_1" id="law_firm_role_id_1">
													<option value="0">Select role</option>
													<?php
													for($law_firm_role_i=0;$law_firm_role_i<$g_view['law_firm_roles_count'];$law_firm_role_i++){
														?>
														<option value="<?php echo $g_view['law_firm_roles'][$law_firm_role_i]['role_id'];?>"><?php echo $g_view['law_firm_roles'][$law_firm_role_i]['role_name'];?></option>
														<?php
													}
													?>
													</select>
													</td>
												</tr>
                                                
                                              </table>
                                          </div>     
                                          <div class="list-item" >
                                              <table width="100%" border="0" cellspacing="0" cellpadding="4">
                                                <tr>
                                                  <td>Law Firm 2:      </td>
                                                  <td><input type="text" name="law_firms[]" style="width: 100%;"></td>
                                                </tr>
												<tr>
													<td>&nbsp;</td>
													<td>
													<select name="law_firm_role_id_2" id="law_firm_role_id_2">
													<option value="0">Select role</option>
													<?php
													for($law_firm_role_i=0;$law_firm_role_i<$g_view['law_firm_roles_count'];$law_firm_role_i++){
														?>
														<option value="<?php echo $g_view['law_firm_roles'][$law_firm_role_i]['role_id'];?>"><?php echo $g_view['law_firm_roles'][$law_firm_role_i]['role_name'];?></option>
														<?php
													}
													?>
													</select>
													</td>
												</tr>
                                                
                                              </table>
                                          </div>
                                          <div class="list-item" >
                                              <table width="100%" border="0" cellspacing="0" cellpadding="4">
                                                <tr>
                                                  <td>Law Firm 3:      </td>
                                                  <td><input type="text" name="law_firms[]" style="width: 100%;"></td>
                                                </tr>
												<tr>
													<td>&nbsp;</td>
													<td>
													<select name="law_firm_role_id_3" id="law_firm_role_id_3">
													<option value="0">Select role</option>
													<?php
													for($law_firm_role_i=0;$law_firm_role_i<$g_view['law_firm_roles_count'];$law_firm_role_i++){
														?>
														<option value="<?php echo $g_view['law_firm_roles'][$law_firm_role_i]['role_id'];?>"><?php echo $g_view['law_firm_roles'][$law_firm_role_i]['role_name'];?></option>
														<?php
													}
													?>
													</select>
													</td>
												</tr>
                                                
                                              </table>
                                          </div>
                                    </div>
                                    <div style="display: block; float: right;"> 
                                        <input type="button" id='add_law_firms_btn' value="Add more" />
                                    </div>                                      
                                    </td>
                                </tr>
                            </tbody>
                        </table> 
                        <hr class="orange" />  
						<?php
						/*****************
						sng:14/mar/2012
						We have this in the simple submission. We allow the sender to specify emails of members who should be notified.
						***********************/
						?>
						<div style="margin: 10px 60px;">
							 Please notify colleagues and partners that this transaction has been submitted (please separate emails with commas):<br>
							<textarea style="width: 100%; height: 150px;" rows="" cols="" name="notification_email_list"></textarea>
							<input type="checkbox" name="not_mine" id="not_mine"><label for="not_mine">Do not attribute email notification to my account</label><br />
							<?php
							/******************
							sng:14/mar/2012
							Now we automatically notify the interested parties
							**********************/
							?>
						</div>
						<hr class="orange" />                  
                        <table width="100%" border="0" cellspacing="0" cellpadding="4">
                            <tr>
                                <td>
                                <input type="button" onclick="previousStep('step2')" value="Previous step" class="next-step-button" />
                                </td>
                                <td>
                                    <input type="checkbox" name="public_details" id="public_details">
                                    <label for="email_participants">Please tick to confirm all details are public information</label>
                                    <div class="ui-widget" style="display: none;" id="mandatoryWarning-step3">
                                        <div style="padding: 0 .3em;" class="ui-state-error ui-corner-all"> 
                                            <span style="float: left; margin-right: .3em;" class="ui-icon ui-icon-alert"></span> 
                                            Fields marked with a <span class="invalidValueField"> &nbsp;&nbsp;&nbsp;</span> are mandatory.
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <input type="submit" name="submit_data" id="submit_data" value="Submit Data" onclick="return validateAndSubmit()">
                                </td>
                            </tr>
                        </table>
                 </div>
                
                </form>
                                 
            </td>
        </tr>
    </tbody>
</table>
<script>
$('#publicly_listed').button().click(function(){
		$('#publicly_listed_details').toggle();
	});
</script>
<?php
/******************
sng:13/dec/2011
If the user is not logged in, we show a popup
********************/
if(!$g_account->is_site_member_logged()){
	?>
	<script>
	$(function(){
		apprise("Please log-in before you enter / submit new data, many thanks.",{'textOk':'OK'});
	});
	</script>
	<?php
}
?>
