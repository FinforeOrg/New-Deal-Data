<?php
include("../include/global.php");
require_once ("admin/checklogin.php");
require_once("classes/class.company.php");
require_once("classes/class.country.php");
require_once("classes/class.magic_quote.php");
///////////////////////////////////////////////////////
$g_view['err'] = array();
$g_view['msg'] = "";
$g_view['input'] = array();
///////////////////////////////////////
if(isset($_POST['action'])&&($_POST['action']=="add")){
     
	$validation_passed = false;
	$success = $g_company->add_bank_lawfirm($_POST,"logo",LOGO_PATH,$validation_passed,$g_view['err']);
	if(!$success){
		die("Cannot add bank/law firm");
	}
	if($validation_passed){
		$g_view['msg'] = $g_view['input']['type']." added";
	}else{
		//the form is to be shown with data just entered
		$g_view['input']['name'] = $g_mc->view_to_view($_POST['name']);
		$g_view['input']['short_name'] = $g_mc->view_to_view($_POST['short_name']);
		$g_view['input']['type'] = $_POST['type'];
	}
}
/////////////////////////////////////////////////

$g_view['heading'] = "Add Bank / Law Firm";
$g_view['content_view'] = "admin/blf_add_view.php";
include("admin/content_view.php");
?>