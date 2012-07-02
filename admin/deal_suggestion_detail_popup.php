<?php
require_once("../include/global.php");
require_once ("admin/checklogin.php");
require_once("classes/class.transaction.php");
require_once("nifty_functions.php");
///////////////////////////////////////////////////////
$g_view['msg'] = "";
//we also need to keep track of the page from where we came
$g_view['from'] = 0;
if(isset($_REQUEST['from'])&&($_REQUEST['from']!="")){
	$g_view['from'] = $_REQUEST['from'];
}
$g_view['id'] = $_REQUEST['id'];
//////////////////////////////////////




//get the suggestion data.
$g_view['data'] = NULL;
/////////////////
$success = $g_trans->get_deal_suggestion_detail($g_view['id'],$g_view['data']);
if(!$success){
	die("Cannot get deal suggestion detail");
}
////////////////////////////////////////////////
include("admin/deal_suggestion_detail_popup_view.php");
?>