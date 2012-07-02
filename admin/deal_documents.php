<?php
require_once("../include/global.php");
require_once ("admin/checklogin.php");
require_once("classes/class.transaction_doc.php");
$trans_doc = new transaction_doc();

$g_view['deal_id'] = $_POST['transaction_id'];
/***************************************************************************************
adding a case study
*/
$validation_passed = false;
$g_view['err'] = array();

if(isset($_POST['action'])&&($_POST['action']=="add")){
	//since this is admin doing the upload, mem_id is 0 is is_approved is y
	$success = $trans_doc->add_document($_POST['transaction_id'],0,'y',$validation_passed,$g_view['err']);
	if(!$success){
		die("Cannot upload document");
	}
	$g_view['msg'] = "Document uploaded";
}
/*******************************************************************************************
delete a document
**/
if(isset($_POST['action'])&&($_POST['action']=="delete")){
	$success = $trans_doc->delete_document($_POST['doc_id']);
	if(!$success){
		die("Cannot delete the document");
	}
	$g_view['msg'] = "Decument deleted";
}
/*************************************************************************
get all the documents for this deal
***/
$g_view['docs'] = array();
$g_view['docs_cnt'] = 0;
$success = $trans_doc->get_all_documents($g_view['deal_id'],$g_view['docs'],$g_view['docs_cnt']);
if(!$success){
	die("cannot get the documents for this deal");
}

$g_view['heading'] = "Documents";
$g_view['content_view'] = "admin/deal_documents_view.php";
include("admin/content_view.php");
?>