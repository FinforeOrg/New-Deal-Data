<?php
/**********
see also getch_ma_metrics_data.php
***************/
require_once("../include/global.php");
require_once("classes/class.account.php");
require_once("classes/class.ma_metrics.php");
/******************************************************************/
$data = array();
/**************************************************************/
if(!$g_account->is_site_member_logged()){
	//send blank data
	$data['has_data'] = 0;
	$data['msg'] = "You need to login to access the data";
	echo json_encode($data);
	exit;
}
/**************************************************************/
$success = $g_ma_metrics->ajax_fetch_series_data($_POST['metrics_region_country_id'],$_POST['metrics_sector_industry_id'],$_GET['metrics_type_id'],$data);
if(!$success){
	$data['has_data'] = 0;
	$data['msg'] = "Db error";
	echo json_encode($data);
	exit;
}
/****************************************************************/
echo json_encode($data);
exit;

?>