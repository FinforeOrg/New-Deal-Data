<?php
/********************
sng:11/may/2012

We load the data using ajax
**********************/
?>
<div class="deal-edit-snippet" id="firm_edit_suggestions">
Loading...
</div>
<script>
$(function(){
	fetch_firm_edit();
});

function fetch_firm_edit(){
	$('#firm_edit_suggestions').html("Loading...");
	$.get('ajax/suggest_company_correction/fetch_submitted_firms.php?firm_id=<?php echo $g_view['company_id'];?>',function(data){
		$('#firm_edit_suggestions').html(data);
	});
}
</script>