<?php
/***************
sng:22/sep/2012
To be used with devbridge autocomplete
GET request with querystring ?query=Li
the json option is
{
 suggestions:['Liberia','Libyan Arab Jamahiriya','Liechtenstein','Lithuania'],
 data:['LR','LY','LI','LT']
}
query - original query value
suggestions - comma separated array of suggested values
***************/
require_once("../include/global.php");
require_once("classes/class.account.php");
require_once("classes/class.member.php");

$data_arr = array();
$data_arr['query'] = $_GET['query'];
$data_arr['suggestions'] = array();
$data_arr['data'] = array();

$temp_arr = NULL;
$temp_count = 0;

/****************************************
check login
************/
if(!$g_account->is_site_member_logged()){
	$data_arr['suggestions'][0] = "Need to login first";
	$data_arr['data'][0] = "Need to login first";
	echo json_encode($data_arr);
	exit;
}

$search_name = $_GET['query'];
$search_mem_type = $_SESSION['member_type'];
$search_company_id = $_SESSION['company_id'];
/***********
Member of same bank/law firm, not ghost
*************/
$ok = $g_mem->ajax_get_members_for_delegates($search_name,$search_mem_type,$search_company_id,10,$temp_arr,$temp_count);
if(!$ok){
	$data_arr['suggestions'][0] = "Error fetching data";
	$data_arr['data'][0] = "Error fetching data";
	echo json_encode($data_arr);
	exit;
}

for($i=0;$i<$temp_count;$i++){
	$data_arr['suggestions'][] = $temp_arr[$i]['f_name']." ".$temp_arr[$i]['l_name']." - ".$temp_arr[$i]['designation']." [".$temp_arr[$i]['work_email']."]";
	$data_arr['data'][] = $temp_arr[$i]['f_name']." ".$temp_arr[$i]['l_name']."|".$temp_arr[$i]['mem_id'];
	/*************
	The receiver split by |
	*************/
}
echo json_encode($data_arr);
exit;
?>