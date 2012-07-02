<?php
/***************************
sng:27/oct/2011

This is like secure_company_list.php, but use json and devbridge auto complete

GET request with querystring ?query=Li
the json option is
{
 suggestions:['Liberia','Libyan Arab Jamahiriya','Liechtenstein','Lithuania'],
 data:['LR','LY','LI','LT']
}
query - original query value
suggestions - comma separated array of suggested values
****************************/
require_once("../include/global.php");
require_once("classes/class.account.php");
require_once("classes/class.company.php");

$input = $_GET['query'];
$type = $_GET['type'];
$data_count = 0;
$data_arr = array();
$data_arr['query'] = $_GET['query'];
$data_arr['suggestions'] = array();
$data_arr['data'] = array();

$temp_arr = NULL;

/****************************************
check login
************/
if(!$g_account->is_site_member_logged()){
	$data_arr['suggestions'][0] = "Need to login first";
	$data_arr['data'][0] = "Need to login first";
	echo json_encode($data_arr);
	exit;
}
/*******************************************/
$success = $g_company->filter_company_name_list_by_type_name($type,$input,false,$temp_arr,$data_count);
if(!$success){
	$data_arr['suggestions'][0] = "Error fetching list";
	$data_arr['data'][0] = "Error fetching list";
	echo json_encode($data_arr);
	exit;
}
for($i=0;$i<$data_count;$i++){
	$data_arr['suggestions'][] = $temp_arr[$i]['name'];
	$data_arr['data'][] = $temp_arr[$i]['company_id'];
}
echo json_encode($data_arr);
exit;
?>