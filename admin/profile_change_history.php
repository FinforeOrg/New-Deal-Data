<?php
include("../include/global.php");
require_once ("admin/checklogin.php");
require_once("classes/class.member.php");
require_once("classes/class.magic_quote.php");
///////////////////////////////////////////////////////
$g_view['msg'] = "";
///////////////////////////////////////////////////////////
//get all members
$g_view['data_count'] = 0;
$g_view['data'] = array();
$g_view['num_to_show'] = 20;
$g_view['start'] = 0;
if(isset($_REQUEST['start'])&&($_REQUEST['start']!="")){
	$g_view['start'] = $_REQUEST['start'];
}
$success = $g_mem->admin_member_profile_change_history_paged($g_view['start'],$g_view['num_to_show']+1,$g_view['data'],$g_view['data_count']);
if(!$success){
	die("Cannot get profile change history");
}
////////////////////////////////////////////////

////////////////////////////////////////////////////////
$g_view['heading'] = "Profile Change History";
$g_view['content_view'] = "admin/profile_change_history_view.php";
include("admin/content_view.php");
?>