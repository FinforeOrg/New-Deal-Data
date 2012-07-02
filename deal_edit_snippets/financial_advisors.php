<?php
/****************
sng:23/mar/2012

sng:5/apr/2012
Let us load the whole thing in ajax. This allows us to refresh the section after suggestion submission

?&&deal_subcat1_name=<?php echo $g_view['deal_data']['deal_subcat1_name'];?>&deal_subcat2_name=<?php echo $g_view['deal_data']['deal_subcat2_name'];?>

sng:13/apr/2012
We change this a bit. We now use a function to load the content via ajax. This way we can reload the content.
*****************/
?>

<div class="deal-edit-snippet" id="deal-edit-snippet-financial-advisors">
Loading...
</div>
<script>
$(function(){
	load_financial_advisor();
});

function load_financial_advisor(){
	$('#deal-edit-snippet-financial-advisors').html("Loading...");
	$.get('ajax/suggest_deal_correction/fetch_submitted_firms.php?deal_id=<?php echo $g_view['deal_data']['deal_id'];?>&type=bank',function(data){
		$('#deal-edit-snippet-financial-advisors').html(data);
	});
}
</script>