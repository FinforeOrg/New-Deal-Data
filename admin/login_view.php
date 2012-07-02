<html>
<head>
<title>Admin Area</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="style.css" rel="stylesheet" type="text/css">
</head>


<body>

<table borderColor="#d4d9db" cellSpacing="2" cellPadding="0" width="900px"  bgColor="#ffffff" align="center" border="1"><!--start of main table-->

<tr valign=top>
<td>
<table height="100%" cellspacing=0 cellpadding=0 width="100%" border="0"><!--start of nav table-->

<tr>
<td colspan="3" height="2"></td></tr>

<tr>
<td colspan="3" height="47" style="background-color:#669933; text-align:center;">
<span style="font-family:Arial, Helvetica, sans-serif; font-size:18px; font-weight:bold; color:#FFFFFF;">Data-CX Admin Panel</span>
</td></tr>

<tr><td colspan="3" style="background-image: url(images/bottom_bg.gif); background-repeat:repeat-x; height:26px;"></td></tr>

<!---------------------------------------TOP ENDS HERE--------------------------------->

<tr valign="top">
<td colSpan="3">
<table cellspacing="10" cellpadding="0" width="100%" border="0"><!--start of body table-->

<tr>
<td align="left">
<table width="50%" align="center" border="0"><!--start of login table-->
<form name="form1" method="post" action="login.php"> 
<input name="action" type="hidden" value="login">

<tr><td colspan="2" align="center"><strong><?php if($g_view['msg']!=""){echo $g_view['msg'];}else{echo "PLEASE LOGIN TO THE ADMIN AREA";} ?></strong></td></tr><br />

<tr><td>&nbsp;</td></tr>

<tr>
<td width="40%"><div align="center">Login name </div></td>
<td><input name="login_name" id="login_name" type="text" value=""  size="20"></td>
</tr>

<tr>
<td><div align="center">Password</div></td>
<td><input name="password" type="password"  size="20" value=""></td>
</tr>

<tr><td>&nbsp;</td></tr>

<tr>
<td>&nbsp;</td>
<td><input type="submit" value="Login" name="Login"></td>
</tr>

<tr><td>&nbsp;</td></tr>

</form>
</table><!--end of login table-->
</td></tr>

<tr>
<td align="center">
Forgor your password? Contact super admin
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
<script type="text/javascript">
document.getElementById("login_name").focus();
</script>
</body>
</html>