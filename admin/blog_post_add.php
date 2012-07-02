<?php
include("../include/global.php");
require_once ("admin/checklogin.php");
require_once("classes/class.blog.php");
require_once("classes/class.magic_quote.php");
///////////////////////////////////////////////////////
$g_view['err'] = array();
$g_view['msg'] = "";
$g_view['input'] = array();
///////////////////////////////////////
if(isset($_POST['action'])&&($_POST['action']=="add")){
	$validation_passed = false;
	$success = $g_blog->add_post($_POST,$validation_passed,$g_view['err']);
	if(!$success){
		die("Cannot post blog entry");
	}
	if($validation_passed){
		$g_view['msg'] = "Blog entry posted";
	}else{
		//the form is to be shown with data just entered
		$g_view['input']['title'] = $g_mc->view_to_view($_POST['title']);
		$g_view['input']['content'] = $g_mc->view_to_view($_POST['content']);
	}
}
///////////////////////////////////////////////////////////
$g_view['heading'] = "Add Blog Posting";
$g_view['content_view'] = "admin/blog_post_add_view.php";
include("admin/content_view.php");
?>