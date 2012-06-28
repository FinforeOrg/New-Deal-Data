<?php
include("../include/global.php");
require_once ("sa/checklogin.php");
require_once("classes/class.account.php");
///////////////////////////////////////////////////////

$g_view['msg'] = "";

///////////////////////////////////////
if(isset($_POST['action'])&&($_POST['action']=="flip_active")){
	$success = $g_account->set_admin_user_active_state($_POST['admin_id'],$_POST['active']);
	if(!$success){
		die("Cannot set admin user state");
	}
	$g_view['msg'] = "Admin user active status changed";
}
///////////////////////////////////////////////////////////
//get the list of admin users
$g_view['data_count'] = 0;
$g_view['data'] = array();
$success = $g_account->get_all_admin_user($g_view['data'],$g_view['data_count']);
if(!$success){
	die("Cannot get admin data");
}
////////////////////////////////////////////////
$g_view['heading'] = "List Admin Users";
$g_view['content_view'] = "sa/list_admin_users_view.php";
include("sa/content_view.php");
?>