<?php
include("../include/global.php");
require_once ("admin/checklogin.php");
require_once("classes/class.sitesetup.php");
///////////////////////////////////////////////////////
$g_view['err'] = array();
$g_view['msg'] = "";
$g_view['data'] = array();
///////////////////////////////////////
if(isset($_POST['action'])&&($_POST['action']=="change")){
	$validation_passed = false;
	$success = $g_site->set_site_emails($_POST);
	if(!$success){
		die("Cannot change site emails");
	}
	if($validation_passed){
		$g_view['msg'] = "Email changed";
	}
}
///////////////////////////////////////////////////////////
//get the current data
$success = $g_site->get_site_emails($g_view['data']);
if(!$success){
	die("Cannot get site emails");
}
///////////////////////////////////////////////////////////////////
$g_view['heading'] = "Edit Site Emails";
$g_view['content_view'] = "admin/site_emails_view.php";
include("admin/content_view.php");
?>