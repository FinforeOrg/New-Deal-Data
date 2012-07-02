<?php
/********************
sng:19/may/2012

We load the data using ajax
**********************/
?>
<div class="deal-edit-snippet" id="company_identifier_edit_suggestions">
Loading...
</div>
<script>
$(function(){
	fetch_company_identifier_edit();
});

function fetch_company_identifier_edit(){
	$('#company_identifier_edit_suggestions').html("Loading...");
	$.get('ajax/suggest_company_correction/fetch_submitted_company_identifiers.php?company_id=<?php echo $g_view['curr_company_id'];?>',function(data){
		$('#company_identifier_edit_suggestions').html(data);
	});
}
</script>