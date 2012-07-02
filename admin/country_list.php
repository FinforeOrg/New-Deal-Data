<?php
include("../include/global.php");
require_once ("admin/checklogin.php");
require_once("classes/class.country.php");
///////////////////////////////////////////////////////

$g_view['msg'] = "";

///////////////////////////////////////

///////////////////////////////////////////////////////////
//get the list of admin users
$g_view['data_count'] = 0;
$g_view['data'] = array();
$success = $g_country->get_all_country_list($g_view['data'],$g_view['data_count']);
if(!$success){
	die("Cannot get country data");
}
////////////////////////////////////////////////
$g_view['heading'] = "List of Country";
$g_view['content_view'] = "admin/country_list_view.php";
include("admin/content_view.php");
?>