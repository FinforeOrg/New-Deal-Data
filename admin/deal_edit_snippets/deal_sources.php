<?php
/**************
sng:3/oct/2012
We show the original submission, and the subsequent additions.
For now, we do not show the source addition section
**************/
?>
<div class="msg_txt" id="source_edit_msg"></div>
<table cellpadding="5" cellspacing="0">
<tr>
<td id="deal_sources"></td>
<td>
	<!--//////////////////placeholder for adding source by admin///////////////////////-->
</td>
</tr>
</table>
<script>
function fetch_deal_sources_for_admin(){
	$.blockUI({ message: '<p><strong>Fetching...</strong></p><img src="../images/loader.gif" />' });
	$.get(
		'ajax/deal_edit/fetch_deal_sources.php?deal_id=<?php echo $g_view['deal_id'];?>&dummy='+$.now(),
		function(data) {
			$.unblockUI();
			$('#deal_sources').html(data);
		}
	)
}

function delete_deal_source(source_id){
	var ok = confirm("Are you sure you want to delete the source URL?");
	if(ok){
		$('#source_edit_msg').html('Deleting...');
		$.post(
			'ajax/deal_edit/delete_deal_sources.php',
			{id:source_id},
			function(data) {
				$('#source_edit_msg').html(data.msg);
				if(data.deleted=='y'){
					fetch_deal_sources_for_admin();
				}
				
			},"json"
		)
	}
}
</script>