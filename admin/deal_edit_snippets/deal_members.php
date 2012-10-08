<?php
/***************
sng:8/oct/2012
We fetch the members associated with deals
***************/
?>
<div id="deal_member_content"></div>
<script>
function fetch_deal_members_for_admin(){
	$.blockUI({ message: '<p><strong>Fetching...</strong></p><img src="../images/loader.gif" />' });
	$.get(
		'ajax/deal_edit/fetch_deal_members.php?deal_id=<?php echo $g_view['deal_id'];?>&dummy='+$.now(),
		function(data) {
			$.unblockUI();
			$('#deal_member_content').html(data);
		}
	)
}
</script>