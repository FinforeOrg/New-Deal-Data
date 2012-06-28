<?php
/****
sng:3/aug/2010
A member comes to this page and wants to generate a graph that shows his/her bank at
rank 1 or 2 etc
The member can select certain fields and let the system do brute calculation
The thing is, the goal seek code can run for long time. Hence a bit of ajax may be used
********/
require_once("include/global.php");
/***
so that if not logged in then go to login
and after login can return here
***/
$_SESSION['after_login'] = "make_me_top.php";
require_once("check_mem_login.php");
require_once("classes/class.preset.php");
///////////////////////////////////////////////
/************************************************
sng:5/apr/2011
only banker and lawyers can access this section
***************/
if(($_SESSION['member_type']!="banker")&&($_SESSION['member_type']!="lawyer")){
	$g_view['page_content'] = "This section is only for bankers and lawyers";
    
    require("not_authorised.php");
    exit;        
    
}
/*******************************************************/
$validation_passed = true;
$g_view['err'] = array();
$g_view['msg'] = "";
//////////////////////////////////////////////
if(isset($_POST['action'])&&($_POST['action']=="search")){
	$success = $g_preset->front_submit_top_search_request($_SESSION['mem_id'],$_POST,$validation_passed,$g_view['err']);
	if(!$success){
		die("Cannot store make me top search request");
	}
	if($validation_passed){
		$g_view['msg'] = "Your request has been stored. It will take some time to produce the result. Once it is done, you will be notified by email. At any moment you want to check the status of your request, you can see it from this page.";
	}
}
////////////////////////////////////////////////
if(isset($_POST['myaction'])&&($_POST['myaction']=="archive")){
	$success = $g_preset->archive_top_search_request($_POST['job_id']);
	if(!$success){
		die("Cannot mark the request as archived");
	}
	$g_view['msg'] = "The request has been archived";
}
//////////////////////////////////////////////////////
/********************************************************
sng:12/jan/2011
Finished jobs submitted for more than 30 days do not show 'view' but 're-run'. The job is to be posted again
***********/
if(isset($_POST['myaction'])&&($_POST['myaction']=="rerun")){
	//echo $_POST['job_id'];
	$success = $g_preset->front_rerun_top_search_request($_SESSION['mem_id'],$_POST['job_id']);
	if(!$success){
		die("Cannot repost the request");
	}
	$g_view['msg'] = "The request has been submitted again for rerun";
}
//get current submitted jobs
$g_view['request_data'] = array();
$g_view['request_count'] = 0;
$success = $g_preset->front_get_all_top_search_request($_SESSION['mem_id'],$g_view['request_data'],$g_view['request_count']);
if(!$success){
	die("Cannot get the search requests");
}
////////////////////////////////////////////////
//get all top search options for country
$g_view['country_data_count'] = 0;
$g_view['country_data'] = array();
$success = $g_preset->front_get_all_top_search_option_for_country($g_view['country_data'],$g_view['country_data_count']);
if(!$success){
	die("Cannot get country top search option data");
}
////////////////////////////
//get all top search options for sector/industry
$g_view['sector_data_count'] = 0;
$g_view['sector_data'] = array();

$success = $g_preset->front_get_all_top_search_option_for_sector_industry($g_view['sector_data'],$g_view['sector_data_count']);
//echo "<div style='display:none'> <pre>"  . print_r($g_view['sector_data'], 1) . "</pre></div>";
if(!$success){
	die("Cannot get sector industry top search option data");
}
////////////////////
//get all top search options for deal type
$g_view['deal_type_data_count'] = 0;
$g_view['deal_type_data'] = array();
$success = $g_preset->front_get_all_top_search_option_for_deal_type($g_view['deal_type_data'],$g_view['deal_type_data_count']);
if(!$success){
	die("Cannot get deal type top search option data");
}
/////////////////
require_once("default_metatags.php");
$g_view['page_heading'] = "Make Me Top";
$g_view['show_help'] = true;
$g_view['content_view'] = "make_me_top_view.php";
require("content_view.php");
////////////////////////////////////////////////////////////
?>
