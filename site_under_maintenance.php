<?php
/***
called inside global.php
**/
$this_path = $_SERVER['REQUEST_URI'];
if((strpos($this_path,"/sa/")===false)&&(strpos($this_path,"/admin/")===false)){
	/***
	front
	***/
	require_once("classes/class.sitesetup.php");
	$g_view['site_maintenance_data'] = array();
	$success = $g_site->get_maintenance_info($g_view['site_maintenance_data']);
	if(!$success){
		die("Cannot get site maintenance data");
	}
	if($g_view['site_maintenance_data']['site_in_maintenance']=='Y'){
		//site is flagged for maintenance
		$g_view['site_maintenance_data']['site_in_maintenance_text'] = nl2br($g_view['site_maintenance_data']['site_in_maintenance_text']);
		include("site_under_maintenance_view.php");
		exit;
	}
}
//////////////////////////////
//admin, return
?>