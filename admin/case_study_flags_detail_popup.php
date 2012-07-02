<?php
require_once("../include/global.php");
require_once ("admin/checklogin.php");

require_once("classes/class.transaction.php");


$case_study_id = $_GET['id'];

$g_view['data_count'] = 0;
$g_view['data'] = array();
$success = $g_trans->case_study_flag_details($case_study_id,$g_view['data'],$g_view['data_count']);
if(!$success){
	die("Cannot get the feedbacks");
}
include("admin/case_study_flags_detail_popup_view.php");
?>