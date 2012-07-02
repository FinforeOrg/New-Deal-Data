<?php
/********
sng:27/oct/2010
It may happen that admin wants to see the transactions where a bank has been added twice (while bulk uploading)
If so, we just show the list of banks and allow to remove duplicate, no addition.
That is why we use this instead of deal_bank_popup.php
**********/
include("../include/global.php");
require_once ("admin/checklogin.php");
require_once("classes/class.company.php");
require_once("classes/class.transaction.php");
require_once("classes/class.magic_quote.php");
///////////////////////////////////////////////////////
$g_view['err'] = array();
$g_view['msg'] = "";
$g_view['input'] = array();
///////////////////////////////////////

if(isset($_POST['action'])&&($_POST['action']=="remove_duplicate")){
	
	$success = $g_trans->remove_duplicate_partner($_POST,"bank",$g_view['msg']);
	if(!$success){
		die("Cannot remove the bank from the deal");
	}
	
}
/////////////////////////////////////////////////////////////
//get_all_partner_name_list
$g_view['data_count'] = 0;
$g_view['data'] = NULL;
$success = $g_trans->get_all_duplicate_partners($_REQUEST['transaction_id'],"bank",$g_view['data'],$g_view['data_count']);
if(!$success){
	die("Cannot get partner data");
}
///////////////////////////////////////////////////////////
include("admin/deal_duplicate_bank_popup_view.php");
?>