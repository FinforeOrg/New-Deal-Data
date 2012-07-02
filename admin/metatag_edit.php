<?php
include("../include/global.php");
require_once ("admin/checklogin.php");
require_once("classes/class.sitesetup.php");
//////////////////////////////////////////////////////
//handle action
if(isset($_POST['action'])&&($_POST['action']=="edit")){
	$success = $g_site->set_metatags($_POST);
	if(!$success){
		$g_view['msg'] = "Could not update metatags";
	}else{
		$g_view['msg'] = "Metatags updated";
	}
}
////////////////////////////////////////////////////////////////////////////
//get page data
$g_view['data'] = NULL;
$success = $g_site->get_metatags($g_view['data']);
if(!$success){
	die("Cannot get default metatag data");
}
///////////////////////////////////////////////////////////
$g_view['heading'] = "Edit Default Meta Tags";
$g_view['content_view'] = "admin/metatag_edit_view.php";
include("admin/content_view.php");
?>