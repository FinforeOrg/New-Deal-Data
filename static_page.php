<?php
require_once("classes/class.page.php");
//get page data
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
////////////////////////////////////////////////////
$g_view['content_view'] = "static_page_view.php";
require("content_view.php");
?>