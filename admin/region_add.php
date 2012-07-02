<?php
include("../include/global.php");
require_once ("admin/checklogin.php");
require_once("classes/class.country.php");
///////////////////////////////////////////////////////
$g_view['err'] = array();
$g_view['msg'] = "";
$g_view['input'] = array();
///////////////////////////////////////
if(isset($_POST['action'])&&($_POST['action']=="add")){
	$validation_passed = false;
	$success = $g_country->create_region($_POST,$validation_passed,$g_view['err']);
	if(!$success){
		die("Cannot create region data");
	}
	if($validation_passed){
		$g_view['msg'] = "Region data inserted";
	}else{
		//the form is to be shown with data just entered
		$g_view['input']['name'] = $_POST['name'];
	}
}
///////////////////////////////////////////////////////////
$g_view['heading'] = "Add Region";
$g_view['content_view'] = "admin/region_add_view.php";
include("admin/content_view.php");
?>