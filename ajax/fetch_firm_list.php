<?php
/***************************
sng:27/nov/2011

Easy way out to show matching banks and law firms

We use json and devbridge auto complete

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
require_once("classes/db.php");

$input = $_GET['query'];

$data_count = 0;
$data_arr = array();
$data_arr['query'] = $_GET['query'];
$data_arr['suggestions'] = array();
$data_arr['data'] = array();

$temp_arr = NULL;


/*******************************************/
$q = "select company_id,name,type from ".TP."company where (type='bank' OR type='law firm') AND name like '".mysql_real_escape_string($input)."%'";
$success = $g_db->select_query_limited($q,0,10);
if(!$success){
	$data_arr['suggestions'][0] = "Error fetching list";
	$data_arr['data'][0] = "Error fetching list";
	echo json_encode($data_arr);
	exit;
}

$data_count = $g_db->row_count();
$temp_arr = $g_db->get_result_set_as_array();

for($i=0;$i<$data_count;$i++){
	$data_arr['suggestions'][] = $temp_arr[$i]['name']." [".$temp_arr[$i]['type']."]";
	$data_arr['data'][] = $temp_arr[$i]['company_id'];
}
echo json_encode($data_arr);
exit;
?>