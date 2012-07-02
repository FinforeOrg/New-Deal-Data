<?php
include("../include/global.php");
require_once ("admin/checklogin.php");
///////////////////////////////////////////////
$g_view['heading'] = "Upload Transactions";
$g_view['content_view'] = "admin/t_script_upload_deal_data_view.php";
include("admin/content_view.php");
//////////////////////////////
?>