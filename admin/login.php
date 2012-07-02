<?php
include ("../include/global.php");
///////////////////////////////////////////
require_once("classes/class.account.php");
///////////////////////////////////////
$g_view['msg'] = "";
$is_authenticated = false;
if(isset($_POST['action'])&&($_POST['action']=="login"))
{
	$success = $g_account->authenticate_admin($_POST['login_name'],$_POST['password'],$is_authenticated,$g_view['msg']);
	if(!$success){
		die("Cannot authenticate admin user");
	}
	if(!$is_authenticated){
		include("admin/login_view.php");
		exit;
	}
	//ok
	header("Location: index.php");
	exit;
	//////////////////////////////////////////////////////
}
include("admin/login_view.php");
?>
