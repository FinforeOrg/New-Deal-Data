<?php
/***************
sng:20/mar/2012

sng:30/apr/2012
We should not get the note for original submission column from the transaction_note table since
any corrective addition update the note.
We now store the original submission in the transaction_note_suggestions also. We use that.

*****************/
?>
<div class="deal-edit-snippet">
<table style="width:950px;">
<tr>
<td class="deal-edit-snippet-header" style="width:300px;">Original Submission:</td>
<td class="deal-edit-snippet-header" style="width:350px;">Additions:</td>
<td class="deal-edit-snippet-header" style="width:300px;">Your Addition:</td>
</tr>

<tr>

<td class="deal-edit-snippet-left-td">

<?php
require_once("classes/class.transaction_suggestion.php");
$g_view['notes'] = NULL;
$g_view['notes_count'] = 0;
$trans_suggestion = new transaction_suggestion();
$ok = $trans_suggestion->fetch_notes($g_view['deal_data']['deal_id'],true,$g_view['notes'],$g_view['notes_count']);
if(!$ok){
	/***********
	as this is enbedded and there are codes after this, we cannot take a short cut
	***********/
	?><div>error fetching original submission</div><?php
}else{
	if(0 == $g_view['notes_count']){
		?><div>None available</div><?php
	}else{
		/**********
		we get the first element only. If there are two originals, it is an error
		We check for the actual content
		************/
		$g_view['original_note_submission'] = $g_view['notes'][0];
		if($g_view['original_note_submission']['note'] == ""){
			?><div>None available</div><?php
		}else{
			/***********
			we have a note
			************/
			?>
			<div><?php echo nl2br($g_view['original_note_submission']['note']);?></div>
			<?php
		}
	}
}
?>

<?php
/********************
For these we use the deal_data info
**********************/
?>
<div class="hr_div"></div>
<div class="deal-edit-snippet-footer">Submitted <?php echo $g_view['submisson_date'];?></div>
<div class="deal-edit-snippet-footer"><?php echo $g_view['deal_submitter'];?></div>
</td>

<td id="suggested_notes" class="deal-edit-snippet-middle-td">

</td>

<td class="deal-edit-snippet-right-td">
<form id="frm_edit_note">
<input type="hidden" name="deal_id" value="<?php echo $g_view['deal_data']['deal_id'];?>" />
<div>
<textarea name="deal_note" class="deal-edit-snippet-textarea"></textarea>
</div>
<div id="result_frm_edit_note" class="msg_txt"></div>
<div class="hr_div"></div>
<div style="text-align:right;"><input type="button" value="Submit" class="btn_auto" onClick="submit_frm_edit_note();" /></div>
</form>
</td>
</tr>

</table>
</div>
<script>
function submit_frm_edit_note(){
	if(can_submit()){
		$('result_frm_edit_note').html('sending...');
		$.post('ajax/suggest_deal_correction/note.php',$('#frm_edit_note').serialize(),function(result){
			$('#result_frm_edit_note').html(result);
		});
	}
}

function fetch_suggested_notes(){
	$.get('ajax/suggest_deal_correction/fetch_submitted_notes.php?deal_id=<?php echo $g_view['deal_data']['deal_id'];?>',function(result){
		$('#suggested_notes').html(result);
	});
}

$(function(){
	fetch_suggested_notes();
});
</script>