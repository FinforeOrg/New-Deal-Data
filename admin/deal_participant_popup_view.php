<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Transaction Participant</title>
<link href="style.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="../js/jquery-1.4.4.min.js"></script>
<script type="text/javascript">
function lookup(inputString) {
	if(inputString.length == 0) {
		// Hide the suggestion box.
		$('#suggestions').hide();
	} else {
		// post data to our php processing page and if there is a return greater than zero
		// show the suggestions box
		
		
		$('#company_searching').html("searching...");
		$.post("ajax/get_company_for_deal.php", {name: ""+inputString+"",type: "company"}, function(data){
			$('#company_searching').html("");
			if(data.length >0) {
				$('#suggestions').show();
				$('#autoSuggestionsList').html(data);
			}else{
				//no matches found, we hide the suggestion list
				setTimeout("$('#suggestions').hide();", 200);
			}
		});
	}
} //end

// if user clicks a suggestion, fill the text box.
function fill(company_id,name) {
	$('#company_name').val(name);
	$('#company_id').val(company_id);
	setTimeout("$('#suggestions').hide();", 200);
}
function hide_suggestion(){
	setTimeout("$('#suggestions').hide();", 200);
}
</script>
<script>
function remove_participant(num){
	$('#remove_company_id').val($('#company_id_'+num).val());
	$('#frm_remove').submit();
	return false;
}

function update_participant(num){
	$('#update_company_id').val($('#company_id_'+num).val());
	$('#update_role_id').val($('#role_id_'+num).val());
	$('#update_footnote').val($('#footnote_'+num).val());
	$('#frm_update').submit();
	return false;
}
</script>
</head>

<body>
<table width="100%" cellspacing="0" cellpadding="3" border="1" style="border-collapse:collapse;" bordercolor="#693520" align="center">
<tr bgcolor="#DEC5B3" height="20">
<td colspan="5" align="center" valign="middle">
<B>:: Transaction Participant ::</B>
</td>
</tr>
<tr>
<td colspan="5"><?php echo $g_view['msg'];?></td>
</tr>
<tr>
<td colspan="5">
<!--add participant form-->
<form method="post" action="deal_participant_popup.php?transaction_id=<?php echo $g_view['deal_id'];?>" enctype="multipart/form-data">

<input type="hidden" name="action" value="add" />
<input type="hidden" name="company_id" id="company_id" value="<?php echo $g_view['input']['company_id'];?>" />
<table width="100%" cellspacing="0" cellpadding="5">
<tr>
<td>Company *</td>
<td>Role</td>
<td>Footnote</td>
<td></td>
</tr>
<tr>
<td>
<input type="text" name="company_name" id="company_name" class="txtbox" style="width:200px;" value="<?php echo $g_view['input']['company_name'];?>" onkeyup="lookup(this.value);" onblur="hide_suggestion();" autocomplete="off" /><br />
		<span class="err_txt"><?php echo $g_view['err']['company_id'];?></span><br />
		<span id="company_searching"></span><br />
		<div class="suggestionsBox" id="suggestions" style="display: none;">
		<img src="images/upArrow.png" style="position: relative; top: -18px; left: 30px;" alt="upArrow"  />
		<div class="suggestionList" id="autoSuggestionsList"></div>
		</div>
</td>
<td style="vertical-align:top">
<select name="role_id">
<option value="">Select Role</option>
<?php
for($role_i=0;$role_i<$g_view['role_count'];$role_i++){
	?>
	<option value="<?php echo $g_view['roles'][$role_i]['role_id'];?>" <?php if($g_view['roles'][$role_i]['role_id']==$g_view['input']['role_id']){?>selected="selected"<?php }?>><?php echo $g_view['roles'][$role_i]['role_name'];?></option>
	<?php
}
?>
</select><br />
<span class="err_txt"><?php echo $g_view['err']['role_id'];?></span>
</td>
<td style="vertical-align:top"><input type="text" name="footnote" value="<?php echo $g_view['input']['footnote'];?>" style="width:200px;" /></td>
<td style="vertical-align:top"><input type="submit" name="submit" value="Add" /></td>
</tr>
<tr>
<td colspan="4">* Type the first few letters. If the company is found, it will be shown in the list. Please select the company.</td>
</tr>
</table>

</form>
<!--add bank form-->
</td>
</tr>
<?php
if($g_view['data_count']==0){
?>
<tr>
<td colspan="5">No participants found</td>
</tr>
<?php
}else{
	?>
	<tr bgcolor="#dec5b3" style="height:20px;">
	<td><strong>Company</strong></td>
	<td><strong>role</strong></td>
	<td><strong>footnote</strong></td>
	<td colspan="2"></td>
	</tr>
	<?php
	for($i=0;$i<$g_view['data_count'];$i++){
		?>
		<input type="hidden" id="company_id_<?php echo $i;?>" value="<?php echo $g_view['data'][$i]['company_id'];?>" />
		<tr>
		<td><?php echo $g_view['data'][$i]['company_name'];?></td>
		<td>
		<select id="role_id_<?php echo $i;?>">
		<?php
		for($role_i=0;$role_i<$g_view['role_count'];$role_i++){
			?>
			<option value="<?php echo $g_view['roles'][$role_i]['role_id'];?>" <?php if($g_view['roles'][$role_i]['role_id']==$g_view['data'][$i]['role_id']){?>selected="selected"<?php }?>><?php echo $g_view['roles'][$role_i]['role_name'];?></option>
			<?php
		}
		?>
		</select>
		</td>
		<td><input type="text" id="footnote_<?php echo $i;?>" value="<?php echo $g_view['data'][$i]['footnote'];?>" style="width:200px;" /></td>
		<td><input type="button" value="update" onclick="return update_participant(<?php echo $i;?>);" /></td>
		<td><input type="button" value="remove" onclick="return remove_participant(<?php echo $i;?>);" /></td>
		</tr>
		<?php
	}
}
?>
</table>
<form id="frm_remove" method="post" action="deal_participant_popup.php?transaction_id=<?php echo $g_view['deal_id'];?>">
<input type="hidden" name="action" value="remove" />
<input type="hidden" name="company_id" id="remove_company_id" value="" />
</form>

<form id="frm_update" method="post" action="deal_participant_popup.php?transaction_id=<?php echo $g_view['deal_id'];?>">
<input type="hidden" name="action" value="update" />
<input type="hidden" name="company_id" id="update_company_id" value="" />
<input type="hidden" name="role_id" id="update_role_id" value="" />
<input type="hidden" name="footnote" id="update_footnote" value="" />
</form>
</body>
</html>
