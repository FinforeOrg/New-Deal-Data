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

<link rel="shortcut icon" type="image/x-icon" href="favicon.ico">
<script src="js/jquery-1.4.4.min.js" type="text/javascript"></script>
<?php
/************************************************************************
29/sep/2011
since we will be using jquery UI everywhere, why not put it here
also, we take a shortcut to use our btn_auto style to make it jqueryUI themed
ditto for prev next hrefs with class link_as_button

30/sep/2011
We also put the js and css for jquery dropdown since it is quite common and require
in every other pages
***********************/
?>
<script src="js/jquery-ui-1.8.11.custom.min.js" type="text/javascript"></script>
<script src="js/jquery.ui.selectmenu.js" type="text/javascript"></script>
<link rel="stylesheet" href="css/custom-theme/jquery-ui-1.8.9.custom.css" />
<link rel="stylesheet" href="css/custom-theme/jquery.ui.selectmenu.css" />

<script>
$(function(){
	$('.btn_auto').button();
	$('.link_as_button').button();
	$('#help').button();
});
</script>
<?php
/******************************************/
?>
<script src="js/apprise-1.5.full.js" type="text/javascript"></script>
<link rel="stylesheet" type="text/css" href="css/apprise.css" />
<script src="js/blockUI.js" type="text/javascript"></script>
<?php
    $pagesWithCharts = array(
        'leagueTableComparisonHistory_details_view.php',
        'oneStop_resultsView.php',
        'leagueTableComparison_view.php',
        'feeData_view.php',
        'showcase_firm_chart_view.php',
        'make_me_top_match_chart_view.php',
        'issuance_data_view.php',
        'ma_metrics_view.php',
		'league_table_view.php'
    );
    if (in_array($g_view['content_view'], $pagesWithCharts)) { ?>
  <!--[if IE]><script language="javascript" type="text/javascript" src="js/jqplot/excanvas.min.js"></script><![endif]-->
  
  <link rel="stylesheet" type="text/css" href="js/jqplot/jquery.jqplot.min.css" />
  
  <script language="javascript" type="text/javascript" src="js/jqplot/jquery.jqplot.js"></script>
  <script language="javascript" type="text/javascript" src="js/jqplot/plugins/jqplot.barRenderer.min.js"></script>
  <script language="javascript" type="text/javascript" src="js/jqplot/plugins/jqplot.categoryAxisRenderer.min.js"></script>
  <script language="javascript" type="text/javascript" src="js/jqplot/plugins/jqplot.pointLabels.min.js"></script>           
<?php } ?>


<?php
require_once("include/google_analytics.php");
?>
<?php
/**********************
sng:24/mar/2011
support for dropdown menu

sng: 1/oct/2011
we now use megamenu
*************************/
?>
<script type="text/javascript" src="js/megamenu.js"></script>
<link rel="stylesheet" href="css/megamenu.css" type="text/css" media="screen" />
<!--[if IE 6]>
<link rel="stylesheet" type="text/css" href="css/ie6.css" />
<![endif]-->
<script type="text/javascript">
$(function() {
	/*jQuery("#menu").megaMenu('hover_fade');*/
	jQuery("#menu").megaMenu('click_fade');
});
</script>
<?php
/**********************************/
?>
<script>
function toggle_help(){
	if($('#explanation')){
		$('#explanation').slideToggle(1000, function(){
			if($('#explanation').css('display')=="none"){
				$('#help').attr('value',"Show Help");
			}else{
				$('#help').attr('value',"Hide Help");
			}
		});
	}
}
</script>
<?php
/*****************************
sng:9/nov/2011
If the user is not logged in, we show alert saying that the user needs to login
********************************/
?>
<script>
function my_firm_cred_alert(){
	apprise("Please login to access your firm's credentials. Alternatively, use the Competitors' Credentials function to find your firm and view your credentials.",{'textOk':'OK'});
	return false;
}
</script>
</head>

