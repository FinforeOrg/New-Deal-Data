<?php
ini_set('display_errors',1);
error_reporting(E_ALL ^ E_NOTICE);
include("../include/global.php");
require_once ("admin/checklogin.php");
require_once("classes/class.company.php");
require_once("classes/class.misc.php");
require_once("classes/class.transaction.php");
require_once("classes/class.country.php");
require_once("classes/class.account.php");
require_once("classes/class.statistics.php");
require_once("classes/class.oneStop.php");
///////////////////////////////////////////////////////

if (isset($_POST['submit'])) {
    if (!oneStop::saveOptions()) {
      $g_view['msg'] = "Options could not be saved";    
    } else {
      $g_view['msg'] = "Options saved";  
    } 
    
} else {
    oneStop::loadOptions();
    $g_view['msg'] = "";   
}


$g_view['heading'] = "One stop options";
$g_view['content_view'] = "admin/oneStopOptions_view.php";
$g_view['deal_size_filter_list_count'] = 0;
$success = $g_trans->front_get_deal_size_filter_list($g_view['deal_size_filter_list'],$g_view['deal_size_filter_list_count']);
include("admin/content_view.php");

?>