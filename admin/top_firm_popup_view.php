<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Top Firms</title>
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
		
		var firm_type = "<?php echo $g_view['firm_type'];?>";
		$('#firm_searching').html("searching...");
		$.post("ajax/get_company_for_top_firm.php", {name: ""+inputString+"",type: ""+firm_type+""}, function(data){
			$('#firm_searching').html("");
			
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
	$('#firm_name').val(name);
	$('#company_id').val(company_id);
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
<td colspan="3" align="center" valign="middle">
<B>:: Top Firm (<?php echo $g_view['firm_type'];?>) ::</B>
</td>
</tr>
<tr>
<td colspan="3"><?php echo $g_view['msg'];?></td>
</tr>
<tr>
<td colspan="3">
<!--add bank form-->
<form method="post" action="top_firm_popup.php?cat_id=<?php echo $g_view['cat_id'];?>&firm_type=<?php echo $g_view['firm_type'];?>">
<input type="hidden" name="action" value="add" />

<input type="hidden" name="company_id" id="company_id" value="<?php echo $g_view['input']['company_id'];?>" />

Type the first few letters. If the firm is found, it will be shown in the list. Please select the firm.<br />
<input type="text" name="firm_name" id="firm_name" class="txtbox" style="width:200px;" value="<?php echo $g_view['input']['firm_name'];?>" onkeyup="lookup(this.value);" onblur="hide_suggestion();" /><br />
		<span id="firm_searching"></span><br />
		<span class="err_txt"><?php echo $g_view['err']['company_id'];?></span>
		<div class="suggestionsBox" id="suggestions" style="display: none;">
		<img src="images/upArrow.png" style="position: relative; top: -18px; left: 30px;" alt="upArrow"  />
		<div class="suggestionList" id="autoSuggestionsList"></div>
		</div>
<br />



<input type="submit" name="submit" value="Add" />
</form>
<!--add bank form-->
</td>
</tr>
<?php
if($g_view['data_count']==0){
?>
<tr>
<td colspan="2">None found</td>
</tr>
<?php
}else{
?>
<tr bgcolor="#dec5b3" style="height:20px;">
<td><strong><?php echo $g_view['firm_type'];?></strong></td>
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
<form method="post" action="top_firm_popup.php?cat_id=<?php echo $g_view['cat_id'];?>&firm_type=<?php echo $g_view['firm_type'];?>">
<input type="hidden" name="action" value="delete" />
<input type="hidden" name="id" value="<?php echo $g_view['data'][$i]['id'];?>" />
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
