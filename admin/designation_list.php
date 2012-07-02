<?php
include("../include/global.php");
require_once ("admin/checklogin.php");
require_once("classes/class.member.php");
///////////////////////////////////////////////////////
$g_view['msg'] = "";
/////////////////////////////
if(isset($_POST['action'])&&($_POST['action']=="delete")){
	$success = $g_mem->delete_designation($_POST['id']);
	if(!$success){
		die("Cannot delete designation");
	}
	$g_view['msg'] = "Deleted";
}
///////////////////////////////////////
//get all designation list
$g_view['data_count'] = 0;
$g_view['data'] = array();
$success = $g_mem->get_all_designation_list($g_view['data'],$g_view['data_count']);
if(!$success){
	die("Cannot get deal data");
}
////////////////////////////////////////////////
///////////////////////////////////////////////////////////
//get_all_partner_name_list

////////////////////////////////////////////////////////
$g_view['heading'] = "List of Designation";
$g_view['content_view'] = "admin/designation_list_view.php";
include("admin/content_view.php");
?>