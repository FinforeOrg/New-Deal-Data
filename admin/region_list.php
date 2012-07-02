<?php
include("../include/global.php");
require_once ("admin/checklogin.php");
require_once("classes/class.country.php");
///////////////////////////////////////////////////////

$g_view['msg'] = "";

///////////////////////////////////////
if(isset($_POST['action'])&&($_POST['action']=="delete_region")){
	$success = $g_country->remove_region($_POST['region_id'],$g_view['msg']);
	if(!$success){
		die("Cannot remove this region");
	}
}
/////////////////////////////////////////////////////////////
/********
sng:23/feb/2011
support for activate/deactivate
*****/
if(isset($_POST['action'])&&($_POST['action']=="toggle_active")){
	$success = $g_country->toggle_region_active($_POST['region_id'],$_POST['is_active'],$g_view['msg']);
	if(!$success){
		die("Cannot change the state of this region");
	}
}
/////////////////////////////////////////////////////////////
/********
sng:24/feb/2011
support for display order
*****/
if(isset($_POST['action'])&&($_POST['action']=="change_display_order")){
	$success = $g_country->set_region_display_order($_POST['region_id'],$_POST['display_order'],$g_view['msg']);
	if(!$success){
		die("Cannot change the display order of this region");
	}
}
///////////////////////////////////////////////////////////
//get the list of admin users
$g_view['data_count'] = 0;
$g_view['data'] = array();
/************************
sng:23/feb/2011
Now that the inactive regions are not shown, we need another method for admin to see active/inactive
************************/
$success = $g_country->admin_get_all_region_list($g_view['data'],$g_view['data_count']);
if(!$success){
	die("Cannot get region data");
}
////////////////////////////////////////////////
$g_view['heading'] = "List of Regions";
$g_view['content_view'] = "admin/region_list_view.php";
include("admin/content_view.php");
?>