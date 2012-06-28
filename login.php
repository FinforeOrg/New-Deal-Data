<?php
require_once("include/global.php");
require_once("default_metatags.php");
require_once("classes/class.account.php");
/************
sng:10/jan/2012
if already logged in, do not show the login form but go to home. This can happen if the user login
via the in-page ajax quick login.
**************/
if($g_account->is_site_member_logged()){
	header("Location: index.php");
	exit;
}
/*********************************************/
$g_view['err'] = array();
$g_view['is_authenticated'] = false;
///////////////////////////////////////////////////////////
if(isset($_POST['action'])&&($_POST['action']=="login")){
	/***
	sng:9/jun/2010
	we check if the user wants to remember the login or not. In the view file, there is a checkbox remember_pass
	However, in the next login, the user may uncheck the box. Then we should not remember
	*******/
	$remember_login = false;
	if(isset($_POST['remember_pass'])){
		$remember_login = true;
	}
	$success = $g_account->authenticate_site_member($_POST['login_email'],$_POST['password'],$remember_login,$g_view['is_authenticated'],$g_view['err']);
	if(!$success){
		die("Cannot authenticate member");
	}
	if(!$g_view['is_authenticated']){
		$g_view['input']['login_email']  = $_POST['login_email'];
	}else{
		//authenticated, now redirect to private home
		//for company rep, the code this different, so we redirect to different home
		/*****
		sng:6/oct/2010
		check the session first. If $_SESSION['after_login'] is there, go to that page
		
		sng:25/oct/2010
		since the user has logged in, regenerate session, so that if somebody has the session id and waiting for this
		user to login, that fellow cannot replay the session id to masquarade as this user
		************/
		session_regenerate_id(true);
		if(isset($_SESSION['after_login'])&&($_SESSION['after_login']!="")){
			$temp_redirect = $_SESSION['after_login'];
			$_SESSION['after_login'] = "";
			header("Location: ".$temp_redirect);
			exit;
		}
		//for data-cx.com, we keep things simple. We just redirect to home page
		header("Location: index.php");
		exit;
		
	}
	/////////////////////////////////////////////////////////////////////////////////////////////////
}
//////////////////////////////////////////////////////////
/***
sng:9/jun/2010
if the user has logged in previously, and at that time if the user wanted to remember the login
there is a cookie mytombstones_mem_id. We use this to get the user name and password and also keep
the Remember pass checked
However, we prefill these fields, only if this is not login request
**/
if(isset($_COOKIE['mytombstones_mem_id'])&&($_COOKIE['mytombstones_mem_id']!="")&&(!isset($_POST['action']))){
	$g_view['input']['login_email'] = "";
	$g_view['input']['pass'] = "";
	$g_view['input']['rem_login'] = true;
	$success = $g_account->get_login_credential($_COOKIE['mytombstones_mem_id'],$g_view['input']['login_email'],$g_view['input']['pass']);
	if(!$success){
		die("Cannot get login credential");
	}
}
////////////////////////////////////////////////////////////////////
$g_view['page_heading'] = "Login";
////////////////////////////////////////////////////
$g_view['content_view'] = "login_view.php";
require("content_view.php");
?>