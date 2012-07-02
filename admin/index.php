<?php
require_once("../include/global.php");
require_once ("admin/checklogin.php");
///////////////////////////////////////////////////////
$g_view['heading'] = "Home";
$g_view['content_view'] = "admin/index_view.php";
include("admin/content_view.php");
?>