<body>
<table width="100%" border="0" cellpadding="0" cellspacing="0" class="maintable">
  <tr>
    <td><table width="100%" border="0" cellspacing="0" cellpadding="0">
      
	  
	  <tr>
	  <td>
	  	<!--///////////////////////////////////////////////////////////////////////////////////////-->
		<div id="menu_container" class="menu_site"><!-- Begin Menu Container -->
		
			<ul id="menu"><!-- Begin Mega Menu -->

				<li style="width:185px;"><a href="#" class="drop">Suggest / Find a deal</a><!-- Begin Item -->
					<div class="drop3columns dropcontent dropfirst" style="width:183px;"><!-- Begin Item Container -->
						<div class="col_2">
							<ul>
								<li><a href="suggest_a_deal_simple.php">Simple Submission</a></li>
								<li><a href="suggest_a_deal.php">Detailed Submission</a></li>
								<li><a href="company.php">Lookup a Company</a></li>
								<li><a href="deal.php">Lookup a Deal</a></li>
							</ul>
						</div>
					</div><!-- End Item Container -->
				</li><!-- End Item -->
				
				<li style="width:160px;"><a href="#" class="drop">Productivity Tools</a><!-- Begin Item -->
					<div class="drop3columns dropcontent dropfirst" style="width:158px;"><!-- Begin Item Container -->
						<div class="col_2">
							<ul>
								<li><a href="watchlist.php">My Watchlist</a></li>
							</ul>
						</div>
					</div><!-- End Item Container -->
				</li><!-- End Item -->
				
				<li style="width:158px;"><a href="#" class="drop">Pitchbook Pages</a><!-- Begin Item -->
						<div class="drop3columns dropcontent" style="width:156px;"><!-- Begin Item Container -->
							<div class="col_2">
								<ul>
									<li><a href="league_table.php">League Tables</a></li>
								</ul>
							</div>
						</div><!-- End Item Container -->
					</li><!-- End Item -->
				
				<li style="width:165px;"><a href="#" class="drop">Competitors</a><!-- Begin Item -->
					<div class="drop3columns dropcontent dropfirst" style="width:164px;"><!-- Begin Item Container -->
						<div class="col_3">
							<ul>
								<?php
								/************
								sng:9/nov/2011
								*************/
								if(!$_SESSION['is_member']){
									?>
									<li><a href="#" onclick="return my_firm_cred_alert();">My Firm's Credentials</a></li>
									<?php
								}else{
									?>
									<li><a href="showcase_firm.php?id=<?php echo $_SESSION['company_id']?>&from=savedSearches">My Firms' Credentials</a></li>
									<?php
								}
								?>
								
								<li><a href="competitor_credentials.php">Competitors' Credentials</a></li>
							</ul>
						</div>
					</div><!-- End Item Container -->
				</li><!-- End Item -->
				
				<?php
				if($_SESSION['is_member']){
					?>
					<li style="width:150px"><a href="#" class="drop">My Account</a><!-- Begin Item -->
						<div class="drop3columns dropcontent" style="width:148px"><!-- Begin Item Container -->
							<div class="col_2">
								<ul>
									<li><a href="my_profile.php">My Profile</a></li>
									<li><a href="logout.php">Log Out</a></li>
								</ul>
							</div>
						</div><!-- End Item Container -->
					</li><!-- End Item -->
					<?php
				}else{
					/******************
					sng:9/nov/2011
					No need for the register link. Users registers from the login screen
					*****************/
				}
				?>
				
				
			</ul><!-- End Mega Menu -->
		
		</div><!-- End Menu Container -->
		<!--///////////////////////////////////////////////////////////////////////////////////////-->
	  </td>
	  <?php
	  /**********
	  sng:2/feb/2012
	  put colspan=2 in all the other places
	  **************/
	  
	  if(!$_SESSION['is_member']){
	  	?>
		<td style="vertical-align:middle">
		<a href="login.php" class="btn_auto">LOGIN</a>
		</td>
		<?php
	  }
	  ?>
	  </tr>
	  
      <tr>
        <td colspan="2"><table width="100%" border="0" cellpadding="0" cellspacing="0" class="banner">
          <tr>
            <td class="middlealign"><a href="index.php"><img src="images/deal_data_logo.png" width="177" height="49" alt="" /></a></td>
            <td><img src="images/spacer.gif" width="30" height="1" alt="" /></td>
            <td><table width="100%" border="0" cellpadding="0" cellspacing="0" class="search">
              <tr>
                <td width="3"><img src="images/searchbox_left.gif" width="3" height="44" alt="" /></td>
                <td>
					<?php
					/***
					sng:23/mar/2010
					search functionality will be different in different pages, so we put the search form dynamically.
					However, if no search view is specified, we show the default search view which does nothing
					
					sng:20/aug/2010
					Now the default sarch view is all_search_view.php
					
					sng:21/nov/2011
					Let us resurrect the default search with dropdowns
					*******/
					/*if(!isset($g_view['top_search_view'])){
						//include("default_search_view.php");
						include("all_search_view.php");
					}else{
						include($g_view['top_search_view']);
					}*/
					include("default_search_view.php");
					?>
				</td>
                <td width="3"><img src="images/searchbox_right.gif" width="3" height="44" alt="" /></td>
              </tr>
            </table></td>
			 <!--
			sng:6/jul/2010
			client do not want to duplicate the site name since it is already in the logo, and do not want the tagline
            <td><img src="images/spacer.gif" width="30" height="1" alt="" /></td>
            <td class="centeralign middlealign"><h1>deal-data</h1>
              A place to share insights &amp; find deal information</td>
			  -->
          </tr>
		  
          </table></td>
      </tr>
      <tr>
        <td colspan="2"><table width="100%" border="0" cellpadding="0" cellspacing="0" class="content">
		<?php
		if($g_view['page_heading']!=""){
			?>
			<tr>
            <td><h1 style="float:left"><?php echo $g_view['page_heading'];?></h1><?php if(isset($g_view)&&$g_view['show_help']){?><div style="float:right"><input type="button" id="help" class="btn_auto" value="Show Help" onclick="toggle_help()" /></div><?php }?></td>
          </tr>
          <tr>
            <td><img src="images/spacer.gif" width="1" height="15" alt="" /></td>
          </tr>
			<?php
		}else{
			/**********************
			sng:11/nov/2011
			No heading. So if there is help, only then put s row
			***********************/
			if(isset($g_view)&&$g_view['show_help']){
				?>
				<tr><td><div style="float:right"><input type="button" id="help" class="btn_auto" value="Show Help" onclick="toggle_help()" /></div></td></tr>
				<tr><td><img src="images/spacer.gif" width="1" height="15" alt="" /></td></tr>
				<?php
			}
		
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
        <td colspan="2"><table width="100%" border="0" cellpadding="0" cellspacing="0" class="footer">
          <tr>
            <td><a href="index.php" >Home</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="<?php echo $g_http_path;?>/aboutus.php">About Us</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="<?php echo $g_http_path;?>/blog.php">News &amp; Demos </a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="<?php echo $g_http_path;?>/privacy.php">Privacy Policy</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="<?php echo $g_http_path;?>/legal.php"> Legal Notices</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="" id="site-contactus2">Contact Us</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="suggest_a_deal.php">Suggest A Deal</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="help.php">Help</a><br />
            Copyright &copy; <?php echo date('Y');?> deal-data.com<br />
			<?php
			/***************
			sng:23/mar/2011
			put help in footer
			************/
			?>
			<div id="footer_tagline">Deal-data is a service provided by <a href="http://www.finfore.info" target="_blank" class="footer_tagline_a">Finfore Limited</a></div>
			</td>
          </tr>
        </table></td>
      </tr>
    </table></td>
  </tr>
</table>
<?php
/*****
sng:20/oct/2010
We want to show the contact us form in a popup instead of using mailto: This way we hide the site email
from spam bots
****/
?>
<style type="text/css">
#background_contact_popup{
	display:none;
	position:fixed;
	_position:absolute; /* hack for internet explorer 6*/
	height:100%;
	width:100%;
	top:0;
	left:0;
	background:#000000;
	border:1px solid #cecece;
	z-index:1;
}
#popup_contact{
	display:none;
	position:fixed;
	_position:absolute; /* hack for internet explorer 6*/
	height:384px;
	width:408px;
	background:#FFFFFF;
	border:2px solid #cecece;
	z-index:2;
	padding:12px;
	font-size:13px;
}
#popup_contact_close{
	font-size:14px;
	line-height:14px;
	right:6px;
	top:4px;
	position:absolute;
	color:#6fa5fd;
	font-weight:700;
	display:block;
}
#popup_contact_result{
	font-size:12px;
	color:#3366FF;
}
</style>
<script type="text/javascript">
var contact_popup_status = 0;
//loading popup with jQuery magic!
function load_contact_Popup(){
	//loads popup only if it is disabled
	if(contact_popup_status==0){
		jQuery("#background_contact_popup").css({
		"opacity": "0.7"
		});
		jQuery("#background_contact_popup").fadeIn("slow");
		jQuery("#popup_contact").fadeIn("slow");
		contact_popup_status = 1;
	}
}
//disabling popup with jQuery magic!
function disable_contact_Popup(){
	//disables popup only if it is enabled
	if(contact_popup_status==1){
		jQuery("#background_contact_popup").fadeOut("slow");
		jQuery("#popup_contact").fadeOut("slow");
		contact_popup_status = 0;
	}
}
//centering popup
function center_contact_Popup(){
	//request data for centering
	var windowWidth = jQuery(window).width();
	var windowHeight = jQuery(window).height();
	var contact_popupHeight = jQuery("#popup_contact").height();
	var contact_popupWidth = jQuery("#popup_contact").width();
	//centering
	jQuery("#popup_contact").css({
		"position": "absolute",
		"top": windowHeight/2-contact_popupHeight/2+jQuery(window).scrollTop(),
		"left": windowWidth/2-contact_popupWidth/2
	});
	//only need force for IE6

	jQuery("#background_contact_popup").css({
		"height": windowHeight
	});

}
</script>
<script type="text/javascript">
function send_contact_email(){
	jQuery("#popup_contact_result").html("sending...");
	jQuery.post(
        'ajax/send_contact_email.php',
        jQuery("#site-contactus-frm").serialize(),
        function(response) {
            jQuery("#popup_contact_result").html(response);
        }
    )
	return false;
}
</script>
<div id="popup_contact">
	<a id="popup_contact_close">x</a>
	<h1>Contact Data-CX</h1>
	<div id="contact_popup_content">
	<form method="post" action="#" onsubmit="return send_contact_email();" id="site-contactus-frm">
	<?php
	/****************************
	sng:30/sep/2011
	the static pages has mailto links. Those are trapped and is popup is used to send the email. The subkect line is set.
	since it may happen that mailto are different, we extract the mailto part and set a hidden field here.
	the ajax code check whether send_contact_to is blank or not. If not blank, use that else use the default.
	******************************/
	?>
	<input type="hidden" name="to" id="send_contact_to" value="" />
	<table width="100%" border="0">
	<tr>
	<td>Your Name</td>
	</tr>
	<tr>
	<td><input type="text" name="contact_from_name" id="contact_from_name" style="width: 400px;" /></td>
	</tr>
	<tr>
	<td>Your Email</td>
	</tr>
	<tr>
	<td><input type="text" name="contact_from_email" id="contact_from_email" style="width: 400px;" /></td>
	</tr>
	<tr>
	<td>Subject</td>
	</tr>
	<tr>
	<td><input type="text" name="contact_subject" id="contact_subject" style="width: 400px;" /></td>
	</tr>
	<tr>
	<td>Message</td>
	</tr>
	<tr>
	<td><textarea name="contact_message" id="contact_message" cols="45" rows="5" style="width: 400px;"></textarea></td>
	</tr>
	<tr><td><input type="submit" name="submit" value="Send" class="btn_auto" /></td></tr>
	</table>
	</form>
	</div>
	<div id="popup_contact_result"></div>
</div>
<div id="background_contact_popup"></div>
<script type="text/javascript">
//LOADING POPUP
//Click the button event!
/****
sng:17/mar/2011
after login, this element is not there. so we check
****/
if(document.getElementById("site-contactus")!=null){
	jQuery("#site-contactus").click(function(){
		//centering with css
		center_contact_Popup();
		//load popup
		load_contact_Popup();
		return false;
	});
}
jQuery("#site-contactus2").click(function(){
	//centering with css
	center_contact_Popup();
	//load popup
	load_contact_Popup();
	return false;
});
//CLOSING POPUP
//Click the x event!
jQuery("#popup_contact_close").click(function(){
	disable_contact_Popup();
});
//Click out event!
jQuery("#background_contact_popup").click(function(){
	disable_contact_Popup();
});
</script>
</body>
</html>
