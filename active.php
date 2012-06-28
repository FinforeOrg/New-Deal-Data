<?php
/****
sng:7/apr/2010
This is called to activate the membership
************/
include("include/global.php");
require_once("classes/class.member.php");
////////////////////////////////////////////////////////////
require_once("default_metatags.php");
//////////////////////////////////////////////////////////////////
$g_view['msg'] = "";
$g_view['activated'] = false;
$req_id = $_GET['uid'];
$success = $g_mem->activate_membership($req_id,$g_view['activated'],$g_view['msg']);
/////////////////////////////////////////////////////
$g_view['page_heading'] = "Membership Activation";
$g_view['content_view'] = "active_view.php";
require("content_view.php");
////////////////////////////////////////////////////////////////////////////
?>