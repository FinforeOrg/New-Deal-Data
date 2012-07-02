<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Note on deal</title>
<link href="style.css" rel="stylesheet" type="text/css">
<style type="text/css">
<!--
body {
	background-color: #FFFFFF;
}
-->
</style>


</head>

<body>
<table width="100%" cellspacing="0" cellpadding="10" border="1" style="border-collapse:collapse;" bordercolor="#693520" align="center">
<?php
if(0 == $g_view['data_count']){
	?>
	<tr><td colspan="2">None suggested</td></tr>
	<?php
}else{
	for($i=0;$i<$g_view['data_count'];$i++){
		?>
		<tr>
		<td>
		<textarea style="width:500px; height:100px;"><?php echo $g_view['data'][$i]['flag_reason'];?></textarea>
		</td>
		<td>
		On <?php echo date("Y-m-d",strtotime($g_view['data'][$i]['date_flagged']));?><br />
		By <?php echo $g_view['data'][$i]['f_name'];?> <?php echo $g_view['data'][$i]['l_name'];?><br />
		<?php echo $g_view['data'][$i]['designation'];?> of <?php echo $g_view['data'][$i]['work_company'];?>
		</td>
		</tr>
		<?php
	}
}
?>


</table>
</body>
</html>