<?php
include("../include/global.php");
require_once ("admin/checklogin.php");
require_once("classes/class.member.php");
require_once("classes/class.company.php");
///////////////////////////////////////////////////////

$g_view['msg'] = "";

///////////////////////////////////////
//get_all_new_regtration_list
$g_view['data_count'] = 0;
$g_view['data'] = array();
$success = $g_mem->get_all_new_reg_list($g_view['data'],$g_view['data_count']);
if(!$success){
	die("Cannot get new registration member data");
}
////////////////////////////////////////////////

////////////////////////////////////////////////////////
$g_view['heading'] = "List of New Registration Request";
$g_view['content_view'] = "admin/member_newreg_list_view.php";
include("admin/content_view.php");
?>