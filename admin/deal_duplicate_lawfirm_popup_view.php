<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Transaction Partner</title>
<link href="style.css" rel="stylesheet" type="text/css">

</head>

<body>
<table width="100%" cellspacing="0" cellpadding="3" border="1" style="border-collapse:collapse;" bordercolor="#693520" align="center">
<tr bgcolor="#DEC5B3" height="20">
<td colspan="3" align="center" valign="middle">
<B>:: Duplicate Transaction Partner ::</B>
</td>
</tr>
<tr>
<td colspan="3"><?php echo $g_view['msg'];?></td>
</tr>

<?php
if($g_view['data_count']==0){
?>
<tr>
<td colspan="2">No law firms found</td>
</tr>
<?php
}else{
?>
<tr bgcolor="#dec5b3" style="height:20px;">
<td><strong>Law firm</strong></td>
<td>&nbsp;</td>
</tr>
<?php
for($i=0;$i<$g_view['data_count'];$i++){
?>
<tr>
<td>
<?php echo $g_view['data'][$i]['company_name'];?>
</td>
<td>
<form method="post" action="deal_duplicate_lawfirm_popup.php">
<input type="hidden" name="action" value="remove_duplicate" />
<input type="hidden" name="transaction_id" value="<?=$_REQUEST['transaction_id']?>" />
<input type="hidden" name="partner_id" value="<?php echo $g_view['data'][$i]['partner_id'];?>" />
<input type="submit" name="submit" value="Remove duplicate" />
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
