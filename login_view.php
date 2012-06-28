<table width="100%" border="0" cellpadding="0" cellspacing="0">
<tr>
<td>&nbsp;</td>
<td style="text-align:center; width:550px">

<table width="100%" border="0" cellpadding="0" cellspacing="0" class="registerinner">
					  <tr>
						<td><table width="100%" border="0" cellpadding="0" cellspacing="0" class="registercontent">
						  <tr>
							<th>Log-In</th>
						  </tr>
						  <tr>
							<td style="padding:2px 20px 20px 20px;">
							<form method="post" action="">
							<input name="action" type="hidden" value="login">
							<table width="100%" border="0" cellspacing="0" cellpadding="0">
							
							  <tr>
								<td>Email Address:</td>
							  
								<td><input type="text" name="login_email" id="login_name" value="<?php echo $g_view['input']['login_email'];?>" style="width:200px"/><br />
										<span class="err_txt"><?php echo $g_view['err']['login_email'];?></span></td>
										<td>&nbsp;</td>
							  </tr>
							  <tr>
								<td colspan="2"><img src="images/spacer.gif" width="1" height="7" alt="" /></td>
							  </tr>
							  <tr>
								<td>Password</td>
							  
								<td><input type="password" name="password" id="password" value="<?php echo $g_view['input']['pass'];?>" style="width:200px" />&nbsp;<input type="checkbox" name="remember_pass" value="remember_pass" <?php if($g_view['input']['rem_login']){?>checked="checked"<?php }?> />&nbsp;Remember<br />
										<span class="err_txt"><?php echo $g_view['err']['password'];?></span></td>
								<td></td>
							  </tr>
							  <tr>
								<td colspan="3"><img src="images/spacer.gif" width="1" height="7" alt="" /></td>
							  </tr>
							  
							  <tr>
								<td colspan="3"><img src="images/spacer.gif" width="1" height="7" alt="" /></td>
							  </tr>
							  <tr>
							  	<td>&nbsp;</td>
								<td colspan="2"><input name="Login" type="submit" class="btn_auto" id="button2" value="LOGIN"/>&nbsp;<a href="#" id="forgot_password" class="link_as_button">Forgot Your Password?</a></td>
							  </tr>
							  <tr>
								<td colspan="3"><img src="images/spacer.gif" width="1" height="20" alt="" /></td>
							  </tr>
							  
							  <tr>
											
											<?php
											  /***********
											  sng:11/apr/2011
											  register view now has link to linkedin
											  *********/
											  ?>
											<td colspan="3" style="text-align:right;"><strong>New user?</strong> <a href="register.php" class="link_as_button">REGISTER HERE</a></td>
										</tr>
							</table>
							</form>
							</td>
						  </tr>
						</table></td>
					  </tr>
					</table>

</td>
<td>&nbsp;</td>
</tr>
</table>
<script>
jQuery("#login_name").focus();
</script>
<?php
/****
sng:04/oct/2010
Need support for forgot password popup
***/
?>
<style type="text/css">
#background_popup{
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
#popup_forgot_password{
	display:none;
	position:fixed;
	_position:absolute; /* hack for internet explorer 6*/
	height:120px;
	width:408px;
	background:#FFFFFF;
	border:2px solid #cecece;
	z-index:2;
	padding:12px;
	font-size:13px;
}
#popup_forgot_password_close{
	font-size:14px;
	line-height:14px;
	right:6px;
	top:4px;
	position:absolute;
	color:#6fa5fd;
	font-weight:700;
	display:block;
}
#popup_result{
	font-size:12px;
	color:#3366FF;
}
.popup_text{
	font-size:12px;
}
</style>
<script type="text/javascript">
var forgot_password_popup_status = 0;
//loading popup with jQuery magic!
function loadPopup(){
	//loads popup only if it is disabled
	if(forgot_password_popup_status==0){
		$("#background_popup").css({
		"opacity": "0.7"
		});
		$("#background_popup").fadeIn("slow");
		$("#popup_forgot_password").fadeIn("slow");
		forgot_password_popup_status = 1;
	}
}
//disabling popup with jQuery magic!
function disablePopup(){
	//disables popup only if it is enabled
	if(forgot_password_popup_status==1){
		$("#background_popup").fadeOut("slow");
		$("#popup_forgot_password").fadeOut("slow");
		forgot_password_popup_status = 0;
	}
}
//centering popup
function centerPopup(){
	//request data for centering
	var windowWidth = document.documentElement.clientWidth;
	var windowHeight = document.documentElement.clientHeight;
	var popupHeight = $("#popup_forgot_password").height();
	var popupWidth = $("#popup_forgot_password").width();
	//centering
	$("#popup_forgot_password").css({
		"position": "absolute",
		"top": windowHeight/2-popupHeight/2,
		"left": windowWidth/2-popupWidth/2
	});
	//only need force for IE6

	$("#background_popup").css({
		"height": windowHeight
	});

}
$(document).ready(function(){
	//LOADING POPUP
	//Click the button event!
	$("#forgot_password").click(function(){
		//centering with css
		centerPopup();
		//load popup
		loadPopup();
		return false;
	});
	//CLOSING POPUP
	//Click the x event!
	$("#popup_forgot_password_close").click(function(){
		disablePopup();
	});
	//Click out event!
	$("#background_popup").click(function(){
		disablePopup();
	});
	//Press Escape event!
	$(document).keypress(function(e){
		if(e.keyCode==27 && forgot_password_popup_status==1){
			disablePopup();
		}
	});
});
</script>
<script type="text/javascript">
function forgot_password(){
	$("#popup_result").html("checking...");
	$.post(
        'ajax/forgot_password.php',
        {work_email:$("#rem_work_email").val()},
        function(response) {
            $("#popup_result").html(response);
        }
    )
	return false;
}
</script>
<div id="popup_forgot_password">
	<a id="popup_forgot_password_close">x</a>
	<h1>Forgot Password</h1>
	<div id="popup_content">
	<form method="post" action="#" onsubmit="return forgot_password();" id="forgot_password_frm">
	<table width="100%" border="0">
	<tr>
	<td>
	<div class="popup_text">Enter you work email address which you used to register with this site</div></td>
	</tr>
	<tr>
	<td><input type="text" id="rem_work_email" /></td>
	</tr>
	
	<tr><td><input type="submit" name="submit" value="Submit" class="btn_auto" /></td></tr>
	</table>
	</form>
	</div>
	<div id="popup_result"></div>
</div>
<div id="background_popup"></div>