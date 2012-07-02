<?php
/******
sng:12/nov/2010
Find the deals where the note has url. That way, admin can edit the deal to move the url to the source section
*********/
include("../include/global.php");
require_once ("admin/checklogin.php");
require_once("classes/class.misc.php");
///////////////////////////////////////////////////////

$g_view['msg'] = "";

///////////////////////////////////////

///////////////////////////////////////////////////////////

$g_view['data_count'] = 0;
$g_view['data'] = array();
$g_view['num_to_show'] = 50;
$g_view['start'] = 0;
if(isset($_REQUEST['start'])&&($_REQUEST['start']!="")){
	$g_view['start'] = $_REQUEST['start'];
}
///////////////

/////////////////
$success = $g_misc->admin_get_deals_with_url_in_note_paged($g_view['start'],$g_view['num_to_show']+1,$g_view['data'],$g_view['data_count']);
if(!$success){
	die("Cannot get data");
}
////////////////////////////////////////////////
$g_view['heading'] = "List of deals with url in note";
$g_view['content_view'] = "admin/misc_list_deal_with_url_in_note_view.php";
include("admin/content_view.php");
?>