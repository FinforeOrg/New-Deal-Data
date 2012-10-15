<?php
/**********
sng:15/oct/2012

Allow admin to fetch company data from co-codes.com.
Since lots of processing is involved, the code that does the actual work runs in the background

see table background_slave_monitor where slave names are listed
************/
require_once("../include/global.php");
require_once ("admin/checklogin.php");

require_once("classes/class.background_slave_controller.php");
$master = new background_slave_controller();
$slave_to_run = "fetch_company_data_co_codes";

$g_view['started'] = false;
$g_view['msg'] = "";
$ok = $master->trigger_slave($slave_to_run,$g_view['started'],$g_view['msg']);
if(!$ok){
	die("Could not start the job");
}
$g_view['heading'] = "Fetch companies from co-codes.com";
$g_view['content_view'] = "admin/fetch_companies_from_co_codes_view.php";
require_once("admin/content_view.php");
?>