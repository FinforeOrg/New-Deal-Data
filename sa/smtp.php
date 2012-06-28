<?php
require_once("../include/global.php");
require_once ("sa/checklogin.php");
require_once("classes/class.sitesetup.php");
///////////////////////////////////////////////////////
$g_view['err'] = array();
$g_view['msg'] = "";
$g_view['data'] = array();
///////////////////////////////////////
if(isset($_POST['action'])&&($_POST['action']=="change")){
	$validation_passed = false;
	$success = $g_site->set_smtp($_POST,$validation_passed,$g_view['err']);
	if(!$success){
		die("Cannot change smtp detail");
	}
	if($validation_passed){
		$g_view['msg'] = "SMTP detail updated";
	}
}
///////////////////////////////////////////////////////////
//get the current data
$success = $g_site->get_smtp($g_view['data']);
if(!$success){
	die("Cannot get smtp details");
}
///////////////////////////////////////////////////////////////////
$g_view['heading'] = "Edit SMTP Details";
$g_view['content_view'] = "sa/smtp_view.php";
include("sa/content_view.php");
?>