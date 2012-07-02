<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>New Deal Suggestion</title>
<link href="style.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="../js/jquery-1.4.4.min.js"></script>
<style type="text/css">
<!--
body {
	background-color: #FFFFFF;
}
-->
</style>
<script>
function delete_deal_suggestion(suggestion_id){
	//alert(suggestion_id);
	var suggestion_accepted = 'n';
	jQuery.post("ajax/delete_deal_suggestion.php",{id: suggestion_id,accepted: suggestion_accepted},function(data){
		alert(data);
		window.close();
	});
}
function accept_deal_suggestion_file(file_id){
	//alert(file_id);
	var txt_id = "#file"+file_id;
	var deal_id = jQuery(txt_id).val();
	jQuery.post("ajax/accept_deal_suggestion_file.php",{id: file_id,transaction_id: deal_id},function(data){
		jQuery("#accept_msg"+file_id).html(data);
	});
}
</script>

</head>

<body>
<table width="100%" cellspacing="0" cellpadding="10" border="1" style="border-collapse:collapse;" bordercolor="#693520" align="center">
	<tr bgcolor="#DEC5B3" height="20">
		<td colspan="3" align="center" valign="middle"><strong>:: New Deal Suggestion ::</strong></td>
	</tr>
<tr>
<td>Suggested By:</td>
<td><?php echo $g_view['data']['f_name'];?> <?php echo $g_view['data']['l_name'];?></td>
</tr>

<tr>
<td>Designation:</td>
<td><?php echo $g_view['data']['designation'];?></td>
</tr>

<tr>
<td>Firm:</td>
<td><?php echo $g_view['data']['work_company'];?></td>
</tr>

<tr>
<td>Date:</td>
<td><?php echo date("Y-m-d",strtotime($g_view['data']['date_suggested']));?></td>
</tr>

<tr><td colspan="2"><strong>Details</strong></td></tr>
<?php
if(strtolower($g_view['data']['deal_cat_name']) == "m&a") require("deal_suggestion_detail_ma.php");
elseif((strtolower($g_view['data']['deal_cat_name']) == "debt")&&(strtolower($g_view['data']['deal_subcat1_name']) == "bond")) require("deal_suggestion_detail_bond.php");
elseif((strtolower($g_view['data']['deal_cat_name']) == "debt")&&(strtolower($g_view['data']['deal_subcat1_name']) == "loan")) require("deal_suggestion_detail_loan.php");
elseif((strtolower($g_view['data']['deal_cat_name']) == "equity")&&(strtolower($g_view['data']['deal_subcat1_name']) == "convertible")) require("deal_suggestion_detail_eq_convertible.php");
elseif((strtolower($g_view['data']['deal_cat_name']) == "equity")&&(strtolower($g_view['data']['deal_subcat1_name']) == "preferred")) require("deal_suggestion_detail_eq_preferred.php");
elseif((strtolower($g_view['data']['deal_cat_name']) == "equity")&&(strtolower($g_view['data']['deal_subcat1_name']) == "equity")&&(strtolower($g_view['data']['deal_subcat2_name']) == "additional")) require("deal_suggestion_detail_eq_additional.php");
elseif((strtolower($g_view['data']['deal_cat_name']) == "equity")&&(strtolower($g_view['data']['deal_subcat1_name']) == "equity")&&(strtolower($g_view['data']['deal_subcat2_name']) == "ipo")) require("deal_suggestion_detail_eq_ipo.php");
elseif((strtolower($g_view['data']['deal_cat_name']) == "equity")&&(strtolower($g_view['data']['deal_subcat1_name']) == "equity")&&(strtolower($g_view['data']['deal_subcat2_name']) == "rights issue")) require("deal_suggestion_detail_eq_rights.php");
?>
<tr><td colspan="2"><strong>Partners</strong></td></tr>
<tr>
<td>Banks</td>
<td>
<ol>
<?php
foreach($g_view['data']['banks'] as $bank){
	?>
	<li><?php echo $bank['partner_name'];?> <?php if($bank['is_sellside_advisor']=='y'){?>[sellside]<?php }?></li>
	<?php
}
?>
</ol>
</td>
</tr>

<tr>
<td>Law Firm</td>
<td>
<ol>
<?php
foreach($g_view['data']['law_firms'] as $law_firm){
	?>
	<li><?php echo $law_firm['partner_name'];?> <?php if($law_firm['is_sellside_advisor']=='y'){?>[sellside]<?php }?></li>
	<?php
}
?>
</ol>
</td>
</tr>

<?php
/****************************
sng:1/sep/2011
We need to show the files uploaded along with this suggestion
*******************************/
?>
<tr><td colspan="2">
<strong>Files</strong><br /><br />
Right click on the [<strong>view</strong>] and save the file to download the file to read it.<br /><br />
If the file is useless, leave it as it is. It will be deleted automatically.<br /><br />
If the file is useful, type the id of the transaction to associate the file with the deal.
</td></tr>
<?php
foreach($g_view['data']['docs'] as $docs){
	?>
	<tr>
	<td><?php echo $docs['caption'];?> [<a href="../temp_suggestion_files/<?php echo $docs['stored_filename'];?>">view</a>] <span id="accept_msg<?php echo $docs['file_id'];?>"></span></td>
	<td><input type="text" id="file<?php echo $docs['file_id'];?>" style="width:100px;" /><input type="button" value="Accept" onclick="return accept_deal_suggestion_file(<?php echo $docs['file_id'];?>);" /></td>
	</tr>
	<?php
}
?>
<tr><td colspan="2">After creating the deal, you can delete this suggestion. Additionally you can mark the suggestion as accepted to give credit to the member.</td></tr>
<tr>
<td><input name="suggestion_accepted" id="suggestion_accepted" type="checkbox" value="y" />&nbsp; Suggestion accepted</td>
<td><input type="button" name="delete" value="Delete" onclick="return delete_deal_suggestion('<?php echo $g_view['data']['id'];?>');" /></td>
</tr>

</table>
</body>
</html>
