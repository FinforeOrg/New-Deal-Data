<?php
include("../include/global.php");
require_once ("admin/checklogin.php");
require_once("classes/class.transaction.php");
///////////////////////////////////////////////////////
$g_view['msg'] = "";
$g_view['err'] = array();
///////////////////////////////////////
if(isset($_POST['action'])&&($_POST['action']=="add")){
	$validation_passed = false;
	$success = $g_trans->add_category_type_subtype($_POST,$validation_passed,$g_view['err']);
	if(!$success){
		die("Cannot add category type subtype");
	}
}
//////////////////////////////////////////
//get all transaction type and sub type and sub sub type
$g_view['data_count'] = 0;
$g_view['data'] = array();
$success = $g_trans->get_all_category_type_subtype($g_view['data'],$g_view['data_count']);
if(!$success){
	die("Cannot get transaction type and subtype data");
}
////////////////////////////////////////////////

$g_view['heading'] = "List of Transaction Types and sub types";
$g_view['content_view'] = "admin/deal_type_subtype_list_view.php";
include("admin/content_view.php");
?>