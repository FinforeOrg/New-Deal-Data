<?php
/************************
sng:5/oct/2012
We fetch the current note and allow admin to append to the note
*******************/
?>
<div class="msg_txt" id="note_edit_msg"></div>
<table cellpadding="5" cellspacing="0">
<tr>
<td id="deal_notes"></td>
<td style="width:50px;">&nbsp;</td>
<td style="vertical-align:top;">
<div><strong>Your Addition</strong></div>
<div class="hr_div"></div>
<div>
<textarea id="deal_note" style="width:300px; height:100px; overflow:auto"></textarea>
</div>
<div><input type="button" value="Submit" onClick="add_to_notes();" /></div>
</td>
</tr>
</table>
<script>
function fetch_deal_notes_for_admin(){
	$.blockUI({ message: '<p><strong>Fetching...</strong></p><img src="../images/loader.gif" />' });
	$.get(
		'ajax/deal_edit/fetch_deal_notes.php?deal_id=<?php echo $g_view['deal_id'];?>&dummy='+$.now(),
		function(data) {
			$.unblockUI();
			$('#deal_notes').html(data);
		}
	)
}

function add_to_notes(){
	$('#note_edit_msg').html("Adding...");
	var deal_id = <?php echo $g_view['deal_id'];?>;
	var note_txt = $('#deal_note').val();
	
	$.post(
		'ajax/deal_edit/update_deal_note.php',
		{deal_id:deal_id,note:note_txt,append:'y'},
		function(data) {
			$('#note_edit_msg').html(data.msg);
			if(data.updated=='y'){
				fetch_deal_notes_for_admin();
			}
			
		},"json"
	)
}
</script>