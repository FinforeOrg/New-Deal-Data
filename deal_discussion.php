<?php
/*****************************
sng:8/apr/2011
Page showing the discussion on the deal
*********/
require_once("include/global.php");
require_once("classes/class.transaction_discussion.php");
require_once("classes/class.transaction.php");

$g_view['deal_id'] = $_REQUEST['deal_id'];

$g_view['deal_found'] = false;
$g_view['deal_data'] = array();
$success = $g_trans->front_get_deal_detail($g_view['deal_id'],$g_view['deal_data'],$g_view['deal_found']);
if(!$success){
	die("Cannot get the deal");
}

//check access
$g_view['show_discussion'] = false;
$success = $g_deal_disc->can_see($g_view['deal_id'],$g_view['show_discussion']);
if(!$success){
	die("Cannot determine whether the user can access deal discussion or not");
}
if(!$g_view['show_discussion']){
	$g_view['page_content'] = "You do not have priviledge to view the discussion for this deal";
	require("not_authorised.php");
	exit;
}
/***********************************************
get the comments. Right now, just get the comments for this deal serially
*********/
$g_view['data_count'] = 0;
$g_view['data'] = array();
$success = $g_deal_disc->get_comments($g_view['deal_id'],$g_view['data'],$g_view['data_count']);
if(!$success){
	die("Cannot get the comments");
}
require_once("default_metatags.php");
$g_view['page_heading'] = "Discussion";
$g_view['content_view'] = "deal_discussion_view.php";
require("content_view.php");
?>