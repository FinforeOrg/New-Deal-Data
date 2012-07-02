<?php
/****************
sng:28/jun/2011
We now allow the members to specify corrections for each fields of a deal. This means, we no longer
use tombstone_transaction_error_reports and no longer show a simple report for a deal and who posted it.

Also, there can be more than one corrections suggested for a deal. What we do is, show only the deal that
has one or more corrections and allow admin to edit the deal. In the edit page we show the corrections and who posted it.
*********************/
require_once("../include/global.php");
require_once ("admin/checklogin.php");
require_once("classes/class.transaction.php");
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
$success = $g_trans->get_error_deals_paged($g_view['start'],$g_view['num_to_show']+1,$g_view['data'],$g_view['data_count']);
if(!$success){
	die("Cannot get flagged deal list");
}
////////////////////////////////////////////////
$g_view['heading'] = "List of Transactions Flagged";
$g_view['content_view'] = "admin/deals_marked_as_error_view.php";
include("admin/content_view.php");
?>