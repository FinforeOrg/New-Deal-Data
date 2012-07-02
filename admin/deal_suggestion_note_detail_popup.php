<?php
require_once("../include/global.php");
require_once ("admin/checklogin.php");

require_once("classes/class.deal_support.php");
$g_deal_support = new deal_support();

$deal_id = $_GET['id'];

$g_view['data_count'] = 0;
$g_view['data'] = NULL;
$success = $g_deal_support->admin_fetch_note_correction_on_deal($deal_id,$g_view['data'],$g_view['data_count']);
if(!$success){
	die("Cannot get the notes");
}
include("admin/deal_suggestion_note_detail_popup_view.php");
?>