<?php
/*********************
sng:16/mar/2012

In the detailed deal submission, we have roles for the banks. If the user
change the deal type, the options will change.

This fetch options for both bank and law firm and update some javascript vars in suggest_a_deal.php.
Inside the suggest_a_deal.php, the dropdowns are updated.
*************************/
require_once("../../include/global.php");
require_once("classes/class.deal_support.php");
$deal_support = new deal_support();

$g_view['bank_roles'] = NULL;
$g_view['bank_roles_count'] = 0;

$g_view['law_firm_roles'] = NULL;
$g_view['law_firm_roles_count'] = 0;

$result = array();
$result['status'] = 0;

$result['bank_role_count'] = 0;
$result['bank_role_ids'] = array();
$result['bank_role_names'] = array();

$result['law_firm_role_count'] = 0;
$result['law_firm_role_ids'] = array();
$result['law_firm_role_names'] = array();

if(isset($_GET['deal_type'])){
	$g_view['deal_type'] = $_GET['deal_type'];
}else{
	$g_view['deal_type'] = "M&A";
}

$success = $deal_support->front_get_deal_partner_roles('bank',$g_view['deal_type'],$g_view['bank_roles'],$g_view['bank_roles_count']);

if(!$success){
	echo json_encode($result);
	return;
}

$result['bank_role_count'] = $g_view['bank_roles_count'];

for($bank_role_i=0;$bank_role_i<$g_view['bank_roles_count'];$bank_role_i++){
	$result['bank_role_ids'][$bank_role_i] = $g_view['bank_roles'][$bank_role_i]['role_id'];
	$result['bank_role_names'][$bank_role_i] = $g_view['bank_roles'][$bank_role_i]['role_name'];
}

$success = $deal_support->front_get_deal_partner_roles('law firm',$g_view['deal_type'],$g_view['law_firm_roles'],$g_view['law_firm_roles_count']);
if(!$success){
	echo json_encode($result);
	return;
}
$result['law_firm_role_count'] = $g_view['law_firm_roles_count'];

for($law_firm_role_i=0;$law_firm_role_i<$g_view['law_firm_roles_count'];$law_firm_role_i++){
	$result['law_firm_role_ids'][$law_firm_role_i] = $g_view['law_firm_roles'][$law_firm_role_i]['role_id'];
	$result['law_firm_role_names'][$law_firm_role_i] = $g_view['law_firm_roles'][$law_firm_role_i]['role_name'];
}

$result['status'] = 1;
echo json_encode($result);
return;
?>