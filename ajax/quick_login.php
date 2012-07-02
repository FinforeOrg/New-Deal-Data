<?php
/******************************
sng:16/dec/2011

Used to log in an user using the quick login popup. Used when user wants to submit data but is not logged in.
NOT used for now
**********************/
require_once("../include/global.php");
require_once("classes/class.account.php");
require_once("classes/db.php");

$json = array();
$json['authenticated'] = 'n';
$json['msg'] = "";
$json['err_arr'] = array();
/******************************************************************************
Not used now
****/
$json['authenticated'] = 'n';
$json['msg'] = "Not used now";
echo json_encode($json);
exit;
/******************************************************************************/

/*******************************
now try to authenticate
********************************/
$is_authenticated = false;
$success = $g_account->authenticate_site_member($_POST['login_email'],$_POST['password'],false,$is_authenticated,$json['err_arr']);
if(!$success){
	$json['authenticated'] = 'n';
	$json['msg'] = "Server error";
	echo json_encode($json);
	exit;
}

if(!$is_authenticated){
	$json['authenticated'] = 'n';
	$json['msg'] = "Please try again";
	echo json_encode($json);
	exit;
}
/*********************************
sng:25/oct/2010
since the user has logged in, regenerate session, so that if somebody has the session id and waiting for this
user to login, that fellow cannot replay the session id to masquarade as this user

will this cause problem?
************/
session_regenerate_id(true);

$json['authenticated'] = 'y';
$json['msg'] = "Logged in";
echo json_encode($json);
exit;
?>