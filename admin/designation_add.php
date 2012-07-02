<?php
include("../include/global.php");
require_once ("admin/checklogin.php");
require_once("classes/class.member.php");
///////////////////////////////////////////////////////
$g_view['err'] = array();
$g_view['msg'] = "";
$g_view['input'] = array();
///////////////////////////////////////
if(isset($_POST['action'])&&($_POST['action']=="add")){
     
	$validation_passed = false;
	$success = $g_mem->add_designation($_POST,$validation_passed,$g_view['err']);
	if(!$success){
		die("Cannot add designation");
	}
	if($validation_passed){
		$g_view['msg'] = "Designation added";
	}else{
		//the form is to be shown with data just entered
		$g_view['input']['designation'] = $_POST['designation'];
		$g_view['input']['type'] = $_POST['type'];
		$g_view['input']['deal_share_weight'] = $_POST['deal_share_weight'];
		
	}
}

///////////////////////////////////////////////////////////

$g_view['heading'] = "Add Designation";
$g_view['content_view'] = "admin/designation_add_view.php";
include("admin/content_view.php");
?>