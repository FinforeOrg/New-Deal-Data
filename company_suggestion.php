<link rel="stylesheet" type="text/css" href="css/accordion/accordion.core.css" />
<link rel="stylesheet" type="text/css" href="css/accordion/accordion.style.css" />
<script src="js/accordion/jquery.accordion.2.0.js"></script>
<script>
function company_sector_changed(){
	var sector_obj = document.getElementById('sector');
	var offset_selected = sector_obj.selectedIndex;
	if(offset_selected != 0){
		var sector_selected = sector_obj.options[offset_selected].value;
		//fetch the list of industries
		$.post("admin/ajax/industry_list_for_sector.php", {sector: ""+sector_selected+""}, function(data){
				if(data.length >0) {
					$('#industry').html(data);
					/*$('#industry').selectmenu();*/
				}
		});
	}
}
function can_edit_alert(){
	<?php
	if(!$g_account->is_site_member_logged()){
		?>
		apprise("Please log-in before you enter / submit new data, many thanks.",{'textOk':'OK'});
		<?php
	}
	?>
}

function show_non_login_alert(){
	apprise("Please log-in before you submit correction, many thanks.",{'textOk':'OK'});
}
</script>
<script>
function validate_company_suggestion_posting(formData, jqForm, options) {
	jQuery("#company_data_frm_msg").html("submitting...");
	return true;
}
function company_suggestion_posted(data, statusText)  {
	if (statusText == 'success') {
		if (data.success == 'y') {
			jQuery("#company_data_frm_msg").html(data.msg);
		} else {
			jQuery("#company_data_frm_msg").html(data.msg);
		}
	} else {
		jQuery("#company_data_frm_msg").html('Unknown error!');
	}
}
</script>
<script>


function submit_company_correction(){
	var suggestion_options = {
		beforeSubmit:  validate_company_suggestion_posting,
		success:       company_suggestion_posted,
		url:       'ajax/suggest_company_correction/company.php',  // your upload script
		dataType:  'json'
	};
	jQuery("#company_data_frm").ajaxSubmit(suggestion_options);
	return false;
}
</script>

<script>
function validate_company_identifier_suggestion_posting(formData, jqForm, options) {
	jQuery("#company_identifier_data_frm_msg").html("submitting...");
	return true;
}

function company_identifier_suggestion_posted(data, statusText)  {
	if (statusText == 'success') {
		if (data.success == 'y') {
			jQuery("#company_identifier_data_frm_msg").html(data.msg);
		} else {
			jQuery("#company_identifier_data_frm_msg").html(data.msg);
		}
	} else {
		jQuery("#company_identifier_data_frm_msg").html('Unknown error!');
	}
}

function submit_company_identifier_correction(){
	var suggestion_options = {
		beforeSubmit:  validate_company_identifier_suggestion_posting,
		success:       company_identifier_suggestion_posted,
		url:       'ajax/suggest_company_correction/company_identifier.php',  // your upload script
		dataType:  'json'
	};
	jQuery("#company_identifier_data_frm").ajaxSubmit(suggestion_options);
	return false;
}
</script>
<style type="text/css">
.hr_div
{
	height:10px;
	margin-top:20px;
	border-top:1px solid #CCCCCC;
}
</style>

<ul id="example1" class="accordion">
	<li>
	<h3>Details</h3>
	<div class="panel loading">
	<?php require("company_edit_snippets/company_edit.php");?>
	</div>
	</li>
	
	<li>
	<h3>Identifiers</h3>
	<div class="panel loading">
	<?php require("company_edit_snippets/company_identifiers_edit.php");?>
	</div>
	</li>
</ul>
<script src="js/jquery.form.js"></script>
<script>
$(function(){
	$('#example1').accordion();
});
</script>