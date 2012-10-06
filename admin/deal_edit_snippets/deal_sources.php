<?php
/**************
sng:3/oct/2012
For now, we just get the current sources
For now, we do not show the source addition section

sng:6/oct/2012
We show source addition
We allow multiple delete at once
**************/
?>
<div class="msg_txt" id="source_edit_msg"></div>
<div id="deal_source_content"></div>

<script>

function add_source_url_markup(){
	var _source_url_markup = '<div><input type="text" name="regulatory_links[]" style="width:200px;" /></div>';
	$('#source_url_list').append(_source_url_markup);
}

function submit_frm_edit_source(){
	
	$('source_edit_msg').html('sending...');
	$.post('ajax/deal_edit/add_deal_sources.php',$('#frm_edit_source').serialize(),function(result){
		$('#source_edit_msg').html(result.msg);
		if('y'==result.added){
			fetch_deal_sources_for_admin();
		}
	},"json");
}
</script>

<script>
function fetch_deal_sources_for_admin(){
	$.blockUI({ message: '<p><strong>Fetching...</strong></p><img src="../images/loader.gif" />' });
	$.get(
		'ajax/deal_edit/fetch_deal_sources.php?deal_id=<?php echo $g_view['deal_id'];?>&dummy='+$.now(),
		function(data) {
			$.unblockUI();
			$('#deal_source_content').html(data);
		}
	)
}

function submit_frm_delete_source(){
	var ok = confirm("Are you sure you want to delete the source URLs?");
	if(!ok){
		return;
	}
	$('#source_edit_msg').html('Deleting...');
	$.post('ajax/deal_edit/delete_deal_sources.php',$('#frm_delete_source').serialize(),function(data){
		$('#source_edit_msg').html(data.msg);
		if(data.deleted=='y'){
			fetch_deal_sources_for_admin();
		}
			
	},"json");
}
</script>