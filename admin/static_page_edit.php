<?php
include("../include/global.php");
require_once ("admin/checklogin.php");
require_once("classes/class.page.php");
//////////////////////////////////////////////////////
//handle action
if(isset($_POST['action'])&&($_POST['action']=="edit")){
	$success = $g_page->set_page_data($_GET['name'],$_POST);
	if(!$success){
		$g_view['msg'] = "Could not update page data";
	}else{
		$g_view['msg'] = "Page data updated";
	}
}
////////////////////////////////////////////////////////////////////////////
//get page data
$g_view['page_data_arr'] = array();
$success = $g_page->get_page_data($_GET['name']);
if(!$success){
	die("Cannot get page data");
}
$g_view['page_data_arr']['page_name'] = $_GET['name'];
$g_view['page_data_arr']['meta_title'] = $g_page->meta_title;
$g_view['page_data_arr']['meta_keywords'] = $g_page->meta_keywords;
$g_view['page_data_arr']['meta_description'] = $g_page->meta_description;
$g_view['page_data_arr']['heading'] = $g_page->heading;
$g_view['page_data_arr']['content'] = $g_page->content;
///////////////////////////////////////////////////////////
$g_view['heading'] = "Edit Page";
$g_view['content_view'] = "admin/static_page_edit_view.php";
include("admin/content_view.php");
?>