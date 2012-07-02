<?php
include("../include/global.php");
require_once ("admin/checklogin.php");
require_once("classes/class.country.php");
///////////////////////////////////////////////////////

$g_view['msg'] = "";
$region_id = $_REQUEST['region_id'];
///////////////////////////////////////
$validation_passed = false;
$g_view['err'] = array();
if(isset($_POST['action'])&&($_POST['action']=="add_country")){
	$success = $g_country->add_country_to_region($region_id,$_POST['country_id'],$validation_passed,$g_view['err']);
	if(!$success){
		die("Cannot add the country to this region");
	}
	if($validation_passed){
		$g_view['msg'] = "Country added to this region";
	}
}
///////////////////////////////////////////////////////////
if(isset($_POST['action'])&&($_POST['action']=="remove_country")){
	$success = $g_country->remove_country_from_region($region_id,$_POST['country_id'],$g_view['msg']);
	if(!$success){
		die("Cannot remove the country from this region");
	}
}
/////////////////////////////////////////////////////////////
//get the region data
$g_view['region_data'] = NULL;
$success = $g_country->get_region_data($region_id,$g_view['region_data']);
if(!$success){
	die("Cannot get region data");
}
///////////////////////////////////////////////////////
//get the list of countries for the region
$g_view['region_country_data'] = array();
$g_view['region_country_data_count'] = 0;
$success = $g_country->get_all_country_list_for_region($region_id,$g_view['region_country_data'],$g_view['region_country_data_count']);
if(!$success){
	die("Cannot get the countries for the region");
}
////////////////////////////////////////////////
//get the list of all countries
$g_view['country_data_count'] = 0;
$g_view['country_data'] = array();
$success = $g_country->get_all_country_list($g_view['country_data'],$g_view['country_data_count']);
if(!$success){
	die("Cannot get country data");
}
///////////////////////////////////////////
$g_view['heading'] = "List of countries for region ".$g_view['region_data']['name'];
$g_view['content_view'] = "admin/region_country_list_view.php";
include("admin/content_view.php");
?>