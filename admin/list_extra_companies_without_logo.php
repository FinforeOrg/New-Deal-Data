<?php
include("../include/global.php");
require_once ("admin/checklogin.php");
require_once("classes/class.company.php");
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
$success = $g_company->get_all_extra_company_without_logo_list_paged($g_view['start'],$g_view['num_to_show']+1,$g_view['data'],$g_view['data_count']);
if(!$success){
	die("Cannot get extra company");
}
////////////////////////////////////////////////
$g_view['heading'] = "List of Extra Companies without logo";
$g_view['content_view'] = "admin/list_extra_companies_without_logo_view.php";
include("admin/content_view.php");
?>