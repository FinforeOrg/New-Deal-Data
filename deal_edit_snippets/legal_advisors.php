<?php
/****************
sng:12/apr/2012

sng:13/apr/2012
We change this a bit. We now use a function to load the content via ajax. This way we can reload the content.
*****************/
?>

<div class="deal-edit-snippet" id="deal-edit-snippet-legal-advisors">
Loading...
</div>
<script>
$(function(){
	load_legal_advisor();
});

function load_legal_advisor(){
	$('#deal-edit-snippet-legal-advisors').html('Loading...');
	$.get('ajax/suggest_deal_correction/fetch_submitted_firms.php?deal_id=<?php echo $g_view['deal_data']['deal_id'];?>&type=law firm',function(data){
		$('#deal-edit-snippet-legal-advisors').html(data);
	});
}
</script>