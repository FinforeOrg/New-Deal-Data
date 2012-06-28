<?php
require_once("include/global.php");
require_once("classes/class.member.php");
///////////////////////////////////////
//called to handle the deal addition to registration request
if(isset($_POST['action'])&&($_POST['action']=="add_deal")){
	$g_view['registration_request_id'] = $_POST['registration_req_id'];
	$g_view['deals_arr'] = $_POST['transaction_id'];
	$success = $g_mem->add_deals_to_registration_request($g_view['registration_request_id'],$g_view['deals_arr']);
	if(!$success){
		die("Cannot add deals to registration request");
	}
	/***
	sng:27/july/2010
	we check if the reistration is to be shown favour or not. Favoured registrations are sent emails then and there, so
	we need to change the message
	*********/
	$g_view['is_favoured'] = false;
	$success = $g_mem->is_registration_favoured($g_view['registration_request_id'],$g_view['is_favoured']);
	//if here is db error, well, neve mind
}
require_once("default_metatags.php");
/////////////////////////////////
$g_view['page_heading'] = "Member Registration Add Deals";
////////////////////////////////////////////////////
$g_view['content_view'] = "register_add_deal_view.php";
require("content_view.php");
?>