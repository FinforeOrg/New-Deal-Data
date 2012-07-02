<?php
require_once("../include/global.php");
require_once ("admin/checklogin.php");
require_once("classes/class.transaction.php");
require_once("classes/class.magic_quote.php");

$g_view['deal_id'] = $_POST['transaction_id'];
/***************************************************************************************
adding a case study

sng:18/nov/2011
For data-cx admin cannot upload case study
*/

/*******************************************************************************************
delete a case study

sng:19/nov/2011
for data-cx, we do not allow to delete from here.

if(isset($_POST['action'])&&($_POST['action']=="delete")){
	$success = $g_trans->delete_case_study($_POST['case_study_id']);
	if(!$success){
		die("Cannot delete the case study");
	}
	$g_view['msg'] = "Case study deleted";
}
**/
/***********************************************************************
get all the banks for this deal
**/
$g_view['banks'] = array();
$g_view['banks_cnt'] = 0;
$success = $g_trans->get_all_partner($g_view['deal_id'],"bank",$g_view['banks'],$g_view['banks_cnt']);
if(!$success){
	die("cannot get the banks for this deal");
}
/*************************************************************************
get all the case studies for this deal by the banks in the deal
***/
$g_view['bank_case_studies'] = array();
$g_view['bank_case_studies_cnt'] = 0;
$success = $g_trans->get_all_case_studies_for_partner_type($g_view['deal_id'],"bank",$g_view['bank_case_studies'],$g_view['bank_case_studies_cnt']);
if(!$success){
	die("cannot get the case studies by banks for this deal");
}
/***********************************************************************
get all the law firms for this deal
**/
$g_view['law_firms'] = array();
$g_view['law_firms_cnt'] = 0;
$success = $g_trans->get_all_partner($g_view['deal_id'],"law firm",$g_view['law_firms'],$g_view['law_firms_cnt']);
if(!$success){
	die("cannot get the law firm for this deal");
}
/*************************************************************************
get all the case studies for this deal by the law firms in the deal
***/
$g_view['law_firm_case_studies'] = array();
$g_view['law_firm_case_studies_cnt'] = 0;
$success = $g_trans->get_all_case_studies_for_partner_type($g_view['deal_id'],"law firm",$g_view['law_firm_case_studies'],$g_view['law_firm_case_studies_cnt']);
if(!$success){
	die("cannot get the case studies by law firm for this deal");
}

$g_view['heading'] = "Case Studies";
$g_view['content_view'] = "admin/deal_case_study_view.php";
include("admin/content_view.php");
?>