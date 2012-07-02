<?php
include("../include/global.php");
require_once ("admin/checklogin.php");
require_once("classes/class.member.php");
require_once("classes/class.company.php");
///////////////////////////////////////////////////////

$g_view['msg'] = "";
////////////////////////////////////////////
/*********
sng:6/oct/2010
in case admin wish to activate directly
***/
$g_view['activated'] = false;
if(isset($_POST['myaction'])&&($_POST['myaction']=="activate")){
	$success = $g_mem->activate_membership($_POST['uid'],$g_view['activated'],$g_view['msg']);
	if(!$success){
		die("Cannot activate the member");
	}
}
///////////////////////////////////////
//get_all_new_regtration_list
$g_view['data_count'] = 0;
$g_view['data'] = array();
$success = $g_mem->get_all_unactivated_favoured_reg_list($g_view['data'],$g_view['data_count']);
if(!$success){
	die("Cannot get favoured unactivated data");
}
////////////////////////////////////////////////

////////////////////////////////////////////////////////
$g_view['heading'] = "List of Unactivated Favoured Accounts";
$g_view['content_view'] = "admin/favoured_unactivated_registration_list_view.php";
include("admin/content_view.php");
?>