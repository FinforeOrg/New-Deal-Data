<?php
/***
sng:30/apr/2010
we take the text from page data table. That way, admin can put what ever text here
***/
include("include/global.php");
require_once("check_mem_login.php");
////////////////////////////////////////////////////////////
require_once("classes/class.member.php");
require_once("classes/class.page.php");
//////////////////////////////////////////////////////////////////
if(isset($_POST['action'])&&($_POST['action']=="unregister")){
	$success = $g_mem->unregister_membership($_SESSION['mem_id']);
	if(!$success){
		die("Cannot unregister member");
	}
	////////////////////////
	//aftre unregister, this user is logged out
	$g_account->site_member_logout();
	exit;
}
///////////////////////////
//get page data
$g_view['page_name'] = "unregister";
$success = $g_page->get_page_data($g_view['page_name']);
if(!$success){
	die("Cannot get page data");
}

$g_view['meta_title'] = $g_page->meta_title;
$g_view['meta_keywords'] = $g_page->meta_keywords;
$g_view['meta_description'] = $g_page->meta_description;
//////////////////////////////////////////////////////////////////
$g_view['page_heading'] = $g_page->heading;
$g_view['page_content'] = $g_page->content;
////////////////////////////////////////////////////////////
$g_view['content_view'] = "member_unregister_view.php";
require("content_view.php");
?>