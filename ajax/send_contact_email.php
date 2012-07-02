<?php
/******
sng:20/oct/2010
Used to get the contact us form data and fire an email
use the mailer class
**********/
require_once("../include/global.php");
require_once("classes/class.mailer.php");
require_once("classes/class.magic_quote.php");
require_once("include/strip_html_tags.php");
$mailer = new mailer();
//validation
if($_POST['contact_from_name']==""){
	echo "Please specify your name";
	exit;
}
if($_POST['contact_from_email']==""){
	echo "Please specify your email";
	exit;
}
if($_POST['contact_subject']==""){
	echo "Please specify the subject";
	exit;
}
if($_POST['contact_message']==""){
	echo "Please specify the message";
	exit;
}
/***************
sng:23/oct/2010
sanitize
***************/
$_POST['contact_from_name'] = strip_tags(strip_html_tags($_POST['contact_from_name']));
$_POST['contact_from_email'] = strip_tags(strip_html_tags($_POST['contact_from_email']));
$_POST['contact_subject'] = strip_tags(strip_html_tags($_POST['contact_subject']));
$_POST['contact_message'] = strip_tags(strip_html_tags($_POST['contact_message']));
/**************************************
sng:30/sep/2011
we now sent a to variable. If that is not blank, use that one
***********************************/
if($_POST['to']!=""){
	$to = $_POST['to'];
}else{
	$to = $g_view['site_emails']['contact_email'];
}

$subject = "Contact email from data-cx.com visitor";
$message = "From ".$_POST['contact_from_name']." (".$_POST['contact_from_email'].")\r\n";
$message.= "Sub ".$g_mc->view_to_view($_POST['contact_subject'])."\r\n";
$message.= $g_mc->view_to_view($_POST['contact_message']);
$success = $mailer->mail($to,$subject,$message);
if($success) echo "The email has been sent.";
else echo "Could not send the email, please try again.";
exit;
?>