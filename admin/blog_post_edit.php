<?php
include("../include/global.php");
require_once ("admin/checklogin.php");
require_once("classes/class.blog.php");
require_once("classes/class.magic_quote.php");
///////////////////////////////////////////////////////
$g_view['blog_id'] = $_POST['blog_id'];
$g_view['err'] = array();
$g_view['msg'] = "";
$g_view['input'] = array();
///////////////////////////////////////
if(isset($_POST['action'])&&($_POST['action']=="edit")){
	$validation_passed = false;
	$success = $g_blog->edit_post($g_view['blog_id'],$_POST,$validation_passed,$g_view['err']);
	if(!$success){
		die("Cannot update blog entry");
	}
	if($validation_passed){
		$g_view['msg'] = "Blog entry updated";
	}
}
/*********************
get the entry
*******************/
$g_view['data'] = NULL;
$success = $g_blog->get_entry($g_view['blog_id'],$g_view['data']);
if(!$success){
	die("Cannot get blog entry");
}
///////////////////////////////////////////////////////////
$g_view['heading'] = "Edit Blog Posting";
$g_view['content_view'] = "admin/blog_post_edit_view.php";
include("admin/content_view.php");
?>