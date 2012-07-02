<?php
/****************
24/mar/2012
Here we load everything in ajax
********************/
?>
<div class="deal-edit-snippet" id="deal-edit-snippet-additional-deal-data">
Loading...
</div>
<script>
$(function(){
	$.get('ajax/suggest_deal_correction/fetch_additional_details.php?deal_id=<?php echo $g_view['deal_data']['deal_id'];?>&deal_cat_name=<?php echo $g_view['deal_data']['deal_cat_name'];?>&deal_subcat1_name=<?php echo $g_view['deal_data']['deal_subcat1_name'];?>&deal_subcat2_name=<?php echo $g_view['deal_data']['deal_subcat2_name'];?>',function(data){
		$('#deal-edit-snippet-additional-deal-data').html(data);
	});
});
</script>