<?php
include("../include/global.php");
require_once ("sa/checklogin.php");
require_once("classes/class.sitesetup.php");
///////////////////////////////////////////////////////
$g_view['err'] = array();
$g_view['msg'] = "";
$g_view['data'] = array();
///////////////////////////////////////
if(isset($_POST['action'])&&($_POST['action']=="change")){
	$validation_passed = false;
	$success = $g_site->set_maintenance_info($_POST);
	if(!$success){
		die("Cannot change site maintenance info");
	}
	$g_view['msg'] = "Updated";
}
///////////////////////////////////////////////////////////
//get the current maintenance setup data
$success = $g_site->get_maintenance_info($g_view['data']);
if(!$success){
	die("Cannot get site maintenance info");
}
///////////////////////////////////////////////////////////////////
$g_view['heading'] = "Site Maintenance";
$g_view['content_view'] = "sa/maintenance_view.php";
include("sa/content_view.php");
?>