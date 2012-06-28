<?
session_start();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>My Tombstones</title>
<meta name="keywords" content="" />
<meta name="description" content="" />
<link href="css/style.css" rel="stylesheet" type="text/css" />
<link rel="Shortcut Icon" href="favicon.ico">
</head>

<body>
<table width="100%" border="0" cellpadding="0" cellspacing="0" class="maintable">
  <tr>
    <td><table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td><table width="100%" border="0" cellpadding="0" cellspacing="0" class="header">
          <tr>
            <td>&nbsp;</td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><table width="100%" border="0" cellpadding="0" cellspacing="0" class="banner">
          <tr>
            <td class="middlealign"><a href="index.php"><img src="images/mytombstones_logo.gif" width="236" height="65" alt="" /></a></td>
            <!--
			sng:6/jul/2010
			client do not want to duplicate the site name since it is already in the logo, and do not want the tagline
			<td><img src="images/spacer.gif" width="30" height="1" alt="" /></td>
            <td>&nbsp;</td>
            <td><img src="images/spacer.gif" width="30" height="1" alt="" /></td>
            <td class="centeralign middlealign"><h1>deal-data</h1>
              A place to share insights &amp; find deal<br>
              information</td>-->
          </tr>
          </table></td>
      </tr>
      <tr>
        <td><table width="100%" border="0" cellpadding="0" cellspacing="0" class="content">
		<tr>
            <td><h1>The site is undergoing maintenance</h1></td>
          </tr>
          <tr>
            <td><img src="images/spacer.gif" width="1" height="15" alt="" /></td>
          </tr>
          
          <tr>
            <td style="height:300px;">
			
			<!--content-->
			<?php echo nl2br($g_view['site_maintenance_data']['site_in_maintenance_text']);?>
			<!--content-->
			
			</td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><table width="100%" border="0" cellpadding="0" cellspacing="0" class="footer">
          <tr>
            <td>
            Copyright &copy; <?php echo date('Y');?> data-cx.com All rights reserved.
			</td>
          </tr>
        </table></td>
      </tr>
    </table></td>
  </tr>
</table>
</body>
</html>
