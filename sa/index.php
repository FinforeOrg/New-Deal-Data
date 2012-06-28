<?php
include("../include/global.php");
require_once ("sa/checklogin.php");
///////////////////////////////////////////////////////
$g_view['heading'] = "Home";
$g_view['content_view'] = "sa/index_view.php";
include("sa/content_view.php");
?>