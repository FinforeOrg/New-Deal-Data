<?php
include ("../include/global.php");
///////////////////////////////////////////
require_once("classes/class.account.php");
///////////////////////////////////////
$g_view['msg'] = "";
$is_authenticated = false;
if(isset($_POST['action'])&&($_POST['action']=="login"))
{
	$success = $g_account->authenticate_sa($_POST['login_name'],$_POST['password'],$is_authenticated,$g_view['msg']);
	if(!$success){
		die("Cannot authenticate super admin user");
	}
	if(!$is_authenticated){
		include("sa/login_view.php");
		exit;
	}
	//ok
	header("Location: index.php");
	exit;
	//////////////////////////////////////////////////////
}
/////////////////////////
if(isset($_POST['action'])&&($_POST['action']=="retrieve_pass")){
	$success = $g_account->email_password_of_sa($g_view['msg']);
	if(!$success){
		die("Cannot email password of super admin user");
	}
}
include("sa/login_view.php");
?>
