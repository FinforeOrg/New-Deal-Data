<?php
/*$data['has_data'] = 1;
$data['points'] = array(10,50,50,78,67);
$data['avg'] = array(15,20,30,80,25);
$data['labels'] = array('jan','feb','mar','apr','may');
echo json_encode($data);
exit;*/

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
/**************************************************************
//as metrics type id is sent as GET, we put that in POST
*****/
$_POST['metrics_type_id'] = $_GET['metrics_type_id'];
$success = $g_ma_metrics->ajax_front_fetch_series_data($_POST,$data);
if(!$success){
	$data['has_data'] = 0;
	$data['msg'] = "Db error";
	echo json_encode($data);
	exit;
}
/****************************************************************/
echo json_encode($data);
exit;

/*$data['points'] = array(10,50,50,78,67);
$data['avg'] = array(15,20,30,80,25);
$data['labels'] = array('jan','feb','mar','apr','may');
echo json_encode($data);*/
?>