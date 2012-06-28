<HTML>
<head>
<title>Super Admin Area</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="style.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="dtree.js"></script>
<script type="text/javascript" src="main.js"></script>
</head>


<body>

<table bordercolor="#d4d9db" cellspacing="2" cellsadding="0" width="900px"  bgcolor="#ffffff" align="center" border="1"><!--start of main table-->

<tr valign="top">
<td>
<table height="100%" cellspacing="0" cellpadding="0" width="100%" border="0"><!--start of nav table-->

<tr>
<td colspan="3" height="2"></td></tr>

<tr>
<td colspan="3" height="47" style="background-color:#669933; text-align:center;">
<span style="font-family:Arial, Helvetica, sans-serif; font-size:18px; font-weight:bold; color:#FFFFFF;">Data-CX Super Admin Panel</span>
</td></tr>

<tr><td colspan="3" style="background-image: url(images/bottom_bg.gif); background-repeat:repeat-x; height:26px;"></td></tr>

<!---------------------------------------TOP ENDS HERE--------------------------------->

<tr valign="top">
<td colspan="3">
<table cellspacing="10" cellpadding="0" width="100%" border="0"><!--start of body table-->

<tr>
<td width="200px"  style="vertical-align:top">
<!--left menu-->
<table width="100%" cellpadding="0" cellspacing="0" border="0">
<tr>
<td><a href="index.php">Home</a></td>
<td><a href="logout.php">Logout</a></td>
</tr>
<tr>
<td style="height:20px;"></td>
</tr>
<tr>
<td colspan="2" style="vertical-align:top;">
<?php include("leftmenu.php");?>
</td>
</tr>
</table>
<!--left menu-->
</td>
<td>
<!--content-->
<table border="1" cellspacing="0" cellpadding="3" width="100%" style="border-collapse:collapse" bordercolor="#693520" align="center" >
	<tr bgcolor="#DEC5B3" height="20">
	<td colspan="3" align="center" valign="middle">
	<B>:: <?php echo $g_view['heading'];?> ::</B>
	</td>
</tr>
<tr>
	<td>
	<!--content part-->
	<?php include $g_view['content_view'];?>
	<!--content part-->
	</td>
</tr>
</table>
<!--content-->
</td>
</tr>

</table><!--end of body table-->
</td></tr>



<!--------------------------bottom STARTS HERE---------------------------------------->
<tr align="middle">
<td colspan="3" height="2">
<table cellspacing="0" cellpadding="0" width="100%" background="images/bottom_bg.gif" border="0"><!--start of bottom table-->
<tr align="middle">
<td align="center" height="23"> 
Copyright &copy; <?php echo date('Y');?> <a href="http://data-cx.com/">data-cx.com</a> All rights reserved.

</td>
</tr>
</table><!--end of bottom table-->
</td></tr>


</table><!--end of nav table-->
</td></tr>


</table><!--end of main table-->
</body>
</html>