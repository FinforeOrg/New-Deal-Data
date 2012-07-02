<?php
/****************
sng:18/apr/2012
Let us load the whole thing in ajax. That way we have a single page and can create a table grid.
We can also refresh the section after storing the suggestion
******************/
?>
<div class="deal-edit-snippet" id="deal-edit-snippet-participants">
Loading...
</div>
<script>
$(function(){
	load_participants();
});

function load_participants(){
	$('#deal-edit-snippet-participants').html("Loading...");
	$.get('ajax/suggest_deal_correction/fetch_submitted_participants.php?deal_id=<?php echo $g_view['deal_data']['deal_id'];?>',function(data){
		$('#deal-edit-snippet-participants').html(data);
	});
}
</script>