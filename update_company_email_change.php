<?php
/**********************
sng: 25/jan/2011
This is called to update the company/work email change request. The member will normally click this link from email.
The page requires login. So remember to send the member to this page
*****************/
include("include/global.php");
$token = $_GET['token'];
$_SESSION['after_login'] = "update_company_email_change.php?token=".$token;
require_once("check_mem_login.php");

require_once("classes/class.member.php");

require_once("default_metatags.php");

$g_view['msg'] = "";
$g_view['updated'] = false;
$mem_id = $_SESSION['mem_id'];

$success = $g_mem->update_company_work_email_change($mem_id,$token,$g_view['updated'],$g_view['msg']);
/////////////////////////////////////////////////////
$g_view['page_heading'] = "Company / Work Email Updation";
$g_view['content_view'] = "update_company_email_change_view.php";
require("content_view.php");
////////////////////////////////////////////////////////////////////////////
?>