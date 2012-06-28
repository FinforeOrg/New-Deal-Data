<?
session_start();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo $g_view['meta_title'];?></title>
<meta name="keywords" content="<?php echo $g_view['meta_keywords'];?>" />
<meta name="description" content="<?php echo $g_view['meta_description'];?>" />
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
            <td><table width="100%" border="0" cellpadding="0" cellspacing="0" class="nav">
              <tr>
			  <?php
			  /***
			  sng:3/jun/2010
			  client: remove, "About Us" from the top of every page. It is better for it to be at the bottom, and nowhere else
			  ***/
			  ?>
			  <!--
                <td><a href="<?php echo $g_http_path;?>/aboutus.php">About Us</a></td>
                <td width="1"><img src="images/navgap.gif" width="1" height="16" alt="" /></td>
				-->
				<?php
			  /***
			  sng:3/jun/2010
			  client: remove the "Bankers" and "Lawyers" pages. We can add them back in in a few months once we have started to generate traffic to the site and start having a large user base. Please keep the functionality for "Bankers" and "Lawyers"  
			  ***/
			  ?>
			  <!--
                <td><a href="bankers_league_table.php">Bankers</a></td>
                <td width="1"><img src="images/navgap.gif" width="1" height="16" alt="" /></td>
                <td><a href="lawyers_league_table.php">Lawyers</a></td>
                <td width="1"><img src="images/navgap.gif" width="1" height="16" alt="" /></td>
				-->
                <!--<td><a href="deal_search.php">Deals</a></td>-->
				<td><a href="league_table.php">League Tables</a></td>
				<td width="1"><img src="images/navgap.gif" width="1" height="16" alt="" /></td>
				<td><a href="issuance_data.php">Volumes</a></td>
				<td width="1"><img src="images/navgap.gif" width="1" height="16" alt="" /></td>
				<td><a href="deal.php">Deals</a></td>
                <td width="1"><img src="images/navgap.gif" width="1" height="16" alt="" /></td>
				<td><a href="top_firms.php">Top Firms</a></td>
				<td width="1"><img src="images/navgap.gif" width="1" height="16" alt="" /></td>
                <td><a href="company.php">Companies</a></td>
                <td width="1"><img src="images/navgap.gif" width="1" height="16" alt="" /></td>
              </tr>
            </table></td>
            <td><table width="100%" border="0" cellpadding="0" cellspacing="0" class="rightnav">
              <tr>
				<?php
				if(!$_SESSION['is_member']){
					$g_home_url = "index.php";
				}else{
					//member but company rep has a different home
					if($_SESSION['member_type']=="company rep"){
						$g_home_url = "company_rep_home.php";
					}else{
						$g_home_url = "member_home.php";
					}
				}
				?>
                <td><?php if($_SESSION['is_member']){?><span style="color:#E86200;">Hi <a href="my_profile.php"><?php echo $_SESSION['f_name']." ".substr($_SESSION['l_name'],0,1);?></a></span> | <?php }?><a href="<?php echo $g_home_url;?>" class="home">Home</a>|<?php if(!$_SESSION['is_member']){?><a href="index.php" class="register">Sign In</a>|<a href="register.php?method=choose" class="register">Register</a>&nbsp;|&nbsp;<a href="mailto:<?php echo $g_view['site_emails']['contact_email'];?>" class="contact">Contact Us</a><?php } else {?>&nbsp;<a href="my_profile.php">My Profile</a>&nbsp;<!--|&nbsp;<a href="member_unregister.php">Unregister</a>&nbsp;-->|&nbsp;<a href="saved_searches.php" >Saved Searches</a>&nbsp;|&nbsp;
                
                
                <a href="logout.php" >Sign Out</a>&nbsp;<?php }?>&nbsp;|&nbsp;<a href="help.php">Help</a></td>
              </tr>
            </table></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><table width="100%" border="0" cellpadding="0" cellspacing="0" class="banner">
          <tr>
            <td class="middlealign"><a href="index.php"><img src="images/mytombstones_logo.gif" width="236" height="65" alt="" /></a></td>
            <td><img src="images/spacer.gif" width="30" height="1" alt="" /></td>
            <td><table width="100%" border="0" cellpadding="0" cellspacing="0" class="search">
              <tr>
                <td width="3"><img src="images/searchbox_left.gif" width="3" height="44" alt="" /></td>
                <td>
					<?php
					/***
					sng:23/mar/2010
					search functionality will be different in different pages, so we put the search form dynamically.
					However, if no search view is specified, we show th edefault search view which does nothing
					*******/
					if(!isset($g_view['top_search_view'])){
						include("default_search_view.php");
					}else{
						include($g_view['top_search_view']);
					}
					?>
				</td>
                <td width="3"><img src="images/searchbox_right.gif" width="3" height="44" alt="" /></td>
              </tr>
            </table></td>
			 <!--
			sng:6/jul/2010
			client do not want to duplicate the site name since it is already in the logo, and do not want the tagline
            <td><img src="images/spacer.gif" width="30" height="1" alt="" /></td>
            <td class="centeralign middlealign"><h1>MyTombstones</h1>
              A place to share insights &amp; find deal information</td>
			  -->
          </tr>
          </table></td>
      </tr>
      <tr>
        <td><table width="100%" border="0" cellpadding="0" cellspacing="0" class="content">
		<?php
		if($g_view['page_heading']!=""){
			?>
			<tr>
            <td><h1><?php echo $g_view['page_heading'];?></h1></td>
          </tr>
          <tr>
            <td><img src="images/spacer.gif" width="1" height="15" alt="" /></td>
          </tr>
			<?php
		}
		?>
          
          <tr>
            <td style="height:300px;">
			
			<!--content-->
			<?php include $g_view['content_view'];?>
			<!--content-->
			
			</td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><table width="100%" border="0" cellpadding="0" cellspacing="0" class="footer">
          <tr>
            <td><a href="<?php echo $g_http_path;?>/aboutus.php">About Us</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="<?php echo $g_http_path;?>/products.php">Products</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="<?php echo $g_http_path;?>/data_audit.php">Data Audit</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="<?php echo $g_http_path;?>/blog.php">Blog</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="<?php echo $g_http_path;?>/privacy.php">Privacy Policy</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="<?php echo $g_http_path;?>/legal.php"> Legal Notices</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="mailto:<?php echo $g_view['site_emails']['contact_email'];?>">Contact Us</a><br />
            Copyright &copy; <?php echo date('Y');?> mytombstones.com<br />
			<div id="footer_tagline"><a href="http://www.movingpixelz.com/" target="_blank" class="footer_tagline_a">Designed</a> by <a href="http://www.movingpixelz.com/" target="_blank">MovingPixelz</a> and <a href="http://www.phppowerhouse.com/" target="_blank">Developed</a> by <a href="http://www.phppowerhouse.com/" target="_blank">PHPPowerHouse</a></div>
			</td>
          </tr>
        </table></td>
      </tr>
    </table></td>
  </tr>
</table>
</body>
</html>
