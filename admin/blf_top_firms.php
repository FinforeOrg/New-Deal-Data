<?php
include("../include/global.php");
require_once ("admin/checklogin.php");
require_once("classes/class.company.php");
require_once("classes/class.magic_quote.php");
///////////////////////////////////////////////////////
$g_view['msg'] = "";
////////////////////////////////////////////


///////////////////////////////////////
$g_view['data_count'] = 0;
$g_view['data'] = array();
$success = $g_company->get_top_firms_list($g_view['data'],$g_view['data_count']);
if(!$success){
	die("Cannot get bank / law firm data");
}
////////////////////////////////////////////////////////
$g_view['heading'] = "Top Firms List";
$g_view['content_view'] = "admin/blf_top_firms_view.php";
include("admin/content_view.php");
?>