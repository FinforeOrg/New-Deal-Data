<?php
require_once("../include/global.php");
require_once ("admin/checklogin.php");
require_once("classes/class.member.php");
///////////////////////////////////////////////////////
$g_view['err'] = array();
$g_view['msg'] = "";
$g_view['input'] = array();
/****************************************************************/
if(isset($_POST['action'])&&($_POST['action']=="add")){
     
	$validation_passed = false;
	$success = $g_mem->add_unfavoured_email($_POST,$validation_passed,$g_view['err']);
	if(!$success){
		die("Cannot add email suffix");
	}
	if($validation_passed){
		$g_view['msg'] = "Email suffix added";
	}else{
		//the form is to be shown with data just entered
		$g_view['input']['email_suffix'] = $_POST['email_suffix'];
	}
}
/****************************************************************/
if(isset($_POST['action'])&&($_POST['action']=="delete")){
	/////////////////////////////////////////////////////////////////
	$success = $g_mem->delete_unfavoured_email($_POST['id'],$g_view['msg']);
	if(!$success){
		die("Cannot delete unfavoured email");
	}
}
///////////////////////////////////////////////////////////
//get all members
$g_view['data_count'] = 0;
$g_view['data'] = array();
$g_view['num_to_show'] = 50;
$g_view['start'] = 0;
if(isset($_REQUEST['start'])&&($_REQUEST['start']!="")){
	$g_view['start'] = $_REQUEST['start'];
}
$success = $g_mem->list_unfavoured_email_paged($g_view['start'],$g_view['num_to_show']+1,$g_view['data'],$g_view['data_count']);
if(!$success){
	die("Cannot get list of unfavoured emails");
}
////////////////////////////////////////////////

////////////////////////////////////////////////////////
$g_view['heading'] = "List of Unfavoured Emails";
$g_view['content_view'] = "admin/unfavoured_email_list_view.php";
include("admin/content_view.php");
?>