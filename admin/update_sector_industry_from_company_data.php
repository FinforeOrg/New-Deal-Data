<?php
include("../include/global.php");
require_once ("admin/checklogin.php");
require_once("classes/class.misc.php");
////////////
$g_view['msg'] = "";
//////////////////
if(isset($_POST['action'])&&($_POST['action']=="update")){
	
	$success = $g_misc->update_sector_industry_master_from_company();
	if(!$success){
		die("Cannot update sector industry master");
	}
	$g_view['msg'] = "sector industry inserted";
}
///////////////////////
$g_view['heading'] = "Update sector industry list from company data";
$g_view['content_view'] = "admin/update_sector_industry_from_company_data_view.php";
include("admin/content_view.php");
?>