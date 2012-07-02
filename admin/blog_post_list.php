<?php
include("../include/global.php");
require_once ("admin/checklogin.php");
require_once("classes/class.blog.php");
///////////////////////////////////////////////////////
$g_view['msg'] = "";
///////////////////////////////////////
if(isset($_POST['action'])&&($_POST['action']=="del")){
	$success = $g_blog->delete_post($_POST['blog_id']);
	if(!$success){
		die("Cannot delete blog data");
	}
	$g_view['msg'] = "Blog entry deleted";
}
///////////////////////////////////////////////////////////
//get the list of blog entries
$g_view['data_count'] = 0;
$g_view['data'] = array();
$g_view['num_to_show'] = 50;
$g_view['start'] = 0;
if(isset($_REQUEST['start'])&&($_REQUEST['start']!="")){
	$g_view['start'] = $_REQUEST['start'];
}
///////////////////////////////////
$success = $g_blog->get_all_post_list_paged($g_view['start'],$g_view['num_to_show']+1,$g_view['data'],$g_view['data_count']);
if(!$success){
	die("Cannot get blog data");
}
////////////////////////////////////////////////
$g_view['heading'] = "Blog Postings";
$g_view['content_view'] = "admin/blog_post_list_view.php";
include("admin/content_view.php");
?>