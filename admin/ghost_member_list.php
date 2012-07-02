<?php
include("../include/global.php");
require_once ("admin/checklogin.php");
require_once("classes/class.member.php");
require_once("classes/class.company.php");
require_once("classes/class.magic_quote.php");
///////////////////////////////////////////////////////

$g_view['msg'] = "";
$mem_deleted = false;
if(isset($_POST['action'])&&($_POST['action']=="delete")){
	$success = $g_mem->delete_member($_POST['mem_id'],$mem_deleted,$g_view['msg']);
	if(!$success){
		die("Cannot delete ghost member");
	}
	if($mem_deleted){
		$g_view['msg'] = "Ghost member deleted";
	}
}
///////////////////////////////////////////////////////////
//get all members
$g_view['data_count'] = 0;
$g_view['data'] = array();
$g_view['num_to_show'] = 50;
$g_view['start'] = 0;
if(isset($_REQUEST['start'])&&($_REQUEST['start']!="")){
	$g_view['start'] = $_REQUEST['start'];
}
$success = $g_mem->search_all_ghost_member_list_paged($_POST,$g_view['start'],$g_view['num_to_show']+1,$g_view['data'],$g_view['data_count']);
if(!$success){
	die("Cannot get member data");
}
////////////////////////////////////////////////

////////////////////////////////////////////////////////
$g_view['heading'] = "List of Ghost Members";
$g_view['content_view'] = "admin/ghost_member_list_view.php";
include("admin/content_view.php");
?>