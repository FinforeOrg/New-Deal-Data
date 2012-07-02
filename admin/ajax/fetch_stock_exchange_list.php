<?php
/**********
used by admin code to get the list of stock exchanges.
To be used with devbridge auto-complete
************/
/******
this is the devbridge autocomplete
GET request with querystring ?query=Li
the json option is
{
 query:'Li',
 suggestions:['Liberia','Libyan Arab Jamahiriya','Liechtenstein','Lithuania'],
 data:['LR','LY','LI','LT']
}
query - original query value
suggestions - comma separated array of suggested values
*************/
require_once("../../include/global.php");
require_once("classes/class.deal_support.php");
$deal_support = new deal_support();

$input = $_GET['query'];
$data_count = 0;
$data_arr = array();
$data_arr['query'] = $_GET['query'];
$data_arr['suggestions'] = array();
$data_arr['data'] = array();

$temp_arr = NULL;

$success = $deal_support->ajax_admin_get_stock_exchange_suggestions($input,$temp_arr,$data_count);
if(!$success){
	$data_arr['suggestions'][0] = "Error fetching list";
	$data_arr['data'][0] = "Error fetching list";
	echo json_encode($data_arr);
	exit;
}
for($i=0;$i<$data_count;$i++){
	$data_arr['suggestions'][] = $temp_arr[$i]['name'];
	$data_arr['data'][] = $temp_arr[$i]['name'];
}
echo json_encode($data_arr);
exit;
?>