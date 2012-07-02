<?php
include("../include/global.php");
require_once ("admin/checklogin.php");
require_once("classes/class.transaction.php");
///////////////////////////////////////////////////////
$g_view['msg'] = "";
/********************************************************************************
sng:19/nov/2011
We now do the approval in ajax. No longer form submission is needed
************************************************************************/


$g_view['data_count'] = 0;
$g_view['data'] = array();
$g_view['num_to_show'] = 3;
$g_view['start'] = 0;
if(isset($_REQUEST['start'])&&($_REQUEST['start']!="")){
	$g_view['start'] = $_REQUEST['start'];
}

/************************
sng:18/nov/2011
We now have access rules for case study. We get the codes, we need the rule name
*************************/
$g_view['access_rule_name'] = array();
$g_view['access_rule'] = $g_trans->access_rules_for_case_studies();
foreach($g_view['access_rule'] as $rule){
	$g_view['access_rule_name'][$rule['rule_code']] = $rule['rule_name'];
}

/******************************
sng:9/dec/2011
we need to filter and order
filterby: blank - show all
filterby: flagged - show only flagged case studies
orderby: blank - default ordering
orderby: downloaded - order by number of times downloaded
orderby: date_uploaded - order by date of upload
*********************************/
$g_view['filterby'] = "";
$g_view['orderby'] = "";
if(isset($_GET['filterby'])){
	$g_view['filterby'] = $_GET['filterby'];
}
if(isset($_GET['orderby'])){
	$g_view['orderby'] = $_GET['orderby'];
}

$success = $g_trans->get_all_case_studies_paged($g_view['filterby'],$g_view['orderby'],$g_view['start'],$g_view['num_to_show']+1,$g_view['data'],$g_view['data_count']);
if(!$success){
	die("Cannot get list case studies");
}
////////////////////////////////////////////////
$g_view['heading'] = "Case Studies Suggested on Deals";
$g_view['content_view'] = "admin/list_case_studies_view.php";
include("admin/content_view.php");
?>