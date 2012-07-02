<?php
include("../include/global.php");
require_once ("admin/checklogin.php");
require_once("classes/class.page.php");
///////////////////////////////////////////////////////
//get page listing
$g_view['num_pages'] = 0;
$g_view['page_data_arr'] = array();
$success = $g_page->get_all_pages($g_view['num_pages'],$g_view['page_data_arr']);
if(!$success){
	die("Cannot get page lising data");
}
///////////////////////////////////////////////////////////
$g_view['heading'] = "Static Page Listing";
$g_view['content_view'] = "admin/static_page_list_view.php";
include("admin/content_view.php");
?>