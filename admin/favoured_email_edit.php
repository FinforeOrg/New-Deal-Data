<?php
include("../include/global.php");
require_once ("admin/checklogin.php");
require_once("classes/class.member.php");
require_once("classes/class.magic_quote.php");
///////////////////////////////////////////////////////
$g_view['err'] = array();
$g_view['msg'] = "";
///////////////////////////////////////
if(isset($_POST['action'])&&($_POST['action']=="edit")){
     
	$validation_passed = false;
	
	
	$success = $g_mem->edit_registration_special_email($_POST['id'],$_POST,$validation_passed,$g_view['err']);
	if(!$success){
		die("Cannot update favoured email");
	}
	if($validation_passed){
		$g_view['msg'] = "Favoured email updated";
	}
}
/////////////////////////////////////////////////////////////////////////
//get the data
$g_view['data'] = NULL;
$success = $g_mem->get_registration_special_email($_POST['id'],$g_view['data']);

if(!$success){
	die("Cannot get the data");
}
///////////////////////////////////////////////////////////
$g_view['heading'] = "Edit Favoured Email";
$g_view['content_view'] = "admin/favoured_email_edit_view.php";
include("admin/content_view.php");
?>