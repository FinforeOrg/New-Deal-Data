<?php
/***************
sng:20/mar/2012
*****************/
?>
<script>
_defaultInputs['regulatory_links1'] = 'http://'; 
_defaultInputs['regulatory_links2'] = 'http://';

//we start with 2 url input box, so next one will be 3
var _current_url_num = 3;
var _url_markup = '';
var _default_url_input = 'http://';

function get_url_markup(){
	_url_markup = '<div><input type="text" name="regulatory_links[]" id="regulatory_links'+_current_url_num+'" class="deal-edit-snippet-textbox std special" value="'+_default_url_input+'"></div>';
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
<div class="deal-edit-snippet">
<table style="width:950px;">
<tr>
<td class="deal-edit-snippet-header" style="width:300px;">Original Submission:</td>
<td class="deal-edit-snippet-header" style="width:350px;">Additions:</td>
<td class="deal-edit-snippet-header" style="width:300px;">Your Addition:</td>
</tr>

<tr>

<td class="deal-edit-snippet-left-td">
<?php
/*****************************
sng:2/may/2012
Now when members suggests sources, those gets added. Therefore, fetching the sources for the deal data
does not show us the original submission.
We store the original submission in transaction_sources_suggestions with is_correction=n. We fetch those.
***************************/
require_once("classes/class.transaction_suggestion.php");
$trans_suggestion = new transaction_suggestion();

$g_view['sources'] = NULL;
$g_view['sources_count'] = 0;

$ok = $trans_suggestion->fetch_sources($g_view['deal_data']['deal_id'],true,$g_view['sources'],$g_view['sources_count']);
if(!$ok){
	/***********
	as this is enbedded and there are codes after this, we cannot take a short cut
	***********/
	?><div>error fetching original submission</div><?php
}else{
	if(0 == $g_view['sources_count']){
		?><div>None available</div><?php
	}else{
		?>
		<div>
		<ol>
		<?php
		for($j=0;$j<$g_view['sources_count'];$j++){
			$source = $g_view['sources'][$j]['source_url'];
			?><li><a href="<?php echo $source;?>" target="_blank"><?php echo $source;?></a></li><?php
		}
		?>
		</ol>
		</div>
		<?php
	}
}

/********************
For these we use the deal_data info
**********************/
?>
<div class="hr_div"></div>
<div class="deal-edit-snippet-footer">Submitted <?php echo $g_view['submisson_date'];?></div>
<div class="deal-edit-snippet-footer"><?php echo $g_view['deal_submitter'];?></div>
</td>

<td id="suggested_sources" class="deal-edit-snippet-middle-td">

</td>

<td class="deal-edit-snippet-right-td">
<form id="frm_edit_source">
<input type="hidden" name="deal_id" value="<?php echo $g_view['deal_data']['deal_id'];?>" />
<div id="url_list">
<div><input type="text" name="regulatory_links[]" id="regulatory_links1" class="deal-edit-snippet-textbox std special"></div>
<div><input type="text" name="regulatory_links[]" id="regulatory_links2" class="deal-edit-snippet-textbox std special"></div>
</div>
<div> 
<input type="button" id='add_url_btn' value="Add More URLs" />
</div>

<div id="result_frm_edit_source" class="msg_txt"></div>
<div class="hr_div"></div>
<div style="text-align:right;"><input type="button" value="Submit" class="btn_auto" onClick="submit_frm_edit_source();" /></div>
</form>
</td>
</tr>

</table>
</div>
<script>
function submit_frm_edit_source(){
	if(can_submit()){
		/*****************
		clear the default texts, then take the values. Unfortunately, it does not clear the dynamically added url boxes.
		I have written a method that scans through each and if the value is default, set it to blank
		*****************/
		$('[id^="regulatory_links"]').each(function(i){
			if($(this).val()==_default_url_input){
				$(this).val('');
			}
		});
		
		$('result_frm_edit_source').html('sending...');
		$.post('ajax/suggest_deal_correction/source.php',$('#frm_edit_source').serialize(),function(result){
			$('#result_frm_edit_source').html(result);
		});
	}
}

function fetch_suggested_sources(){
	$.get('ajax/suggest_deal_correction/fetch_submitted_sources.php?deal_id=<?php echo $g_view['deal_data']['deal_id'];?>',function(result){
		$('#suggested_sources').html(result);
	});
}

$(function(){
	$('#add_url_btn').button().click(function(){
        $('#url_list').append(get_url_markup());
		set_url_inputs_defaults();
    });
	$('[id^="regulatory_links"]').val(_default_url_input);
	set_url_inputs_defaults();
	fetch_suggested_sources();
});
</script>