<?php
require_once("include/global.php");
///////////////////////////////////////////////////////////
require_once("default_metatags.php");

$g_view['page_heading'] = "Access Denied";
$g_view['page_content'] = "You do not have access to this section";
////////////////////////////////////////////////////
$g_view['content_view'] = "not_authorised_view.php";
require("content_view.php");
?>