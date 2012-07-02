<?php
/****
even though this is called in ajax, we need to check if the caller has logged in or not
The return data is in json
*******/
include("../include/global.php");
require_once("classes/class.transaction_discussion.php");

$json = array();
$json['error'] = "";
$json['posted'] = 'n';

$g_view['deal_id'] = $_POST['transaction_id'];
$g_view['show_discussion'] = false;
///////////////

$success = $g_deal_disc->can_see($g_view['deal_id'],$g_view['show_discussion']);
if(!$success){
	$json['error'] = "Cannot determine whether the user can access deal discussion or not";
	$json['posted'] = 'n';
	echo json_encode($json);
	exit;
}
if(!$g_view['show_discussion']){
	$json['error'] = "You do not have priviledge to post in the discussion for this deal";
	$json['posted'] = 'n';
	echo json_encode($json);
	exit;
}

$validation_passed = false;
$err = "";
$success = $g_deal_disc->post_comment($g_view['deal_id'],$_SESSION['mem_id'],$_POST['parent_posting_id'],$_POST['posting_txt'],$validation_passed,$err);
if(!$success){
	$json['posted'] = 'n';
	$json['error'] = "Error posting to discussion";
	echo json_encode($json);
	exit;
}
////////////////////
if($validation_passed){
	$json['posted'] = 'y';
	echo json_encode($json);
	exit;
}else{
	//validation failed
	$json['posted'] = 'n';
	$json['error'] = $err;
	echo json_encode($json);
	exit;
}
////////////////////
$json['posted'] = 'n';
$json['error'] = "Error posting to discussion";
echo json_encode($json);
exit;
?>