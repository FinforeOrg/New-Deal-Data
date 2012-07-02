<?php
/********************************
sng:9/dec/2011
We now allow the members to specify corrections for each fields of a company.

Also, there can be more than one corrections suggested for a company. What we do is, show only the company that
has one or more corrections and allow admin to edit the company. In the edit page we show the corrections and who posted it.
***************************/
require_once("../include/global.php");
require_once ("admin/checklogin.php");
require_once("classes/class.company.php");
///////////////////////////////////////////////////////
$g_view['msg'] = "";


/////////////////////////////////////////////
//get the list of error deals
$g_view['data_count'] = 0;
$g_view['data'] = array();
$g_view['num_to_show'] = 50;
$g_view['start'] = 0;
if(isset($_REQUEST['start'])&&($_REQUEST['start']!="")){
	$g_view['start'] = $_REQUEST['start'];
}
/////////////////
$success = $g_company->get_error_companies_paged($g_view['start'],$g_view['num_to_show']+1,$g_view['data'],$g_view['data_count']);
if(!$success){
	die("Cannot get flagged company list");
}

////////////////////////////////////////////////
$g_view['heading'] = "List of Companies Flagged";
$g_view['content_view'] = "admin/company_correction_list_view.php";
include("admin/content_view.php");
?>