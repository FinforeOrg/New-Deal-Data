<?php
include("../include/global.php");
require_once ("admin/checklogin.php");
require_once("classes/class.misc.php");
////////////
$g_view['msg'] = "";
//////////////////
if(isset($_POST['action'])&&($_POST['action']=="update")){
	
	$success = $g_misc->update_country_master_from_company_hq_countries();
	if(!$success){
		die("Cannot update country master");
	}
	$g_view['msg'] = "country/countries inserted";
}
///////////////////////
$g_view['heading'] = "Update country list from company HQ countries";
$g_view['content_view'] = "admin/update_country_list_from_company_data_view.php";
include("admin/content_view.php");
?>