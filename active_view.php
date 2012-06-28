<?php
/***************************************
sng14/apr/2011
If not activated then do not show the message sent from the member class. Show a custom message here
****************************************/
?>
<?php
if(!$g_view['activated']){
	?>
	<p>Have you already activated your registration? Please try and log-in at: <a href="https://data-cx.com/login.php">https://data-cx.com/login.php</a></p>
	<p>If this does not work, there are several possibilities:<br />
	a) Click the "forgotten password" link on the Log-In page<br />
	b) Your activation code was deleted because 48 hr has elapsed or admin is yet to approve it.</p>
	<p>If these options did not work, please email us at <a href="mailto:activation@data-cx.com">activation@data-cx.com</a> and we shall do our best to help.</p>
	<p>Many thanks,</p>
	<p>Customer Service.</p>
	<?php
}else{
	//activated
	?>
	<h3>
	<?php echo $g_view['msg'];?>
	</h3>
	<p>
	Redirecting to login page...
	</p>
	<?php
}
?>
<?php
if($g_view['activated']){
	?>
	<script>
	function goto_login(){
		window.location.replace('<?php echo $g_http_path;?>/login.php');
	}
	$(function(){
		setTimeout("goto_login()",3*1000);
	});
	</script>
	<?php
	}
?>