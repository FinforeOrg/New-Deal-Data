<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Transaction Partner</title>
<link href="style.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="../js/jquery-1.2.1.pack.js"></script>
<script type="text/javascript">
function lookup(inputString) {
	if(inputString.length == 0) {
		// Hide the suggestion box.
		$('#suggestions').hide();
	} else {
		// post data to our php processing page and if there is a return greater than zero
		// show the suggestions box
		
		
		$('#law_firm_searching').html("searching...");
		$.post("ajax/get_company_for_deal.php", {name: ""+inputString+"",type: "law firm"}, function(data){
			$('#law_firm_searching').html("");
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
function fill(law_firm_id,name) {
	$('#firm_name').val(name);
	$('#partner_id').val(law_firm_id);
	setTimeout("$('#suggestions').hide();", 200);
}
function hide_suggestion(){
	setTimeout("$('#suggestions').hide();", 200);
}
</script>
</head>

<body>
<table width="100%" cellspacing="0" cellpadding="3" border="1" style="border-collapse:collapse;" bordercolor="#693520" align="center">
<tr bgcolor="#DEC5B3" height="20">
<td colspan="5" align="center" valign="middle">
<B>:: Transaction Partner ::</B>
</td>
</tr>
<tr>
<td colspan="5"><?php echo $g_view['msg'];?></td>
</tr>
<tr>
<td colspan="5">
<!--add law firm form-->
<form method="post" action="deal_lawfirm_popup.php?transaction_id=<?php echo $_REQUEST['transaction_id'];?>" enctype="multipart/form-data">
<input type="hidden" name="transaction_id" value="<?=$_REQUEST['transaction_id']?>" />
<input type="hidden" name="action" value="add" />
<input type="hidden" name="partner_id" id="partner_id" value="<?php echo $g_view['input']['partner_id'];?>" />
Type the first few letters. If the law firm is found, it will be shown in the list. Please select the law firm.<br />
<input type="text" name="firm_name" id="firm_name" class="txtbox" style="width:200px;" value="<?php echo $g_view['input']['firm_name'];?>" onkeyup="lookup(this.value);" onblur="hide_suggestion();" /><br />
		<span id="law_firm_searching"></span><br />
		<span class="err_txt"><?php echo $g_view['err']['partner_id'];?></span>
		<div class="suggestionsBox" id="suggestions" style="display: none;">
		<img src="images/upArrow.png" style="position: relative; top: -18px; left: 30px;" alt="upArrow"  />
		<div class="suggestionList" id="autoSuggestionsList"></div>
		</div>
<br />
<?php
/***************
sng:16/sep/2011
for non M&A deals, no need to show sellside advisor checkbox'
*******************/
if(strtolower($g_view['deal_type']['deal_cat_name'])=="m&a"){
?>
<input name="is_sellside_advisor" type="checkbox" value="y" />&nbsp;Sellside Advisor
<?php
}
?>
<input type="submit" name="submit" value="Add" />
</form>
<!--add law firm form-->
</td>
</tr>
<?php
if($g_view['data_count']==0){
?>
<tr>
<td colspan="5">No law firms found</td>
</tr>
<?php
}else{
?>
<tr bgcolor="#dec5b3" style="height:20px;">
<td><strong>Law firm</strong></td>
<td></td>
<td><strong>role</strong></td>
<?php
if(strtolower($g_view['deal_type']['deal_cat_name'])=="m&a"){
?>
<td>&nbsp;</td>
<?php
}
?>
<td>&nbsp;</td>
</tr>
<?php
for($i=0;$i<$g_view['data_count'];$i++){
?>
<tr>
<td>
<?php echo $g_view['data'][$i]['name'];?>
</td>
<td>
<form method="post" action="deal_lawfirm_popup.php">
<input type="hidden" name="action" value="flip_is_insignificant_status" />
<input type="hidden" name="transaction_id" value="<?=$_REQUEST['transaction_id']?>" />
<input type="hidden" name="partner_id" value="<?php echo $g_view['data'][$i]['partner_id'];?>" />
<input name="is_insignificant" type="checkbox" value="y" <?php if($g_view['data'][$i]['is_insignificant']=='y'){?>checked="checked"<?php }?> onchange="$(this).parent().submit();" />&nbsp;Not lead advisor
</form>
</td>
<td>
<form method="post" action="deal_lawfirm_popup.php">
<input type="hidden" name="action" value="role" />
<input type="hidden" name="transaction_id" value="<?=$_REQUEST['transaction_id']?>" />
<input type="hidden" name="partner_id" value="<?php echo $g_view['data'][$i]['partner_id'];?>" />
<select name="role" onchange="$(this).parent().submit();">
<option value="0" <?php if($g_view['data'][$i]['role_id']==0){?>selected="selected"<?php }?>>select role</option>
<?php
for($j=0;$j<$g_view['roles_count'];$j++){
?>
<option value="<?php echo $g_view['roles'][$j]['role_id'];?>" <?php if($g_view['data'][$i]['role_id']==$g_view['roles'][$j]['role_id']){?>selected="selected"<?php }?>><?php echo $g_view['roles'][$j]['role_name'];?></option>
<?php
}
?>
</select>
</form>
</td>
<?php
if(strtolower($g_view['deal_type']['deal_cat_name'])=="m&a"){
?>
<td>
<form method="post" action="deal_lawfirm_popup.php">
<input type="hidden" name="action" value="flip_sellside_status" />
<input type="hidden" name="transaction_id" value="<?=$_REQUEST['transaction_id']?>" />
<input type="hidden" name="partner_id" value="<?php echo $g_view['data'][$i]['partner_id'];?>" />
<input name="is_sellside_advisor" type="checkbox" value="y" <?php if($g_view['data'][$i]['is_sellside_advisor']=='y'){?>checked="checked"<?php }?> onchange="$(this).parent().submit();" />&nbsp;Sellside Advisor
</form>
</td>
<?php
}
?>
<td>
<form method="post" action="deal_lawfirm_popup.php">
<input type="hidden" name="action" value="delete" />
<input type="hidden" name="transaction_id" value="<?=$_REQUEST['transaction_id']?>" />
<input type="hidden" name="partner_id" value="<?php echo $g_view['data'][$i]['partner_id'];?>" />
<input type="submit" name="submit" value="Delete" />
</form>
</td>
</tr>
<?php
}
}
?>
</table>
</body>
</html>
