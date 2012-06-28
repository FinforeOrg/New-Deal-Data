<?php
include("include/global.php");
require_once("classes/class.blog.php");
///////////////////////////////////////////////////////////
//get the list of blog entries
$g_view['data_count'] = 0;
$g_view['data'] = array();
$g_view['num_to_show'] = 5;
$g_view['start'] = 0;
if(isset($_REQUEST['start'])&&($_REQUEST['start']!="")){
	$g_view['start'] = $_REQUEST['start'];
}
$success = $g_blog->get_all_post_list_paged($g_view['start'],$g_view['num_to_show']+1,$g_view['data'],$g_view['data_count']);
if(!$success){
	die("Cannot get blog data");
}
////////////////////////////////////////////////////////////
require_once("default_metatags.php");
//////////////////////////////////////////////////////////////////
/****
sng:4/oct/2010
Client wants this section to be News & Demos

sng:24/oct/2011
The site has been renamed to deal-data. Get rid of myTombstones in the heading
***/
$g_view['page_heading'] = "News &amp; Demos";
////////////////////////////////////////////////////
$g_view['content_view'] = "blog_view.php";
require("content_view.php");
?>