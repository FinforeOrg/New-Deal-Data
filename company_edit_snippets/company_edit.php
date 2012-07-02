<?php
/********************
sng:19/may/2012

We load the data using ajax
**********************/
?>
<div class="deal-edit-snippet" id="company_edit_suggestions">
Loading...
</div>
<script>
$(function(){
	fetch_company_edit();
});

function fetch_company_edit(){
	$('#company_edit_suggestions').html("Loading...");
	$.get('ajax/suggest_company_correction/fetch_submitted_companies.php?company_id=<?php echo $g_view['curr_company_id'];?>',function(data){
		$('#company_edit_suggestions').html(data);
	});
}
</script>