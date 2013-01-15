<?php
/****************
sng:15/jan/2013

We have ajax handlers to recommend colleague, admire competitor. Problem is, those are old scripts. Now we want a unified
script that will handle both the cases and will use json as response.

We use this is deal_page_partners_members.php

We get this member's id from the session
We need to check if the user has logged in

We need to check if the user type is banker or lawyer
The member cannot recommend self
The member cannot recommend twice
(but maybe these are to be checked in the member object)?
*****************/
include("../include/global.php");
require_once("classes/class.account.php");
require_once("classes/class.member.php");

$result = array();
$result['mem_added'] = 0;
$result['msg'] = 0;

if(!$g_account->is_site_member_logged()){
	$result['mem_added'] = 0;
	$result['msg'] = "You need to login first";
	echo json_encode($result);
	exit;
}

$this_mem_id = $_SESSION['mem_id'];
$other_mem_id = $_POST['member_id'];
$added = false;
$msg = "";

$ok = $g_mem->front_admire_recommend($this_mem_id,$other_mem_id,$added,$msg);
if(!$ok){
	$result['mem_added'] = 0;
	$result['msg'] = "Error while adding";
	echo json_encode($result);
	exit;
}
if($added){
	$result['mem_added'] = 1;
	$result['msg'] = "Added";
	echo json_encode($result);
	exit;
}else{
	$result['mem_added'] = 0;
	$result['msg'] = $msg;
	echo json_encode($result);
	exit;
}
?>