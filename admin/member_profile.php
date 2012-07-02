<?php
include("../include/global.php");
require_once ("admin/checklogin.php");
require_once("classes/class.country.php");
require_once("classes/class.member.php");
require_once("classes/class.company.php");
require_once("classes/class.magic_quote.php");
///////////////////////////////////////////////////////
$g_view['err'] = array();
$g_view['msg'] = "";



//get the deal data

$g_view['data'] = NULL;
$success = $g_mem->get_member_profile($_POST['mem_id'],$g_view['data']);
if(!$success){
	die("Cannot get reg req data");
}
/////////////////////////////////////////////////////////////


///////////////////////////////////////////////////////////
$g_view['heading'] = "Member profile data";
$g_view['content_view'] = "admin/member_profile_view.php";
include("admin/content_view.php");
?>