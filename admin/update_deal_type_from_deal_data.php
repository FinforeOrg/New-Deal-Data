<?php
include("../include/global.php");
require_once ("admin/checklogin.php");
require_once("classes/class.misc.php");
////////////
$g_view['msg'] = "";
//////////////////
if(isset($_POST['action'])&&($_POST['action']=="update")){
	
	$success = $g_misc->update_deal_type_master_from_deal_type();
	if(!$success){
		die("Cannot update deal type master");
	}
	$g_view['msg'] = "deal types inserted";
}
///////////////////////
$g_view['heading'] = "Update deal type subtype from deal data";
$g_view['content_view'] = "admin/update_deal_type_from_deal_data_view.php";
include("admin/content_view.php");
?>