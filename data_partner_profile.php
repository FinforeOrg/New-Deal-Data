<?php
require_once("include/global.php");
require_once("classes/class.member.php");
/////////////////////////////////////////////
//get the previous work records, if any
$g_view['prev_work_data'] = array();
$g_view['prev_work_count'] = 0;
$success = $g_mem->front_prev_work_list($g_view['member_id'],$g_view['prev_work_data'],$g_view['prev_work_count']);
if(!$success){
	die("Cannot fetch prev work data");
}
/////////
////////////////////////////////////
require_once("default_metatags.php");
$g_view['content_view'] = "data_partner_profile_view.php";
require("content_view.php");
?>