<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Transaction Partner</title>
<script type="text/javascript" src="../js/jquery-1.2.1.pack.js"></script>
<link href="style.css" rel="stylesheet" type="text/css">
<script type="text/javascript">
function search_banker(){
	var f_name = document.getElementById('f_name').value;
	var l_name = document.getElementById('l_name').value;
	$('#banker_searching').html("searching...");
	$.post("ajax/get_member_for_deal.php", {first_name: ""+f_name+"",last_name: ""+l_name+"",type: "banker"}, function(data){
		$('#banker_searching').html("");
		$('#banker_list').html(data);
	});
}
</script>
<body>
<table width="100%" cellspacing="0" cellpadding="3" border="1" style="border-collapse:collapse;" bordercolor="#693520" align="center">
<tr bgcolor="#DEC5B3" height="20">
<td colspan="3" align="center" valign="middle">
<B>:: Add Banker ::</B>
</td>
</tr>

<tr>
<td>
<form method="post" action="deal_add_banker_popup.php?transaction_id=<?php echo $g_view['deal_id'];?>">
<input type="hidden" name="myaction" value="add" />
	<table>
	<tr><td><?php echo $g_view['msg'];?></td></tr>
	<tr><td>
	<select name="partner_id">
	<option value="">Select bank</option>
	<?php
	for($i=0;$i<$g_view['data_count'];$i++){
		?>
		<option value="<?php echo $g_view['data'][$i]['partner_id'];?>"><?php echo $g_view['data'][$i]['company_name'];?></option>
		<?php
	}
	?>
	</select>
	</td></tr>
	<tr><td>
	First name: <input type="text" name="f_name" id="f_name" style="width:200px;" /> Last name: <input type="text" name="l_name" id="l_name" style="width:200px;" />
	</td></tr>
	<tr><td><input type="button" value="Search member" onClick="search_banker()" /></td></tr>
	<tr><td><span id="banker_searching"></span></td></tr>
	<!--////////////matched bankers/////////////////////-->
	<tr><td>
	<div id="banker_list">
	</div>
	</td></tr>
	<tr><td><input type="submit" value="Add" />
	<!--////////////matched bankers/////////////////////-->
	</table>
</form>
</td>
</tr>
</table>
</body>
</html>