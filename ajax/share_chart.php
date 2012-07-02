<?php
/***
the sender address will be a tombstone address
I will use contact email
**/
require_once("../include/global.php");
require_once("include/strip_html_tags.php");
/******
sng:12/oct/2010
use the mailer class
******/
require_once("classes/class.mailer.php");
$mailer = new mailer();
		
require_once("classes/class.magic_quote.php");
$sender_email = $g_view['site_emails']['contact_email'];
$headers = "From: ".$sender_email."\r\n";
$subject = "Graph / data for your presentation";
$emails = explode(",",$_POST['email_addresses']);
foreach($emails as $to){
	//echo $g_mc->view_to_view($_POST['email_message']);
	//mail($to,$subject,$g_mc->view_to_view($_POST['email_message']),$headers);
	$success = $mailer->mail($to,$subject,$g_mc->view_to_view(strip_tags(strip_html_tags($_POST['email_message']))));
}
echo "emails sent";
?>